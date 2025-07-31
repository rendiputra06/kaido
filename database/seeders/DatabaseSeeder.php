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
        // Memanggil seeder utama untuk satu semester berjalan
        $this->call([
            ShieldSeeder::class,
            PermissionDosenSeeder::class,
            // UserRoleSeeder::class, // User role bisa di-handle oleh seeder utama jika perlu
            SemesterAktifSeeder::class,
            KomponenNilaiSeeder::class, // Menambahkan seeder untuk komponen nilai
            GradeScaleSeeder::class, // Menambahkan seeder untuk skala nilai
            // Seeder di bawah ini tidak perlu dipanggil lagi karena sudah dicakup oleh SemesterAktifSeeder
            // PeriodeKrsSeeder::class, 
            // MataKuliahPrerequisiteSeeder::class,
        ]);

        // Membuat user admin utama
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);
        $admin->assignRole('super_admin');

        // Membuat user mahasiswa untuk testing
        $mahasiswaUser = User::factory()->create([
            'name' => 'mahasiswa',
            'email' => 'mahasiswa@test.com',
        ]);
        $mahasiswaUser->assignRole('mahasiswa');

        // Membuat user dosen untuk testing
        $dosenUser = User::factory()->create([
            'name' => 'dosen',
            'email' => 'dosen@test.com',
        ]);
        $dosenUser->assignRole('dosen');
    }
}
