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
        // Membuat user admin utama
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);
        // $this->call([
        //     BookSeeder::class,
        //     PostSeeder::class,
        //     ContactSeeder::class,
        //     ProgramStudiSeeder::class,
        //     MataKuliahSeeder::class,
        //     TahunAjaranSeeder::class,
        //     KurikulumSeeder::class,
        //     UserSeeder::class,
        //     MahasiswaSeeder::class,
        //     DosenSeeder::class,
        //     RuangKuliahSeeder::class,
        // );
        // Memanggil seeder utama untuk satu semester berjalan
        $this->call(SemesterAktifSeeder::class);
    }
}
