<?php

namespace App\Interfaces;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface KelasRepositoryInterface
{
    public function getAllKelas(int $perPage = 10): LengthAwarePaginator;

    public function getKelasById(int $id): ?Kelas;

    public function createKelas(array $data): Kelas;

    public function updateKelas(int $id, array $data): ?Kelas;

    public function deleteKelas(int $id): bool;
}