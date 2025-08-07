<?php

namespace App\Filament\Pages\Mahasiswa;

use Filament\Pages\Page;
use App\Services\KhsService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;

class TranskripPage extends Page
{
    use HasPageShield;

    protected static ?string $permissionName = 'page_transkrip';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.mahasiswa.transkrip-page';
    protected static ?string $title = 'Transkrip Nilai';
    protected static ?string $slug = 'mahasiswa/transkrip';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?int $navigationSort = 3;

    public ?Mahasiswa $mahasiswa;
    public array $transcriptData;

    public function mount(KhsService $khsService): void
    {
        $this->mahasiswa = Auth::user()->mahasiswa;

        if (!$this->mahasiswa) {
            Notification::make()
                ->title('Data Mahasiswa Tidak Ditemukan')
                ->body('Silakan hubungi admin untuk memperbaiki data mahasiswa Anda.')
                ->danger()
                ->send();

            $this->transcriptData = [
                'mahasiswa' => null,
                'ipk' => 0,
                'total_sks' => 0,
                'riwayat_semester' => collect(),
            ];
            return;
        }

        $this->loadTranscriptData($khsService);
    }

    public function loadTranscriptData(KhsService $khsService): void
    {
        $this->transcriptData = $khsService->generateTranscript($this->mahasiswa->id);
    }
}
