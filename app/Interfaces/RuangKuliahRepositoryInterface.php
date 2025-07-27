<?php

namespace App\Interfaces;

use App\Models\RuangKuliah;
use Illuminate\Database\Eloquent\Collection;

interface RuangKuliahRepositoryInterface
{
    public function getAllRuangKuliah(): Collection;

    public function getRuangKuliahById(int $id): ?RuangKuliah;

    public function createRuangKuliah(array $data): RuangKuliah;

    public function updateRuangKuliah(int $id, array $data): ?RuangKuliah;

    public function deleteRuangKuliah(int $id): bool;
}
