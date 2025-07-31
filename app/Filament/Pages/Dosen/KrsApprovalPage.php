<?php

namespace App\Filament\Pages\Dosen;

use App\Enums\KrsStatusEnum;
use App\Interfaces\KrsRepositoryInterface;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class KrsApprovalPage extends Page
{
    use HasPageShield;

    protected static string $permissionName = 'krs_approval_page';
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static string $view = 'filament.pages.dosen.krs-approval-page-v2';
    protected static ?string $title = 'KRS Mahasiswa';
    protected static ?string $slug = 'dosen/krs-approval';
    protected static ?string $navigationGroup = 'Dosen';
    protected static ?int $navigationSort = 1;

    public array $krsList = [];

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
            $this->krsList = [];
            return;
        }

        $this->krsList = app(KrsRepositoryInterface::class)
            ->getKrsByDosenPa($dosen->id, 50)
            ->all();
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

    public function getTotalKrsByStatus(KrsStatusEnum $status): int
    {
        return collect($this->krsList)->where('status', $status)->count();
    }
}
