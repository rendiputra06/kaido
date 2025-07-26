# Contoh Implementasi Factory dan Seeder

Dokumen ini berisi contoh implementasi factory dan seeder untuk model-model yang telah dibuat pada Fase 1 pengembangan SIAKAD. Contoh ini dapat digunakan sebagai referensi saat mengimplementasikan task list seeder.

## 1. Contoh Factory

### 1.1. Contoh Factory untuk Program Studi

```php
<?php

namespace Database\Factories;

use App\Models\ProgramStudi;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramStudiFactory extends Factory
{
    protected $model = ProgramStudi::class;

    public function definition()
    {
        $jenjang = $this->faker->randomElement(['S1', 'S2', 'S3', 'D3', 'D4']);
        $kodeProdi = $this->faker->unique()->numerify('##');
        
        return [
            'kode_prodi' => $kodeProdi,
            'nama_prodi' => $this->faker->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Teknik Elektro',
                'Teknik Mesin',
                'Manajemen Bisnis',
                'Akuntansi',
                'Ilmu Komunikasi',
                'Psikologi',
                'Kedokteran',
                'Farmasi'
            ]) . ' ' . $jenjang,
            'jenjang' => $jenjang,
        ];
    }
}
```

### 1.2. Contoh Factory untuk Mata Kuliah

```php
<?php

namespace Database\Factories;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Database\Eloquent\Factories\Factory;

class MataKuliahFactory extends Factory
{
    protected $model = MataKuliah::class;

    public function definition()
    {
        static $kode = 1;
        
        return [
            'program_studi_id' => ProgramStudi::factory(),
            'kode_mk' => 'MK' . str_pad($kode++, 3, '0', STR_PAD_LEFT),
            'nama_mk' => $this->faker->randomElement([
                'Pemrograman Dasar',
                'Algoritma dan Struktur Data',
                'Basis Data',
                'Jaringan Komputer',
                'Sistem Operasi',
                'Kecerdasan Buatan',
                'Rekayasa Perangkat Lunak',
                'Interaksi Manusia dan Komputer',
                'Keamanan Informasi',
                'Pemrograman Web',
                'Pemrograman Mobile',
                'Analisis dan Desain Sistem',
                'Manajemen Proyek Perangkat Lunak',
                'Etika Profesi',
                'Matematika Diskrit',
                'Kalkulus',
                'Statistika',
                'Fisika Dasar',
                'Bahasa Inggris',
                'Kewirausahaan'
            ]),
            'sks' => $this->faker->numberBetween(1, 4),
            'semester' => $this->faker->numberBetween(1, 8),
        ];
    }
}
```

## 2. Contoh Seeder

### 2.1. Contoh Seeder untuk Program Studi

```php
<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run()
    {
        // Membuat data program studi secara manual
        $programStudis = [
            [
                'kode_prodi' => 'TI',
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'S1',
            ],
            [
                'kode_prodi' => 'SI',
                'nama_prodi' => 'Sistem Informasi',
                'jenjang' => 'S1',
            ],
            [
                'kode_prodi' => 'TE',
                'nama_prodi' => 'Teknik Elektro',
                'jenjang' => 'S1',
            ],
            [
                'kode_prodi' => 'TM',
                'nama_prodi' => 'Teknik Mesin',
                'jenjang' => 'S1',
            ],
            [
                'kode_prodi' => 'MB',
                'nama_prodi' => 'Manajemen Bisnis',
                'jenjang' => 'S1',
            ],
        ];

        foreach ($programStudis as $programStudi) {
            ProgramStudi::create($programStudi);
        }

        // Atau menggunakan factory untuk membuat data tambahan
        // ProgramStudi::factory()->count(5)->create();
    }
}
```

### 2.2. Contoh Seeder untuk Kurikulum dengan Relasi Many-to-Many

```php
<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    public function run()
    {
        // Mendapatkan semua program studi
        $programStudis = ProgramStudi::all();

        foreach ($programStudis as $programStudi) {
            // Membuat kurikulum untuk setiap program studi
            $kurikulum = Kurikulum::create([
                'program_studi_id' => $programStudi->id,
                'nama_kurikulum' => 'Kurikulum ' . $this->faker->year(2018, 2023),
                'tahun_mulai' => $this->faker->year(2018, 2023),
            ]);

            // Mendapatkan mata kuliah yang terkait dengan program studi
            $mataKuliahs = MataKuliah::where('program_studi_id', $programStudi->id)
                ->inRandomOrder()
                ->take(10)
                ->get();

            // Mengaitkan mata kuliah dengan kurikulum
            $kurikulum->mataKuliahs()->attach($mataKuliahs->pluck('id')->toArray());
        }

        // Atau menggunakan factory
        // Kurikulum::factory()->count(5)->create()->each(function ($kurikulum) {
        //     $mataKuliahs = MataKuliah::where('program_studi_id', $kurikulum->program_studi_id)
        //         ->inRandomOrder()
        //         ->take(10)
        //         ->get();
        //     $kurikulum->mataKuliahs()->attach($mataKuliahs->pluck('id')->toArray());
        // });
    }
}
```

### 2.3. Contoh Database Seeder Utama

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Urutan pemanggilan seeder penting untuk menjaga integritas referensial
        $this->call([
            ProgramStudiSeeder::class,
            MataKuliahSeeder::class,
            TahunAjaranSeeder::class,
            KurikulumSeeder::class,
            UserSeeder::class,
            MahasiswaSeeder::class,
            DosenSeeder::class,
        ]);
    }
}
```

## 3. Tips Implementasi

1. **Gunakan Faker dengan Bijak**: Faker dapat menghasilkan data yang realistis, tetapi pastikan untuk menyesuaikan dengan kebutuhan domain aplikasi Anda.

2. **Perhatikan Urutan Seeder**: Urutan pemanggilan seeder sangat penting untuk menjaga integritas referensial. Pastikan model yang direferensikan sudah dibuat sebelum model yang mereferensikan.

3. **Gunakan Factory untuk Data Massal**: Factory sangat berguna untuk membuat data dalam jumlah besar dengan cepat. Gunakan `factory()->count(n)->create()` untuk membuat banyak data sekaligus.

4. **Kombinasikan Factory dan Create Manual**: Untuk data yang memerlukan kontrol lebih, gunakan `create()` manual. Untuk data massal, gunakan factory.

5. **Gunakan State dan Sequence**: Factory Laravel mendukung state dan sequence untuk membuat variasi data. Gunakan fitur ini untuk membuat data yang lebih bervariasi.

6. **Tes Seeder Secara Bertahap**: Jalankan seeder satu per satu untuk memastikan setiap seeder berfungsi dengan baik sebelum menjalankan semua seeder sekaligus.

7. **Dokumentasikan Data Seed**: Catat data penting yang dibuat oleh seeder, seperti kredensial admin, untuk memudahkan pengujian dan pengembangan.