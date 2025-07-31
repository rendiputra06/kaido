<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorangNilaiResource\Pages;
use App\Models\BorangNilai;
use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\Dosen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BorangNilaiResource extends Resource
{
    protected static ?string $model = BorangNilai::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $modelLabel = 'Borang Nilai';
    protected static ?string $navigationGroup = 'Penilaian';
    protected static ?string $title = 'Borang Nilai';
    protected static ?string $navigationLabel = 'Borang Nilai';
    protected static ?string $pluralModelLabel = 'Borang Nilai';
    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Borang Nilai')
                    ->description('Konfigurasi komponen penilaian untuk kelas tertentu')
                    ->schema([
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->required()
                            ->searchable()
                            ->options(
                                Kelas::query()
                                    ->with(['mataKuliah', 'dosen'])
                                    ->get()
                                    ->mapWithKeys(fn($kelas) => [
                                        $kelas->id => "{$kelas->mataKuliah->nama} - {$kelas->nama} (Dosen: {$kelas->dosen->nama})"
                                    ])
                            )
                            ->columnSpanFull(),

                        Forms\Components\Select::make('komponen_nilai_id')
                            ->label('Komponen Nilai')
                            ->required()
                            ->searchable()
                            ->options(
                                KomponenNilai::where('is_aktif', true)
                                    ->get()
                                    ->pluck('nama', 'id')
                            ),

                        Forms\Components\TextInput::make('bobot')
                            ->label('Bobot (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Bobot dalam persentase (0-100%)'),

                        Forms\Components\Toggle::make('is_locked')
                            ->label('Dikunci')
                            ->helperText('Jika dikunci, nilai tidak dapat diubah')
                            ->default(false),

                        Forms\Components\Hidden::make('dosen_id')
                            ->default(fn() => Auth::id()),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Catatan tambahan tentang borang nilai ini'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kelas.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('komponenNilai.nama')
                    ->label('Komponen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bobot')
                    ->label('Bobot')
                    ->suffix('%')
                    ->sortable()
                    ->alignRight(),

                Tables\Columns\IconColumn::make('is_locked')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('dosen.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('komponen_nilai_id')
                    ->label('Komponen Nilai')
                    ->relationship('komponenNilai', 'nama')
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_locked')
                    ->label('Status Kunci')
                    ->placeholder('Semua')
                    ->trueLabel('Terkunci')
                    ->falseLabel('Terbuka'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit')
                    ->visible(fn(BorangNilai $record) => !$record->is_locked),

                Tables\Actions\Action::make('lock')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->action(fn(BorangNilai $record) => $record->update(['is_locked' => true]))
                    ->visible(fn(BorangNilai $record) => !$record->is_locked)
                    ->requiresConfirmation('Yakin ingin mengunci borang nilai ini? Nilai tidak dapat diubah setelah dikunci.'),

                Tables\Actions\Action::make('unlock')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->action(fn(BorangNilai $record) => $record->update(['is_locked' => false]))
                    ->visible(fn(BorangNilai $record) => $record->is_locked)
                    ->requiresConfirmation('Yakin ingin membuka kunci borang nilai ini?'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->visible(fn(BorangNilai $record) => !$record->is_locked)
                    ->successNotificationTitle('Borang nilai berhasil dihapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->modalHeading('Hapus borang nilai yang dipilih?')
                        ->modalDescription('Borang nilai yang sudah dikunci tidak dapat dihapus.')
                        ->action(function ($records) {
                            $records->each->delete();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Borang nilai berhasil dihapus'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Borang Nilai')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Borang Nilai Baru'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relations can be added here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorangNilais::route('/'),
            'create' => Pages\CreateBorangNilai::route('/create'),
            'edit' => Pages\EditBorangNilai::route('/{record}/edit'),
        ];
    }
}
