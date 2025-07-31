<?php

namespace Database\Factories;

use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->afterCreating(function (User $user) {
                $user->assignRole('mahasiswa');
            }),
            'program_studi_id' => ProgramStudi::factory(),
            'nim' => $this->faker->unique()->numerify('########'),
            'nama' => fake('id_ID')->firstName() . ' ' . fake('id_ID')->lastName(),
            'angkatan' => $this->faker->numberBetween(2020, 2024),
            'status_mahasiswa' => $this->faker->randomElement(['Aktif', 'Cuti', 'Lulus', 'Dropout']),
            'foto' => null,
        ];
    }

    /**
     * Indicate that the mahasiswa is active.
     */
    public function aktif(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_mahasiswa' => 'Aktif',
        ]);
    }
}
