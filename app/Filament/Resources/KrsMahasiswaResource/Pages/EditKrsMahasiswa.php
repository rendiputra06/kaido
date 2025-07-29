<?php

namespace App\Filament\Resources\KrsMahasiswaResource\Pages;

use App\Filament\Resources\KrsMahasiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKrsMahasiswa extends EditRecord
{
    protected static string $resource = KrsMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}