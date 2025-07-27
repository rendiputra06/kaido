<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface JadwalKuliahRepositoryInterface
{
    /**
     * Mencari jadwal yang berpotensi bentrok berdasarkan kriteria yang diberikan.
     *
     * @param int $ruangKuliahId
     * @param int $dosenId
     * @param string $hari
     * @param string $jamMulai
     * @param string $jamSelesai
     * @param int|null $exceptJadwalId
     * @return Collection
     */
    public function getConflictingSchedules(
        int $ruangKuliahId,
        int $dosenId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int $exceptJadwalId = null
    ): Collection;
}