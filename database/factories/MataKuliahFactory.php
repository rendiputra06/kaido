<?php

namespace Database\Factories;

use App\Models\ProgramStudi;
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
        return [
            'program_studi_id' => ProgramStudi::factory(),
            'kode_mk' => $this->faker->unique()->lexify('MK-???'),
            'nama_mk' => 'Matkul ' . $this->faker->unique()->word,
            'sks' => $this->faker->randomElement([2, 3, 4]),
            'semester' => $this->faker->numberBetween(1, 8),
        ];
    }
}
