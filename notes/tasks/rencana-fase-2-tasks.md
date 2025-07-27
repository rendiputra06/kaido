# Task List Implementasi Fase 2: Proses Akademik Inti

Berikut adalah daftar tugas yang telah disempurnakan untuk implementasi Fase 2 dari Sistem Informasi Akademik (SIAKAD).

## Catatan Umum & Best Practices
- **Keamanan**: Pastikan semua fitur mengimplementasikan policy dan permission (e.g., `spatie/laravel-permission`) sesuai dengan role. Lakukan otorisasi di level `Form` dan `Table` Filament.
- **Pola Desain**: Gunakan pattern repository dan service layer secara konsisten untuk memisahkan logika bisnis dari controller/resource.
- **Performa**: Implementasikan eager loading (`with()`) untuk menghindari N+1 query problem. Gunakan caching untuk data yang jarang berubah (misal: data master, setting).
- **Database**: Tambahkan indeks pada kolom yang sering digunakan dalam query (`foreign key`, kolom untuk filter/pencarian).
- **Testing**: Pastikan semua service layer memiliki unit test dan alur kerja utama di Filament memiliki feature test.
- **Logging**: Implementasikan logging untuk aksi-aksi krusial (misal: finalisasi nilai, perubahan status KRS) untuk kebutuhan audit.
- **User Experience (UX)**: Beri perhatian pada notifikasi, pesan error yang jelas, dan alur yang intuitif, terutama untuk halaman mahasiswa dan dosen.

---

## Sprint 1: Manajemen Kelas & Jadwal (1 Minggu)

### Hari 1-2: Setup Model, Repository, dan Service

- [x] **Database & Migrations**
  - [x] Membuat migrasi untuk tabel `ruang_kuliahs` (nama, kode, kapasitas).
  - [x] Membuat migrasi untuk tabel `kelas` (nama, kuota, sisa_kuota, `mata_kuliah_id`, `tahun_ajaran_id`, `dosen_id`).
  - [x] Membuat migrasi untuk tabel `jadwal_kuliahs` (`kelas_id`, `ruang_kuliah_id`, hari, jam_mulai, jam_selesai).
- [x] **Models & Relations**
  - [x] Membuat model `RuangKuliah`.
  - [x] Membuat model `Kelas` dengan relasi: `belongsTo(MataKuliah)`, `belongsTo(TahunAjaran)`, `belongsTo(Dosen)`, `hasMany(JadwalKuliah)`.
  - [x] Membuat model `JadwalKuliah` dengan relasi: `belongsTo(Kelas)`, `belongsTo(RuangKuliah)`.
- [x] **Data Seeder**
  - [x] Membuat `RuangKuliahSeeder` untuk data awal ruang kuliah.
- [x] **Service & Repository Layer**
  - [x] Membuat `KelasRepositoryInterface` & `KelasRepository`.
  - [x] Membuat `JadwalKuliahRepositoryInterface` & `JadwalKuliahRepository`.
  - [ ] Membuat `RuangKuliahRepositoryInterface` & `RuangKuliahRepository`.
  - [ ] Membuat `KelasService` (logika bisnis pembukaan kelas).
  - [ ] Membuat `JadwalService` (logika bisnis penjadwalan dan validasi bentrok).

### Hari 3-4: Implementasi Filament Resources

- [x] **Filament Resource: `RuangKuliah`**
  - [x] Form untuk create/edit (nama, kode, kapasitas).
  - [x] Table untuk menampilkan daftar ruang kuliah dengan pencarian dan filter.
- [x] **Filament Resource: `Kelas`**
  - [x] Form untuk pembukaan kelas (pilih Matkul, Dosen, Tahun Ajaran, isi kuota).
  - [x] Validasi: Kuota harus > 0.
  - [x] Table untuk menampilkan daftar kelas dengan filter (Tahun Ajaran, Program Studi, Dosen).
  - [x] *Resource telah direfaktor untuk menggunakan Repository Pattern.*
