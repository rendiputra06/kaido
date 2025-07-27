<?php

namespace App\Repositories;

use App\Interfaces\KelasRepositoryInterface;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class KelasRepository implements KelasRepositoryInterface
{
    /**
     * Mengambil semua data kelas dengan paginasi dan eager loading.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllKelas(int $perPage = 10): LengthAwarePaginator
    {
        return Kelas::with(['mataKuliah.programStudi', 'dosen', 'tahunAjaran'])->paginate($perPage);
    }

    /**
     * Mengambil data kelas berdasarkan ID dengan eager loading.
     *
     * @param int $id
     * @return Kelas|null
     */
    public function getKelasById(int $id): ?Kelas
    {
        return Kelas::with(['mataKuliah.programStudi', 'dosen', 'tahunAjaran'])->find($id);
    }

    /**
     * Membuat data kelas baru.
     * Secara otomatis mengatur sisa kuota sama dengan kuota.
     *
     * @param array $data
     * @return Kelas
     */
    public function createKelas(array $data): Kelas
    {
        // Pastikan sisa kuota diisi sama dengan kuota saat kelas dibuat
        if (isset($data['kuota'])) {
            $data['sisa_kuota'] = $data['kuota'];
        }

        return Kelas::create($data);
    }

    /**
     * Memperbarui data kelas berdasarkan ID.
     *
     * @param int $id
     * @param array $data
     * @return Kelas|null
     */
    public function updateKelas(int $id, array $data): ?Kelas
    {
        $kelas = $this->getKelasById($id);

        if ($kelas) {
            $kelas->update($data);
            return $kelas;
        }

        return null;
    }

    /**
     * Menghapus data kelas berdasarkan ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteKelas(int $id): bool
    {
        $kelas = $this->getKelasById($id);

        if ($kelas) {
            return $kelas->delete();
        }

        return false;
    }
}