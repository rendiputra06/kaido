<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin SIAKAD',
            'email' => 'admin@siakad.ac.id',
            'password' => Hash::make('password'),
        ]);
        
        // Users untuk dosen
        $dosenNames = [
            'Dr. Ahmad Fauzi',
            'Prof. Siti Rahayu',
            'Dr. Budi Santoso',
            'Ir. Dewi Kartika',
            'Dr. Hendra Wijaya',
            'Prof. Rina Anggraini',
            'Dr. Bambang Supriyanto',
            'Dra. Lina Susanti',
            'Dr. Agus Purnomo',
            'Prof. Maya Indah',
        ];
        
        foreach ($dosenNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => 'dosen' . ($index + 1) . '@siakad.ac.id',
                'password' => Hash::make('password'),
            ]);
        }
        
        // Users untuk mahasiswa
        $mahasiswaNames = [
            'Ahmad Rizki',
            'Siti Nurhaliza',
            'Budi Pratama',
            'Dewi Lestari',
            'Hendra Gunawan',
            'Rina Putri',
            'Bambang Hermawan',
            'Lina Fitriani',
            'Agus Setiawan',
            'Maya Sari',
            'Dian Permata',
            'Eko Prasetyo',
            'Fitri Handayani',
            'Galih Ramadhan',
            'Hani Safitri',
        ];
        
        foreach ($mahasiswaNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => 'mhs' . ($index + 1) . '@student.siakad.ac.id',
                'password' => Hash::make('password'),
            ]);
        }
    }
}