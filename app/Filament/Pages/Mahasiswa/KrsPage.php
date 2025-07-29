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
    protected static string $view = 'filament.pages.mahasiswa.krs-page';
    protected static ?string $title = 'Kartu Rencana Studi';
    protected static ?string $slug = 'mahasiswa/krs';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?int $navigationSort = 1;

    public ?KrsMahasiswa $krs = null;
    public $availableClasses = [];
    public $activePeriod = null;
    public $totalSks = 0;
    public $maxSks = 24;

    public function mount(): void
    {
        $this->loadKrsData();
    }

    public function loadKrsData(): void
    {
        $mahasiswa = Auth::user()->mahasiswa;

        if (!$mahasiswa) {
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

        // Load KRS mahasiswa
        $this->krs = app(KrsRepositoryInterface::class)
            ->getKrsByMahasiswaAndPeriode($mahasiswa->id, $this->activePeriod->id);

        // Load kelas tersedia
        $this->loadAvailableClasses();

        // Hitung total SKS
        $this->calculateTotalSks();
    }

    public function loadAvailableClasses(): void
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $this->availableClasses = Kelas::with(['mataKuliah', 'dosen', 'jadwalKuliahs.ruangKuliah'])
            ->where('program_studi_id', $mahasiswa->program_studi_id)
            ->where('sisa_kuota', '>', 0)
            ->get()
            ->map(function ($kelas) {
                return [
                    'id' => $kelas->id,
                    'nama' => $kelas->nama,
                    'mata_kuliah' => $kelas->mataKuliah->nama_mk,
                    'dosen' => $kelas->dosen->nama,
                    'sks' => $kelas->mataKuliah->sks,
                    'sisa_kuota' => $kelas->sisa_kuota,
                    'jadwal' => $kelas->jadwalKuliahs->map(function ($jadwal) {
                        return [
                            'hari' => $jadwal->hari,
                            'jam' => $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai,
                            'ruang' => $jadwal->ruangKuliah->nama,
                        ];
                    })->toArray(),
                    'is_taken' => $this->isClassTaken($kelas->id),
                ];
            })
            ->toArray();
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

            if (!$this->krs) {
                // Buat KRS baru
                $this->krs = app(KrsService::class)->createKrs(
                    $mahasiswa->id,
                    $this->activePeriod->id,
                    $mahasiswa->dosen_pa_id ?? 1 // Default dosen PA
                );
            }

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

    public function getKrsStatusColor(): string
    {
        if (!$this->krs) {
            return 'gray';
        }

        return match ($this->krs->status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getKrsStatusLabel(): string
    {
        if (!$this->krs) {
            return 'Belum Ada KRS';
        }

        return match ($this->krs->status) {
            'draft' => 'Draft',
            'submitted' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }
}
