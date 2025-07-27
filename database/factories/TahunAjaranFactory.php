<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->unique()->year;
        return [
            'kode' => $year,
            'nama' => 'Tahun Ajaran ' . $year . '/' . ($year + 1),
            'tgl_mulai' => $this->faker->date(),
            'tgl_selesai' => $this->faker->date(),
            'is_active' => false,
        ];
    }
}
