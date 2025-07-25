# Rencana Pengembangan Sistem Informasi Akademik (SIAKAD)

Dokumen ini menguraikan rencana pengembangan untuk Sistem Informasi Akademik (SIAKAD) Sekolah Tinggi. Proyek ini akan dibangun menggunakan **Laravel** dan **Filament** untuk akselerasi pengembangan antarmuka admin dan manajemen data.

## 1. Visi & Tujuan

**Visi:** Menciptakan sistem informasi akademik yang terintegrasi, modern, dan mudah digunakan untuk mendukung kegiatan operasional dan akademik di lingkungan sekolah tinggi.

**Tujuan:**
-   Digitalisasi proses akademik mulai dari penerimaan mahasiswa baru hingga kelulusan.
-   Menyediakan satu sumber data (single source of truth) untuk semua data akademik.
-   Mempermudah akses informasi bagi mahasiswa, dosen, dan staf administrasi.
-   Meningkatkan efisiensi pelaporan untuk keperluan internal dan akreditasi.

## 2. Arsitektur & Teknologi

-   **Backend:** Laravel
-   **Frontend & Admin Panel:** Filament (TALL Stack: TailwindCSS, Alpine.js, Livewire, Laravel)
-   **Database:** Sesuai konfigurasi Laravel (MySQL/PostgreSQL direkomendasikan)
-   **Manajemen Akses:** Filament Shield (untuk manajemen role & permission yang granular)

## 3. Rencana Pengembangan per Modul (Fase)

Pengembangan akan dibagi menjadi beberapa fase berdasarkan prioritas modul.

---

### **Fase 1: Fondasi & Data Master**

Tujuan fase ini adalah menyiapkan data inti yang akan menjadi dasar bagi modul lainnya.

**Modul & Fitur:**
1.  **Manajemen Pengguna & Akses (Users & Roles)**
    -   Entitas: `User`, `Role`, `Permission`
    -   Fitur:
        -   CRUD untuk Pengguna (Staf, Dosen, Mahasiswa).
        -   Penetapan Roles (SuperAdmin, Admin Akademik, Dosen, Mahasiswa).
        -   Menggunakan Filament Shield untuk membatasi akses ke setiap menu/resource.

2.  **Data Master Akademik (Core Academic Data)**
    -   Entitas:
        -   `ProgramStudi` (Program Studi)
        -   `MataKuliah` (Mata Kuliah)
        -   `TahunAjaran` (Tahun Ajaran Akademik)
        -   `Kurikulum`
    -   Fitur:
        -   CRUD untuk semua entitas di atas.
        -   Relasi antara Kurikulum dengan Program Studi dan Mata Kuliah.

3.  **Data Master Pengguna (User Profiles)**
    -   Entitas:
        -   `Mahasiswa` (Data detail mahasiswa, seperti NIM, angkatan, status, dll)
        -   `Dosen` (Data detail dosen, seperti NIDN, jabatan, dll)
    -   Fitur:
        -   CRUD untuk data Mahasiswa dan Dosen.
        -   Setiap entitas ini akan berelasi `One-to-One` dengan model `User`.

---

### **Fase 2: Proses Akademik Inti**

Fokus pada proses utama yang terjadi setiap semester.

**Modul & Fitur:**
1.  **Manajemen Kelas & Jadwal**
    -   Entitas: `Kelas`, `JadwalKuliah`
    -   Fitur:
        -   Admin dapat membuka kelas untuk setiap Mata Kuliah di semester berjalan.
        -   Admin dapat menetapkan Dosen pengampu dan jadwal (hari, jam, ruang).

2.  **Kartu Rencana Studi (KRS)**
    -   Entitas: `KrsMahasiswa`
    -   Fitur:
        -   Halaman khusus bagi mahasiswa untuk mengisi/mengedit KRS pada periode yang ditentukan.
        -   Validasi SKS maksimum, prasyarat mata kuliah, dan kuota kelas.
        -   Proses persetujuan (approval) KRS oleh Dosen Pembimbing Akademik (DPA).

3.  **Manajemen Nilai**
    -   Entitas: `Nilai`
    -   Fitur:
        -   Dosen dapat menginput nilai mahasiswa untuk kelas yang diampu.
        -   Komponen nilai (tugas, UTS, UAS) dapat dikonfigurasi.
        -   Perhitungan nilai akhir dan Indeks Prestasi Semester (IPS).

---

### **Fase 3: Portal & Laporan**

Menyediakan antarmuka khusus untuk user dan fitur pelaporan.

**Modul & Fitur:**
1.  **Portal Mahasiswa (Student Dashboard)**
    -   Halaman khusus (bukan di `/admin`) yang bisa diakses mahasiswa.
    -   Fitur:
        -   Lihat jadwal kuliah.
        -   Lihat Kartu Hasil Studi (KHS) per semester.
        -   Lihat Transkrip Nilai sementara.
        -   Cetak KRS dan KHS.

2.  **Portal Dosen (Lecturer Dashboard)**
    -   Halaman khusus untuk dosen.
    -   Fitur:
        -   Lihat jadwal mengajar.
        -   Input nilai.
        -   Lihat daftar mahasiswa bimbingan.
        -   Setujui KRS mahasiswa bimbingan.

3.  **Laporan & Transkrip**
    -   Fitur:
        -   Generate Transkrip Nilai resmi (PDF).
        -   Laporan mahasiswa aktif per angkatan/program studi.
        -   Laporan Indeks Prestasi Kumulatif (IPK).

---

## 4. Struktur Data (Model & Migrasi)

Berikut adalah gambaran awal model dan relasi utamanya:

-   `users`: (id, name, email, password) -> Bawaan Laravel & Filament
-   `roles`, `permissions`: -> Dari Filament Shield
-   `program_studis`: (id, nama_prodi, jenjang, kode_prodi)
-   `mata_kuliahs`: (id, program_studi_id, kode_mk, nama_mk, sks, semester)
-   `kurikulums`: (id, program_studi_id, nama_kurikulum, tahun_mulai)
-   `kurikulum_matakuliah` (pivot table)
-   `mahasiswas`: (id, user_id, nim, nama, angkatan, program_studi_id)
-   `dosens`: (id, user_id, nidn, nama, jabatan)
-   `tahun_ajarans`: (id, kode, nama, tgl_mulai, tgl_selesai, is_active)
-   `kelas`: (id, mata_kuliah_id, tahun_ajaran_id, dosen_id, nama_kelas, kuota)
-   `jadwals`: (id, kelas_id, hari, jam_mulai, jam_selesai, ruang)
-   `krs_mahasiswas`: (id, mahasiswa_id, kelas_id, tahun_ajaran_id, status_approval)
-   `nilais`: (id, krs_mahasiswa_id, nilai_akhir, nilai_huruf)

## 5. Rencana Implementasi (Timeline)

-   **Sprint 1-2 (2 Minggu):** Implementasi Fase 1.
-   **Sprint 3-4 (2 Minggu):** Implementasi Fase 2.
-   **Sprint 5 (1 Minggu):** Implementasi Fase 3.
-   **Sprint 6 (1 Minggu):** Pengujian, perbaikan bug, dan persiapan deployment.

Dokumen ini bersifat hidup dan dapat disesuaikan seiring dengan berjalannya pengembangan.
