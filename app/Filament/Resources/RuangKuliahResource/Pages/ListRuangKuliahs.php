<?php

namespace App\Filament\Resources\RuangKuliahResource\Pages;

use App\Filament\Resources\RuangKuliahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRuangKuliahs extends ListRecords
{
    protected static string $resource = RuangKuliahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
