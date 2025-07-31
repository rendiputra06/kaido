<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder untuk data master dan role terlebih dahulu
        $this->call([
            ShieldSeeder::class,
            PermissionDosenSeeder::class,
            KomponenNilaiSeeder::class,
            GradeScaleSeeder::class,
        ]);

        // Panggil seeder utama yang akan men-generate data transaksional
        $this->call(SemesterAktifSeeder::class);

        // Membuat user-user spesifik untuk testing
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);
        $admin->assignRole('super_admin');

        $dosenUser = User::factory()->create([
            'name' => 'dosen',
            'email' => 'dosen@test.com',
        ]);
        $dosenUser->assignRole('dosen');
        // Note: Mahasiswa/Dosen record untuk user ini tidak dibuat otomatis,
        // Seeder utama sudah membuat data dosen & mahasiswa yang lebih realistis.
        // User ini bisa di-link manual ke salah satu record tsb jika perlu.
    }
}
