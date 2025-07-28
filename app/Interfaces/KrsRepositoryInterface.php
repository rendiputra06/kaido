<?php

namespace App\Interfaces;

use App\Models\KrsMahasiswa;
use App\Models\KrsDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface KrsRepositoryInterface
{
    /**
     * Mengambil KRS mahasiswa berdasarkan mahasiswa dan periode
     */
    public function getKrsByMahasiswaAndPeriode(int $mahasiswaId, int $periodeId): ?KrsMahasiswa;

    /**
     * Mengambil semua KRS mahasiswa dengan paginasi
     */
    public function getAllKrs(int $perPage = 10): LengthAwarePaginator;

    /**
     * Mengambil KRS berdasarkan ID
     */
    public function getKrsById(int $id): ?KrsMahasiswa;

    /**
     * Membuat KRS baru
     */
    public function createKrs(array $data): KrsMahasiswa;

    /**
     * Memperbarui KRS
     */
    public function updateKrs(int $id, array $data): ?KrsMahasiswa;

    /**
     * Menghapus KRS
     */
    public function deleteKrs(int $id): bool;

    /**
     * Menambahkan detail KRS (mata kuliah)
     */
    public function addKrsDetail(int $krsId, int $kelasId): KrsDetail;

    /**
     * Menghapus detail KRS
     */
    public function removeKrsDetail(int $krsDetailId): bool;

    /**
     * Mengambil KRS mahasiswa bimbingan dosen
     */
    public function getKrsByDosenPa(int $dosenId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Mengambil KRS berdasarkan status
     */
    public function getKrsByStatus(string $status, int $perPage = 10): LengthAwarePaginator;

    /**
     * Validasi apakah kelas sudah diambil dalam KRS
     */
    public function isKelasAlreadyTaken(int $krsId, int $kelasId): bool;

    /**
     * Hitung total SKS KRS
     */
    public function calculateTotalSks(int $krsId): int;
}
