<?php

namespace Database\Seeders;

use App\Models\GradeScale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeScaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skala = [
            ['nilai_huruf' => 'A', 'nilai_indeks' => 4.00, 'rentang_bawah' => 85.00, 'rentang_atas' => 100.00],
            ['nilai_huruf' => 'A-', 'nilai_indeks' => 3.75, 'rentang_bawah' => 80.00, 'rentang_atas' => 84.99],
            ['nilai_huruf' => 'B+', 'nilai_indeks' => 3.25, 'rentang_bawah' => 75.00, 'rentang_atas' => 79.99],
            ['nilai_huruf' => 'B', 'nilai_indeks' => 3.00, 'rentang_bawah' => 70.00, 'rentang_atas' => 74.99],
            ['nilai_huruf' => 'B-', 'nilai_indeks' => 2.75, 'rentang_bawah' => 65.00, 'rentang_atas' => 69.99],
            ['nilai_huruf' => 'C+', 'nilai_indeks' => 2.25, 'rentang_bawah' => 60.00, 'rentang_atas' => 64.99],
            ['nilai_huruf' => 'C', 'nilai_indeks' => 2.00, 'rentang_bawah' => 55.00, 'rentang_atas' => 59.99],
            ['nilai_huruf' => 'D', 'nilai_indeks' => 1.00, 'rentang_bawah' => 40.00, 'rentang_atas' => 54.99],
            ['nilai_huruf' => 'E', 'nilai_indeks' => 0.00, 'rentang_bawah' => 0.00, 'rentang_atas' => 39.99],
        ];

        foreach ($skala as $item) {
            // Membuat skala nilai umum (tanpa program_studi_id)
            GradeScale::create($item);
        }
    }
}