<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RuangKuliah>
 */
class RuangKuliahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Ruang ' . $this->faker->unique()->word,
            'kode' => $this->faker->unique()->lexify('R-???'),
            'kapasitas' => $this->faker->numberBetween(20, 50),
        ];
    }
}
