<?php

namespace App\Interfaces;

use App\Models\PeriodeKrs;

interface PeriodeKrsRepositoryInterface
{
    public function getActivePeriod(): ?PeriodeKrs;
}
