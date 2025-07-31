<?php

namespace Database\Seeders;

use App\Models\KomponenNilai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KomponenNilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $komponen = [
            ['kode' => 'TUGAS', 'nama' => 'Tugas', 'keterangan' => 'Nilai dari tugas-tugas yang diberikan selama semester.'],
            ['kode' => 'PARTISIPASI', 'nama' => 'Partisipasi Kelas', 'keterangan' => 'Nilai keaktifan dan partisipasi di dalam kelas.'],
            ['kode' => 'KUIS', 'nama' => 'Kuis', 'keterangan' => 'Nilai dari kuis-kuis singkat.'],
            ['kode' => 'UTS', 'nama' => 'Ujian Tengah Semester (UTS)', 'keterangan' => 'Nilai dari ujian yang dilaksanakan di tengah semester.'],
            ['kode' => 'UAS', 'nama' => 'Ujian Akhir Semester (UAS)', 'keterangan' => 'Nilai dari ujian yang dilaksanakan di akhir semester.'],
            ['kode' => 'PRAKTIKUM', 'nama' => 'Praktikum', 'keterangan' => 'Nilai dari kegiatan praktikum di laboratorium.'],
        ];

        foreach ($komponen as $item) {
            KomponenNilai::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
