<?php

namespace App\Filament\Resources\BorangNilaiResource\Pages;

use App\Filament\Resources\BorangNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorangNilai extends EditRecord
{
    protected static string $resource = BorangNilaiResource::class;

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
