<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KomponenNilaiResource\Pages;
use App\Filament\Resources\KomponenNilaiResource\RelationManagers;
use App\Models\KomponenNilai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KomponenNilaiResource extends Resource
{
    protected static ?string $model = KomponenNilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            'index' => Pages\ListKomponenNilais::route('/'),
            'create' => Pages\CreateKomponenNilai::route('/create'),
            'edit' => Pages\EditKomponenNilai::route('/{record}/edit'),
        ];
    }
}
