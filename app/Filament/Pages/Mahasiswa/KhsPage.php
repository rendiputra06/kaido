<?php

namespace App\Filament\Pages\Mahasiswa;

use Filament\Pages\Page;
use App\Services\KhsService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;

class KhsPage extends Page
{
    use HasPageShield;

    protected static ?string $permissionName = 'page_khs';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.mahasiswa.khs-page';
    protected static ?string $title = 'Kartu Hasil Studi';
    protected static ?string $slug = 'mahasiswa/khs';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?int $navigationSort = 2;

    public ?Mahasiswa $mahasiswa;
    public Collection $khsHistory;
    public array $gpa;

    public function mount(KhsService $khsService): void
    {
        $this->mahasiswa = Auth::user()->mahasiswa;

        if (!$this->mahasiswa) {
            Notification::make()
                ->title('Data Mahasiswa Tidak Ditemukan')
                ->body('Silakan hubungi admin untuk memperbaiki data mahasiswa Anda.')
                ->danger()
                ->send();

            $this->khsHistory = collect();
            $this->gpa = ['ipk' => 0, 'total_sks' => 0];
            return;
        }

        $this->loadKhsData($khsService);
    }

    public function loadKhsData(KhsService $khsService): void
    {
        $this->khsHistory = $khsService->getKhsHistory($this->mahasiswa->id);
        $this->gpa = $khsService->calculateGPA($this->mahasiswa->id);
    }
}
