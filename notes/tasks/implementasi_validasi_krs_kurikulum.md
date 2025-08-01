# Task List (Versi 2.0): Implementasi Sistem Akademik Berbasis Kurikulum

Ini adalah daftar tugas yang diperluas untuk mengimplementasikan logika validasi KRS dan fitur akademik lainnya secara komprehensif.

## Fase 1: Penyempurnaan Struktur & Relasi Data

- [x] **Sempurnakan Tabel Pivot `kurikulum_matakuliah`:**
    - [x] Pastikan tabel pivot yang menghubungkan `Kurikulum` dan `MataKuliah` ada.
    - [x] Tambahkan kolom `semester_ditawarkan` (integer) pada tabel pivot tersebut. Ini untuk menandai di semester berapa mata kuliah ini idealnya diambil.
    - [x] Tambahkan kolom `jenis` (enum: 'wajib', 'pilihan') pada tabel pivot. Ini membedakan mata kuliah wajib dan pilihan dalam sebuah kurikulum.
- [x] **Buat Tabel Prasyarat `matakuliah_prasyarat`:**
    - [x] Buat tabel pivot baru untuk menangani relasi many-to-many antara `MataKuliah` dengan `MataKuliah` itu sendiri.
    - [x] Kolomnya bisa berisi: `matakuliah_id` (mata kuliah utama) dan `prasyarat_id` (mata kuliah yang harus diambil sebelumnya).
- [x] **Verifikasi Kolom Penting di Model Utama:**
    - [x] Pastikan model `MataKuliah` memiliki kolom `sks` (integer).
    - [x] Pastikan model `Kelas` memiliki kolom `kuota` (integer) untuk membatasi jumlah pendaftar.
- [x] **Perbarui Definisi Relasi di Model Eloquent:**
    - [x] Definisikan relasi `belongsToMany` di model `Kurikulum` dan `MataKuliah` dengan menyertakan kolom pivot baru (`semester_ditawarkan`, `jenis`) menggunakan `.withPivot()`.
    - [x] Definisikan relasi `belongsToMany` untuk prasyarat di model `MataKuliah`.

## Fase 2: Implementasi Logika Bisnis (Service Layer)

- [x] **Lokasi Logika:** Gunakan `app/Services/KrsService.php` (atau buat jika belum ada) sebagai pusat logika.
- [x] **Kembangkan Fungsi Validasi `canEnroll(Mahasiswa $mahasiswa, Kelas $kelas): array`:**
    - Fungsi ini harus mengembalikan sebuah array, misal `['success' => true/false, 'message' => '...']`.
    - **Implementasikan Aturan Validasi di Dalamnya:**
        - [x] **Validasi Kurikulum:** Apakah mata kuliah dari `Kelas` ini ada di kurikulum mahasiswa?
        - [x] **Validasi Prasyarat:** Apakah mahasiswa sudah lulus semua mata kuliah prasyarat? (Ini memerlukan pengecekan ke tabel `nilai_akhir`).
        - [x] **Validasi Batas SKS:** Apakah total SKS yang akan diambil (termasuk yang baru ini) melebihi batas maksimal per semester? (Batas SKS bisa ditentukan berdasarkan IPK semester lalu).
        - [x] **Validasi Kuota Kelas:** Apakah jumlah mahasiswa yang sudah terdaftar di `Kelas` ini masih di bawah `kuota`?
        - [x] **Validasi Jadwal Bentrok:** (Tingkat lanjut) Apakah jadwal `Kelas` ini bentrok dengan kelas lain yang sudah diambil mahasiswa?

## Fase 3: Peningkatan Antarmuka & Pengalaman Pengguna (UI/UX)

- [x] **Filter Cerdas di Halaman Pemilihan Mata Kuliah:**
    - [x] Secara default, hanya tampilkan kelas/mata kuliah yang sesuai dengan `semester_ditawarkan` di kurikulum mahasiswa.
    - [x] Sediakan opsi/filter bagi mahasiswa untuk bisa melihat mata kuliah dari semester lain (untuk keperluan mengulang).
- [x] **Buat Halaman Visualisasi Kurikulum:**
    - [x] Buat halaman baru di mana mahasiswa bisa melihat seluruh struktur kurikulumnya dari semester 1 sampai 8.
    - [x] Gunakan warna atau ikon untuk menandai status setiap mata kuliah: `Sudah Lulus`, `Sedang Diambil`, `Belum Diambil`.
- [x] **Tampilkan Umpan Balik Validasi yang Jelas:**
    - [x] Saat validasi gagal, tampilkan pesan yang spesifik. Contoh: "Gagal: Anda belum lulus mata kuliah prasyarat: Dasar Pemrograman." atau "Gagal: Kuota kelas ini sudah penuh."
- [x] **Notifikasi di Dasbor:**
    - [x] Tambahkan widget atau notifikasi di dasbor untuk mengingatkan mahasiswa, misal: "Anda memiliki mata kuliah wajib dari semester 2 yang belum diambil."

## Fase 4: Pengujian Komprehensif

- [ ] **Perluas Skenario Uji:**
    - [ ] Uji kasus gagal karena prasyarat tidak terpenuhi.
    - [ ] Uji kasus gagal karena SKS melebihi batas.
    - [ ] Uji kasus gagal karena kuota kelas penuh.
    - [ ] Uji kasus sukses mengambil mata kuliah pilihan.
    - [ ] Uji kasus sukses mengambil mata kuliah mengulang.
