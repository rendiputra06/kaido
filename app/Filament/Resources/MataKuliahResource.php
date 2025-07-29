<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataKuliahResource\Pages;
use App\Filament\Resources\MataKuliahResource\RelationManagers;
use App\Models\MataKuliah;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Filters\SelectFilter;

class MataKuliahResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = MataKuliah::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Akademik';
    
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_studi_id')
                    ->relationship('programStudi', 'nama_prodi')
                    ->required(),
                Forms\Components\Select::make('kurikulum_id')
                    ->relationship('kurikulum', 'nama_kurikulum')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('kode_mk')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_mk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sks')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('semester')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('programStudi.nama_prodi')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kurikulum.nama_kurikulum')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kode_mk')->searchable(),
                Tables\Columns\TextColumn::make('nama_mk')->searchable(),
                Tables\Columns\TextColumn::make('sks'),
                Tables\Columns\TextColumn::make('semester'),
            ])
            ->filters([
                SelectFilter::make('program_studi_id')
                    ->relationship('programStudi', 'nama_prodi')
                    ->label('Program Studi')
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
            RelationManagers\PrerequisitesRelationManager::class,
            RelationManagers\AdvancedCoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMataKuliahs::route('/'),
            'create' => Pages\CreateMataKuliah::route('/create'),
            'edit' => Pages\EditMataKuliah::route('/{record}/edit'),
        ];
    }
}
