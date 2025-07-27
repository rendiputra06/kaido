<?php

namespace App\Services;

use App\Interfaces\JadwalKuliahRepositoryInterface;
use App\Interfaces\JadwalServiceInterface;

class JadwalService implements JadwalServiceInterface
{
    protected JadwalKuliahRepositoryInterface $jadwalRepository;

    public function __construct(JadwalKuliahRepositoryInterface $jadwalRepository)
    {
        $this->jadwalRepository = $jadwalRepository;
    }

    /**
     * Mengecek apakah ada jadwal yang bentrok.
     * Logika bisnis utama ada di sini.
     */
    public function isScheduleConflict(
        int $ruangKuliahId,
        int $dosenId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int $exceptJadwalId = null
    ): bool {
        $conflictingSchedules = $this->jadwalRepository->getConflictingSchedules(
            $ruangKuliahId,
            $dosenId,
            $hari,
            $jamMulai,
            $jamSelesai,
            $exceptJadwalId
        );

        return $conflictingSchedules->isNotEmpty();
    }
}
