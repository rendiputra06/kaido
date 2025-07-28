<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MahasiswaResource\Pages;
use App\Filament\Resources\MahasiswaResource\RelationManagers;
use App\Models\Mahasiswa;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Filters\SelectFilter;

class MahasiswaResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Mahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Civitas';
    protected static ?string $title = 'Mahasiswa';
    protected static ?string $navigationLabel = 'Mahasiswa';
    protected static ?string $pluralModelLabel = 'Mahasiswa';

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
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('program_studi_id')
                    ->relationship('programStudi', 'nama_prodi')
                    ->required(),
                Forms\Components\TextInput::make('nim')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('angkatan')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status_mahasiswa')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Cuti' => 'Cuti',
                        'Lulus' => 'Lulus',
                        'Dropout' => 'Dropout',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->directory('mahasiswa-photos'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')->circular(),
                Tables\Columns\TextColumn::make('nim')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('programStudi.nama_prodi')->sortable(),
                Tables\Columns\TextColumn::make('angkatan')->sortable(),
                Tables\Columns\TextColumn::make('status_mahasiswa')->badge(),
            ])
            ->filters([
                SelectFilter::make('program_studi_id')
                    ->relationship('programStudi', 'nama_prodi')
                    ->label('Program Studi'),
                SelectFilter::make('status_mahasiswa')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Cuti' => 'Cuti',
                        'Lulus' => 'Lulus',
                        'Dropout' => 'Dropout',
                    ])
                    ->label('Status'),
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
            'index' => Pages\ListMahasiswas::route('/'),
            'create' => Pages\CreateMahasiswa::route('/create'),
            'edit' => Pages\EditMahasiswa::route('/{record}/edit'),
        ];
    }
}
