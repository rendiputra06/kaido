<?php

namespace App\Filament\Pages\Dosen;

use App\Enums\KrsStatusEnum;
use App\Filament\Resources\KrsMahasiswaResource\RelationManagers\KrsDetailsRelationManager;
use App\Models\KrsMahasiswa;
use App\Services\KrsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Pages\Concerns\HasRelationManagers;

class KrsDetailPage extends Page
{
    use HasRelationManagers;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.dosen.krs-detail-page';
    protected static ?string $title = 'Detail KRS Mahasiswa';
    protected static ?string $slug = 'dosen/krs-approval/{record}';

    public KrsMahasiswa $record;

    public function mount(KrsMahasiswa $record): void
    {
        $this->record = $record;
    }

    public function getRelationManagers(): array
    {
        return [
            KrsDetailsRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Setujui KRS')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(fn() => $this->approveKrs())
                ->visible($this->record->status === KrsStatusEnum::SUBMITTED),

            Action::make('reject')
                ->label('Tolak KRS')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    Textarea::make('catatan')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->action(fn(array $data) => $this->rejectKrs($data['catatan']))
                ->visible($this->record->status === KrsStatusEnum::SUBMITTED),
        ];
    }

    public function approveKrs(): void
    {
        try {
            app(KrsService::class)->approveKrs($this->record->id, $this->record->catatan_pa);
            Notification::make()->title('KRS Berhasil Disetujui')->success()->send();
            $this->redirect(KrsApprovalPage::getUrl());
        } catch (\Exception $e) {
            Notification::make()->title('Gagal Menyetujui KRS')->body($e->getMessage())->danger()->send();
        }
    }

    public function rejectKrs(string $catatan): void
    {
        try {
            app(KrsService::class)->rejectKrs($this->record->id, $catatan);
            Notification::make()->title('KRS Berhasil Ditolak')->success()->send();
            $this->redirect(KrsApprovalPage::getUrl());
        } catch (\Exception $e) {
            Notification::make()->title('Gagal Menolak KRS')->body($e->getMessage())->danger()->send();
        }
    }

    public static function getNavigationLabel(): string
    {
        return 'Detail KRS';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Sembunyikan dari navigasi utama
    }
}
