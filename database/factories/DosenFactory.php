<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dosen>
 */
class DosenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nidn' => $this->faker->unique()->numerify('########'),
            'nama' => $this->faker->name(),
            'jabatan_fungsional' => $this->faker->randomElement(['Lektor', 'Lektor Kepala', 'Guru Besar', 'Asisten Ahli']),
            'foto' => null,
        ];
    }
}
