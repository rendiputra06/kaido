<?php

namespace Database\Factories;

use App\Models\ProgramStudi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kurikulum>
 */
class KurikulumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tahun = $this->faker->unique()->year();
        return [
            'nama_kurikulum' => 'Kurikulum ' . $tahun,
            'tahun_mulai' => $tahun,
            'program_studi_id' => ProgramStudi::factory(),
        ];
    }
}