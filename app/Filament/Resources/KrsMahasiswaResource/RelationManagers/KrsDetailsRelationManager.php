<?php

namespace App\Filament\Resources\KrsMahasiswaResource\RelationManagers;

use App\Enums\KrsStatusEnum;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KrsDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'krsDetails';
    protected static ?string $title = 'Mata Kuliah yang Diambil';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')
                    ->label('Mata Kuliah')
                    ->options(Kelas::with(['mataKuliah', 'dosen'])->get()->mapWithKeys(function ($kelas) {
                        return [$kelas->id => "{$kelas->mataKuliah->nama_mk} ({$kelas->mataKuliah->sks} SKS) - Dosen: {$kelas->dosen->nama}"];
                    }))
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kelas.mataKuliah.nama_mk')
            ->columns([
                Tables\Columns\TextColumn::make('kelas.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.dosen.nama')
                    ->label('Dosen Pengampu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.mataKuliah.sks')
                    ->label('SKS')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Mata Kuliah')
                    ->visible(fn () => $this->ownerRecord->status === KrsStatusEnum::SUBMITTED),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => $this->ownerRecord->status === KrsStatusEnum::SUBMITTED),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => $this->ownerRecord->status === KrsStatusEnum::SUBMITTED),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->visible(fn () => $this->ownerRecord->status === KrsStatusEnum::SUBMITTED),
            ]);
    }
}
