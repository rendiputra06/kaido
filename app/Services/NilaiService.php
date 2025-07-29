<?php

namespace App\Services;

use App\Models\BorangNilai;
use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\KrsDetail;
use App\Models\NilaiAkhir;
use App\Models\NilaiMahasiswa;
use App\Repositories\Interfaces\NilaiRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NilaiService
{
    protected $nilaiRepository;

    public function __construct(NilaiRepositoryInterface $nilaiRepository)
    {
        $this->nilaiRepository = $nilaiRepository;
    }

    // Komponen Nilai Methods
    public function getAllKomponenNilai(bool $aktifOnly = true): Collection
    {
        return $this->nilaiRepository->getAllKomponenNilai($aktifOnly);
    }

    public function createKomponenNilai(array $data): KomponenNilai
    {
        return $this->nilaiRepository->createKomponenNilai($data);
    }

    public function updateKomponenNilai(KomponenNilai $komponenNilai, array $data): bool
    {
        return $this->nilaiRepository->updateKomponenNilai($komponenNilai, $data);
    }

    public function deleteKomponenNilai(KomponenNilai $komponenNilai): bool
    {
        return $this->nilaiRepository->deleteKomponenNilai($komponenNilai);
    }

    // Borang Nilai Methods
    public function getBorangNilaiByKelas(int $kelasId): Collection
    {
        return $this->nilaiRepository->getBorangNilaiByKelas($kelasId);
    }

    public function saveBorangNilai(int $kelasId, array $komponenNilaiData): bool
    {
        $dosenId = Auth::id();
        return $this->nilaiRepository->createOrUpdateBorangNilai($kelasId, $dosenId, $komponenNilaiData);
    }

    public function lockBorangNilai(int $kelasId): bool
    {
        $dosenId = Auth::id();
        return $this->nilaiRepository->lockBorangNilai($kelasId, $dosenId);
    }

    public function isBorangNilaiLocked(int $kelasId): bool
    {
        return $this->nilaiRepository->isBorangNilaiLocked($kelasId);
    }

    // Nilai Mahasiswa Methods
    public function getNilaiMahasiswaByKelas(int $kelasId, int $mahasiswaId): Collection
    {
        return $this->nilaiRepository->getNilaiMahasiswaByKelas($kelasId, $mahasiswaId);
    }

    public function saveNilaiMahasiswa(int $krsDetailId, int $borangNilaiId, float $nilai): NilaiMahasiswa
    {
        return $this->nilaiRepository->saveNilaiMahasiswa($krsDetailId, $borangNilaiId, $nilai);
    }

    public function importNilaiMahasiswaFromExcel(int $kelasId, $file): array
    {
        $fileName = 'import_nilai_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('temp', $fileName);
        
        try {
            $result = $this->nilaiRepository->importNilaiMahasiswaFromExcel($kelasId, storage_path('app/' . $filePath));
            Storage::delete($filePath); // Clean up temp file
            return $result;
        } catch (\Exception $e) {
            Storage::delete($filePath); // Clean up temp file on error
            throw $e;
        }
    }

    public function generateTemplateExcel(int $kelasId)
    {
        $kelas = Kelas::with(['mataKuliah', 'mahasiswas'])->findOrFail($kelasId);
        $borangNilai = $this->getBorangNilaiByKelas($kelasId);
        
        if ($borangNilai->isEmpty()) {
            throw new \Exception('Borang nilai untuk kelas ini belum disetup');
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['NIM', 'Nama'];
        foreach ($borangNilai as $item) {
            $headers[] = $item->komponenNilai->nama . ' (' . $item->bobot . '%)';
        }
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Add student data
        $row = 2;
        foreach ($kelas->mahasiswas as $mahasiswa) {
            $sheet->setCellValue('A' . $row, $mahasiswa->nim);
            $sheet->setCellValue('B' . $row, $mahasiswa->nama);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Save to temp file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_nilai_' . $kelas->kode . '.xlsx';
        $tempPath = storage_path('app/temp/' . $fileName);
        
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $writer->save($tempPath);
        
        return $tempPath;
    }

    // Nilai Akhir Methods
    public function hitungNilaiAkhir(int $krsDetailId): NilaiAkhir
    {
        $krsDetail = KrsDetail::with([
            'nilaiMahasiswa.borangNilai.komponenNilai', 
            'kelas.mataKuliah',
            'kelas.borangNilais' => function($q) {
                $q->where('is_locked', true);
            },
            'krsMahasiswa.mahasiswa'
        ])->findOrFail($krsDetailId);
        
        // Check if all required components have grades
        $missingComponents = [];
        $totalNilai = 0;
        $totalBobot = 0;
        $hasAllComponents = true;
        
        // Get all required components for this class
        $requiredComponents = $krsDetail->kelas->borangNilais->pluck('komponen_nilai_id')->toArray();
        
        // Check for missing grades
        foreach ($krsDetail->kelas->borangNilais as $borang) {
            $nilai = $krsDetail->nilaiMahasiswa->firstWhere('borang_nilai_id', $borang->id);
            
            if (!$nilai || $nilai->nilai === null) {
                $missingComponents[] = $borang->komponenNilai->nama;
                $hasAllComponents = false;
            } else {
                // Validate grade range
                if ($nilai->nilai < 0 || $nilai->nilai > 100) {
                    throw new \InvalidArgumentException(
                        "Nilai untuk komponen {$borang->komponenNilai->nama} harus antara 0-100"
                    );
                }
                
                $totalNilai += $nilai->nilai * ($borang->bobot / 100);
                $totalBobot += $borang->bobot;
            }
        }
        
        // If not all components have grades, throw exception with details
        if (!$hasAllComponents) {
            throw new \RuntimeException(
                "Lengkapi nilai untuk komponen berikut: " . implode(', ', $missingComponents)
            );
        }
        
        // Calculate final score (scale to 0-100 if total weight is not 100)
        $nilaiAkhir = $totalBobot > 0 ? round(($totalNilai / $totalBobot) * 100, 2) : 0;
        
        // Get Program Studi ID from Mahasiswa
        $programStudiId = $krsDetail->krsMahasiswa->mahasiswa->program_studi_id ?? null;

        // Get grade scale from repository
        $gradeScale = $this->nilaiRepository->getGradeScaleByScore($nilaiAkhir, $programStudiId);

        if (!$gradeScale) {
            // If no grade scale is found, handle it gracefully or throw an exception
            // For now, we'll set them to default values, but logging a warning would be good.
            // Log::warning("Grade scale not found for score: {$nilaiAkhir} and program studi: {$programStudiId}");
            $nilaiHuruf = 'E'; // Default failing grade
            $bobotNilai = 0.00; // Default failing weight
        } else {
            $nilaiHuruf = $gradeScale->nilai_huruf;
            $bobotNilai = $gradeScale->nilai_indeks;
        }
        
        // Save or update nilai akhir
        return NilaiAkhir::updateOrCreate(
            ['krs_detail_id' => $krsDetailId],
            [
                'nilai_angka' => $nilaiAkhir,
                'nilai_huruf' => $nilaiHuruf,
                'bobot_nilai' => $bobotNilai,
                'is_final' => false,
                'diperbarui_oleh' => Auth::id(),
                'diperbarui_pada' => now(),
            ]
        );
    }

    /**
     * Finalize student's grades for a class
     * 
     * @param int $krsDetailId
     * @return NilaiAkhir
     * @throws \Exception if grades cannot be finalized
     */
    public function finalizeNilai(int $krsDetailId): NilaiAkhir
    {
        $krsDetail = KrsDetail::with(['nilaiAkhir', 'kelas'])->findOrFail($krsDetailId);
        
        // Check if already finalized
        if ($krsDetail->nilaiAkhir && $krsDetail->nilaiAkhir->is_final) {
            throw new \Exception('Nilai sudah difinalisasi sebelumnya');
        }
        
        // Check if all components are locked
        $unlockedComponents = $krsDetail->kelas->borangNilais()
            ->where('is_locked', false)
            ->count();
            
        if ($unlockedComponents > 0) {
            throw new \Exception('Tidak dapat memfinalisasi nilai karena terdapat komponen yang belum dikunci');
        }
        
        // Calculate final grade
        $nilaiAkhir = $this->hitungNilaiAkhir($krsDetailId);
        
        // Update to final
        $nilaiAkhir->update([
            'is_final' => true,
            'difinalisasi_oleh' => Auth::id(),
            'difinalisasi_pada' => now(),
        ]);
        
        // Log the finalization
        activity()
            ->causedBy(Auth::user())
            ->performedOn($nilaiAkhir)
            ->withProperties([
                'nilai_angka' => $nilaiAkhir->nilai_angka,
                'nilai_huruf' => $nilaiAkhir->nilai_huruf,
                'bobot_nilai' => $nilaiAkhir->bobot_nilai,
            ])
            ->log('Nilai difinalisasi');
        
        return $nilaiAkhir;
    }
    
    /**
     * Finalize all students' grades for a class
     * 
     * @param int $kelasId
     * @return array Count of successful and failed finalizations
     */
    public function finalizeAllNilai(int $kelasId): array
    {
        $kelas = Kelas::findOrFail($kelasId);
        $krsDetails = KrsDetail::where('kelas_id', $kelasId)->get();
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($krsDetails as $krsDetail) {
            try {
                $this->finalizeNilai($krsDetail->id);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][$krsDetail->mahasiswa->nim] = $e->getMessage();
            }
        }
        
        // Log the bulk finalization
        activity()
            ->causedBy(Auth::user())
            ->performedOn($kelas)
            ->withProperties($results)
            ->log("Finalisasi nilai untuk {$results['success']} mahasiswa");
        
        return $results;
    }
    
    /**
     * Unfinalize a student's grades (admin only)
     * 
     * @param int $krsDetailId
     * @param string $reason
     * @return NilaiAkhir
     */
    public function unfinalizeNilai(int $krsDetailId, string $reason): NilaiAkhir
    {
        $nilaiAkhir = NilaiAkhir::where('krs_detail_id', $krsDetailId)
            ->where('is_final', true)
            ->firstOrFail();
            
        $nilaiAkhir->update([
            'is_final' => false,
            'alasan_pembatalan' => $reason,
            'dibatalkan_oleh' => Auth::id(),
            'dibatalkan_pada' => now(),
        ]);
        
        // Log the unfinalization
        activity()
            ->causedBy(Auth::user())
            ->performedOn($nilaiAkhir)
            ->withProperties(['reason' => $reason])
            ->log('Pembatalan finalisasi nilai');
            
        return $nilaiAkhir;
    }

    public function getNilaiAkhirByMahasiswa(int $mahasiswaId, ?int $semester = null): Collection
    {
        return $this->nilaiRepository->getNilaiAkhirByMahasiswa($mahasiswaId, $semester);
    }

    public function getNilaiAkhirByKelas(int $kelasId): Collection
    {
        return $this->nilaiRepository->getNilaiAkhirByKelas($kelasId);
    }

    // Laporan Methods
    public function getRekapNilaiKelas(int $kelasId): array
    {
        return $this->nilaiRepository->getRekapNilaiKelas($kelasId);
    }

    public function getRekapNilaiMahasiswa(int $mahasiswaId, ?int $semester = null): array
    {
        return $this->nilaiRepository->getRekapNilaiMahasiswa($mahasiswaId, $semester);
    }

    public function getStatistikNilaiKelas(int $kelasId): array
    {
        return $this->nilaiRepository->getStatistikNilaiKelas($kelasId);
    }

    public function generateKhs(int $mahasiswaId, ?int $semester = null): array
    {
        $data = $this->getRekapNilaiMahasiswa($mahasiswaId, $semester);
        
        // Calculate IPK if not filtered by semester
        $ipk = null;
        if ($semester === null) {
            $ipk = $data['ipk'];
        }
        
        return [
            'mahasiswa' => [
                'nama' => Auth::user()->name,
                'nim' => Auth::user()->mahasiswa->nim,
                'program_studi' => Auth::user()->mahasiswa->programStudi->nama,
                'semester' => $semester ?? 'Semua Semester',
                'ipk' => $ipk,
                'ips' => $semester ? $data['ipk'] : null,
            ],
            'mata_kuliah' => $data['detail_matkul'],
            'total_sks' => collect($data['detail_matkul'])->sum('sks'),
        ];
    }
}
