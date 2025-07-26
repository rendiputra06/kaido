# Task List: Seeder untuk Fase 1 - Data Master

Dokumen ini berisi rincian tugas untuk membuat seeder dari model-model yang telah dibuat pada Fase 1 pengembangan SIAKAD.

---

## 1. Persiapan Factory

Factory digunakan untuk membuat data dummy dengan mudah. Berikut adalah langkah-langkah untuk membuat factory untuk setiap model:

### 1.1. Factory untuk Program Studi

- [ ] Buat factory untuk model `ProgramStudi` dengan perintah:
  ```bash
  php artisan make:factory ProgramStudiFactory --model=ProgramStudi
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `kode_prodi` (gunakan sequence atau faker untuk memastikan unik)
  - `nama_prodi` (gunakan faker untuk nama program studi)
  - `jenjang` (pilih acak dari array ['S1', 'S2', 'S3', 'D3', 'D4'])

### 1.2. Factory untuk Mata Kuliah

- [ ] Buat factory untuk model `MataKuliah` dengan perintah:
  ```bash
  php artisan make:factory MataKuliahFactory --model=MataKuliah
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `program_studi_id` (gunakan `ProgramStudi::factory()` atau `ProgramStudi::inRandomOrder()->first()->id`)
  - `kode_mk` (gunakan sequence atau faker untuk memastikan unik)
  - `nama_mk` (gunakan faker untuk nama mata kuliah)
  - `sks` (nilai acak antara 1-4)
  - `semester` (nilai acak antara 1-8)

### 1.3. Factory untuk Tahun Ajaran

- [ ] Buat factory untuk model `TahunAjaran` dengan perintah:
  ```bash
  php artisan make:factory TahunAjaranFactory --model=TahunAjaran
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `kode` (gunakan sequence atau faker untuk memastikan unik)
  - `nama` (contoh: "2023/2024 Ganjil")
  - `tgl_mulai` (tanggal acak untuk awal semester)
  - `tgl_selesai` (tanggal acak untuk akhir semester, pastikan setelah `tgl_mulai`)
  - `is_active` (default: false)

### 1.4. Factory untuk Kurikulum

- [ ] Buat factory untuk model `Kurikulum` dengan perintah:
  ```bash
  php artisan make:factory KurikulumFactory --model=Kurikulum
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `program_studi_id` (gunakan `ProgramStudi::factory()` atau `ProgramStudi::inRandomOrder()->first()->id`)
  - `nama_kurikulum` (contoh: "Kurikulum 2020")
  - `tahun_mulai` (nilai acak antara 2018-2023)

### 1.5. Factory untuk User (untuk Mahasiswa dan Dosen)

- [ ] Pastikan factory untuk model `User` sudah ada, jika belum, buat dengan perintah:
  ```bash
  php artisan make:factory UserFactory --model=User
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `name` (gunakan faker untuk nama lengkap)
  - `email` (gunakan faker untuk email)
  - `password` (gunakan `Hash::make('password')` atau nilai default lainnya)

### 1.6. Factory untuk Mahasiswa

- [ ] Buat factory untuk model `Mahasiswa` dengan perintah:
  ```bash
  php artisan make:factory MahasiswaFactory --model=Mahasiswa
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `user_id` (gunakan `User::factory()`)
  - `program_studi_id` (gunakan `ProgramStudi::factory()` atau `ProgramStudi::inRandomOrder()->first()->id`)
  - `nim` (gunakan sequence atau faker untuk memastikan unik)
  - `nama` (gunakan faker untuk nama lengkap)
  - `angkatan` (nilai acak antara 2018-2023)
  - `status_mahasiswa` (pilih acak dari array ['Aktif', 'Cuti', 'Lulus', 'Drop Out'])
  - `foto` (nullable, bisa dikosongkan atau gunakan faker untuk URL gambar)

### 1.7. Factory untuk Dosen

- [ ] Buat factory untuk model `Dosen` dengan perintah:
  ```bash
  php artisan make:factory DosenFactory --model=Dosen
  ```
- [ ] Definisikan atribut-atribut berikut di dalam factory:
  - `user_id` (gunakan `User::factory()`)
  - `nidn` (gunakan sequence atau faker untuk memastikan unik)
  - `nama` (gunakan faker untuk nama lengkap)
  - `jabatan_fungsional` (pilih acak dari array ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'])
  - `foto` (nullable, bisa dikosongkan atau gunakan faker untuk URL gambar)

---

## 2. Pembuatan Seeder

Seeder digunakan untuk mengisi database dengan data awal. Berikut adalah langkah-langkah untuk membuat seeder untuk setiap model:

### 2.1. Seeder untuk Program Studi

- [ ] Buat seeder untuk model `ProgramStudi` dengan perintah:
  ```bash
  php artisan make:seeder ProgramStudiSeeder
  ```
- [ ] Isi seeder dengan kode untuk membuat beberapa program studi (minimal 5).

### 2.2. Seeder untuk Mata Kuliah

- [ ] Buat seeder untuk model `MataKuliah` dengan perintah:
  ```bash
  php artisan make:seeder MataKuliahSeeder
  ```
- [ ] Isi seeder dengan kode untuk membuat beberapa mata kuliah (minimal 20) yang terkait dengan program studi yang ada.

### 2.3. Seeder untuk Tahun Ajaran

- [ ] Buat seeder untuk model `TahunAjaran` dengan perintah:
  ```bash
  php artisan make:seeder TahunAjaranSeeder
  ```
- [ ] Isi seeder dengan kode untuk membuat beberapa tahun ajaran (minimal 4) dengan satu yang aktif.

### 2.4. Seeder untuk Kurikulum

- [ ] Buat seeder untuk model `Kurikulum` dengan perintah:
  ```bash
  php artisan make:seeder KurikulumSeeder
  ```
- [ ] Isi seeder dengan kode untuk membuat beberapa kurikulum (minimal 2) yang terkait dengan program studi yang ada.
- [ ] Tambahkan kode untuk mengaitkan mata kuliah dengan kurikulum (relasi many-to-many).

### 2.5. Seeder untuk User, Mahasiswa, dan Dosen

- [ ] Buat seeder untuk model `User`, `Mahasiswa`, dan `Dosen` dengan perintah:
  ```bash
  php artisan make:seeder UserSeeder
  php artisan make:seeder MahasiswaSeeder
  php artisan make:seeder DosenSeeder
  ```
- [ ] Isi seeder dengan kode untuk membuat beberapa user, mahasiswa, dan dosen (minimal 10 untuk masing-masing).

### 2.6. Database Seeder Utama

- [ ] Update file `DatabaseSeeder.php` untuk memanggil semua seeder yang telah dibuat dengan urutan yang benar:
  ```php
  public function run()
  {
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
  ```

---

## 3. Menjalankan Seeder

- [ ] Jalankan migrasi dan seeder dengan perintah:
  ```bash
  php artisan migrate:fresh --seed
  ```
  atau jika hanya ingin menjalankan seeder tanpa migrasi ulang:
  ```bash
  php artisan db:seed
  ```

- [ ] Verifikasi data yang telah dibuat melalui Filament Admin Panel.

---

## 4. Dokumentasi

- [ ] Dokumentasikan struktur data yang telah dibuat.
- [ ] Catat jumlah data yang telah dibuat untuk setiap model.
- [ ] Tambahkan catatan khusus jika ada (misalnya, user admin dengan kredensial tertentu).