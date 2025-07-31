# Task List Implementasi Fase 2: Proses Akademik Inti

Berikut adalah daftar tugas yang telah disempurnakan untuk implementasi Fase 2 dari Sistem Informasi Akademik (SIAKAD).

## Catatan Umum & Best Practices

-   **Keamanan**: Pastikan semua fitur mengimplementasikan policy dan permission (e.g., `spatie/laravel-permission`) sesuai dengan role. Lakukan otorisasi di level `Form` dan `Table` Filament.
-   **Pola Desain**: Gunakan pattern repository dan service layer secara konsisten untuk memisahkan logika bisnis dari controller/resource.
-   **Performa**: Implementasikan eager loading (`with()`) untuk menghindari N+1 query problem. Gunakan caching untuk data yang jarang berubah (misal: data master, setting).
-   **Database**: Tambahkan indeks pada kolom yang sering digunakan dalam query (`foreign key`, kolom untuk filter/pencarian).
-   **Testing**: Pastikan semua service layer memiliki unit test dan alur kerja utama di Filament memiliki feature test.
-   **Logging**: Implementasikan logging untuk aksi-aksi krusial (misal: finalisasi nilai, perubahan status KRS) untuk kebutuhan audit.
-   **User Experience (UX)**: Beri perhatian pada notifikasi, pesan error yang jelas, dan alur yang intuitif, terutama untuk halaman mahasiswa dan dosen.

---

## Sprint 1: Manajemen Kelas & Jadwal (1 Minggu)

### Hari 1-2: Setup Model, Repository, dan Service

-   [x] **Database & Migrations**
    -   [x] Membuat migrasi untuk tabel `ruang_kuliahs` (nama, kode, kapasitas).
    -   [x] Membuat migrasi untuk tabel `kelas` (nama, kuota, sisa_kuota, `mata_kuliah_id`, `tahun_ajaran_id`, `dosen_id`).
    -   [x] Membuat migrasi untuk tabel `jadwal_kuliahs` (`kelas_id`, `ruang_kuliah_id`, hari, jam_mulai, jam_selesai).
-   [x] **Models & Relations**
    -   [x] Membuat model `RuangKuliah`.
    -   [x] Membuat model `Kelas` dengan relasi: `belongsTo(MataKuliah)`, `belongsTo(TahunAjaran)`, `belongsTo(Dosen)`, `hasMany(JadwalKuliah)`.
    -   [x] Membuat model `JadwalKuliah` dengan relasi: `belongsTo(Kelas)`, `belongsTo(RuangKuliah)`.
-   [x] **Data Seeder**
    -   [x] Membuat `RuangKuliahSeeder` untuk data awal ruang kuliah.
-   [x] **Service & Repository Layer**
    -   [x] Membuat `KelasRepositoryInterface` & `KelasRepository`.
    -   [x] Membuat `JadwalKuliahRepositoryInterface` & `JadwalKuliahRepository`.
    -   [x] Membuat `RuangKuliahRepositoryInterface` & `RuangKuliahRepository`.
    -   [ ] Membuat `KelasService` (logika bisnis pembukaan kelas).
    -   [x] Membuat `JadwalService` (logika bisnis penjadwalan dan validasi bentrok).

### Hari 3-4: Implementasi Filament Resources

-   [x] **Filament Resource: `RuangKuliah`**
    -   [x] Form untuk create/edit (nama, kode, kapasitas).
    -   [x] Table untuk menampilkan daftar ruang kuliah dengan pencarian dan filter.
-   [x] **Filament Resource: `Kelas`**
    -   [x] Form untuk pembukaan kelas (pilih Matkul, Dosen, Tahun Ajaran, isi kuota).
    -   [x] Validasi: Kuota harus > 0.
    -   [x] Table untuk menampilkan daftar kelas dengan filter (Tahun Ajaran, Program Studi, Dosen).
    -   [x] _Resource telah direfaktor untuk menggunakan Repository Pattern._
