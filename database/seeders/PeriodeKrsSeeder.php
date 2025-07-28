<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodeKrsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil tahun ajaran aktif
        $tahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaran) {
            $this->command->warn('Tidak ada tahun ajaran aktif. Membuat tahun ajaran baru...');
            $tahunAjaran = \App\Models\TahunAjaran::factory()->create(['is_active' => true]);
        }

        // Buat periode KRS untuk semester ganjil
        \App\Models\PeriodeKrs::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama_periode' => 'Periode KRS Semester Ganjil ' . $tahunAjaran->nama,
            'tgl_mulai' => now()->addDays(7),
            'tgl_selesai' => now()->addDays(14),
            'status' => 'tidak_aktif',
            'keterangan' => 'Periode pengisian KRS untuk semester ganjil',
        ]);

        // Buat periode KRS untuk semester genap
        \App\Models\PeriodeKrs::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama_periode' => 'Periode KRS Semester Genap ' . $tahunAjaran->nama,
            'tgl_mulai' => now()->addMonths(6)->addDays(7),
            'tgl_selesai' => now()->addMonths(6)->addDays(14),
            'status' => 'tidak_aktif',
            'keterangan' => 'Periode pengisian KRS untuk semester genap',
        ]);

        $this->command->info('Periode KRS berhasil dibuat.');
    }
}
