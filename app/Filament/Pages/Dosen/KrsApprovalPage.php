<?php

namespace App\Filament\Pages\Dosen;

use Filament\Pages\Page;
use App\Interfaces\KrsRepositoryInterface;
use App\Models\KrsMahasiswa;
use App\Services\KrsService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class KrsApprovalPage extends Page
{
    use HasPageShield;
    
    protected static string $permissionName = 'krs_approval_page';
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static string $view = 'filament.pages.dosen.krs-approval-page';
    protected static ?string $title = 'KRS Mahasiswa';
    protected static ?string $slug = 'dosen/krs-approval';
    protected static ?string $navigationGroup = 'Dosen';
    protected static ?int $navigationSort = 1;

    public $krsList = [];
    public $selectedKrs = null;
    public $catatan = '';

    public function mount(): void
    {
        $this->loadKrsList();
    }

    public function loadKrsList(): void
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            Notification::make()
                ->title('Data Dosen Tidak Ditemukan')
                ->body('Silakan hubungi admin untuk memperbaiki data dosen Anda.')
                ->danger()
                ->send();
            return;
        }

        $this->krsList = app(KrsRepositoryInterface::class)
            ->getKrsByDosenPa($dosen->id, 50)
            ->items();
    }

    public function viewKrsDetail($krsId): void
    {
        $this->selectedKrs = app(KrsRepositoryInterface::class)->getKrsById($krsId);
    }

    public function approveKrs($krsId): void
    {
        try {
            app(KrsService::class)->approveKrs($krsId, $this->catatan);

            $this->catatan = '';
            $this->selectedKrs = null;
            $this->loadKrsList();

            Notification::make()
                ->title('KRS Berhasil Disetujui')
                ->body('KRS mahasiswa telah disetujui.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menyetujui KRS')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function rejectKrs($krsId): void
    {
        if (empty($this->catatan)) {
            Notification::make()
                ->title('Catatan Diperlukan')
                ->body('Harap berikan catatan untuk penolakan KRS.')
                ->warning()
                ->send();
            return;
        }

        try {
            app(KrsService::class)->rejectKrs($krsId, $this->catatan);

            $this->catatan = '';
            $this->selectedKrs = null;
            $this->loadKrsList();

            Notification::make()
                ->title('KRS Berhasil Ditolak')
                ->body('KRS mahasiswa telah ditolak dengan catatan.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menolak KRS')
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
                ->action(fn() => $this->loadKrsList()),
        ];
    }

    public function getKrsStatusColor($status): string
    {
        return match ($status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getKrsStatusLabel($status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'submitted' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }

    public function getTotalKrsByStatus($status): int
    {
        return collect($this->krsList)->where('status', $status)->count();
    }
}