-   [x] **Filament Resource: `JadwalKuliah`**
    -   [x] Form untuk menambahkan jadwal ke kelas (pilih hari, jam, ruang).
    -   [x] Integrasikan validasi bentrok jadwal dari `JadwalService` saat menyimpan.
    -   [x] Table untuk menampilkan semua jadwal, bisa di-grup berdasarkan kelas atau hari.

### Hari 5-7: Fitur Lanjutan dan Testing

-   [x] **Algoritma Pengecekan Bentrok Jadwal (`JadwalService`)**
    -   [x] Validasi bentrok ruangan (ruangan tidak bisa dipakai di jam yang sama pada hari yang sama).
    -   [x] Validasi bentrok dosen (dosen tidak bisa mengajar di dua kelas berbeda pada waktu yang sama).
    -   [x] Validasi kapasitas ruangan (kuota kelas tidak boleh melebihi kapasitas ruangan).
-   [ ] **Laporan & Ekspor**
-   [ ] **Visualisasi Jadwal**
-   [x] **Testing**
    -   [x] Membuat unit test untuk `JadwalService` (skenario bentrok dan tidak bentrok).
    -   [x] Membuat feature test untuk alur kerja CRUD Filament Resources. _(KelasResource selesai, JadwalResource selesai)_.
-   [ ] **Refinement & Bug Fixing**

---

## Sprint 2: Manajemen Pembimbing Akademik & KRS (1.5 Minggu)

### Hari 1-2: Fondasi Manajemen Dosen PA (BARU)
-   [x] **Database & Model**
    -   [x] Membuat migrasi untuk menambahkan `dosen_pa_id` ke tabel `mahasiswas`.
    -   [x] Memperbarui model `Mahasiswa` dengan relasi `dosenPa()`.
    -   [x] Memperbarui model `Dosen` dengan relasi `mahasiswaBimbingan()`.
-   [x] **Halaman Kustom: Penetapan Dosen PA**
    -   [x] Membuat halaman kustom Filament `PenetapanDosenPA`.
    -   [x] Menampilkan daftar dosen dengan jumlah bimbingan (`withCount`).
    -   [x] Implementasi logika pemilihan dosen dan refresh data tabel.
    -   [x] Menampilkan tabel mahasiswa bimbingan dan mahasiswa tanpa PA.
    -   [x] Implementasi aksi "Jadikan Bimbingan" dan "Lepaskan" dengan notifikasi.
-   [x] **Pembaruan Seeder**
    -   [x] Memperbarui `SemesterAktifSeeder` untuk menetapkan Dosen PA ke mahasiswa secara otomatis.
    -   [x] Memastikan `KrsMahasiswa` yang dibuat menggunakan `dosen_pa_id` dari data mahasiswa.

### Hari 3-4: Setup Model dan Struktur Dasar KRS (EXISTING)
-   [x] **Database & Migrations**
    -   [x] Membuat migrasi untuk tabel `periode_krs`.
    -   [x] Membuat migrasi untuk tabel `krs_mahasiswas`.
    -   [x] Membuat migrasi untuk tabel `krs_details`.
-   [x] **Models & Relations**
    -   [x] Membuat model `PeriodeKrs`, `KrsMahasiswa`, `KrsDetail` dengan relasi yang benar.
-   [x] **Service & Repository Layer**
    -   [x] Membuat `KrsRepositoryInterface` & `KrsRepository`.
    -   [x] Membuat `PeriodeKrsRepositoryInterface` & `PeriodeKrsRepository`.
    -   [x] Membuat `KrsService`.
-   [x] **Middleware**
    -   [x] Membuat `CheckKrsPeriodeMiddleware`.

### Hari 5-6: Implementasi Antarmuka KRS Mahasiswa (EXISTING)
-   [x] **Filament Resource: `PeriodeKrs`**
-   [x] **Halaman Pengisian KRS (Custom Filament Page untuk Mahasiswa)**
-   [x] **Logika Validasi di `KrsService`**
    -   [x] Validasi batas maksimum SKS.
    -   [ ] Validasi prasyarat mata kuliah.
    -   [x] Validasi bentrok jadwal.
    -   [x] Validasi sisa kuota kelas.
    -   [x] Perhitungan otomatis total SKS.

