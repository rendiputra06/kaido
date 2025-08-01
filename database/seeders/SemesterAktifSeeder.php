<?php

namespace Database\Seeders;

use App\Models\BorangNilai;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\KrsDetail;
use App\Models\KrsMahasiswa;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeKrs;
use App\Models\ProgramStudi;
use App\Models\RuangKuliah;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterAktifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Program Studi
            $prodi = ProgramStudi::factory()->create(['nama_prodi' => 'Ekonomi Islam', 'kode_prodi' => 'EI']);

            // 2. Kurikulum
            $kurikulum = Kurikulum::factory()->create(['nama_kurikulum' => 'Kurikulum 2024', 'tahun_mulai' => 2024, 'program_studi_id' => $prodi->id]);

            // 3. Ruang Kuliah
            $ruangan = RuangKuliah::factory(5)->create();

            // 4. Dosen (dengan sinkronisasi nama dari User)
            $dosens = Dosen::factory(5)->make()->each(function ($dosen) {
                $user = User::factory()->create([
                    'name' => fake('id_ID')->name,
                    'email' => fake()->unique()->safeEmail,
                ]);
                $user->assignRole('dosen');
                $dosen->user_id = $user->id;
                $dosen->nama = $user->name; // Sinkronisasi nama
                $dosen->save();
            });

            // 5. Mahasiswa (dengan sinkronisasi nama dari User)
            $mahasiswas = Mahasiswa::factory(50)->make(['program_studi_id' => $prodi->id])->each(function ($mahasiswa) {
                $user = User::factory()->create([
                    'name' => fake('id_ID')->name,
                    'email' => fake()->unique()->safeEmail,
                ]);
                $user->assignRole('mahasiswa');
                $mahasiswa->user_id = $user->id;
                $mahasiswa->nama = $user->name; // Sinkronisasi nama
                $mahasiswa->save();
            });

            // 5.1. Penetapan Dosen PA
            $dosenIds = $dosens->pluck('id');
            foreach ($mahasiswas as $mahasiswa) {
                $mahasiswa->update(['dosen_pa_id' => $dosenIds->random()]);
            }
            $mahasiswas->fresh(); // Refresh data

            // 6. Tahun Ajaran & Periode KRS Aktif
            $tahunAjaran = TahunAjaran::factory()->create(['nama' => '2024/2025 Ganjil', 'is_active' => true]);
            $periodeKrs = PeriodeKrs::factory()->create([
                'nama_periode' => 'Pengisian KRS Ganjil 2024/2025',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'status' => 'aktif',
                'tgl_mulai' => now()->subWeek(),
                'tgl_selesai' => now()->addMonth(),
            ]);

            // 7. Generate unique course codes
            $usedCodes = [];
            $generateUniqueCode = function($name) use (&$usedCodes) {
                do {
                    $prefix = strtoupper(substr(str_replace(' ', '', $name), 0, 3));
                    $code = 'MK' . $prefix . rand(100, 999);
                } while (in_array($code, $usedCodes));
                $usedCodes[] = $code;
                return $code;
            };
            
            // 8. Mata Kuliah for Islamic Economics program
            $mataKuliahs = collect();
            $mataKuliahEkonomiIslam = [
                // Semester 1
                'Pengantar Ekonomi Islam', 'Fiqh Muamalah', 'Bahasa Arab Ekonomi', 'Pengantar Bisnis Syariah',
                'Matematika Ekonomi', 'Bahasa Inggris Bisnis', 'Pendidikan Agama Islam', 'Kewarganegaraan',
                
                // Semester 2
                'Akuntansi Syariah', 'Ekonomi Mikro Islam', 'Sejarah Pemikiran Ekonomi Islam', 'Bahasa Indonesia',
                'Statistika Ekonomi', 'Manajemen Syariah', 'Pendidikan Pancasila', 'Kewirausahaan Syariah',
                
                // Semester 3
                'Ekonomi Makro Islam', 'Manajemen Keuangan Syariah', 'Hukum Bisnis Syariah', 'Metodologi Penelitian',
                'Perbankan Syariah', 'Etika Bisnis Islam',
                
                // Semester 4
                'Pasar Modal Syariah', 'Manajemen Zakat dan Wakaf', 'Ekonomi Pembangunan Islam', 'Komunikasi Bisnis',
                'Asuransi Syariah', 'Koperasi Syariah',
                
                // Semester 5
                'Ekonomi Moneter Islam', 'Manajemen Resiko Syariah', 'Ekonomi Internasional Islam',
                'Manajemen Sumber Daya Manusia Syariah',
                
                // Semester 6
                'Akuntansi Lembaga Keuangan Syariah', 'Perekonomian Indonesia', 'Studi Kelayakan Bisnis Syariah',
                'Kepemimpinan dalam Islam',
                
                // Semester 7
                'Magang Kerja', 'Metodologi Penelitian Lanjutan', 'Kewirausahaan Syariah Lanjutan',
                
                // Semester 8
                'Skripsi', 'Studi Kasus Bisnis Syariah'
            ];
            
            // Create mata kuliah with different semesters
            $courseIndex = 0;
            $semesterData = [];
            
            for ($semester = 1; $semester <= 8; $semester++) {
                // Get courses for this semester (4 courses for semesters 1-4, 2 courses for semesters 5-8)
                $coursesPerSemester = $semester <= 4 ? 4 : 2;
                $mkSemester = collect();
                
                for ($i = 0; $i < $coursesPerSemester && $courseIndex < count($mataKuliahEkonomiIslam); $i++, $courseIndex++) {
                    $namaMk = $mataKuliahEkonomiIslam[$courseIndex];
                    $kodeMk = $generateUniqueCode($namaMk);
                    
                    $mk = MataKuliah::create([
                        'program_studi_id' => $prodi->id,
                        'kode_mk' => $kodeMk,
                        'nama_mk' => $namaMk,
                        'sks' => $semester <= 4 ? 3 : 4, // 3 SKS for semesters 1-4, 4 SKS for semesters 5-8
                        'semester' => $semester,
                    ]);
                    
                    // Store semester and type information
                    $isWajib = true; // Most courses are wajib in this curriculum
                    $semesterData[$mk->id] = [
                        'semester_ditawarkan' => $semester,
                        'jenis' => $isWajib ? 'wajib' : 'pilihan',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    $mkSemester->push($mk);
                }
                
                // Attach to kurikulum with semester and type
                foreach ($mkSemester as $mk) {
                    $kurikulum->mataKuliahs()->attach($mk->id, $semesterData[$mk->id]);
                }
                
                $mataKuliahs = $mataKuliahs->merge($mkSemester);
            }
            
            // Create prerequisite relationships
            foreach ($mataKuliahs as $mk) {
                $semesterMk = $semesterData[$mk->id]['semester_ditawarkan'];
                
                // Only add prerequisites to courses in semester 3 and above
                if ($semesterMk >= 3) {
                    // Get courses from previous semesters as prerequisites
                    $prerequisites = $mataKuliahs->filter(function ($prasyarat) use ($semesterMk, $mk, $semesterData) {
                        $semesterPrasyarat = $semesterData[$prasyarat->id]['semester_ditawarkan'];
                        return $semesterPrasyarat < $semesterMk && 
                               $prasyarat->id != $mk->id;
                    });
                    
                    // Add 1-3 random prerequisites
                    if ($prerequisites->isNotEmpty()) {
                        $prerequisitesCount = min(rand(1, 3), $prerequisites->count());
                        $selectedPrerequisites = $prerequisites->random($prerequisitesCount);
                        
                        foreach ($selectedPrerequisites as $prasyarat) {
                            DB::table('matakuliah_prasyarat')->insert([
                                'matakuliah_id' => $mk->id,
                                'prasyarat_id' => $prasyarat->id,
                            ]);
                        }
                    }
                }
            }

            // 8. Kelas, Jadwal, dan Borang Nilai
            $kelasList = collect();
            $komponenNilaiIds = KomponenNilai::pluck('id');

            foreach ($mataKuliahs as $mk) {
                $kelas = Kelas::factory()->create([
                    'mata_kuliah_id' => $mk->id,
                    'dosen_id' => $dosens->random()->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kuota' => 50,
                    'sisa_kuota' => 50,
                ]);

                // Buat 1 jadwal untuk setiap kelas
                JadwalKuliah::factory()->create([
                    'kelas_id' => $kelas->id,
                    'ruang_kuliah_id' => $ruangan->random()->id,
                ]);

                // Buat Borang Nilai untuk kelas ini
                $komponenDipilih = $komponenNilaiIds->random(rand(2, 4));
                $bobotTersisa = 100;
                foreach ($komponenDipilih as $index => $komponenId) {
                    if ($index === $komponenDipilih->count() - 1) {
                        // Komponen terakhir mengambil semua sisa bobot
                        $bobot = $bobotTersisa;
                    } else {
                        // Bobot acak, pastikan tidak menghabiskan semua sisa
                        $bobot = rand(10, (int)($bobotTersisa / ($komponenDipilih->count() - $index) * 1.2));
                        $bobot = min($bobot, $bobotTersisa - ($komponenDipilih->count() - 1 - $index) * 10); // Pastikan sisa cukup
                        $bobot = max(10, $bobot); // Minimal 10
                        $bobotTersisa -= $bobot;
                    }
                    
                    BorangNilai::create([
                        'kelas_id' => $kelas->id,
                        'komponen_nilai_id' => $komponenId,
                        'bobot' => $bobot,
                        'dosen_id' => $kelas->dosen_id,
                    ]);
                }

                $kelasList->push($kelas);
            }

            // 9. Simulasi Pengisian KRS
            foreach ($mahasiswas as $mahasiswa) {
                if (!$mahasiswa->dosen_pa_id) continue;

                $krs = KrsMahasiswa::factory()->create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'periode_krs_id' => $periodeKrs->id,
                    'dosen_pa_id' => $mahasiswa->dosen_pa_id,
                    'status' => 'approved', // Langsung approved untuk simulasi
                    'total_sks' => 0,
                    'max_sks' => 24,
                ]);

                $jumlahMkDiambil = rand(4, 7);
                $kelasDiambil = $kelasList->where('sisa_kuota', '>', 0)->random($jumlahMkDiambil);
                $totalSks = 0;

                foreach ($kelasDiambil as $kelas) {
                    if ($kelas->sisa_kuota > 0) {
                        KrsDetail::factory()->create([
                            'krs_mahasiswa_id' => $krs->id,
                            'kelas_id' => $kelas->id,
                            'status' => 'active',
                        ]);
                        $totalSks += $kelas->mataKuliah->sks;
                        $kelas->decrement('sisa_kuota');
                    }
                }
                $krs->update(['total_sks' => $totalSks]);
            }
        });
    }
}
