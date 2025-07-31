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
use Illuminate\Database\Eloquent\Collection;

class InputNilaiPage extends Page
{
    use HasPageShield;

    protected static string $permissionName = 'page_InputNilaiPage';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static string $view = 'filament.pages.dosen.input-nilai-page-v2';
    protected static ?string $title = 'Input Nilai Mahasiswa';
    protected static ?string $slug = 'dosen/input-nilai';
    protected static ?string $navigationGroup = 'Dosen';
    protected static ?int $navigationSort = 2;

    public ?string $selectedKelasId = null;
    public array $mahasiswaList = [];
    public array $borangNilai = [];
    public array $nilaiInput = [];
    public bool $isBorangLocked = false;

    public ?TahunAjaran $tahunAjaranAktif;
    public Collection $kelasList;

    public function mount(): void
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak terasosiasi dengan data dosen manapun.')
                ->danger()
                ->send();
            $this->kelasList = new Collection();
            return;
        }

        $this->tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$this->tahunAjaranAktif) {
            Notification::make()
                ->title('Tahun Ajaran Aktif Tidak Ditemukan')
                ->body('Silakan hubungi admin untuk mengatur tahun ajaran yang aktif.')
                ->warning()
                ->send();
            $this->kelasList = new Collection();
            return;
        }

        $this->kelasList = Kelas::where('dosen_id', $dosen->id)
            ->where('tahun_ajaran_id', $this->tahunAjaranAktif->id)
            ->with(['mataKuliah.programStudi', 'krsDetails.krsMahasiswa'])
            ->get()
            ->map(function ($kelas) {
                $kelas->jumlah_mahasiswa = $kelas->krsDetails
                    ->where('krsMahasiswa.status', 'approved')
                    ->count();
                return $kelas;
            });
    }

    public function selectKelas($kelasId): void
    {
        if (!$kelasId) {
            $this->selectedKelasId = null;
            $this->mahasiswaList = [];
            $this->borangNilai = [];
            $this->nilaiInput = [];
            $this->isBorangLocked = false;
            return;
        }

        $this->selectedKelasId = $kelasId;
        $kelas = Kelas::with('borangNilais.komponenNilai')->find($kelasId);

        // Cek jika semua borang nilai sudah dikunci
        $this->isBorangLocked = $kelas->borangNilais->isNotEmpty() && $kelas->borangNilais->every(fn ($borang) => $borang->is_locked);

        if (!$this->isBorangLocked) {
            Notification::make()
                ->title('Borang Nilai Belum Dikunci')
                ->body('Anda belum bisa menginput nilai karena borang nilai untuk kelas ini belum dikunci.')
                ->warning()
                ->send();
        }

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
        if (!$this->isBorangLocked) {
            Notification::make()
                ->title('Aksi Ditolak')
                ->body('Tidak dapat menyimpan nilai karena borang nilai belum dikunci.')
                ->danger()
                ->send();
            return;
        }

        // Logic to save grades will be implemented here
        Notification::make()
            ->title('Fitur Dalam Pengembangan')
            ->body('Fungsi simpan nilai akan segera diimplementasikan.')
            ->info()
            ->send();
    }

    public function finalizeNilai(): void
    {
        if (!$this->isBorangLocked) {
            Notification::make()
                ->title('Aksi Ditolak')
                ->body('Tidak dapat finalisasi nilai karena borang nilai belum dikunci.')
                ->danger()
                ->send();
            return;
        }
        // Logic to finalize grades will be implemented here
        Notification::make()
            ->title('Fitur Dalam Pengembangan')
            ->body('Fungsi finalisasi nilai akan segera diimplementasikan.')
            ->info()
            ->send();
    }
}