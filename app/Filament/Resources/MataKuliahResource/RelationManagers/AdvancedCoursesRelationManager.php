<?php

namespace App\Filament\Resources\MataKuliahResource\RelationManagers;

use App\Models\MataKuliah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdvancedCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'mataKuliahLanjutan';

    protected static ?string $recordTitleAttribute = 'nama_mk';
    
    protected static ?string $title = 'Mata Kuliah Lanjutan';
    
    protected static ?string $label = 'Mata Kuliah Lanjutan';
    
    protected static ?string $modelLabel = 'Mata Kuliah Lanjutan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mata_kuliah_id')
                    ->label('Mata Kuliah Lanjutan')
                    ->options(function (RelationManager $livewire): array {
                        // Ambil ID mata kuliah saat ini
                        $currentMataKuliahId = $livewire->getOwnerRecord()->id;
                        
                        // Ambil semua mata kuliah kecuali yang saat ini dan yang sudah menjadi mata kuliah lanjutan
                        return MataKuliah::query()
                            ->where('id', '!=', $currentMataKuliahId)
                            ->whereDoesntHave('prasyarats', function (Builder $query) use ($currentMataKuliahId) {
                                $query->where('prasyarat_id', $currentMataKuliahId);
                            })
                            ->pluck('nama_mk', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_mk')
            ->columns([
                Tables\Columns\TextColumn::make('kode_mk')
                    ->label('Kode MK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_mk')
                    ->label('Nama Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sks')
                    ->label('SKS'),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Mata Kuliah Lanjutan')
                    ->modalHeading('Tambah Mata Kuliah Lanjutan'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus dari Mata Kuliah Lanjutan'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}