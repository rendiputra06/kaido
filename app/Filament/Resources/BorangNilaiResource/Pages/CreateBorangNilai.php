<?php

namespace App\Filament\Resources\BorangNilaiResource\Pages;

use App\Filament\Resources\BorangNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBorangNilai extends CreateRecord
{
    protected static string $resource = BorangNilaiResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
