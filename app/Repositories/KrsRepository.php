<?php

namespace App\Repositories;

use App\Interfaces\KrsRepositoryInterface;
use App\Models\KrsMahasiswa;
use App\Models\KrsDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KrsRepository implements KrsRepositoryInterface
{
    /**
     * Mengambil KRS mahasiswa berdasarkan mahasiswa dan periode
     */
    public function getKrsByMahasiswaAndPeriode(int $mahasiswaId, int $periodeId): ?KrsMahasiswa
    {
        return KrsMahasiswa::with(['krsDetails.kelas.mataKuliah', 'periodeKrs', 'dosenPa'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('periode_krs_id', $periodeId)
            ->first();
    }

    /**
     * Mengambil semua KRS mahasiswa dengan paginasi
     */
    public function getAllKrs(int $perPage = 10): LengthAwarePaginator
    {
        return KrsMahasiswa::with(['mahasiswa', 'periodeKrs', 'dosenPa'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Mengambil KRS berdasarkan ID
     */
    public function getKrsById(int $id): ?KrsMahasiswa
    {
        return KrsMahasiswa::with(['krsDetails.kelas.mataKuliah', 'mahasiswa', 'periodeKrs', 'dosenPa'])
            ->find($id);
    }

    /**
     * Membuat KRS baru
     */
    public function createKrs(array $data): KrsMahasiswa
    {
        return KrsMahasiswa::create($data);
    }

    /**
     * Memperbarui KRS
     */
    public function updateKrs(int $id, array $data): ?KrsMahasiswa
    {
        $krs = $this->getKrsById($id);

        if ($krs) {
            $krs->update($data);
            return $krs;
        }

        return null;
    }

    /**
     * Menghapus KRS
     */
    public function deleteKrs(int $id): bool
    {
        $krs = $this->getKrsById($id);

        if ($krs) {
            return $krs->delete();
        }

        return false;
    }

    /**
     * Menambahkan detail KRS (mata kuliah)
     */
    public function addKrsDetail(int $krsId, int $kelasId): KrsDetail
    {
        $krs = $this->getKrsById($krsId);
        $kelas = \App\Models\Kelas::with('mataKuliah')->find($kelasId);

        if (!$krs || !$kelas) {
            throw new \Exception('KRS atau Kelas tidak ditemukan');
        }

        // Cek apakah kelas sudah diambil
        if ($this->isKelasAlreadyTaken($krsId, $kelasId)) {
            throw new \Exception('Kelas sudah diambil dalam KRS ini');
        }

        // Cek sisa kuota kelas
        if ($kelas->sisa_kuota <= 0) {
            throw new \Exception('Kuota kelas sudah penuh');
        }

        // Buat detail KRS
        $krsDetail = KrsDetail::create([
            'krs_mahasiswa_id' => $krsId,
            'kelas_id' => $kelasId,
            'sks' => $kelas->mataKuliah->sks,
            'status' => 'active',
        ]);

        // Kurangi sisa kuota kelas
        $kelas->decrement('sisa_kuota');

        return $krsDetail;
    }

    /**
     * Menghapus detail KRS
     */
    public function removeKrsDetail(int $krsDetailId): bool
    {
        $krsDetail = KrsDetail::with('kelas')->find($krsDetailId);

        if ($krsDetail) {
            // Tambah kembali sisa kuota kelas
            $krsDetail->kelas->increment('sisa_kuota');

            return $krsDetail->delete();
        }

        return false;
    }

    /**
     * Mengambil KRS mahasiswa bimbingan dosen
     */
    public function getKrsByDosenPa(int $dosenId, int $perPage = 10): LengthAwarePaginator
    {
        return KrsMahasiswa::with(['mahasiswa', 'periodeKrs', 'krsDetails.kelas.mataKuliah'])
            ->where('dosen_pa_id', $dosenId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Mengambil KRS berdasarkan status
     */
    public function getKrsByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return KrsMahasiswa::with(['mahasiswa', 'periodeKrs', 'dosenPa'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Validasi apakah kelas sudah diambil dalam KRS
     */
    public function isKelasAlreadyTaken(int $krsId, int $kelasId): bool
    {
        return KrsDetail::where('krs_mahasiswa_id', $krsId)
            ->where('kelas_id', $kelasId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Hitung total SKS KRS
     */
    public function calculateTotalSks(int $krsId): int
    {
        return KrsDetail::where('krs_mahasiswa_id', $krsId)
            ->where('status', 'active')
            ->join('kelas', 'krs_details.kelas_id', '=', 'kelas.id')
            ->join('mata_kuliahs', 'kelas.mata_kuliah_id', '=', 'mata_kuliahs.id')
            ->sum('mata_kuliahs.sks');
    }
    
    /**
     * Reset status KRS menjadi draft
     *
     * @param KrsMahasiswa $krs
     * @return KrsMahasiswa
     */
    public function resetKrsStatus(KrsMahasiswa $krs): KrsMahasiswa
    {
        $krs->update([
            'status' => 'draft',
            'tanggal_submit' => null,
            'tanggal_approval' => null,
        ]);
        
        return $krs;
    }
}
