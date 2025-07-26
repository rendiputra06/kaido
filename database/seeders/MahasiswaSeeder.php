<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programStudi = ProgramStudi::where('kode_prodi', 'EI')->first();
        
        if (!$programStudi) {
            return;
        }
        
        $mahasiswaUsers = User::where('email', 'like', 'mhs%@student.siakad.ac.id')->get();
        
        $angkatan = [2020, 2021, 2022, 2023, 2024];
        $status = ['Aktif', 'Aktif', 'Aktif', 'Aktif', 'Cuti'];
        
        foreach ($mahasiswaUsers as $index => $user) {
            $tahunAngkatan = $angkatan[$index % count($angkatan)];
            $nim = $tahunAngkatan . '1001' . str_pad(($index + 1), 3, '0', STR_PAD_LEFT);
            
            Mahasiswa::create([
                'user_id' => $user->id,
                'program_studi_id' => $programStudi->id,
                'nim' => $nim,
                'nama' => $user->name,
                'angkatan' => $tahunAngkatan,
                'status_mahasiswa' => $status[$index % count($status)],
                'foto' => null,
            ]);
        }
    }
}