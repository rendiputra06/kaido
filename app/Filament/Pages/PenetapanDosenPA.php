<?php

namespace App\Filament\Pages;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class PenetapanDosenPA extends Page
{
    use HasPageShield;
    
    protected static string $permissionName = 'penetapan_dosen_pa';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationLabel = 'Dosen PA';
    protected static ?string $title = 'Penetapan Dosen Pembimbing Akademik';
    protected static string $view = 'filament.pages.penetapan-dosen-p-a';

    public $dosens;
    public $selectedDosenId;
    public $mahasiswaBimbingan;
    public $mahasiswaTanpaPA;

    public function mount(): void
    {
        $this->loadDosens();
        $this->mahasiswaBimbingan = collect();
        $this->mahasiswaTanpaPA = Mahasiswa::whereNull('dosen_pa_id')->orderBy('nama')->get();
    }

    public function loadDosens(): void
    {
        $this->dosens = Dosen::withCount('mahasiswaBimbingan')->get();
    }

    public function selectDosen($dosenId): void
    {
        $this->selectedDosenId = $dosenId;
        $this->loadMahasiswaBimbingan();
    }

    public function loadMahasiswaBimbingan(): void
    {
        if ($this->selectedDosenId) {
            $this->mahasiswaBimbingan = Mahasiswa::where('dosen_pa_id', $this->selectedDosenId)->orderBy('nama')->get();
        } else {
            $this->mahasiswaBimbingan = collect();
        }
    }

    public function jadikanBimbingan(Mahasiswa $mahasiswa): void
    {
        if (!$this->selectedDosenId) {
            Notification::make()
                ->title('Pilih Dosen Terlebih Dahulu')
                ->body('Anda harus memilih seorang dosen dari daftar di sebelah kiri.')
                ->warning()
                ->send();
            return;
        }

        $mahasiswa->update(['dosen_pa_id' => $this->selectedDosenId]);
        $this->refreshData();
        Notification::make()->title('Mahasiswa berhasil ditambahkan ke bimbingan.')->success()->send();
    }

    public function lepaskan(Mahasiswa $mahasiswa): void
    {
        $mahasiswa->update(['dosen_pa_id' => null]);
        $this->refreshData();
        Notification::make()->title('Mahasiswa berhasil dilepaskan dari bimbingan.')->success()->send();
    }

    private function refreshData(): void
    {
        $this->loadDosens();
        $this->loadMahasiswaBimbingan();
        $this->mahasiswaTanpaPA = Mahasiswa::whereNull('dosen_pa_id')->orderBy('nama')->get();
    }
}
