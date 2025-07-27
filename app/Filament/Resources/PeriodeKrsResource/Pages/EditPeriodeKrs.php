<?php

namespace App\Filament\Resources\PeriodeKrsResource\Pages;

use App\Filament\Resources\PeriodeKrsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeriodeKrs extends EditRecord
{
    protected static string $resource = PeriodeKrsResource::class;

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