- [x] **Filament Resource: `JadwalKuliah`**
  - [x] Form untuk menambahkan jadwal ke kelas (pilih hari, jam, ruang).
  - [x] Integrasikan validasi bentrok jadwal dari `JadwalService` saat menyimpan.
  - [x] Table untuk menampilkan semua jadwal, bisa di-grup berdasarkan kelas atau hari.

### Hari 5-7: Fitur Lanjutan dan Testing

- [x] **Algoritma Pengecekan Bentrok Jadwal (`JadwalService`)**
  - [x] Validasi bentrok ruangan (ruangan tidak bisa dipakai di jam yang sama pada hari yang sama).
  - [x] Validasi bentrok dosen (dosen tidak bisa mengajar di dua kelas berbeda pada waktu yang sama).
  - [x] Validasi kapasitas ruangan (kuota kelas tidak boleh melebihi kapasitas ruangan).
- [ ] **Laporan & Ekspor**
- [ ] **Visualisasi Jadwal**
- [/] **Testing**
  - [ ] Membuat unit test untuk `JadwalService` (skenario bentrok dan tidak bentrok).
  - [/] Membuat feature test untuk alur kerja CRUD Filament Resources. *(KelasResource selesai, yang lain placeholder)*.
- [ ] **Refinement & Bug Fixing**

---

## Sprint 2: Kartu Rencana Studi (KRS) (1 Minggu)

### Hari 1-2: Setup Model dan Struktur Dasar

- [x] **Database & Migrations**
  - [x] Membuat migrasi untuk tabel `periode_krs` (`tahun_ajaran_id`, tgl_mulai, tgl_selesai, status).
  - [x] Membuat migrasi untuk tabel `krs_mahasiswas` (`mahasiswa_id`, `periode_krs_id`, `dosen_pa_id`, status, total_sks, catatan_pa).
  - [x] Membuat migrasi untuk tabel `krs_details` (`krs_mahasiswa_id`, `kelas_id`).
- [x] **Models & Relations**
  - [x] Membuat model `PeriodeKrs` dengan relasi ke `TahunAjaran`.
  - [x] Membuat model `KrsMahasiswa` dengan relasi ke `Mahasiswa`, `PeriodeKrs`, `Dosen`, dan `hasMany(KrsDetail)`.
  - [x] Membuat model `KrsDetail` dengan relasi ke `KrsMahasiswa` dan `Kelas`.
- [x] **Data Seeder**
  - [x] Membuat `PeriodeKrsSeeder` untuk data awal.
- [ ] **Service & Repository Layer**
  - [ ] Membuat `KrsRepositoryInterface` & `KrsRepository`.
  - [ ] Membuat `PeriodeKrsRepositoryInterface` & `PeriodeKrsRepository`.
  - [ ] Membuat `KrsService` (logika validasi, submit, dan approval KRS).
- [ ] **Middleware**
  - [ ] Membuat `CheckKrsPeriodeMiddleware` untuk memastikan pengisian KRS hanya pada periode aktif.
  - [ ] Daftarkan middleware pada route yang relevan.

### Hari 3-4: Implementasi Antarmuka KRS Mahasiswa

- [x] **Filament Resource: `PeriodeKrs`**
  - [ ] Form untuk create/edit periode KRS.
  - [ ] Aksi untuk aktivasi/deaktivasi periode.
- [ ] **Halaman Pengisian KRS (Custom Filament Page untuk Mahasiswa)**
  - [ ] Terapkan `CheckKrsPeriodeMiddleware`.
  - [ ] Tampilkan daftar kelas yang tersedia untuk program studi mahasiswa.
  - [ ] Fitur pencarian dan filter kelas (berdasarkan nama matkul, dosen).
  - [ ] Aksi "Ambil Kelas" dan "Batalkan Kelas" pada setiap baris.
  - [ ] Tampilkan ringkasan KRS (daftar kelas yang diambil, total SKS).
  - [ ] Tombol "Submit KRS" untuk dikirim ke Dosen PA.
- [ ] **Logika Validasi di `KrsService`**
  - [ ] Validasi batas maksimum SKS (berdasarkan IPK sebelumnya jika ada).
  - [ ] Validasi prasyarat mata kuliah.
  - [ ] Validasi bentrok jadwal dengan kelas lain yang sudah diambil.
  - [ ] Validasi sisa kuota kelas (cek secara real-time).
  - [ ] Perhitungan otomatis total SKS.

