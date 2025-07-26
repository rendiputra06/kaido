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
                'nama' => '2023/2024 Ganjil',
                'tgl_mulai' => '2023-09-01',
                'tgl_selesai' => '2024-01-31',
                'is_active' => false,
            ],
            [
                'kode' => '20232',
                'nama' => '2023/2024 Genap',
                'tgl_mulai' => '2024-02-01',
                'tgl_selesai' => '2024-06-30',
                'is_active' => false,
            ],
            [
                'kode' => '20241',
                'nama' => '2024/2025 Ganjil',
                'tgl_mulai' => '2024-09-01',
                'tgl_selesai' => '2025-01-31',
                'is_active' => true,
            ],
            [
                'kode' => '20242',
                'nama' => '2024/2025 Genap',
                'tgl_mulai' => '2025-02-01',
                'tgl_selesai' => '2025-06-30',
                'is_active' => false,
            ],
        ];
        
        foreach ($tahunAjarans as $ta) {
            TahunAjaran::create($ta);
        }
    }
}