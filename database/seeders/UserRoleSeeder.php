<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hubungkan User dengan Mahasiswa
        $mahasiswaUsers = User::role('mahasiswa')->get();
        $mahasiswas = Mahasiswa::all();

        foreach ($mahasiswaUsers as $index => $user) {
            if (isset($mahasiswas[$index])) {
                $mahasiswas[$index]->update(['user_id' => $user->id]);
            }
        }

        // Hubungkan User dengan Dosen
        $dosenUsers = User::role('dosen')->get();
        $dosens = Dosen::all();

        foreach ($dosenUsers as $index => $user) {
            if (isset($dosens[$index])) {
                $dosens[$index]->update(['user_id' => $user->id]);
            }
        }

        $this->command->info('User roles connected successfully.');
    }
}
