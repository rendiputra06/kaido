<?php

namespace App\Repositories;

use App\Interfaces\JadwalKuliahRepositoryInterface;
use App\Models\JadwalKuliah;
use Illuminate\Database\Eloquent\Collection;

class JadwalKuliahRepository implements JadwalKuliahRepositoryInterface
{
    /**
     * Mencari jadwal yang bentrok berdasarkan kriteria yang diberikan.
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
    ): Collection {
        $query = JadwalKuliah::where('hari', $hari)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where(function ($q2) use ($jamMulai, $jamSelesai) {
                    $q2->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                });
            })
            ->where(function ($q) use ($ruangKuliahId, $dosenId) {
                $q->where('ruang_kuliah_id', $ruangKuliahId)
                    ->orWhereHas('kelas', function ($q2) use ($dosenId) {
                        $q2->where('dosen_id', $dosenId);
                    });
            });

        if ($exceptJadwalId) {
            $query->where('id', '!=', $exceptJadwalId);
        }

        return $query->get();
    }
}