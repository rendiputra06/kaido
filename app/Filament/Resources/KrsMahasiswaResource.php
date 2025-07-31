<?php

namespace App\Filament\Resources;

use App\Enums\KrsStatusEnum;
use App\Filament\Resources\KrsMahasiswaResource\Pages;
use App\Filament\Resources\KrsMahasiswaResource\RelationManagers;
use App\Models\KrsMahasiswa;
use App\Services\KrsService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class KrsMahasiswaResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = KrsMahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationLabel = 'Manajemen KRS';
    protected static ?string $pluralModelLabel = 'Manajemen KRS';
    protected static ?int $navigationSort = 3;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
            'reorder',
            'reset_status',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return parent::getEloquentQuery();
        }

        if ($user->hasRole('dosen')) {
            $dosenId = $user->dosen->id ?? null;
            return parent::getEloquentQuery()->where('dosen_pa_id', $dosenId);
        }

        if ($user->hasRole('mahasiswa')) {
            $mahasiswaId = $user->mahasiswa->id ?? null;
            return parent::getEloquentQuery()->where('mahasiswa_id', $mahasiswaId);
        }

        // Default, jangan tampilkan apa-apa jika role tidak cocok
        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mahasiswa_id')
                    ->relationship('mahasiswa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('periode_krs_id')
                    ->relationship('periodeKrs', 'nama_periode')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('dosen_pa_id')
                    ->relationship('dosenPa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(KrsStatusEnum::class)
                    ->required(),
                Forms\Components\TextInput::make('total_sks')
                    ->numeric()
                    ->disabled(),
                Forms\Components\Textarea::make('catatan_pa')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.nama')
                    ->searchable()
                    ->description(fn($record): string => $record->mahasiswa->nim)
                    ->sortable(),
                Tables\Columns\TextColumn::make('mahasiswa.nim')
                    ->searchable()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodeKrs.nama_periode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dosenPa.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(KrsStatusEnum $state): string => $state->getColor())
                    ->formatStateUsing(fn(KrsStatusEnum $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sks')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(KrsStatusEnum::class),
                SelectFilter::make('periode_krs_id')
                    ->label('Periode KRS')
                    ->relationship('periodeKrs', 'nama_periode'),
                SelectFilter::make('dosen_pa_id')
                    ->label('Dosen PA')
                    ->relationship('dosenPa', 'nama'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('resetStatus')
                    ->label('Reset Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Status KRS')
                    ->modalDescription('Apakah Anda yakin ingin mereset status KRS ini menjadi draft? Tindakan ini akan memungkinkan mahasiswa untuk mengubah KRS kembali.')
                    ->modalSubmitActionLabel('Ya, Reset Status')
                    ->modalCancelActionLabel('Batal')
                    ->visible(fn(KrsMahasiswa $record): bool => $record->status !== KrsStatusEnum::DRAFT)
                    ->action(function (KrsMahasiswa $record, KrsService $krsService) {
                        try {
                            $krsService->resetKrsStatus($record->id);
                            Notification::make()
                                ->title('Status KRS berhasil direset')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal mereset status KRS')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('resetStatusBulk')
                        ->label('Reset Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Reset Status KRS')
                        ->modalDescription('Apakah Anda yakin ingin mereset status KRS yang dipilih menjadi draft? Tindakan ini akan memungkinkan mahasiswa untuk mengubah KRS kembali.')
                        ->modalSubmitActionLabel('Ya, Reset Status')
                        ->modalCancelActionLabel('Batal')
                        ->action(function (Collection $records, KrsService $krsService) {
                            $successCount = 0;
                            $failedCount = 0;

                            foreach ($records as $record) {
                                if ($record->status === KrsStatusEnum::DRAFT) {
                                    $failedCount++;
                                    continue;
                                }

                                try {
                                    $krsService->resetKrsStatus($record->id);
                                    $successCount++;
                                } catch (\Exception $e) {
                                    $failedCount++;
                                }
                            }

                            if ($successCount > 0) {
                                Notification::make()
                                    ->title("$successCount KRS berhasil direset")
                                    ->success()
                                    ->send();
                            }

                            if ($failedCount > 0) {
                                Notification::make()
                                    ->title("$failedCount KRS gagal direset")
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\KrsDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKrsMahasiswas::route('/'),
            'create' => Pages\CreateKrsMahasiswa::route('/create'),
            'edit' => Pages\EditKrsMahasiswa::route('/{record}/edit'),
            'view' => Pages\ViewKrsMahasiswa::route('/{record}'),
        ];
    }
}
