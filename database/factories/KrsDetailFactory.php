<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\KrsMahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KrsDetail>
 */
class KrsDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'krs_mahasiswa_id' => KrsMahasiswa::factory(),
            'kelas_id' => Kelas::factory(),
            'sks' => $this->faker->numberBetween(2, 4),
            'status' => $this->faker->randomElement(['active', 'canceled']),
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the detail is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the detail is canceled.
     */
    public function canceled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'canceled',
            'keterangan' => $this->faker->sentence(),
        ]);
    }
}
