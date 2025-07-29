<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataKuliahPrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh prasyarat untuk mata kuliah Ekonomi Mikro Islam (EI203)
        $ekonomiMikro = MataKuliah::where('kode_mk', 'EI203')->first();
        $pengantarEkonomi = MataKuliah::where('kode_mk', 'EI101')->first();
        
        if ($ekonomiMikro && $pengantarEkonomi) {
            $ekonomiMikro->prasyarats()->syncWithoutDetaching([$pengantarEkonomi->id]);
        }
        
        // Contoh prasyarat untuk mata kuliah Ekonomi Makro Islam (EI301)
        $ekonomiMakro = MataKuliah::where('kode_mk', 'EI301')->first();
        
        if ($ekonomiMakro && $ekonomiMikro) {
            $ekonomiMakro->prasyarats()->syncWithoutDetaching([$ekonomiMikro->id]);
        }
        
        // Contoh prasyarat untuk mata kuliah Pasar Modal Syariah (EI303)
        $pasarModal = MataKuliah::where('kode_mk', 'EI303')->first();
        $perbankanSyariah = MataKuliah::where('kode_mk', 'EI201')->first();
        
        if ($pasarModal && $perbankanSyariah) {
            $pasarModal->prasyarats()->syncWithoutDetaching([$perbankanSyariah->id]);
        }
    }
}