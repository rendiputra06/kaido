<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Filament\Resources\KelasResource;
use App\Interfaces\KelasRepositoryInterface;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditKelas extends EditRecord
{
    protected static string $resource = KelasResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $kelasRepository = app(KelasRepositoryInterface::class);
        $kelasRepository->updateKelas($record->id, $data);
        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}