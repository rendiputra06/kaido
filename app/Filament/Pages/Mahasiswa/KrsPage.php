<?php

namespace App\Filament\Pages\Mahasiswa;

use Filament\Pages\Page;
use App\Interfaces\KrsRepositoryInterface;
use App\Interfaces\PeriodeKrsRepositoryInterface;
use App\Models\Kelas;
use App\Models\KrsMahasiswa;
use App\Services\KrsService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class KrsPage extends Page
{
    use HasPageShield;

    protected static string $permissionName = 'krs_page';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static string $view = 'filament.pages.mahasiswa.krs-page-v4';
    protected static ?string $title = 'Kartu Rencana Studi';
    protected static ?string $slug = 'mahasiswa/krs';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?int $navigationSort = 1;

    public ?KrsMahasiswa $krs = null;
    public $availableClasses = [];
    public $activePeriod = null;
    public $totalSks = 0;
    public $maxSks = 24;
    public $mahasiswa = null;
    public $semesterSekarang = 1;
    
    // Search and filter properties
    public $search = '';
    public $semesterFilter = '';
    public $jenisFilter = '';

    public function mount(): void
    {
        $this->mahasiswa = Auth::user()->mahasiswa;
        $this->loadKrsData();
    }

    public function loadKrsData(): void
    {
        if (!$this->mahasiswa) {
            $this->mahasiswa = Auth::user()->mahasiswa;
        }

        if (!$this->mahasiswa) {
            Notification::make()
                ->title('Data Mahasiswa Tidak Ditemukan')
                ->body('Silakan hubungi admin untuk memperbaiki data mahasiswa Anda.')
                ->danger()
                ->send();
            return;
        }

        // Cek periode KRS aktif
        $this->activePeriod = app(PeriodeKrsRepositoryInterface::class)->getActivePeriod();

        if (!$this->activePeriod) {
            Notification::make()
                ->title('Periode KRS Tidak Aktif')
                ->body('Periode pengisian KRS belum dibuka atau sudah ditutup.')
                ->warning()
                ->send();
            return;
        }

        // Load KRS mahasiswa with necessary relationships for the view
        $this->krs = KrsMahasiswa::with([
            'krsDetails' => fn($query) => $query->where('status', 'active'),
            'krsDetails.kelas.mataKuliah',
            'krsDetails.kelas.dosen',
            'krsDetails.kelas.jadwalKuliahs.ruangKuliah'
        ])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->where('periode_krs_id', $this->activePeriod->id)
            ->first();
            
        // Hitung semester sekarang berdasarkan angkatan dan periode aktif
        $this->hitungSemesterSekarang();

        // Load kelas tersedia
        $this->loadAvailableClasses();

        // Hitung total SKS
        $this->calculateTotalSks();
    }

    protected function hitungSemesterSekarang(): void
    {
        if (!$this->mahasiswa || !$this->activePeriod) {
            $this->semesterSekarang = 1;
            return;
        }
        
        try {
            // Get the active academic year from the TahunAjaran model
            $tahunAjaran = \App\Models\TahunAjaran::find($this->activePeriod->tahun_ajaran_id);
            
            if (!$tahunAjaran) {
                throw new \Exception('Tahun ajaran tidak ditemukan');
            }
            
            // Parse the academic year (format: '2023/2024')
            $tahunAkademik = explode('/', $tahunAjaran->tahun_akademik);
            if (count($tahunAkademik) !== 2) {
                throw new \Exception('Format tahun akademik tidak valid');
            }
            
            $tahunAwal = (int)$tahunAkademik[0];
            $tahunMasuk = (int)$this->mahasiswa->angkatan;
            
            // Calculate semester (1 for Ganjil, 2 for Genap)
            $semesterMultiplier = strtolower($tahunAjaran->semester) === 'ganjil' ? 1 : 2;
            
            // Calculate current semester
            $semester = (($tahunAwal - $tahunMasuk) * 2) + $semesterMultiplier;
            
            // Ensure semester is between 1 and 14
            $this->semesterSekarang = max(1, min(14, $semester));
            
        } catch (\Exception $e) {
            // Fallback to default semester if there's an error
            $this->semesterSekarang = 1;
            \Log::error('Error calculating current semester: ' . $e->getMessage());
        }
    }

    public function loadAvailableClasses(): void
    {
        if (!$this->mahasiswa) {
            $this->availableClasses = [];
            return;
        }
        
        // Reset semester filter to current semester if not set
        if (empty($this->semesterFilter)) {
            $this->semesterFilter = $this->semesterSekarang;
        }
        
        // Get active curriculum for the student's program
        $kurikulum = $this->mahasiswa->programStudi->kurikulums()
            ->first();

        if (!$kurikulum) {
            Notification::make()
                ->title('Kurikulum Tidak Ditemukan')
                ->body('Tidak ada kurikulum aktif untuk program studi Anda.')
                ->warning()
                ->send();
            return;
        }

        // Get courses in the curriculum with their pivot data
        $query = $kurikulum->mataKuliahs()
            ->withPivot(['semester_ditawarkan', 'jenis'])
            ->wherePivot('semester_ditawarkan', '<=', $this->semesterSekarang);
        
        $mataKuliahKurikulum = $query->get();
        // Get class IDs that are in the curriculum
        $mataKuliahIds = $mataKuliahKurikulum->pluck('id');

        // Get available classes for the current academic year
        $availableClasses = Kelas::with([
                'mataKuliah',
                'dosen',
                'jadwalKuliahs.ruangKuliah',
                'mataKuliah.prasyarats',
                'tahunAjaran' // Eager load tahunAjaran relationship
            ])
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->where('sisa_kuota', '>', 0)
            ->where('tahun_ajaran_id', $this->activePeriod->tahun_ajaran_id)
            ->get()
            ->map(function ($kelas) use ($mataKuliahKurikulum) {
                // Initialize status
                $kelas->can_be_taken = true;
                $kelas->status_message = 'Dapat diambil';
                $kelas->status_type = 'success';
                
                // Check if student has completed prerequisites
                $prasyaratTidakTerpenuhi = [];
                
                foreach ($kelas->mataKuliah->prasyarats as $prasyarat) {
                    $lulus = $this->mahasiswa->riwayatNilai()
                        ->where('mata_kuliah_id', $prasyarat->id)
                        ->where('nilai_akhir', '>=', 60)
                        ->exists();
                        
                    if (!$lulus) {
                        $prasyaratTidakTerpenuhi[] = $prasyarat->nama_mk;
                    }
                }
                
                if (!empty($prasyaratTidakTerpenuhi)) {
                    $kelas->can_be_taken = false;
                    $kelas->status_message = 'Belum lulus prasyarat: ' . implode(', ', $prasyaratTidakTerpenuhi);
                    $kelas->status_type = 'warning';
                }
                
                return $kelas;
            });
            
        // Filter based on semester and type if needed
        $availableClasses = $availableClasses->filter(function($kelas) {
            if (!empty($this->semesterFilter) && $kelas->mataKuliah->semester != $this->semesterFilter) {
                return false;
            }
            if (!empty($this->jenisFilter) && $kelas->mataKuliah->jenis != $this->jenisFilter) {
                return false;
            }
            return true;
        });
        
        // Transform the final output
        $availableClasses = $availableClasses->map(function ($kelas) use ($mataKuliahKurikulum) {
            $mkKurikulum = $mataKuliahKurikulum->firstWhere('id', $kelas->mata_kuliah_id);
            $jenis = $mkKurikulum ? $mkKurikulum->pivot->jenis : 'wajib';
                $semester = $mkKurikulum ? $mkKurikulum->pivot->semester_ditawarkan : 1;
                
                return [
                    'id' => $kelas->id,
                    'nama' => $kelas->nama,
                    'mata_kuliah' => $kelas->mataKuliah->nama_mk,
                    'kode_mk' => $kelas->mataKuliah->kode_mk,
                    'dosen' => $kelas->dosen->nama,
                    'sks' => $kelas->mataKuliah->sks,
                    'sisa_kuota' => $kelas->sisa_kuota,
                    'semester' => $semester,
                    'jenis' => $jenis,
                    'prasyarat' => $kelas->mataKuliah->prasyarats->pluck('nama_mk')->toArray(),
                    'jadwal' => $kelas->jadwalKuliahs->map(function ($jadwal) {
                        return [
                            'hari' => $jadwal->hari,
                            'jam_mulai' => date('H:i', strtotime($jadwal->jam_mulai)),
                            'jam_selesai' => date('H:i', strtotime($jadwal->jam_selesai)),
                            'ruang' => $jadwal->ruangKuliah->nama_ruang ?? 'Belum ditentukan',
                        ];
                    })->toArray(),
                    'is_taken' => $this->isClassTaken($kelas->id),
                ];
            })
            ->sortBy([
                ['semester', 'asc'],
                ['jenis', 'desc'], // Wajib first, then pilihan
                ['kode_mk', 'asc']
            ])
            ->values()
            ->toArray();
            
        $this->availableClasses = $availableClasses;
    }

    public function isClassTaken($kelasId): bool
    {
        if (!$this->krs) {
            return false;
        }

        return $this->krs->krsDetails()
            ->where('kelas_id', $kelasId)
            ->where('status', 'active')
            ->exists();
    }

    public function calculateTotalSks(): void
    {
        if (!$this->krs) {
            $this->totalSks = 0;
            return;
        }

        $this->totalSks = $this->krs->krsDetails()
            ->where('status', 'active')
            ->join('kelas', 'krs_details.kelas_id', '=', 'kelas.id')
            ->join('mata_kuliahs', 'kelas.mata_kuliah_id', '=', 'mata_kuliahs.id')
            ->sum('mata_kuliahs.sks');
    }

    #[On('add-class')]
    public function addClass($kelasId): void
    {
        try {
            $mahasiswa = Auth::user()->mahasiswa;
            $kelas = Kelas::with(['mataKuliah.prasyarats'])->findOrFail($kelasId);

            // Check prerequisites
            $prerequisites = $kelas->mataKuliah->prasyarats;
            if ($prerequisites->isNotEmpty()) {
                $missingPrerequisites = [];
                
                foreach ($prerequisites as $prasyarat) {
                    $hasPassed = $mahasiswa->riwayatNilai()
                        ->where('mata_kuliah_id', $prasyarat->id)
                        ->where('nilai_akhir', '>=', 60)
                        ->exists();
                    
                    if (!$hasPassed) {
                        $missingPrerequisites[] = $prasyarat->nama_mk;
                    }
                }
                
                if (!empty($missingPrerequisites)) {
                    throw new \Exception(
                        'Anda belum memenuhi prasyarat untuk mata kuliah ini. ' .
                        'Prasyarat yang belum terpenuhi: ' . implode(', ', $missingPrerequisites)
                    );
                }
            }

            // Create KRS if not exists
            if (!$this->krs) {
                $this->krs = app(KrsService::class)->createKrs(
                    $mahasiswa->id,
                    $this->activePeriod->id,
                    $mahasiswa->dosen_pa_id ?? 1
                );
            }

            // Add the class
            app(KrsService::class)->addMataKuliah($this->krs->id, $kelasId);

            // Reload data
            $this->loadKrsData();

            Notification::make()
                ->title('Kelas Berhasil Ditambahkan')
                ->body('Kelas telah ditambahkan ke KRS Anda.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menambahkan Kelas')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    #[On('remove-class')]
    public function removeClass($krsDetailId): void
    {
        try {
            if (!$this->krs) {
                throw new \Exception('KRS tidak ditemukan');
            }

            app(KrsService::class)->removeMataKuliah($this->krs->id, $krsDetailId);

            // Reload data
            $this->loadKrsData();

            Notification::make()
                ->title('Kelas Berhasil Dihapus')
                ->body('Kelas telah dihapus dari KRS Anda.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menghapus Kelas')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function submitKrs(): void
    {
        try {
            if (!$this->krs) {
                throw new \Exception('KRS tidak ditemukan');
            }

            app(KrsService::class)->submitKrs($this->krs->id);

            // Reload data
            $this->loadKrsData();

            Notification::make()
                ->title('KRS Berhasil Disubmit')
                ->body('KRS Anda telah dikirim untuk persetujuan dosen PA.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Submit KRS')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function cancelSubmit(): void
    {
        try {
            if (!$this->krs) {
                throw new \Exception('KRS tidak ditemukan');
            }

            app(KrsService::class)->resetKrsStatus($this->krs->id);

            // Reload data
            $this->loadKrsData();

            Notification::make()
                ->title('Submit KRS Dibatalkan')
                ->body('KRS Anda kembali ke status draft dan dapat diubah kembali.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Membatalkan Submit')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->loadKrsData())
                ->visible(fn() => $this->activePeriod !== null),
        ];
    }
}