### Hari 5-7: Fitur Persetujuan Dosen & Admin

- [ ] **Halaman Persetujuan KRS (Custom Filament Page untuk Dosen PA)**
  - [ ] Tampilkan daftar mahasiswa bimbingan yang sudah submit KRS.
  - [ ] Tampilkan detail KRS mahasiswa (matkul, sks, jadwal).
  - [ ] Form untuk memberikan catatan perbaikan.
  - [ ] Tombol "Setujui" dan "Tolak" KRS.
    - Jika disetujui: kurangi `sisa_kuota` di tabel `kelas`, status KRS jadi `disetujui`.
    - Jika ditolak: status KRS jadi `revisi`, mahasiswa bisa edit lagi.
- [ ] **Notifikasi**
  - [ ] Notifikasi ke Dosen PA saat mahasiswa submit KRS.
  - [ ] Notifikasi ke Mahasiswa saat KRS disetujui atau ditolak.
- [ ] **Halaman Admin untuk Manajemen KRS**
  - [ ] Laporan status pengisian KRS (sudah/belum mengisi, status persetujuan).
  - [ ] Fitur `force unlock` atau reset status KRS untuk kasus khusus.
- [ ] **Testing**
  - [ ] Membuat unit test untuk `KrsService` (validasi, approval flow).
  - [ ] Membuat feature test untuk halaman pengisian dan persetujuan KRS.
- [ ] **Refinement & Bug Fixing**

---

## Sprint 3: Manajemen Nilai (1 Minggu)

### Hari 1-2: Setup Model dan Struktur Dasar

- [ ] **Database & Migrations**
  - [ ] Membuat migrasi untuk tabel `komponen_nilais` (nama, default_bobot).
  - [ ] Membuat migrasi untuk tabel `borang_nilais` (`kelas_id`, `komponen_nilai_id`, bobot).
  - [ ] Membuat migrasi untuk tabel `nilai_mahasiswas` (`krs_detail_id`, `borang_nilai_id`, nilai).
  - [ ] Membuat migrasi untuk tabel `nilai_akhirs` (`krs_detail_id`, nilai_angka, nilai_huruf, bobot_nilai).
- [ ] **Models & Relations**
  - [ ] `KomponenNilai`, `BorangNilai`, `NilaiMahasiswa`, `NilaiAkhir` dengan relasi yang sesuai.
- [ ] **Data Seeder**
  - [ ] Membuat `KomponenNilaiSeeder` (Tugas, UTS, UAS, Praktikum, dll).
- [ ] **Service & Repository Layer**
  - [ ] Membuat `NilaiRepositoryInterface` & `NilaiRepository`.
  - [ ] Membuat `NilaiService` (logika perhitungan, konversi, dan finalisasi nilai).
- [ ] **Konfigurasi**
  - [ ] Membuat tabel `grade_scales` atau file config untuk konversi nilai angka ke huruf (A, B, C, D, E).

### Hari 3-4: Implementasi Antarmuka Penilaian Dosen

- [ ] **Filament Resource: `KomponenNilai`**
  - [ ] CRUD untuk komponen nilai default.
- [ ] **Halaman Pengaturan Borang Nilai (Bagian dari Resource `Kelas` atau halaman custom Dosen)**
  - [ ] Dosen memilih komponen nilai untuk kelas yang diampu.
  - [ ] Dosen mengatur bobot per komponen (total harus 100%).
  - [ ] Fitur "Kunci Borang Nilai" agar tidak bisa diubah saat pengisian nilai.
- [ ] **Halaman Input Nilai (Custom Filament Page untuk Dosen)**
  - [ ] Pilih kelas yang diampu.
  - [ ] Tampilkan daftar mahasiswa (dari KRS yang disetujui) dan kolom komponen nilai.
  - [ ] Form input nilai per mahasiswa.
  - [ ] Validasi rentang nilai (0-100).
  - [ ] Fitur import nilai dari Excel.
  - [ ] Fitur "Simpan Sementara" dan "Hitung & Finalisasi".

