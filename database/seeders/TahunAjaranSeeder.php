<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjarans = [
            [
                'kode' => '20231',
                'nama' => 'Tahun Ajaran 2023/2024 Semester Ganjil',
                'semester' => 'Ganjil',
                'tahun_akademik' => '2023/2024',
                'tgl_mulai' => '2023-03-01',
                'tgl_selesai' => '2023-07-31',
                'is_active' => false,
            ],
            [
                'kode' => '20232',
                'nama' => 'Tahun Ajaran 2023/2024 Semester Genap',
                'semester' => 'Genap',
                'tahun_akademik' => '2023/2024',
                'tgl_mulai' => '2023-09-01',
                'tgl_selesai' => '2023-12-31',
                'is_active' => false,
            ],
            [
                'kode' => '20241',
                'nama' => 'Tahun Ajaran 2024/2025 Semester Ganjil',
                'semester' => 'Ganjil',
                'tahun_akademik' => '2024/2025',
                'tgl_mulai' => '2024-03-01',
                'tgl_selesai' => '2024-07-31',
                'is_active' => false,
            ],
            [
                'kode' => '20242',
                'nama' => 'Tahun Ajaran 2024/2025 Semester Genap',
                'semester' => 'Genap',
                'tahun_akademik' => '2024/2025',
                'tgl_mulai' => '2024-09-01',
                'tgl_selesai' => '2024-12-31',
                'is_active' => true,
            ],
        ];
        
        foreach ($tahunAjarans as $ta) {
            TahunAjaran::create($ta);
        }
    }
}