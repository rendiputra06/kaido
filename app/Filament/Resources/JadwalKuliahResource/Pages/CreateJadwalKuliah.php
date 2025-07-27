<?php

namespace App\Filament\Resources\JadwalKuliahResource\Pages;

use App\Filament\Resources\JadwalKuliahResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalKuliah extends CreateRecord
{
    protected static string $resource = JadwalKuliahResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
