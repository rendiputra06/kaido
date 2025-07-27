<?php

namespace App\Filament\Resources\KomponenNilaiResource\Pages;

use App\Filament\Resources\KomponenNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKomponenNilai extends EditRecord
{
    protected static string $resource = KomponenNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
