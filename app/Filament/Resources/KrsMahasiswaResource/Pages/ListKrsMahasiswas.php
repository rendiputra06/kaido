<?php

namespace App\Filament\Resources\KrsMahasiswaResource\Pages;

use App\Filament\Resources\KrsMahasiswaResource;
use App\Filament\Widgets\KrsStatusOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKrsMahasiswas extends ListRecords
{
    protected static string $resource = KrsMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            KrsStatusOverview::class,
        ];
    }
}