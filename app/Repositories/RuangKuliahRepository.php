<?php

namespace App\Repositories;

use App\Interfaces\RuangKuliahRepositoryInterface;
use App\Models\RuangKuliah;
use Illuminate\Database\Eloquent\Collection;

class RuangKuliahRepository implements RuangKuliahRepositoryInterface
{
    public function getAllRuangKuliah(): Collection
    {
        return RuangKuliah::all();
    }

    public function getRuangKuliahById(int $id): ?RuangKuliah
    {
        return RuangKuliah::find($id);
    }

    public function createRuangKuliah(array $data): RuangKuliah
    {
        return RuangKuliah::create($data);
    }

    public function updateRuangKuliah(int $id, array $data): ?RuangKuliah
    {
        $ruang = $this->getRuangKuliahById($id);

        if ($ruang) {
            $ruang->update($data);
            return $ruang;
        }

        return null;
    }

    public function deleteRuangKuliah(int $id): bool
    {
        $ruang = $this->getRuangKuliahById($id);

        if ($ruang) {
            return $ruang->delete();
        }

        return false;
    }
}
