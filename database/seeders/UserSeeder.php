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
        $admin = User::firstOrCreate(
            ['email' => 'admin@siakad.ac.id'],
            [
                'name' => 'Admin SIAKAD',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super_admin');

        // Users untuk dosen
        $dosenNames = [
            'Dr. Ahmad Fauzi',
            'Prof. Siti Rahayu',
            'Dr. Budi Santoso',
            'Ir. Dewi Kartika',
        ];

        foreach ($dosenNames as $index => $name) {
            $dosen = User::firstOrCreate(
                ['email' => 'dosen' . ($index + 1) . '@siakad.ac.id'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $dosen->assignRole('dosen');
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
            $mahasiswa = User::firstOrCreate(
                ['email' => 'mhs' . ($index + 1) . '@student.siakad.ac.id'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $mahasiswa->assignRole('mahasiswa');
        }
    }
}
