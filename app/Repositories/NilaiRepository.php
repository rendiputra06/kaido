<?php

namespace App\Repositories;

use App\Models\BorangNilai;
use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\KrsDetail;
use App\Models\NilaiAkhir;
use App\Models\NilaiMahasiswa;
use App\Models\GradeScale;
use App\Repositories\Interfaces\NilaiRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NilaiRepository implements NilaiRepositoryInterface
{
    // Komponen Nilai
    public function getAllKomponenNilai(bool $aktifOnly = true): Collection
    {
        $query = KomponenNilai::query();
        
        if ($aktifOnly) {
            $query->where('is_aktif', true);
        }
        
        return $query->orderBy('nama')->get();
    }

    public function createKomponenNilai(array $data): KomponenNilai
    {
        return KomponenNilai::create($data);
    }

    public function updateKomponenNilai(KomponenNilai $komponenNilai, array $data): bool
    {
        return $komponenNilai->update($data);
    }

    public function deleteKomponenNilai(KomponenNilai $komponenNilai): bool
    {
        // Check if komponen nilai is being used in any borang nilai
        if ($komponenNilai->borangNilai()->exists()) {
            throw new \Exception('Komponen nilai tidak dapat dihapus karena sudah digunakan dalam borang nilai.');
        }
        
        return $komponenNilai->delete();
    }

    // Borang Nilai
    public function getBorangNilaiByKelas(int $kelasId): Collection
    {
        return BorangNilai::with('komponenNilai')
            ->where('kelas_id', $kelasId)
            ->orderBy('created_at')
            ->get();
    }

    public function createOrUpdateBorangNilai(int $kelasId, int $dosenId, array $komponenNilaiData): bool
    {
        return DB::transaction(function () use ($kelasId, $dosenId, $komponenNilaiData) {
            $kelas = Kelas::findOrFail($kelasId);
            
            // Validate total bobot is 100
            $totalBobot = collect($komponenNilaiData)->sum('bobot');
            
            if (abs($totalBobot - 100) > 0.01) { // Allow for floating point precision
                throw new \Exception('Total bobot harus 100%');
            }
            
            // Delete existing borang nilai for this class
            BorangNilai::where('kelas_id', $kelasId)->delete();
            
            // Create new borang nilai entries
            foreach ($komponenNilaiData as $item) {
                BorangNilai::create([
                    'kelas_id' => $kelasId,
                    'komponen_nilai_id' => $item['komponen_nilai_id'],
                    'bobot' => $item['bobot'],
                    'dosen_id' => $dosenId,
                    'is_locked' => false,
                ]);
            }
            
            return true;
        });
    }

    public function lockBorangNilai(int $kelasId, int $dosenId): bool
    {
        $borangNilai = BorangNilai::where('kelas_id', $kelasId)
            ->where('dosen_id', $dosenId)
            ->firstOrFail();
            
        $borangNilai->lock();
        return true;
    }

    public function isBorangNilaiLocked(int $kelasId): bool
    {
        return BorangNilai::where('kelas_id', $kelasId)
            ->where('is_locked', true)
            ->exists();
    }

    // Nilai Mahasiswa
    public function getNilaiMahasiswaByKelas(int $kelasId, int $mahasiswaId): Collection
    {
        return NilaiMahasiswa::with(['borangNilai.komponenNilai'])
            ->whereHas('borangNilai', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->whereHas('krsDetail', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })
            ->get();
    }

    public function saveNilaiMahasiswa(int $krsDetailId, int $borangNilaiId, float $nilai): NilaiMahasiswa
    {
        // Validate nilai is between 0 and 100
        if ($nilai < 0 || $nilai > 100) {
            throw new \InvalidArgumentException('Nilai harus antara 0 dan 100');
        }
        
        // Check if borang nilai is locked
        $borangNilai = BorangNilai::findOrFail($borangNilaiId);
        if ($borangNilai->isLocked()) {
            throw new \Exception('Borang nilai sudah dikunci dan tidak dapat diubah');
        }
        
        return NilaiMahasiswa::updateOrCreate(
            [
                'krs_detail_id' => $krsDetailId,
                'borang_nilai_id' => $borangNilaiId,
            ],
            [
                'nilai' => $nilai
            ]
        );
    }

    public function importNilaiMahasiswaFromExcel(int $kelasId, string $filePath): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            array_shift($rows);
            
            // Get borang nilai for this class
            $borangNilaiList = $this->getBorangNilaiByKelas($kelasId);
            
            DB::beginTransaction();
            
            foreach ($rows as $index => $row) {
                try {
                    $nim = $row[0]; // Assuming NIM is in first column
                    $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
                    
                    // Get KRS detail for this student and class
                    $krsDetail = KrsDetail::whereHas('krs', function($q) use ($mahasiswa) {
                        $q->where('mahasiswa_id', $mahasiswa->id);
                    })
                    ->whereHas('kelas', function($q) use ($kelasId) {
                        $q->where('id', $kelasId);
                    })
                    ->firstOrFail();
                    
                    // Process each komponen nilai
                    foreach ($borangNilaiList as $i => $borangNilai) {
                        $nilai = $row[$i + 1]; // Assuming values start from second column
                        
                        if (is_numeric($nilai)) {
                            $this->saveNilaiMahasiswa(
                                $krsDetail->id,
                                $borangNilai->id,
                                (float) $nilai
                            );
                        }
                    }
                    
                    $results['success']++;
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    Log::error("Error importing nilai at row {$index}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return $results;
    }

    public function getGradeScaleByScore(float $score, ?int $programStudiId = null): ?GradeScale
    {
        // Prioritize program studi-specific grade scale
        if ($programStudiId) {
            $gradeScale = GradeScale::where('program_studi_id', $programStudiId)
                ->where('rentang_bawah', '<=', $score)
                ->where('rentang_atas', '>=', $score)
                ->where('is_aktif', true)
                ->first();

            if ($gradeScale) {
                return $gradeScale;
            }
        }

        // Fallback to default grade scale (where program_studi_id is null)
        return GradeScale::whereNull('program_studi_id')
            ->where('rentang_bawah', '<=', $score)
            ->where('rentang_atas', '>=', $score)
            ->where('is_aktif', true)
            ->first();
    }

    // Nilai Akhir
    public function hitungNilaiAkhir(int $krsDetailId): NilaiAkhir
    {
        $krsDetail = KrsDetail::with(['nilaiMahasiswa.borangNilai.komponenNilai'])->findOrFail($krsDetailId);
        
        // Calculate weighted average
        $totalNilai = 0;
        $totalBobot = 0;
        
        foreach ($krsDetail->nilaiMahasiswa as $nilai) {
            $totalNilai += $nilai->nilai * ($nilai->borangNilai->bobot / 100);
            $totalBobot += $nilai->borangNilai->bobot;
        }
        
        // If total bobot is 0, return 0 to avoid division by zero
        $nilaiAkhir = $totalBobot > 0 ? $totalNilai : 0;
        
        // Convert to letter grade
        $nilaiHuruf = $this->convertToLetterGrade($nilaiAkhir);
        
        // Calculate bobot nilai for IPK (A=4, B=3, C=2, D=1, E=0)
        $bobotNilai = $this->convertToGradePoint($nilaiHuruf);
        
        // Save or update nilai akhir
        return NilaiAkhir::updateOrCreate(
            ['krs_detail_id' => $krsDetailId],
            [
                'nilai_angka' => $nilaiAkhir,
                'nilai_huruf' => $nilaiHuruf,
                'bobot_nilai' => $bobotNilai,
                'is_final' => false,
            ]
        );
    }
    
    public function finalizeNilai(int $krsDetailId, int $dosenId): NilaiAkhir
    {
        $nilaiAkhir = $this->hitungNilaiAkhir($krsDetailId);
        
        // Mark as finalized
        $nilaiAkhir->update([
            'is_final' => true,
            'finalized_by' => $dosenId,
            'finalized_at' => now(),
        ]);
        
        return $nilaiAkhir;
    }
    
    public function getNilaiAkhirByMahasiswa(int $mahasiswaId, ?int $semester = null): Collection
    {
        $query = NilaiAkhir::with(['krsDetail.kelas.mataKuliah', 'krsDetail.krs'])
            ->whereHas('krsDetail.krs', function($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId);
            });
            
        if ($semester) {
            $query->whereHas('krsDetail.krs', function($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }
        
        return $query->get();
    }
    
    public function getNilaiAkhirByKelas(int $kelasId): Collection
    {
        return NilaiAkhir::with(['krsDetail.krs.mahasiswa', 'krsDetail.kelas.mataKuliah'])
            ->whereHas('krsDetail', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->get();
    }
    
    // Laporan
    public function getRekapNilaiKelas(int $kelasId): array
    {
        $nilaiAkhir = $this->getNilaiAkhirByKelas($kelasId);
        
        return [
            'total_mahasiswa' => $nilaiAkhir->unique('krs_detail.krs.mahasiswa_id')->count(),
            'nilai_rata_rata' => $nilaiAkhir->avg('nilai_angka'),
            'distribusi_nilai' => $nilaiAkhir->groupBy('nilai_huruf')
                ->map->count()
                ->toArray(),
            'detail_nilai' => $nilaiAkhir->map(function($item) {
                return [
                    'mahasiswa_id' => $item->krsDetail->krs->mahasiswa_id,
                    'nim' => $item->krsDetail->krs->mahasiswa->nim,
                    'nama' => $item->krsDetail->krs->mahasiswa->nama,
                    'nilai_angka' => $item->nilai_angka,
                    'nilai_huruf' => $item->nilai_huruf,
                    'bobot_nilai' => $item->bobot_nilai,
                ];
            })->toArray(),
        ];
    }
    
    public function getRekapNilaiMahasiswa(int $mahasiswaId, ?int $semester = null): array
    {
        $nilaiAkhir = $this->getNilaiAkhirByMahasiswa($mahasiswaId, $semester);
        
        return [
            'total_matkul' => $nilaiAkhir->count(),
            'ipk' => $nilaiAkhir->avg('bobot_nilai'),
            'detail_matkul' => $nilaiAkhir->map(function($item) {
                return [
                    'kode_matkul' => $item->krsDetail->kelas->mataKuliah->kode,
                    'nama_matkul' => $item->krsDetail->kelas->mataKuliah->nama,
                    'sks' => $item->krsDetail->kelas->mataKuliah->sks,
                    'nilai_angka' => $item->nilai_angka,
                    'nilai_huruf' => $item->nilai_huruf,
                    'bobot_nilai' => $item->bobot_nilai,
                ];
            })->toArray(),
        ];
    }
    
    public function getStatistikNilaiKelas(int $kelasId): array
    {
        $nilaiAkhir = $this->getNilaiAkhirByKelas($kelasId);
        
        $nilaiAngka = $nilaiAkhir->pluck('nilai_angka')->filter()->toArray();
        
        return [
            'total_mahasiswa' => $nilaiAkhir->unique('krs_detail.krs.mahasiswa_id')->count(),
            'nilai_tertinggi' => count($nilaiAngka) > 0 ? max($nilaiAngka) : 0,
            'nilai_terendah' => count($nilaiAngka) > 0 ? min($nilaiAngka) : 0,
            'nilai_rata_rata' => count($nilaiAngka) > 0 ? array_sum($nilaiAngka) / count($nilaiAngka) : 0,
            'distribusi_nilai' => $nilaiAkhir->groupBy('nilai_huruf')
                ->map->count()
                ->toArray(),
        ];
    }
    
    // Helper Methods
    protected function convertToLetterGrade(float $nilai): string
    {
        if ($nilai >= 80) return 'A';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }
    
    protected function convertToGradePoint(string $nilaiHuruf): float
    {
        return match($nilaiHuruf) {
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }
}
