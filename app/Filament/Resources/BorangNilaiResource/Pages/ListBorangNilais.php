<?php

namespace App\Filament\Resources\BorangNilaiResource\Pages;

use App\Filament\Resources\BorangNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorangNilais extends ListRecords
{
    protected static string $resource = BorangNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
