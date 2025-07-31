<?php

namespace App\Interfaces;

use App\Models\BorangNilai;
use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\NilaiAkhir;
use App\Models\NilaiMahasiswa;
use Illuminate\Support\Collection;

interface NilaiRepositoryInterface
{
    // Komponen Nilai
    public function getAllKomponenNilai(bool $aktifOnly = true): Collection;
    public function createKomponenNilai(array $data): KomponenNilai;
    public function updateKomponenNilai(KomponenNilai $komponenNilai, array $data): bool;
    public function deleteKomponenNilai(KomponenNilai $komponenNilai): bool;

    // Borang Nilai
    public function getBorangNilaiByKelas(int $kelasId): Collection;
    public function createOrUpdateBorangNilai(int $kelasId, int $dosenId, array $komponenNilaiData): bool;
    public function lockBorangNilai(int $kelasId, int $dosenId): bool;
    public function isBorangNilaiLocked(int $kelasId): bool;

    // Nilai Mahasiswa
    public function getNilaiMahasiswaByKelas(int $kelasId, int $mahasiswaId): Collection;
    public function saveNilaiMahasiswa(int $krsDetailId, int $borangNilaiId, float $nilai): NilaiMahasiswa;
    public function importNilaiMahasiswaFromExcel(int $kelasId, string $filePath): array;

    // Nilai Akhir
    public function hitungNilaiAkhir(int $krsDetailId): NilaiAkhir;
    public function finalizeNilai(int $krsDetailId, int $dosenId): NilaiAkhir;
    public function getNilaiAkhirByMahasiswa(int $mahasiswaId, ?int $semester = null): Collection;
    public function getNilaiAkhirByKelas(int $kelasId): Collection;

    // Laporan
    public function getRekapNilaiKelas(int $kelasId): array;
    public function getRekapNilaiMahasiswa(int $mahasiswaId, ?int $semester = null): array;
    public function getStatistikNilaiKelas(int $kelasId): array;

    // Grade Scale
    public function getGradeScaleByScore(float $score, ?int $programStudiId = null): ?\App\Models\GradeScale;
}
