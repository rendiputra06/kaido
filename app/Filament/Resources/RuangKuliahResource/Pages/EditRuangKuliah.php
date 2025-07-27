<?php

namespace App\Filament\Resources\RuangKuliahResource\Pages;

use App\Filament\Resources\RuangKuliahResource;
use App\Interfaces\RuangKuliahRepositoryInterface;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRuangKuliah extends EditRecord
{
    protected static string $resource = RuangKuliahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $repository = app(RuangKuliahRepositoryInterface::class);
        $repository->updateRuangKuliah($record->id, $data);
        return $record;
    }
}