<?php

namespace App\Filament\Resources\RuangKuliahResource\Pages;

use App\Filament\Resources\RuangKuliahResource;
use App\Interfaces\RuangKuliahRepositoryInterface;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRuangKuliah extends CreateRecord
{
    protected static string $resource = RuangKuliahResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $repository = app(RuangKuliahRepositoryInterface::class);
        return $repository->createRuangKuliah($data);
    }
}