<?php

namespace App\Filament\Resources\KrsMahasiswaResource\RelationManagers;

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

    protected static ?string $recordTitleAttribute = 'id';
    
    protected static ?string $title = 'Detail KRS';
    protected static ?string $label = 'Mata Kuliah';
    protected static ?string $pluralLabel = 'Mata Kuliah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama', function (Builder $query) {
                        // Filter kelas yang masih memiliki kuota
                        return $query->where('sisa_kuota', '>', 0);
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sks')
                    ->label('SKS')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'canceled' => 'Dibatalkan',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('kelas.mataKuliah.kode_mk')
                    ->label('Kode MK')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.dosen.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sks')
                    ->label('SKS')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'canceled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'canceled' => 'Dibatalkan',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}