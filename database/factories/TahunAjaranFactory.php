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
        $year = $this->faker->numberBetween(2020, 2030);
        $semester = $this->faker->randomElement(['Ganjil', 'Genap']);
        $semesterNum = $semester === 'Ganjil' ? '1' : '2';
        $kode = $year . $semesterNum;
        
        // Generate dates for the semester
        $tglMulai = $this->faker->dateTimeBetween(
            $year . '-03-01', // Start of Ganjil semester
            $year . '-06-30'  // End of Ganjil semester
        );
        
        $tglSelesai = $this->faker->dateTimeBetween(
            $semester === 'Ganjil' ? $year . '-07-01' : $year . '-12-01',
            $semester === 'Ganjil' ? $year . '-08-31' : ($year + 1) . '-01-31'
        );
        
        // Generate tahun_akademik based on kode
        $tahunAkademik = $year . '/' . ($semester === 'Ganjil' ? $year + 1 : $year);
        
        return [
            'kode' => $kode,
            'nama' => 'Tahun Ajaran ' . $year . '/' . ($year + 1) . ' Semester ' . $semester,
            'semester' => $semester,
            'tahun_akademik' => $tahunAkademik,
            'tgl_mulai' => $tglMulai->format('Y-m-d'),
            'tgl_selesai' => $tglSelesai->format('Y-m-d'),
            'is_active' => false,
        ];
    }
}
