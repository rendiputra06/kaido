# Task List: Fase 1 - Fondasi & Data Master

Dokumen ini berisi rincian tugas untuk menyelesaikan **Fase 1** dari rencana pengembangan SIAKAD.

---

## 1. Data Master Akademik

Tugas-tugas ini berfokus pada pembuatan entitas inti yang berkaitan dengan struktur akademik.

### 1.1. Program Studi

-   [x] Buat model `ProgramStudi` beserta file migrasinya (`php artisan make:model ProgramStudi -m`).
-   [ ] Definisikan skema pada file migrasi `create_program_studis_table`:
    -   `string('kode_prodi')->unique()`
    -   `string('nama_prodi')`
    -   `string('jenjang')` (contoh: S1, D3)
-   [x] Buat Filament Resource untuk `ProgramStudi` (`php artisan make:filament-resource ProgramStudi`).
-   [ ] Konfigurasikan form pada `ProgramStudiResource.php` dengan `TextInput` dan `Select` untuk jenjang.
-   [ ] Konfigurasikan tabel pada `ProgramStudiResource.php` untuk menampilkan data utama.
-   [ ] Tambahkan aturan validasi yang sesuai (misal: `required`, `unique`).

### 1.2. Mata Kuliah

-   [x] Buat model `MataKuliah` beserta file migrasinya.
-   [ ] Definisikan skema pada file migrasi `create_mata_kuliahs_table`:
    -   `foreignId('program_studi_id')->constrained()`
    -   `string('kode_mk')->unique()`
    -   `string('nama_mk')`
    -   `integer('sks')`
    -   `integer('semester')` (semester ganjil/genap ditawarkannya)
-   [x] Buat Filament Resource untuk `MataKuliah`.
-   [ ] Konfigurasikan form dengan `Select` atau `RelationshipSelect` untuk `program_studi_id`.
-   [ ] Konfigurasikan tabel dan tambahkan filter berdasarkan Program Studi.

### 1.3. Tahun Ajaran

-   [x] Buat model `TahunAjaran` beserta file migrasinya.
-   [ ] Definisikan skema pada file migrasi `create_tahun_ajarans_table`:
    -   `string('kode')->unique()` (contoh: 20231 untuk 2023 Ganjil)
    -   `string('nama')` (contoh: 2023/2024 Ganjil)
    -   `date('tgl_mulai')`
    -   `date('tgl_selesai')`
    -   `boolean('is_active')->default(false)`
-   [ ] Buat Filament Resource untuk `TahunAjaran`.
-   [ ] Konfigurasikan form dengan `DatePicker` dan `Toggle` untuk `is_active`.
-   [ ] Tambahkan logika untuk memastikan hanya ada satu `TahunAjaran` yang bisa aktif dalam satu waktu.

### 1.4. Kurikulum

-   [x] Buat model `Kurikulum` beserta file migrasinya.
-   [ ] Definisikan skema pada file migrasi `create_kurikulums_table`:
    -   `foreignId('program_studi_id')->constrained()`
    -   `string('nama_kurikulum')`
    -   `integer('tahun_mulai')`
-   [ ] Buat tabel pivot `kurikulum_matakuliah` untuk relasi `ManyToMany` antara `Kurikulum` dan `MataKuliah`.
-   [x] Buat Filament Resource untuk `Kurikulum`.
-   [ ] Pada form `KurikulumResource`, gunakan `Select` dengan `multiple()` atau `CheckboxList` untuk menautkan Mata Kuliah ke Kurikulum.

---

## 2. Data Master Pengguna

Tugas-tugas ini berfokus pada pembuatan profil detail untuk `Mahasiswa` dan `Dosen` yang terhubung ke model `User`.

### 2.1. Mahasiswa

-   [x] Buat model `Mahasiswa` beserta file migrasinya.
-   [ ] Definisikan skema pada file migrasi `create_mahasiswas_table`:
    -   `foreignId('user_id')->unique()->constrained()`
    -   `foreignId('program_studi_id')->constrained()`
    -   `string('nim')->unique()`
    -   `string('nama')`
    -   `integer('angkatan')`
    -   `string('status_mahasiswa')->default('Aktif')`
    -   `string('foto')->nullable()`
-   [ ] Definisikan relasi `belongsTo` ke `User` dan `ProgramStudi` di model `Mahasiswa.php`.
-   [x] Buat Filament Resource untuk `Mahasiswa`.
-   [ ] Konfigurasikan form dengan `RelationshipSelect` untuk `user_id` dan `program_studi_id`.
-   [ ] Gunakan `FileUpload` untuk field `foto`.
-   [ ] Pertimbangkan alur: Apakah `User` dibuat manual terlebih dahulu, atau dibuat otomatis saat `Mahasiswa` baru ditambahkan?

### 2.2. Dosen

-   [x] Buat model `Dosen` beserta file migrasinya.
-   [ ] Definisikan skema pada file migrasi `create_dosens_table`:
    -   `foreignId('user_id')->unique()->constrained()`
    -   `string('nidn')->unique()`
    -   `string('nama')`
    -   `string('jabatan_fungsional')->nullable()`
    -   `string('foto')->nullable()`
-   [ ] Definisikan relasi `belongsTo` ke `User` di model `Dosen.php`.
-   [x] Buat Filament Resource untuk `Dosen`.
-   [ ] Konfigurasikan form dan tabel seperti pada resource `Mahasiswa`.
