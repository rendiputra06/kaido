<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramStudi::create([
            'kode_prodi' => 'EI',
            'nama_prodi' => 'Ekonomi Islam',
            'jenjang' => 'S1',
        ]);
    }
}