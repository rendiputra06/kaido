<?php

namespace App\Filament\Resources\GradeScaleResource\Pages;

use App\Filament\Resources\GradeScaleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGradeScale extends CreateRecord
{
    protected static string $resource = GradeScaleResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
