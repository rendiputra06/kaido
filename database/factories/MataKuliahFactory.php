<?php

namespace Database\Factories;

use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MataKuliah>
 */
class MataKuliahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mataKuliahIndonesia = [
            'Kalkulus I', 'Fisika Dasar', 'Kimia Dasar', 'Algoritma dan Pemrograman', 'Pengantar Ekonomi',
            'Dasar-dasar Manajemen', 'Akuntansi Dasar', 'Bahasa Indonesia', 'Pendidikan Pancasila', 'Statistika',
            'Struktur Data', 'Basis Data', 'Jaringan Komputer', 'Sistem Operasi', 'Ekonomi Mikro', 'Ekonomi Makro'
        ];

        $namaMk = $this->faker->randomElement($mataKuliahIndonesia);
        // Generate a more unique code with timestamp and random number
        $prefix = strtoupper(substr(str_replace(' ', '', $namaMk), 0, 3));
        $kodeMk = 'MK-' . $prefix . '-' . time() . $this->faker->randomNumber(3);

        return [
            'program_studi_id' => ProgramStudi::factory(),
            'kurikulum_id' => Kurikulum::factory(),
            'kode_mk' => $kodeMk,
            'nama_mk' => $namaMk,
            'sks' => $this->faker->randomElement([2, 3, 4]),
            'semester' => $this->faker->numberBetween(1, 8),
        ];
    }
}
