<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodeKrsResource\Pages;
use App\Models\PeriodeKrs;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PeriodeKrsResource extends Resource
{
    protected static ?string $model = PeriodeKrs::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel = 'Periode KRS';
    protected static ?string $title = 'Periode KRS';
    protected static ?string $navigationLabel = 'Periode KRS';
    protected static ?string $pluralModelLabel = 'Periode KRS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(TahunAjaran::all()->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('nama_periode')
                    ->label('Nama Periode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),
                Forms\Components\DatePicker::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->after('tgl_mulai'),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'selesai' => 'Selesai',
                    ])
                    ->required()
                    ->default('tidak_aktif'),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_periode')
                    ->label('Nama Periode')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'tidak_aktif' => 'gray',
                        'aktif' => 'success',
                        'selesai' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleStatus')
                    ->label(fn(PeriodeKrs $record): string => $record->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color(fn(PeriodeKrs $record): string => $record->status === 'aktif' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (PeriodeKrs $record) {
                        if ($record->status === 'aktif') {
                            $record->update(['status' => 'tidak_aktif']);
                        } else {
                            // Pastikan hanya ada satu periode aktif dalam satu waktu
                            PeriodeKrs::where('status', 'aktif')->update(['status' => 'tidak_aktif']);
                            $record->update(['status' => 'aktif']);
                        }
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriodeKrs::route('/'),
            'create' => Pages\CreatePeriodeKrs::route('/create'),
            'edit' => Pages\EditPeriodeKrs::route('/{record}/edit'),
        ];
    }
}
