<?php

namespace App\Interfaces;

use App\Models\KrsMahasiswa;
use App\Models\KrsDetail;

interface KrsServiceInterface
{
    public function createKrs(int $mahasiswaId, int $periodeId, int $dosenPaId): KrsMahasiswa;

    public function addMataKuliah(int $krsId, int $kelasId): KrsDetail;

    public function removeMataKuliah(int $krsId, int $krsDetailId): bool;

    public function submitKrs(int $krsId): KrsMahasiswa;

    public function approveKrs(int $krsId, string $catatan = null): KrsMahasiswa;

    public function rejectKrs(int $krsId, string $catatan): KrsMahasiswa;

    public function resetKrsStatus(int $krsId): KrsMahasiswa;
}
