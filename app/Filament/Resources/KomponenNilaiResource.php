<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KomponenNilaiResource\Pages;
use App\Models\KomponenNilai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class KomponenNilaiResource extends Resource
{
    protected static ?string $model = KomponenNilai::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $modelLabel = 'Komponen Nilai';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('kode')
                            ->label('Kode Komponen')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('TUGAS')
                            ->helperText('Kode unik untuk komponen nilai (maks. 20 karakter)'),
                            
                        TextInput::make('nama')
                            ->label('Nama Komponen')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Tugas 1')
                            ->helperText('Nama lengkap komponen nilai'),
                            
                        TextInput::make('default_bobot')
                            ->label('Bobot Default (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Bobot default dalam persentase (0-100%)'),
                            
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Deskripsi singkat tentang komponen nilai ini'),
                            
                        Toggle::make('is_aktif')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk menyembunyikan komponen nilai'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('KODE')
                    ->searchable()
                    ->sortable()
                    ->description(fn (KomponenNilai $record) => $record->keterangan)
                    ->wrap(),
                    
                TextColumn::make('nama')
                    ->label('NAMA KOMPONEN')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('default_bobot')
                    ->label('BOBOT')
                    ->suffix('%')
                    ->sortable()
                    ->alignRight(),
                    
                ToggleColumn::make('is_aktif')
                    ->label('AKTIF')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('DIBUAT')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('DIPERBARUI')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_aktif')
                    ->label('Status Aktif')
                    ->boolean()
                    ->default(true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->successNotificationTitle('Komponen nilai berhasil dihapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->modalHeading('Hapus komponen nilai yang dipilih?')
                        ->modalDescription('Komponen nilai yang sudah digunakan tidak dapat dihapus.')
                        ->successNotificationTitle('Komponen nilai berhasil dihapus'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Komponen Nilai')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Komponen Nilai Baru'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKomponenNilais::route('/'),
            'create' => Pages\CreateKomponenNilai::route('/create'),
            'edit' => Pages\EditKomponenNilai::route('/{record}/edit'),
        ];
    }
}