### Hari 7-9: Fitur Persetujuan Dosen & Admin (DIPERBAIKI)
-   [x] **Keamanan & Otorisasi (BARU & DIPERBAIKI)**
    -   [x] Implementasi `getEloquentQuery` di `KrsMahasiswaResource` untuk memfilter data berdasarkan role (Admin, Dosen PA, Mahasiswa).
    -   [x] Memverifikasi dan memastikan `KrsMahasiswaPolicy` sudah benar-benar mengunci aksi (`view`, `update`, `approveOrReject`) hanya untuk Dosen PA yang bersangkutan atau Admin.
-   [ ] **Halaman Persetujuan KRS (Custom Filament Page untuk Dosen PA)**
    -   [x] Tampilkan daftar mahasiswa bimbingan yang sudah submit KRS (sekarang sudah divalidasi dengan benar).
    -   [x] Tampilkan detail KRS mahasiswa.
    -   [x] Form untuk memberikan catatan perbaikan.
    -   [x] Tombol "Setujui" dan "Tolak" KRS.
-   [ ] **Notifikasi**
-   [ ] **Halaman Admin untuk Manajemen KRS**
-   [x] **Testing**
    -   [x] Membuat unit test untuk `KrsService`.
    -   [ ] Membuat feature test untuk halaman pengisian dan persetujuan KRS.
-   [ ] **Refinement & Bug Fixing**

---

## Sprint 3: Manajemen Nilai (1 Minggu)

### Hari 1-2: Setup Model dan Struktur Dasar

-   [x] **Database & Migrations**
    -   [x] Membuat migrasi untuk tabel `komponen_nilais` (nama, default_bobot).
    -   [x] Membuat migrasi untuk tabel `borang_nilais` (`kelas_id`, `komponen_nilai_id`, bobot).
    -   [x] Membuat migrasi untuk tabel `nilai_mahasiswas` (`krs_detail_id`, `borang_nilai_id`, nilai).
    -   [x] Membuat migrasi untuk tabel `nilai_akhirs` (`krs_detail_id`, nilai_angka, nilai_huruf, bobot_nilai).
-   [x] **Models & Relations**
    -   [x] `KomponenNilai`, `BorangNilai`, `NilaiMahasiswa`, `NilaiAkhir` dengan relasi yang sesuai.
-   [x] **Data Seeder**
    -   [x] Membuat `KomponenNilaiSeeder` (Tugas, UTS, UAS, Praktikum, dll).
-   [x] **Service & Repository Layer**
    -   [x] Membuat `NilaiRepositoryInterface` & `NilaiRepository`.
    -   [x] Membuat `NilaiService` (logika perhitungan, konversi, dan finalisasi nilai).
-   [x] **Konfigurasi Skala Nilai**
    -   [x] Membuat tabel `grade_scales` untuk konversi nilai.
    -   [x] Membuat `GradeScaleSeeder` untuk data awal.
    -   [x] Membuat `GradeScaleResource` untuk manajemen skala nilai oleh Admin.

### Hari 3-4: Implementasi Antarmuka Penilaian Dosen

-   [x] **Filament Resource: `KomponenNilai`**
    -   [x] CRUD untuk komponen nilai default.
-   [x] **Halaman Pengaturan Borang Nilai (Halaman custom Dosen)**
    -   [x] Dosen memilih komponen nilai untuk kelas yang diampu.
    -   [x] Dosen mengatur bobot per komponen (total harus 100%).
    -   [x] Fitur "Kunci Borang Nilai" agar tidak bisa diubah saat pengisian nilai.
-   [ ] **Halaman Input Nilai (Custom Filament Page untuk Dosen)**
    -   [ ] Pilih kelas yang diampu.
    -   [ ] Tampilkan daftar mahasiswa (dari KRS yang disetujui) dan kolom komponen nilai.
    -   [ ] Form input nilai per mahasiswa.
    -   [ ] Validasi rentang nilai (0-100).
    -   [ ] Fitur import nilai dari Excel.
    -   [ ] Fitur "Simpan Sementara" dan "Hitung & Finalisasi".

### Hari 5-7: Fitur Perhitungan dan Laporan

