<?php

namespace App\Filament\Pages\Dosen;

use App\Models\Kelas;
use App\Models\Dosen;
use App\Models\KrsMahasiswa;
use App\Models\TahunAjaran;
use App\Services\NilaiService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class InputNilaiPage extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected static string $permissionName = 'page_InputNilaiPage';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static string $view = 'filament.pages.dosen.input-nilai-page';
    protected static ?string $title = 'Input Nilai Mahasiswa';
    protected static ?string $slug = 'dosen/input-nilai';
    protected static ?string $navigationGroup = 'Dosen';
    protected static ?int $navigationSort = 2;

    public ?string $selectedKelasId = null;
    public array $mahasiswaList = [];
    public array $borangNilai = [];
    public array $nilaiInput = [];

    protected $dosen;
    protected $tahunAjaranAktif;

    public function mount(): void
    {
        $this->dosen = Auth::user()->dosen;
        if (!$this->dosen) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak terasosiasi dengan data dosen manapun.')
                ->danger()
                ->send();
            return;
        }

        $this->tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$this->tahunAjaranAktif) {
            Notification::make()
                ->title('Tahun Ajaran Aktif Tidak Ditemukan')
                ->body('Silakan hubungi admin untuk mengatur tahun ajaran yang aktif.')
                ->warning()
                ->send();
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedKelasId')
                ->label('Pilih Kelas')
                ->options($this->getKelasOptions())
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->loadMahasiswaAndBorang($state);
                })
                ->placeholder('Pilih kelas yang Anda ajar'),
        ];
    }

    protected function getKelasOptions(): array
    {
        if (!$this->dosen || !$this->tahunAjaranAktif) {
            return [];
        }

        return Kelas::where('dosen_id', $this->dosen->id)
            ->where('tahun_ajaran_id', $this->tahunAjaranAktif->id)
            ->pluck('nama', 'id')
            ->toArray();
    }

    public function loadMahasiswaAndBorang($kelasId): void
    {
        if (!$kelasId) {
            $this->mahasiswaList = [];
            $this->borangNilai = [];
            $this->nilaiInput = [];
            return;
        }

        $this->selectedKelasId = $kelasId;
        $kelas = Kelas::with('borangNilais.komponenNilai')->find($kelasId);
        $this->borangNilai = $kelas->borangNilais->toArray();

        $mahasiswaIds = KrsMahasiswa::whereHas('krsDetails', function ($query) use ($kelasId) {
            $query->where('kelas_id', $kelasId);
        })->where('status', 'approved')->pluck('mahasiswa_id');

        $this->mahasiswaList = \App\Models\Mahasiswa::whereIn('id', $mahasiswaIds)->get()->toArray();

        // Initialize nilaiInput array
        $this->initializeNilaiInput();
    }

    protected function initializeNilaiInput(): void
    {
        $this->nilaiInput = [];
        foreach ($this->mahasiswaList as $mahasiswa) {
            foreach ($this->borangNilai as $borang) {
                $this->nilaiInput[$mahasiswa['id']][$borang['id']] = '';
            }
        }
    }

    public function saveNilai(): void
    {
        // Logic to save grades will be implemented here
        Notification::make()
            ->title('Fitur Dalam Pengembangan')
            ->body('Fungsi simpan nilai akan segera diimplementasikan.')
            ->info()
            ->send();
    }

    public function finalizeNilai(): void
    {
        // Logic to finalize grades will be implemented here
        Notification::make()
            ->title('Fitur Dalam Pengembangan')
            ->body('Fungsi finalisasi nilai akan segera diimplementasikan.')
            ->info()
            ->send();
    }
}
