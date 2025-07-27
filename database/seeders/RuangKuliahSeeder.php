<?php

namespace Database\Seeders;

use App\Models\RuangKuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RuangKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RuangKuliah::create([
            'nama' => 'Ruang Teori 1',
            'kode' => 'RT01',
            'kapasitas' => 40,
        ]);

        RuangKuliah::create([
            'nama' => 'Ruang Teori 2',
            'kode' => 'RT02',
            'kapasitas' => 40,
        ]);

        RuangKuliah::create([
            'nama' => 'Laboratorium Komputer 1',
            'kode' => 'LK01',
            'kapasitas' => 30,
        ]);
    }
}