<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PeriodeKrs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KrsMahasiswa>
 */
class KrsMahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'periode_krs_id' => PeriodeKrs::factory(),
            'dosen_pa_id' => Dosen::factory(),
            'total_sks' => $this->faker->numberBetween(18, 24),
            'max_sks' => 24,
            'status' => $this->faker->randomElement(['draft', 'submitted', 'approved', 'rejected']),
            'catatan_pa' => $this->faker->optional()->paragraph(),
            'tanggal_submit' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'tanggal_approval' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the KRS is draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
            'tanggal_submit' => null,
            'tanggal_approval' => null,
        ]);
    }

    /**
     * Indicate that the KRS is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'submitted',
            'tanggal_submit' => now(),
            'tanggal_approval' => null,
        ]);
    }

    /**
     * Indicate that the KRS is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'approved',
            'tanggal_submit' => now()->subDays(7),
            'tanggal_approval' => now(),
        ]);
    }

    /**
     * Indicate that the KRS is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
            'tanggal_submit' => now()->subDays(7),
            'tanggal_approval' => now(),
            'catatan_pa' => $this->faker->paragraph(),
        ]);
    }
}
