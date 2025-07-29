<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\KrsDetail;
use App\Models\KrsMahasiswa;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeKrs;
use App\Models\ProgramStudi;
use App\Models\RuangKuliah;
use App\Models\TahunAjaran;
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

            // 4. Dosen
            $dosens = Dosen::factory(3)->create();

            // 5. Mahasiswa
            $mahasiswas = Mahasiswa::factory(20)->create(['program_studi_id' => $prodi->id]);

            // 5.1. Penetapan Dosen PA
            $dosenChunks = $dosens->pluck('id');
            $mahasiswaChunks = $mahasiswas->split(count($dosenChunks));
            
            foreach ($mahasiswaChunks as $index => $chunk) {
                Mahasiswa::whereIn('id', $chunk->pluck('id'))->update(['dosen_pa_id' => $dosenChunks[$index]]);
            }
            
            // Refresh data mahasiswa setelah diupdate
            $mahasiswas = Mahasiswa::all();

            // 6. Tahun Ajaran & Periode KRS Aktif
            $tahunAjaran = TahunAjaran::factory()->create(['nama' => '2024/2025']);
            $periodeKrs = PeriodeKrs::factory()->create([
                'nama_periode' => 'Ganjil 2024/2025',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'status' => 'aktif',
                'tgl_mulai' => now()->toDateString(),
                'tgl_selesai' => now()->addMonths(4)->toDateString(),
            ]);

            // 7. Mata Kuliah
            $mataKuliahs = MataKuliah::factory(5)->create([
                'kurikulum_id' => $kurikulum->id,
                'program_studi_id' => $prodi->id,
            ]);

            // 8. Kelas & Jadwal
            $kelasList = collect();
            foreach ($mataKuliahs as $mk) {
                $kelas = Kelas::factory()->create([
                    'mata_kuliah_id' => $mk->id,
                    'dosen_id' => $dosens->random()->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kuota' => 50,
                ]);

                // Buat 1 atau 2 jadwal untuk setiap kelas
                $jumlahJadwal = rand(1, 2);
                for ($i = 0; $i < $jumlahJadwal; $i++) {
                    JadwalKuliah::factory()->create([
                        'kelas_id' => $kelas->id,
                        'ruang_kuliah_id' => $ruangan->random()->id,
                    ]);
                }
                $kelasList->push($kelas);
            }

            // 9. Simulasi Pengisian KRS
            foreach ($mahasiswas as $mahasiswa) {
                // Pastikan mahasiswa punya Dosen PA
                if (!$mahasiswa->dosen_pa_id) {
                    continue;
                }

                // Buat KRS Mahasiswa
                $krs = KrsMahasiswa::factory()->create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'periode_krs_id' => $periodeKrs->id,
                    'dosen_pa_id' => $mahasiswa->dosen_pa_id, // Ambil dari data mahasiswa
                    'status' => 'draft', // Status awal
                    'total_sks' => 0,
                    'max_sks' => 24,
                ]);

                // Ambil 3-5 kelas secara acak
                $jumlahMkDiambil = rand(3, 5);
                $kelasDiambil = $kelasList->random($jumlahMkDiambil);
                $totalSks = 0;

                foreach ($kelasDiambil as $kelas) {
                    // Tambahkan ke KRS Detail
                    KrsDetail::factory()->create([
                        'krs_mahasiswa_id' => $krs->id,
                        'kelas_id' => $kelas->id,
                        'status' => 'active',
                    ]);
                    // Akumulasi SKS
                    $totalSks += $kelas->mataKuliah->sks;
                }

                // Update total SKS di KrsMahasiswa
                $krs->update(['total_sks' => $totalSks]);
            }
        });
    }
}