<?php

namespace App\Filament\Resources\KrsMahasiswaResource\Pages;

use App\Filament\Resources\KrsMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKrsMahasiswa extends ViewRecord
{
    protected static string $resource = KrsMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}