<?php

namespace Database\Seeders;

use App\Models\KomponenNilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomponenNilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('komponen_nilais')->delete();
        
        // Reset auto-increment counter for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('DELETE FROM sqlite_sequence WHERE name = ?', ['komponen_nilais']);
        }
        
        // Default grade components
        $components = [
            [
                'kode' => 'TGS',
                'nama' => 'Tugas',
                'default_bobot' => 20.00,
                'keterangan' => 'Nilai tugas individu atau kelompok',
                'is_aktif' => true,
            ],
            [
                'kode' => 'KUIS',
                'nama' => 'Kuis',
                'default_bobot' => 15.00,
                'keterangan' => 'Nilai kuis singkat',
                'is_aktif' => true,
            ],
            [
                'kode' => 'UTS',
                'nama' => 'Ujian Tengah Semester',
                'default_bobot' => 30.00,
                'keterangan' => 'Nilai Ujian Tengah Semester',
                'is_aktif' => true,
            ],
            [
                'kode' => 'UAS',
                'nama' => 'Ujian Akhir Semester',
                'default_bobot' => 35.00,
                'keterangan' => 'Nilai Ujian Akhir Semester',
                'is_aktif' => true,
            ],
            [
                'kode' => 'PRAK',
                'nama' => 'Praktikum',
                'default_bobot' => 25.00,
                'keterangan' => 'Nilai praktikum/laboratorium',
                'is_aktif' => true,
            ],
            [
                'kode' => 'PROYEK',
                'nama' => 'Proyek',
                'default_bobot' => 30.00,
                'keterangan' => 'Nilai proyek akhir',
                'is_aktif' => true,
            ],
            [
                'kode' => 'PARTISIPASI',
                'nama' => 'Partisipasi',
                'default_bobot' => 10.00,
                'keterangan' => 'Keaktifan di kelas',
                'is_aktif' => true,
            ],
        ];
        
        // Insert the components
        foreach ($components as $component) {
            KomponenNilai::create($component);
        }
        
        $this->command->info('Komponen nilai berhasil disimpan!');
    }
}
