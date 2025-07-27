<?php

namespace App\Filament\Resources\PeriodeKrsResource\Pages;

use App\Filament\Resources\PeriodeKrsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriodeKrs extends CreateRecord
{
    protected static string $resource = PeriodeKrsResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
