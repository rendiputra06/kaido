<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeScaleResource\Pages;
use App\Filament\Resources\GradeScaleResource\RelationManagers;
use App\Models\GradeScale;
use App\Models\ProgramStudi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradeScaleResource extends Resource
{
    protected static ?string $model = GradeScale::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Akademik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Aturan Skala Nilai')
                    ->schema([
                        Forms\Components\Select::make('program_studi_id')
                            ->relationship('programStudi', 'nama_prodi')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Program Studi (Opsional)'),
                        Forms\Components\TextInput::make('nilai_huruf')
                            ->required()
                            ->maxLength(2)
                            ->label('Nilai Huruf'),
                        Forms\Components\TextInput::make('nilai_indeks')
                            ->required()
                            ->numeric()
                            ->label('Nilai Indeks (Bobot)'),
                        Forms\Components\TextInput::make('rentang_bawah')
                            ->required()
                            ->numeric()
                            ->label('Batas Bawah Nilai'),
                        Forms\Components\TextInput::make('rentang_atas')
                            ->required()
                            ->numeric()
                            ->label('Batas Atas Nilai'),
                        Forms\Components\Toggle::make('is_aktif')
                            ->required()
                            ->default(true)
                            ->label('Aktif'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('programStudi.nama_prodi')
                    ->label('Program Studi')
                    ->placeholder('Umum (Default)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai_huruf')
                    ->label('Nilai Huruf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai_indeks')
                    ->label('Indeks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rentang_bawah')
                    ->label('Rentang Bawah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rentang_atas')
                    ->label('Rentang Atas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program_studi_id')
                    ->relationship('programStudi', 'nama_prodi')
                    ->label('Program Studi'),
                Tables\Filters\TernaryFilter::make('is_aktif')
                    ->label('Status Aktif'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeScales::route('/'),
            'create' => Pages\CreateGradeScale::route('/create'),
            'edit' => Pages\EditGradeScale::route('/{record}/edit'),
        ];
    }
}
