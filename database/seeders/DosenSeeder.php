<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosenUsers = User::where('email', 'like', 'dosen%@siakad.ac.id')->get();
        
        $jabatan = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'];
        
        foreach ($dosenUsers as $index => $user) {
            $nidn = '0' . str_pad(($index + 1), 9, '0', STR_PAD_LEFT);
            
            Dosen::create([
                'user_id' => $user->id,
                'nidn' => $nidn,
                'nama' => $user->name,
                'jabatan_fungsional' => $jabatan[$index % count($jabatan)],
                'foto' => null,
            ]);
        }
    }
}