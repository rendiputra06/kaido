<?php

namespace App\Filament\Resources\KelasResource\RelationManagers;

use App\Interfaces\JadwalServiceInterface;
use App\Models\JadwalKuliah;
use Closure;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class JadwalKuliahRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwalKuliahs';

    protected static ?string $recordTitleAttribute = 'hari';

    protected static ?string $title = 'Jadwal Kuliah';

    protected static ?string $modelLabel = 'Jadwal';

    protected static ?string $pluralModelLabel = 'Jadwal Kuliah';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('ruang_kuliah_id')
                            ->label('Ruang Kuliah')
                            ->relationship('ruangKuliah', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->helperText('Pilih ruang kuliah yang sesuai dengan kapasitas kelas'),
                        
                        Forms\Components\Select::make('hari')
                            ->label('Hari')
                            ->options([
                                'Senin' => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu' => 'Rabu',
                                'Kamis' => 'Kamis',
                                'Jumat' => 'Jumat',
                                'Sabtu' => 'Sabtu',
                            ])
                            ->required()
                            ->live(),
                    ]),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TimePicker::make('jam_mulai')
                            ->label('Jam Mulai')
                            ->required()
                            ->seconds(false)
                            ->live(),
                        
                        Forms\Components\TimePicker::make('jam_selesai')
                            ->label('Jam Selesai')
                            ->required()
                            ->seconds(false)
                            ->after('jam_mulai'),
                    ]),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Senin' => 'primary',
                        'Selasa' => 'success',
                        'Rabu' => 'warning',
                        'Kamis' => 'danger',
                        'Jumat' => 'info',
                        'Sabtu' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ruangKuliah.nama')
                    ->label('Ruang Kuliah')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('ruangKuliah.kapasitas')
                    ->label('Kapasitas Ruang')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ]),
                
                Tables\Filters\SelectFilter::make('ruang_kuliah_id')
                    ->label('Ruang Kuliah')
                    ->relationship('ruangKuliah', 'nama'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Jadwal')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->emptyStateHeading('Belum ada jadwal')
            ->emptyStateDescription('Tambahkan jadwal kuliah untuk kelas ini')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->defaultSort('hari', 'asc');
    }
}

