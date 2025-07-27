<?php

namespace App\Interfaces;

interface JadwalServiceInterface
{
    public function isScheduleConflict(
        int $ruangKuliahId,
        int $dosenId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int $exceptJadwalId = null
    ): bool;
}
