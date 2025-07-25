<?php

namespace App\Filament\Resources\ProgramStudiResource\Pages;

use App\Filament\Resources\ProgramStudiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramStudi extends EditRecord
{
    protected static string $resource = ProgramStudiResource::class;

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