### Hari 5-7: Fitur Perhitungan dan Laporan

- [ ] **Logika Perhitungan Nilai (`NilaiService`)**
  - [ ] Perhitungan nilai akhir berdasarkan bobot komponen.
  - [ ] Konversi nilai angka ke huruf (menggunakan `grade_scales`).
  - [ ] Perhitungan bobot nilai untuk IPK (A=4, B=3, dst).
- [ ] **Fitur Finalisasi Nilai**
  - [ ] Saat finalisasi, simpan data ke tabel `nilai_akhirs`.
  - [ ] Setelah finalisasi, nilai tidak bisa diubah oleh dosen.
  - [ ] Admin memiliki akses untuk membuka kembali (unlock) nilai jika ada revisi, dengan pencatatan log.
- [ ] **Laporan Nilai & KHS**
  - [ ] Halaman Kartu Hasil Studi (KHS) untuk mahasiswa (menampilkan nilai akhir per matkul dan IP semester).
  - [ ] Laporan nilai per kelas untuk dosen.
  - [ ] Laporan statistik nilai (distribusi A, B, C) untuk Kaprodi/Admin.
- [ ] **Testing**
  - [ ] Membuat unit test untuk `NilaiService` (perhitungan dan konversi).
  - [ ] Membuat feature test untuk alur input dan finalisasi nilai.
- [ ] **Refinement & Bug Fixing**

---

## Sprint 4: Integrasi dan Finalisasi (1 Minggu)

### Hari 1-3: Integrasi Antar Modul & Dashboard

- [ ] **Integrasi Modul**
  - [ ] Pastikan kuota kelas di `Kelas` berkurang secara atomik saat KRS disetujui.
  - [ ] Pastikan halaman input nilai hanya menampilkan mahasiswa dengan KRS status `disetujui`.
  - [ ] Pastikan KHS mahasiswa menghitung IPK dan SKS kumulatif dengan benar.
- [ ] **Dashboard Mahasiswa**
  - [ ] Widget Jadwal Kuliah Hari Ini.
  - [ ] Widget Status KRS (progress, status).
  - [ ] Widget Pengumuman Akademik.
  - [ ] Link cepat ke halaman KRS dan KHS.
- [ ] **Dashboard Dosen**
  - [ ] Widget Jadwal Mengajar Hari Ini.
  - [ ] Widget Daftar Mahasiswa Bimbingan (yang perlu persetujuan KRS).
  - [ ] Widget Daftar Kelas yang Diampu (yang perlu input nilai).

### Hari 4-5: UAT dan Feedback

- [ ] **Persiapan UAT**
  - [ ] Menyiapkan skenario testing end-to-end untuk setiap role (Admin, Dosen, Mahasiswa).
  - [ ] Menyiapkan data dummy yang realistis.
- [ ] **Pelaksanaan UAT**
  - [ ] Melakukan sesi UAT dengan perwakilan pengguna.
  - [ ] Mencatat semua feedback dan bug yang ditemukan secara sistematis.
- [ ] **Perbaikan**
  - [ ] Prioritaskan perbaikan bug kritikal dan blocker.
  - [ ] Lakukan optimasi query atau alur berdasarkan feedback.

### Hari 6-7: Dokumentasi dan Persiapan Deployment

- [ ] **Dokumentasi**
  - [ ] Membuat panduan pengguna (user guide) untuk setiap role.
  - [ ] Melengkapi dokumentasi teknis (struktur DB, arsitektur, API jika ada).
- [ ] **Persiapan Deployment**
  - [ ] Finalisasi environment variables (`.env`).
  - [ ] Lakukan optimasi Laravel:
    - `composer install --no-dev --optimize-autoloader`
    - `php artisan config:cache`
    - `php artisan route:cache`
    - `php artisan view:cache`
  - [ ] Konfigurasi server (web server, PHP, database).
  - [ ] Rencanakan strategi backup dan restore.
- [ ] **Final Security Review**
  - [ ] Cek kembali semua policy dan permission.
  - [ ] Pastikan tidak ada celah keamanan seperti XSS atau SQL Injection.
