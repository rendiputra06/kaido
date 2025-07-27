<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kuota = $this->faker->numberBetween(30, 40);
        return [
            'nama' => $this->faker->randomElement(['A', 'B', 'C']),
            'kuota' => $kuota,
            'sisa_kuota' => $kuota,
            'mata_kuliah_id' => MataKuliah::factory(),
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'dosen_id' => Dosen::factory(),
        ];
    }
}
