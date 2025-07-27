<?php

namespace App\Filament\Resources\KomponenNilaiResource\Pages;

use App\Filament\Resources\KomponenNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKomponenNilais extends ListRecords
{
    protected static string $resource = KomponenNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
