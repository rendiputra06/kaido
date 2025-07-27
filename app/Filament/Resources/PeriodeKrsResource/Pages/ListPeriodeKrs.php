<?php

namespace App\Filament\Resources\PeriodeKrsResource\Pages;

use App\Filament\Resources\PeriodeKrsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriodeKrs extends ListRecords
{
    protected static string $resource = PeriodeKrsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