-   [x] **Logika Perhitungan Nilai (`NilaiService`)**
    -   [x] Perhitungan nilai akhir berdasarkan bobot komponen.
    -   [x] Konversi nilai angka ke huruf (menggunakan `grade_scales`).
    -   [x] Perhitungan bobot nilai untuk IPK (A=4, B=3, dst).
-   [x] **Fitur Finalisasi Nilai**
    -   [x] Saat finalisasi, simpan data ke tabel `nilai_akhirs`.
    -   [x] Setelah finalisasi, nilai tidak bisa diubah oleh dosen.
    -   [x] Admin memiliki akses untuk membuka kembali (unlock) nilai jika ada revisi, dengan pencatatan log.
-   [x] **Laporan Nilai & KHS**
    -   [x] Halaman Kartu Hasil Studi (KHS) untuk mahasiswa (menampilkan nilai akhir per matkul dan IP semester).
    -   [x] Laporan nilai per kelas untuk dosen.
    -   [x] Laporan statistik nilai (distribusi A, B, C) untuk Kaprodi/Admin.
-   [ ] **Testing**
    -   [ ] Membuat unit test untuk `NilaiService` (perhitungan dan konversi). *(Note: File test ada, tapi masih placeholder)*
    -   [ ] Membuat feature test untuk alur input dan finalisasi nilai.
-   [ ] **Refinement & Bug Fixing**

---

## Sprint 4: Integrasi dan Finalisasi (1 Minggu)

### Hari 1-3: Integrasi Antar Modul & Dashboard

-   [ ] **Integrasi Modul**
    -   [ ] Pastikan kuota kelas di `Kelas` berkurang secara atomik saat KRS disetujui.
    -   [ ] Pastikan halaman input nilai hanya menampilkan mahasiswa dengan KRS status `disetujui`.
    -   [ ] Pastikan KHS mahasiswa menghitung IPK dan SKS kumulatif dengan benar.
-   [ ] **Dashboard Mahasiswa**
    -   [ ] Widget Jadwal Kuliah Hari Ini.
    -   [ ] Widget Status KRS (progress, status).
    -   [ ] Widget Pengumuman Akademik.
    -   [ ] Link cepat ke halaman KRS dan KHS.
-   [ ] **Dashboard Dosen**
    -   [ ] Widget Jadwal Mengajar Hari Ini.
    -   [ ] Widget Daftar Mahasiswa Bimbingan (yang perlu persetujuan KRS).
    -   [ ] Widget Daftar Kelas yang Diampu (yang perlu input nilai).

### Hari 4-5: UAT dan Feedback

-   [ ] **Persiapan UAT**
    -   [ ] Menyiapkan skenario testing end-to-end untuk setiap role (Admin, Dosen, Mahasiswa).
    -   [ ] Menyiapkan data dummy yang realistis.
-   [ ] **Pelaksanaan UAT**
    -   [ ] Melakukan sesi UAT dengan perwakilan pengguna.
    -   [ ] Mencatat semua feedback dan bug yang ditemukan secara sistematis.
-   [ ] **Perbaikan**
    -   [ ] Prioritaskan perbaikan bug kritikal dan blocker.
    -   [ ] Lakukan optimasi query atau alur berdasarkan feedback.

### Hari 6-7: Dokumentasi dan Persiapan Deployment

-   [ ] **Dokumentasi**
    -   [ ] Membuat panduan pengguna (user guide) untuk setiap role.
    -   [ ] Melengkapi dokumentasi teknis (struktur DB, arsitektur, API jika ada).
-   [ ] **Persiapan Deployment**
    -   [ ] Finalisasi environment variables (`.env`).
    -   [ ] Lakukan optimasi Laravel:
        -   `composer install --no-dev --optimize-autoloader`
        -   `php artisan config:cache`
        -   `php artisan route:cache`
        -   `php artisan view:cache`
    -   [ ] Konfigurasi server (web server, PHP, database).
    -   [ ] Rencanakan strategi backup dan restore.
-   [ ] **Final Security Review**
    -   [ ] Cek kembali semua policy dan permission.
    -   [ ] Pastikan tidak ada celah keamanan seperti XSS atau SQL Injection.

