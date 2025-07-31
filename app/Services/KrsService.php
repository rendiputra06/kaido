<?php

namespace App\Services;

use App\Interfaces\KrsRepositoryInterface;
use App\Interfaces\KrsServiceInterface;
use App\Interfaces\PeriodeKrsRepositoryInterface;
use App\Interfaces\JadwalServiceInterface;
use App\Models\KrsMahasiswa;
use App\Models\KrsDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KrsService implements KrsServiceInterface
{
    protected KrsRepositoryInterface $krsRepository;
    protected PeriodeKrsRepositoryInterface $periodeRepository;
    protected JadwalServiceInterface $jadwalService;

    public function __construct(
        KrsRepositoryInterface $krsRepository,
        PeriodeKrsRepositoryInterface $periodeRepository,
        JadwalServiceInterface $jadwalService
    ) {
        $this->krsRepository = $krsRepository;
        $this->periodeRepository = $periodeRepository;
        $this->jadwalService = $jadwalService;
    }

    /**
     * Membuat KRS baru untuk mahasiswa
     */
    public function createKrs(int $mahasiswaId, int $periodeId, int $dosenPaId): KrsMahasiswa
    {
        // Cek apakah periode KRS aktif
        $periode = $this->periodeRepository->getActivePeriod();
        if (!$periode || $periode->id !== $periodeId) {
            throw new \Exception('Periode KRS tidak aktif atau tidak ditemukan');
        }

        // Cek apakah mahasiswa sudah memiliki KRS di periode ini
        $existingKrs = $this->krsRepository->getKrsByMahasiswaAndPeriode($mahasiswaId, $periodeId);
        if ($existingKrs) {
            throw new \Exception('Mahasiswa sudah memiliki KRS di periode ini');
        }

        // Buat KRS baru
        return $this->krsRepository->createKrs([
            'mahasiswa_id' => $mahasiswaId,
            'periode_krs_id' => $periodeId,
            'dosen_pa_id' => $dosenPaId,
            'status' => 'draft',
            'total_sks' => 0,
            'max_sks' => 24, // Default max SKS
        ]);
    }

    /**
     * Menambahkan mata kuliah ke KRS
     */
    public function addMataKuliah(int $krsId, int $kelasId): KrsDetail
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        if (!$krs) {
            throw new \Exception('KRS tidak ditemukan');
        }

        // Cek status KRS
        if ($krs->status !== \App\Enums\KrsStatusEnum::DRAFT) {
            throw new \Exception('KRS sudah disubmit, tidak bisa menambah mata kuliah');
        }

        // Validasi bentrok jadwal
        $this->validateScheduleConflict($krsId, $kelasId);

        // Validasi SKS maksimum
        $this->validateMaxSks($krsId, $kelasId);
        
        // Validasi prasyarat mata kuliah
        $this->validatePrerequisites($krs->mahasiswa_id, $kelasId);

        // Tambahkan detail KRS
        $krsDetail = $this->krsRepository->addKrsDetail($krsId, $kelasId);

        // Update total SKS
        $this->updateTotalSks($krsId);

        Log::info('Mata kuliah ditambahkan ke KRS', [
            'krs_id' => $krsId,
            'kelas_id' => $kelasId,
            'mahasiswa_id' => $krs->mahasiswa_id,
        ]);

        return $krsDetail;
    }

    /**
     * Menghapus mata kuliah dari KRS
     */
    public function removeMataKuliah(int $krsId, int $krsDetailId): bool
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        if (!$krs) {
            throw new \Exception('KRS tidak ditemukan');
        }

        // Cek status KRS
        if ($krs->status !== \App\Enums\KrsStatusEnum::DRAFT) {
            throw new \Exception('KRS sudah disubmit, tidak bisa menghapus mata kuliah');
        }

        // Hapus detail KRS
        $result = $this->krsRepository->removeKrsDetail($krsDetailId);

        if ($result) {
            // Update total SKS
            $this->updateTotalSks($krsId);

            Log::info('Mata kuliah dihapus dari KRS', [
                'krs_id' => $krsId,
                'krs_detail_id' => $krsDetailId,
                'mahasiswa_id' => $krs->mahasiswa_id,
            ]);
        }

        return $result;
    }

    /**
     * Submit KRS untuk persetujuan
     */
    public function submitKrs(int $krsId): KrsMahasiswa
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        if (!$krs) {
            throw new \Exception('KRS tidak ditemukan');
        }

        // Cek status KRS
        if ($krs->status !== \App\Enums\KrsStatusEnum::DRAFT) {
            throw new \Exception('KRS sudah disubmit sebelumnya');
        }

        // Cek apakah ada mata kuliah yang diambil
        if ($krs->krsDetails->where('status', 'active')->count() === 0) {
            throw new \Exception('KRS harus memiliki minimal satu mata kuliah');
        }

        // Update status KRS
        $krs = $this->krsRepository->updateKrs($krsId, [
            'status' => 'submitted',
            'tanggal_submit' => now(),
        ]);

        Log::info('KRS disubmit untuk persetujuan', [
            'krs_id' => $krsId,
            'mahasiswa_id' => $krs->mahasiswa_id,
            'dosen_pa_id' => $krs->dosen_pa_id,
        ]);

        return $krs;
    }

    /**
     * Persetujuan KRS oleh dosen PA
     */
    public function approveKrs(int $krsId, string $catatan = null): KrsMahasiswa
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        if (!$krs) {
            throw new \Exception('KRS tidak ditemukan');
        }

        // Cek status KRS
        if ($krs->status !== \App\Enums\KrsStatusEnum::SUBMITTED) {
            throw new \Exception('KRS belum disubmit');
        }

        // Mulai transaksi untuk atomic operation
        return \DB::transaction(function () use ($krsId, $catatan, $krs) {
            // Ambil semua detail KRS yang aktif
            $activeDetails = $krs->krsDetails()->where('status', 'active')->get();
            
            // Kurangi kuota untuk setiap kelas yang diambil
            foreach ($activeDetails as $detail) {
                $kelas = $detail->kelas;
                
                // Cek apakah masih ada kuota yang tersedia
                if ($kelas->sisa_kuota <= 0) {
                    throw new \Exception("Kuota kelas {$kelas->kode_kelas} sudah penuh");
                }
                
                // Kurangi kuota secara atomik
                $kelas->decrement('sisa_kuota');
                
                Log::info('Kuota kelas berkurang', [
                    'kelas_id' => $kelas->id,
                    'kode_kelas' => $kelas->kode_kelas,
                    'mahasiswa_id' => $krs->mahasiswa_id,
                    'krs_id' => $krsId,
                    'sisa_kuota' => $kelas->fresh()->sisa_kuota,
                ]);
            }

            // Update status KRS
            $krs = $this->krsRepository->updateKrs($krsId, [
                'status' => 'approved',
                'catatan_pa' => $catatan,
                'tanggal_approval' => now(),
            ]);

            Log::info('KRS disetujui oleh dosen PA', [
                'krs_id' => $krsId,
                'mahasiswa_id' => $krs->mahasiswa_id,
                'dosen_pa_id' => $krs->dosen_pa_id,
                'total_kelas' => $activeDetails->count(),
            ]);

            return $krs;
        });
    }

    /**
     * Penolakan KRS oleh dosen PA
     */
    public function rejectKrs(int $krsId, string $catatan): KrsMahasiswa
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        if (!$krs) {
            throw new \Exception('KRS tidak ditemukan');
        }

        // Cek status KRS
        if ($krs->status !== \App\Enums\KrsStatusEnum::SUBMITTED) {
            throw new \Exception('KRS belum disubmit');
        }

        // Update status KRS
        $krs = $this->krsRepository->updateKrs($krsId, [
            'status' => 'rejected',
            'catatan_pa' => $catatan,
            'tanggal_approval' => now(),
        ]);

        Log::info('KRS ditolak oleh dosen PA', [
            'krs_id' => $krsId,
            'mahasiswa_id' => $krs->mahasiswa_id,
            'dosen_pa_id' => $krs->dosen_pa_id,
            'catatan' => $catatan,
        ]);

        return $krs;
    }
    
    /**
     * Reset status KRS menjadi draft
     * 
     * Fungsi ini digunakan oleh admin untuk mereset status KRS dalam kasus khusus
     * seperti kesalahan persetujuan atau kebutuhan revisi setelah KRS disubmit/diapprove
     */
    public function resetKrsStatus(int $krsId): KrsMahasiswa
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        if (!$krs) {
            throw new \Exception('KRS tidak ditemukan');
        }

        // Hanya bisa reset jika status bukan draft
        if ($krs->status === \App\Enums\KrsStatusEnum::DRAFT) {
            throw new \Exception('KRS sudah berstatus draft');
        }

        // Catat status sebelumnya untuk log
        $previousStatus = $krs->status;

        // Update status KRS menjadi draft
        $updatedKrs = $this->krsRepository->updateKrs($krsId, [
            'status' => 'draft',
            'tanggal_submit' => null,
            'tanggal_approval' => null,
        ]);

        Log::info('Status KRS direset oleh admin', [
            'krs_id' => $krsId,
            'mahasiswa_id' => $krs->mahasiswa_id,
            'previous_status' => $previousStatus,
        ]);

        return $updatedKrs;
    }

    /**
     * Validasi bentrok jadwal
     */
    private function validateScheduleConflict(int $krsId, int $kelasId): void
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        $newKelas = \App\Models\Kelas::with('jadwalKuliahs')->find($kelasId);

        if (!$krs || !$newKelas) {
            throw new \Exception('Data tidak valid');
        }

        // Ambil jadwal kelas yang sudah diambil
        $existingSchedules = $krs->krsDetails()
            ->where('status', 'active')
            ->with('kelas.jadwalKuliahs')
            ->get();

        foreach ($existingSchedules as $existingDetail) {
            $existingKelas = $existingDetail->kelas;

            foreach ($existingKelas->jadwalKuliahs as $existingJadwal) {
                foreach ($newKelas->jadwalKuliahs as $newJadwal) {
                    // Cek bentrok hari dan jam
                    if ($existingJadwal->hari === $newJadwal->hari) {
                        $existingStart = strtotime($existingJadwal->jam_mulai);
                        $existingEnd = strtotime($existingJadwal->jam_selesai);
                        $newStart = strtotime($newJadwal->jam_mulai);
                        $newEnd = strtotime($newJadwal->jam_selesai);

                        if (($existingStart < $newEnd) && ($newStart < $existingEnd)) {
                            throw new \Exception("Bentrok jadwal dengan mata kuliah {$existingKelas->mataKuliah->nama_mk} pada hari {$existingJadwal->hari}");
                        }
                    }
                }
            }
        }
    }

    /**
     * Validasi SKS maksimum
     */
    private function validateMaxSks(int $krsId, int $kelasId): void
    {
        $krs = $this->krsRepository->getKrsById($krsId);
        $newKelas = \App\Models\Kelas::with('mataKuliah')->find($kelasId);

        if (!$krs || !$newKelas) {
            throw new \Exception('Data tidak valid');
        }

        $currentSks = $this->krsRepository->calculateTotalSks($krsId);
        $newSks = $newKelas->mataKuliah->sks;
        $maxSks = $krs->max_sks;

        if (($currentSks + $newSks) > $maxSks) {
            throw new \Exception("Total SKS ({$currentSks} + {$newSks}) melebihi batas maksimum ({$maxSks})");
        }
    }

    /**
     * Update total SKS KRS
     */
    private function updateTotalSks(int $krsId): void
    {
        $totalSks = $this->krsRepository->calculateTotalSks($krsId);
        $this->krsRepository->updateKrs($krsId, ['total_sks' => $totalSks]);
    }
    
    /**
     * Validasi prasyarat mata kuliah
     * 
     * Memeriksa apakah mahasiswa telah lulus semua mata kuliah prasyarat
     * untuk mata kuliah yang ingin diambil
     */
    private function validatePrerequisites(int $mahasiswaId, int $kelasId): void
    {
        // Ambil data kelas dan mata kuliah yang ingin diambil
        $kelas = \App\Models\Kelas::with('mataKuliah.prasyarats')->find($kelasId);
        
        if (!$kelas || !$kelas->mataKuliah) {
            throw new \Exception('Data kelas atau mata kuliah tidak valid');
        }
        
        $mataKuliah = $kelas->mataKuliah;
        
        // Jika tidak ada prasyarat, langsung return
        if ($mataKuliah->prasyarats->isEmpty()) {
            return;
        }
        
        // Ambil daftar mata kuliah yang telah diambil dan lulus oleh mahasiswa
        $lulusMataKuliah = \App\Models\NilaiAkhir::where('mahasiswa_id', $mahasiswaId)
            ->where('nilai_huruf', '!=', 'E') // Nilai E dianggap tidak lulus
            ->with('krsDetail.kelas.mataKuliah')
            ->get()
            ->pluck('krsDetail.kelas.mataKuliah.id')
            ->toArray();
        
        // Cek setiap prasyarat
        $belumLulus = [];
        foreach ($mataKuliah->prasyarats as $prasyarat) {
            if (!in_array($prasyarat->id, $lulusMataKuliah)) {
                $belumLulus[] = $prasyarat->nama_mk;
            }
        }
        
        // Jika ada prasyarat yang belum lulus, throw exception
        if (!empty($belumLulus)) {
            throw new \Exception('Anda belum lulus mata kuliah prasyarat: ' . implode(', ', $belumLulus));
        }
    }
}
