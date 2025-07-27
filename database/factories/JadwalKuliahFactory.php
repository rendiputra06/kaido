<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\RuangKuliah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JadwalKuliah>
 */
class JadwalKuliahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'ruang_kuliah_id' => RuangKuliah::factory(),
            'hari' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ];
    }
}
