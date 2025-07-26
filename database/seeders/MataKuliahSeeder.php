<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programStudi = ProgramStudi::where('kode_prodi', 'EI')->first();
        
        if (!$programStudi) {
            return;
        }
        
        $mataKuliahs = [
            ['kode_mk' => 'EI101', 'nama_mk' => 'Pengantar Ekonomi Islam', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'EI102', 'nama_mk' => 'Fiqih Muamalah', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'EI103', 'nama_mk' => 'Akuntansi Syariah Dasar', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'EI104', 'nama_mk' => 'Bahasa Arab untuk Ekonomi', 'sks' => 2, 'semester' => 1],
            ['kode_mk' => 'EI105', 'nama_mk' => 'Sejarah Pemikiran Ekonomi Islam', 'sks' => 2, 'semester' => 2],
            ['kode_mk' => 'EI201', 'nama_mk' => 'Perbankan Syariah', 'sks' => 3, 'semester' => 2],
            ['kode_mk' => 'EI202', 'nama_mk' => 'Manajemen Zakat dan Wakaf', 'sks' => 3, 'semester' => 2],
            ['kode_mk' => 'EI203', 'nama_mk' => 'Ekonomi Mikro Islam', 'sks' => 3, 'semester' => 2],
            ['kode_mk' => 'EI301', 'nama_mk' => 'Ekonomi Makro Islam', 'sks' => 3, 'semester' => 3],
            ['kode_mk' => 'EI302', 'nama_mk' => 'Asuransi Syariah', 'sks' => 3, 'semester' => 3],
            ['kode_mk' => 'EI303', 'nama_mk' => 'Pasar Modal Syariah', 'sks' => 3, 'semester' => 3],
            ['kode_mk' => 'EI304', 'nama_mk' => 'Lembaga Keuangan Syariah Non-Bank', 'sks' => 3, 'semester' => 4],
            ['kode_mk' => 'EI401', 'nama_mk' => 'Manajemen Bisnis Syariah', 'sks' => 3, 'semester' => 4],
            ['kode_mk' => 'EI402', 'nama_mk' => 'Ekonomi Pembangunan Islam', 'sks' => 3, 'semester' => 4],
            ['kode_mk' => 'EI403', 'nama_mk' => 'Kewirausahaan Syariah', 'sks' => 3, 'semester' => 5],
            ['kode_mk' => 'EI501', 'nama_mk' => 'Analisis Laporan Keuangan Syariah', 'sks' => 3, 'semester' => 5],
            ['kode_mk' => 'EI502', 'nama_mk' => 'Manajemen Risiko Syariah', 'sks' => 3, 'semester' => 5],
            ['kode_mk' => 'EI503', 'nama_mk' => 'Etika Bisnis Islam', 'sks' => 2, 'semester' => 6],
            ['kode_mk' => 'EI601', 'nama_mk' => 'Metodologi Penelitian Ekonomi Islam', 'sks' => 3, 'semester' => 6],
            ['kode_mk' => 'EI602', 'nama_mk' => 'Seminar Ekonomi Islam', 'sks' => 2, 'semester' => 7],
            ['kode_mk' => 'EI701', 'nama_mk' => 'Skripsi', 'sks' => 6, 'semester' => 8],
        ];
        
        foreach ($mataKuliahs as $mk) {
            MataKuliah::create([
                'program_studi_id' => $programStudi->id,
                'kode_mk' => $mk['kode_mk'],
                'nama_mk' => $mk['nama_mk'],
                'sks' => $mk['sks'],
                'semester' => $mk['semester'],
            ]);
        }
    }
}