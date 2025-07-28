<?php

namespace App\Interfaces;

use App\Models\PeriodeKrs;
use Illuminate\Database\Eloquent\Collection;

interface PeriodeKrsRepositoryInterface
{
    public function getActivePeriod(): ?PeriodeKrs;

    public function getAllPeriods(): Collection;

    public function getPeriodById(int $id): ?PeriodeKrs;

    public function createPeriod(array $data): PeriodeKrs;

    public function updatePeriod(int $id, array $data): ?PeriodeKrs;

    public function deletePeriod(int $id): bool;

    public function activatePeriod(int $id): bool;
}
