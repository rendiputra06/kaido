<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
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
        
        // Membuat kurikulum
        $kurikulum2020 = Kurikulum::create([
            'program_studi_id' => $programStudi->id,
            'nama_kurikulum' => 'Kurikulum Ekonomi Islam 2020',
            'tahun_mulai' => 2020,
        ]);
        
        $kurikulum2023 = Kurikulum::create([
            'program_studi_id' => $programStudi->id,
            'nama_kurikulum' => 'Kurikulum Ekonomi Islam 2023',
            'tahun_mulai' => 2023,
        ]);
        
        // Mengaitkan mata kuliah dengan kurikulum 2020
        $mataKuliah2020 = MataKuliah::where('program_studi_id', $programStudi->id)
            ->whereIn('kode_mk', [
                'EI101', 'EI102', 'EI103', 'EI104', 'EI105',
                'EI201', 'EI202', 'EI203', 'EI301', 'EI302',
                'EI303', 'EI304', 'EI401', 'EI402', 'EI403',
                'EI501', 'EI502', 'EI503', 'EI601', 'EI602', 'EI701'
            ])
            ->get();
        
        $kurikulum2020->mataKuliahs()->attach($mataKuliah2020->pluck('id')->toArray());
        
        // Mengaitkan mata kuliah dengan kurikulum 2023 (dengan beberapa perbedaan)
        $mataKuliah2023 = MataKuliah::where('program_studi_id', $programStudi->id)
            ->whereIn('kode_mk', [
                'EI101', 'EI102', 'EI103', 'EI104', 'EI105',
                'EI201', 'EI202', 'EI203', 'EI301', 'EI302',
                'EI303', 'EI304', 'EI401', 'EI402', 'EI403',
                'EI501', 'EI502', 'EI503', 'EI601', 'EI701'
            ])
            ->get();
        
        $kurikulum2023->mataKuliahs()->attach($mataKuliah2023->pluck('id')->toArray());
    }
}