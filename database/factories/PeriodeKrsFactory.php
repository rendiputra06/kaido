<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriodeKrs>
 */
class PeriodeKrsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tglMulai = $this->faker->dateTimeBetween('now', '+1 month');
        $tglSelesai = $this->faker->dateTimeBetween($tglMulai, '+2 months');

        return [
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'nama_periode' => $this->faker->sentence(3),
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
            'status' => $this->faker->randomElement(['aktif', 'tidak_aktif', 'selesai']),
            'keterangan' => $this->faker->paragraph(),
        ];
    }

    /**
     * Indicate that the periode is active.
     */
    public function aktif(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'aktif',
            'tgl_mulai' => now()->subDays(7),
            'tgl_selesai' => now()->addDays(7),
        ]);
    }

    /**
     * Indicate that the periode is inactive.
     */
    public function tidakAktif(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'tidak_aktif',
        ]);
    }
}
