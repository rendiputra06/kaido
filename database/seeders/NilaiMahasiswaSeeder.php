<?php

namespace Database\Seeders;

use App\Models\BorangNilai;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\KrsDetail;
use App\Models\KrsMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\NilaiMahasiswa;
use App\Models\PeriodeKrs;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Get existing data
            $prodi = ProgramStudi::firstOrCreate(
                ['kode_prodi' => 'EI'],
                ['nama_prodi' => 'Ekonomi Islam']
            );

            $mahasiswas = Mahasiswa::where('program_studi_id', $prodi->id)->get();
            $mataKuliahs = MataKuliah::where('program_studi_id', $prodi->id)->get();
            $dosens = Dosen::all();
            
            if ($mahasiswas->isEmpty() || $mataKuliahs->isEmpty() || $dosens->isEmpty()) {
                $this->command->error('Required data not found. Please run SemesterAktifSeeder first.');
                return;
            }

            // 2. Create previous academic years (2023/2024 Ganjil & Genap)
            $tahunAjaranGanjil = TahunAjaran::firstOrCreate(
                ['kode' => '20231'],
                [
                    'nama' => '2023/2024 Ganjil',
                    'semester' => 'Ganjil',
                    'tahun_akademik' => '2023/2024',
                    'tgl_mulai' => now()->subYear()->startOfYear(),
                    'tgl_selesai' => now()->subYear()->startOfYear()->addMonths(5),
                    'is_active' => false,
                ]
            );

            $tahunAjaranGenap = TahunAjaran::firstOrCreate(
                ['kode' => '20232'],
                [
                    'nama' => '2023/2024 Genap',
                    'semester' => 'Genap',
                    'tahun_akademik' => '2023/2024',
                    'tgl_mulai' => now()->subYear()->startOfYear()->addMonths(6),
                    'tgl_selesai' => now()->subYear()->endOfYear(),
                    'is_active' => false,
                ]
            );

            // 3. Create KRS periods for each semester
            $periodeKrsGanjil = PeriodeKrs::firstOrCreate(
                ['tahun_ajaran_id' => $tahunAjaranGanjil->id],
                [
                    'nama_periode' => 'Pengisian KRS Ganjil 2023/2024',
                    'status' => 'tidak_aktif', // Inactive since it's in the past
                    'tgl_mulai' => $tahunAjaranGanjil->tgl_mulai->subMonth(),
                    'tgl_selesai' => $tahunAjaranGanjil->tgl_mulai->subDay(),
                ]
            );

            $periodeKrsGenap = PeriodeKrs::firstOrCreate(
                ['tahun_ajaran_id' => $tahunAjaranGenap->id],
                [
                    'nama_periode' => 'Pengisian KRS Genap 2023/2024',
                    'status' => 'tidak_aktif', // Inactive since it's in the past
                    'tgl_mulai' => $tahunAjaranGenap->tgl_mulai->subMonth(),
                    'tgl_selesai' => $tahunAjaranGenap->tgl_mulai->subDay(),
                ]
            );

            // 4. For each semester, create classes and grades
            $this->createSemesterData($mahasiswas, $mataKuliahs, $tahunAjaranGanjil, $periodeKrsGanjil, $dosens);
            $this->createSemesterData($mahasiswas, $mataKuliahs, $tahunAjaranGenap, $periodeKrsGenap, $dosens);
        });
    }

    private function createSemesterData($mahasiswas, $mataKuliahs, $tahunAjaran, $periodeKrs, $dosens)
    {
        // Filter courses for this semester based on academic year
        $semesterKe = $tahunAjaran->semester === 'Ganjil' ? 1 : 2;
        $mataKuliahSemester = $mataKuliahs->filter(function($mk) use ($semesterKe) {
            return $mk->semester === $semesterKe;
        });

        if ($mataKuliahSemester->isEmpty()) {
            $this->command->warn("No courses found for semester {$semesterKe}");
            return;
        }

        // Create classes for this semester
        $kelasList = collect();
        foreach ($mataKuliahSemester as $mk) {
            $dosenId = $dosens->random()->id;
            
            // Generate a class name like 'A' or 'B' for the course in this academic year
            $kelasNumber = Kelas::where('mata_kuliah_id', $mk->id)
                              ->where('tahun_ajaran_id', $tahunAjaran->id)
                              ->count() + 65; // 65 is ASCII for 'A'
            $kelasHuruf = chr($kelasNumber);
            
            $kelas = Kelas::firstOrCreate(
                [
                    'mata_kuliah_id' => $mk->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ],
                [
                    'nama' => $mk->kode_mk . ' ' . $tahunAjaran->tahun_akademik . ' ' . $kelasHuruf,
                    'dosen_id' => $dosenId,
                    'kuota' => 50,
                    'sisa_kuota' => 50,
                ]
            );

            // Create grade components for this class if not exists
            $this->createGradeComponents($mk->id, $kelas->id, $dosenId);
            
            $kelasList->push($kelas);
        }

        // Enroll students in classes and generate grades
        foreach ($mahasiswas as $mahasiswa) {
            if (!$mahasiswa->dosen_pa_id) continue;

            // Create KRS for this student and semester
            $krs = KrsMahasiswa::firstOrCreate(
                [
                    'mahasiswa_id' => $mahasiswa->id,
                    'periode_krs_id' => $periodeKrs->id,
                ],
                [
                    'dosen_pa_id' => $mahasiswa->dosen_pa_id,
                    'status' => 'approved',
                    'total_sks' => 0,
                    'max_sks' => 24,
                ]
            );

            // Take 4-6 random courses for this semester
            $jumlahMkDiambil = min(rand(4, 6), $kelasList->count());
            $kelasDiambil = $kelasList->where('sisa_kuota', '>', 0)
                                    ->random($jumlahMkDiambil);

            $totalSks = 0;
            
            foreach ($kelasDiambil as $kelas) {
                if ($kelas->sisa_kuota <= 0) continue;

                // Enroll student in class
                $krsDetail = KrsDetail::firstOrCreate(
                    [
                        'krs_mahasiswa_id' => $krs->id,
                        'kelas_id' => $kelas->id,
                    ],
                    [
                        'sks' => $kelas->mataKuliah->sks,
                        'status' => 'active',
                    ]
                );

                $totalSks += $kelas->mataKuliah->sks;
                $kelas->decrement('sisa_kuota');

                // Get or create a grade form for this class
                $borangNilai = BorangNilai::where('kelas_id', $kelas->id)
                    ->first();
                
                if ($borangNilai) {
                    // Generate random final grade (0-100)
                    $nilai = rand(50, 100);
                    
                    // Create final grade
                    NilaiMahasiswa::firstOrCreate(
                        [
                            'krs_detail_id' => $krsDetail->id,
                            'borang_nilai_id' => $borangNilai->id,
                        ],
                        [
                            'nilai' => $nilai,
                            'terakhir_diubah' => now(),
                            'diubah_oleh' => $kelas->dosen->user_id ?? null,
                            'keterangan' => 'Nilai diisi otomatis oleh sistem',
                        ]
                    );
                }
            }

            // Update total SKS in KRS
            if ($totalSks > 0) {
                $krs->update(['total_sks' => $totalSks]);
            }
        }
    }

    private function createGradeComponents($mataKuliahId, $kelasId, $dosenId)
    {
        // Get all active grade components
        $komponenNilais = KomponenNilai::where('is_aktif', true)->get();
        
        if ($komponenNilais->isEmpty()) {
            // If no components exist, create default ones
            $komponenNilais = collect([
                ['kode' => 'UTS', 'nama' => 'UTS', 'default_bobot' => 0.3],
                ['kode' => 'UAS', 'nama' => 'UAS', 'default_bobot' => 0.3],
                ['kode' => 'TGS', 'nama' => 'Tugas', 'default_bobot' => 0.2],
                ['kode' => 'KUIS', 'nama' => 'Kuis', 'default_bobot' => 0.2],
            ])->map(function ($item) {
                return KomponenNilai::firstOrCreate(
                    ['kode' => $item['kode']],
                    [
                        'nama' => $item['nama'],
                        'default_bobot' => $item['default_bobot'],
                        'is_aktif' => true
                    ]
                );
            });
        }

        // Create BorangNilai for each component
        foreach ($komponenNilais as $komponen) {
            BorangNilai::firstOrCreate(
                [
                    'kelas_id' => $kelasId,
                    'komponen_nilai_id' => $komponen->id,
                ],
                [
                    'dosen_id' => $dosenId,
                    'bobot' => $komponen->default_bobot,
                ]
            );
        }
    }

    private function convertToLetterGrade($nilai)
    {
        if ($nilai >= 80) return 'A';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'C+';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }
}
