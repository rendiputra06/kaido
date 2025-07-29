<?php

namespace App\Filament\Resources\KrsMahasiswaResource\Pages;

use App\Filament\Resources\KrsMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKrsMahasiswa extends CreateRecord
{
    protected static string $resource = KrsMahasiswaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}