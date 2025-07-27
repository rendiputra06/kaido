<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramStudi>
 */
class ProgramStudiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_prodi' => $this->faker->unique()->lexify('PRODI-???'),
            'nama_prodi' => 'Prodi ' . $this->faker->unique()->word,
            'jenjang' => 'S1',
        ];
    }
}
