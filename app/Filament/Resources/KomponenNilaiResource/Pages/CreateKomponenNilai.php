<?php

namespace App\Filament\Resources\KomponenNilaiResource\Pages;

use App\Filament\Resources\KomponenNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKomponenNilai extends CreateRecord
{
    protected static string $resource = KomponenNilaiResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
