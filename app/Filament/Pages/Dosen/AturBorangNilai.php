<?php

namespace App\Filament\Pages\Dosen;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\KomponenNilai;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\BorangNilai;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AturBorangNilai extends Page
{
    use HasPageShield;

    protected static string $permissionName = 'page_AturBorangNilai';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.dosen.atur-borang-nilai-v2';
    protected static ?string $title = 'Pengaturan Borang Nilai';
    protected static ?string $slug = 'dosen/atur-borang-nilai';
    protected static ?string $navigationGroup = 'Dosen';
    protected static ?int $navigationSort = 2;

    public ?Collection $kelasList = null;
    public ?Collection $komponenOptions = null;
    public ?TahunAjaran $tahunAjaranAktif;

    public ?int $selectedKelasId = null;
    public array $borang = [];
    public int $totalBobot = 0;
    public bool $isLocked = false;

    public function mount(): void
    {
        $this->tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $this->loadKelasList();
        $this->komponenOptions = KomponenNilai::where('is_aktif', true)->pluck('nama', 'id');
    }

    protected function loadKelasList(): void
    {
        $dosenId = Auth::user()->dosen->id ?? null;

        if (!$dosenId || !$this->tahunAjaranAktif) {
            $this->kelasList = collect();
            return;
        }

        $this->kelasList = Kelas::where('dosen_id', $dosenId)
            ->where('tahun_ajaran_id', $this->tahunAjaranAktif->id)
            ->with(['mataKuliah', 'borangNilais'])
            ->get()
            ->map(function ($kelas) {
                $isLocked = $kelas->borangNilais->isNotEmpty() && $kelas->borangNilais->first()->is_locked;
                $isFilled = $kelas->borangNilais->isNotEmpty();

                $kelas->borang_status = $isLocked ? 'Terkunci' : ($isFilled ? 'Terisi' : 'Kosong');
                return $kelas;
            });
    }

    public function selectKelas(?int $kelasId): void
    {
        if (!$kelasId) {
            $this->resetState();
            return;
        }

        $this->selectedKelasId = $kelasId;
        $kelas = $this->kelasList->firstWhere('id', $kelasId);

        $this->isLocked = $kelas->borang_status === 'Terkunci';

        $this->borang = $kelas->borangNilais->map(fn($b) => [
            'komponen_nilai_id' => $b->komponen_nilai_id,
            'bobot' => $b->bobot,
        ])->toArray();

        $this->calculateTotalBobot();
    }

    public function updatedBorang(): void
    {
        $this->calculateTotalBobot();
    }

    protected function calculateTotalBobot(): void
    {
        $this->totalBobot = collect($this->borang)->sum('bobot');
    }

    public function addBorangItem(): void
    {
        $this->borang[] = ['komponen_nilai_id' => '', 'bobot' => 0];
    }

    public function removeBorangItem(int $index): void
    {
        unset($this->borang[$index]);
        $this->borang = array_values($this->borang);
        $this->calculateTotalBobot();
    }

    public function saveBorang(bool $andLock = false): void
    {
        // Validasi duplikat & kosong
        $borangBersih = collect($this->borang)
            ->filter(fn($item) => !empty($item['komponen_nilai_id']))
            ->values();

        $komponenIds = $borangBersih->pluck('komponen_nilai_id');
        if ($komponenIds->count() !== $komponenIds->unique()->count()) {
            Notification::make()
                ->title('Validasi Gagal')
                ->body('Terdapat komponen nilai yang dipilih lebih dari satu kali atau kosong.')
                ->danger()
                ->send();
            return;
        }

        if ($this->totalBobot != 100) {
            Notification::make()
                ->title('Validasi Gagal')
                ->body('Total bobot semua komponen harus tepat 100%.')
                ->danger()
                ->send();
            return;
        }

        $dosenId = Auth::user()->dosen->id;

        // Hapus data lama di luar transaction
        BorangNilai::where('kelas_id', $this->selectedKelasId)->delete();

        if (BorangNilai::where('kelas_id', $this->selectedKelasId)->exists()) {
            Notification::make()
                ->title('Gagal Menghapus Data Lama')
                ->body('Data lama tidak berhasil dihapus, silakan coba lagi.')
                ->danger()
                ->send();
            return;
        }

        // Insert data baru
        try {
            foreach ($borangBersih as $item) {
                BorangNilai::create([
                    'kelas_id' => $this->selectedKelasId,
                    'komponen_nilai_id' => $item['komponen_nilai_id'],
                    'bobot' => $item['bobot'],
                    'dosen_id' => $dosenId,
                    'is_locked' => $andLock,
                ]);
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menyimpan Borang Nilai')
                ->body('Terjadi error: ' . $e->getMessage())
                ->danger()
                ->send();
            return;
        }

        if ($andLock) {
            $this->isLocked = true;
            Notification::make()->title('Borang Nilai Berhasil Disimpan & Dikunci')->success()->send();
        } else {
            Notification::make()->title('Borang Nilai Berhasil Disimpan')->success()->send();
        }

        $this->loadKelasList(); // Refresh status
    }

    public function saveAndLockBorang(): void
    {
        $this->saveBorang(true);
    }

    protected function resetState(): void
    {
        $this->selectedKelasId = null;
        $this->borang = [];
        $this->totalBobot = 0;
        $this->isLocked = false;
    }
}
