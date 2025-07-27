<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\RuangKuliah;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterAktifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan pengecekan foreign key untuk truncate
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Uncomment if using MySQL
        DB::statement('PRAGMA foreign_keys = OFF;'); // Uncomment if using SQLite

        // 1. Bersihkan data lama untuk memastikan lingkungan yang bersih
        JadwalKuliah::truncate();
        Kelas::truncate();
        MataKuliah::truncate();
        ProgramStudi::truncate();
        TahunAjaran::truncate();
        RuangKuliah::truncate();
        Dosen::truncate();
        // User yang terkait dengan dosen juga bisa di-truncate jika perlu
        // DB::table('users')->where('role', 'dosen')->delete();


        // 2. Buat satu Tahun Ajaran yang aktif
        $this->command->info('Membuat Tahun Ajaran aktif...');
        $tahunAjaran = TahunAjaran::factory()->create([
            'nama' => 'Tahun Ajaran 2024/2025 Ganjil',
            'is_active' => true,
        ]);

        // 3. Buat data master
        $this->command->info('Membuat data master (Ruangan, Prodi, Dosen)...');
        $ruangan = RuangKuliah::factory()->count(5)->create();
        $prodiTI = ProgramStudi::factory()->create(['nama_prodi' => 'Teknik Informatika', 'kode_prodi' => 'TI']);
        $prodiSI = ProgramStudi::factory()->create(['nama_prodi' => 'Ekonomi Islam', 'kode_prodi' => 'SI']);
        $dosens = Dosen::factory()->count(10)->create();

        // 4. Buat Mata Kuliah
        $this->command->info('Membuat Mata Kuliah...');
        $matkulTI = MataKuliah::factory()->count(10)->create(['program_studi_id' => $prodiTI->id]);
        $matkulSI = MataKuliah::factory()->count(8)->create(['program_studi_id' => $prodiSI->id]);

        // 5. Buka Kelas untuk semester aktif
        $this->command->info('Membuka kelas...');
        $kelasDibuka = new \Illuminate\Database\Eloquent\Collection();
        foreach ($matkulTI->concat($matkulSI) as $matkul) {
            // Buka 1 atau 2 kelas untuk setiap matkul
            for ($i = 0; $i < rand(1, 2); $i++) {
                $kelas = Kelas::factory()->create([
                    'mata_kuliah_id' => $matkul->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'dosen_id' => $dosens->random()->id,
                    'nama' => chr(65 + $i), // Kelas A, B
                ]);
                $kelasDibuka->add($kelas);
            }
        }

        // 6. Buat Jadwal Kuliah (dengan logika anti-bentrok sederhana)
        $this->command->info('Membuat jadwal kuliah...');
        $jadwalDibuat = []; // [hari][jam_mulai][ruang_id] = true
        $hariKuliah = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamKuliah = ['08:00:00', '10:15:00', '13:00:00', '15:15:00'];

        foreach ($kelasDibuka as $kelas) {
            $jadwalBerhasilDibuat = false;
            $maxTries = 20; // Mencegah infinite loop
            $tryCount = 0;

            while (!$jadwalBerhasilDibuat && $tryCount < $maxTries) {
                $hari = $hariKuliah[array_rand($hariKuliah)];
                $jamMulai = $jamKuliah[array_rand($jamKuliah)];
                $ruang = $ruangan->random();

                // Cek apakah slot ruangan sudah terisi
                if (!isset($jadwalDibuat[$hari][$jamMulai][$ruang->id])) {
                    JadwalKuliah::factory()->create([
                        'kelas_id' => $kelas->id,
                        'ruang_kuliah_id' => $ruang->id,
                        'hari' => $hari,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => date('H:i:s', strtotime($jamMulai) + (50 * 3 * 60)), // Asumsi 3 SKS = 150 menit
                    ]);

                    // Tandai slot sebagai terisi
                    $jadwalDibuat[$hari][$jamMulai][$ruang->id] = true;
                    $jadwalBerhasilDibuat = true;
                }
                $tryCount++;
            }
        }

        // Aktifkan kembali pengecekan foreign key
        DB::statement('PRAGMA foreign_keys = ON;'); // Uncomment if using SQLite
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Uncomment if using MySQL

        $this->command->info('Seeder Semester Aktif berhasil dijalankan!');
    }
}
