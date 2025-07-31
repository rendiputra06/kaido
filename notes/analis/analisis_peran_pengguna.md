# Analisis Menu Berdasarkan Role Pengguna

Proyek ini menggunakan sistem kontrol akses berbasis peran (RBAC) dengan paket `filament-shield`. Berikut adalah ringkasan **menu utama** yang akan muncul di dashboard Filament ketika login dengan role tertentu.

---

## 1. Super Admin

**Menu yang Muncul:**
- **Dashboard Utama**: Overview sistem
- **Manajemen Pengguna**: Kelola semua user (Admin, Dosen, Mahasiswa)
- **Manajemen Role & Permissions**: Kelola peran dan izin
- **Manajemen Buku**: Kelola data buku
- **Manajemen Akademik**: Program Studi, Mata Kuliah, Tahun Ajaran, Kurikulum
- **Manajemen KRS**: Semua fitur KRS untuk semua user
- **Manajemen Dosen**: Kelola data dosen
- **Manajemen Mahasiswa**: Kelola data mahasiswa
- **Pengaturan Sistem**: Semua pengaturan aplikasi

---

## 2. Moderator

**Menu yang Muncul:**
- **Dashboard**: Overview ringkas
- **Manajemen Buku**: Kelola data buku (tambah, edit, hapus)
- **Pengaturan Profil**: Update informasi pribadi
- **Pengaturan Umum**: Akses ke pengaturan dasar sistem

---

## 3. Admin Akademik

**Menu yang Muncul:**
- **Dashboard Akademik**: Overview data akademik
- **Program Studi**: Kelola data program studi
- **Mata Kuliah**: Kelola data mata kuliah
- **Tahun Ajaran**: Kelola tahun ajaran
- **Kurikulum**: Kelola kurikulum
- **Mahasiswa**: Kelola data mahasiswa
- **Dosen**: Kelola data dosen
- **Periode KRS**: Kelola periode pendaftaran KRS
- **Monitoring KRS**: Pantau semua KRS mahasiswa
- **Penetapan Dosen PA**: Kelola penugasan dosen pembimbing akademik
- **Profil**: Update informasi pribadi

---

## 4. Dosen

**Menu yang Muncul:**
- **Dashboard Dosen**: Overview untuk dosen
- **Data Akademik**: Lihat program studi, mata kuliah, kurikulum
- **Data Mahasiswa**: Lihat data mahasiswa (termasuk yang menjadi bimbingannya)
- **Data Dosen**: Lihat data dosen
- **KRS Mahasiswa**: Lihat KRS mahasiswa
- **Persetujuan KRS**: Setujui atau tolak KRS mahasiswa
- **Monitoring KRS**: Pantau KRS mahasiswa
- **Profil**: Update informasi pribadi

---

## 5. Mahasiswa

**Menu yang Muncul:**
- **Dashboard Mahasiswa**: Overview untuk mahasiswa
- **Data Akademik**: Lihat program studi, mata kuliah, kurikulum
- **Data Dosen**: Lihat data dosen
- **KRS Saya**: Kelola KRS pribadi (tambah, edit mata kuliah)
- **History KRS**: Lihat riwayat KRS semester lalu
- **Profil**: Update informasi pribadi

---

**Catatan:** Menu yang muncul di dashboard Filament akan otomatis disesuaikan berdasarkan role pengguna yang login. Tidak semua menu akan muncul untuk semua role.

**Izin yang Dimiliki:**
- `view_program_studi` & `view_any_program_studi`
- `create_program_studi` & `update_program_studi`
- `delete_program_studi` & `delete_any_program_studi`
- `view_mata_kuliah` & `view_any_mata_kuliah`
- `create_mata_kuliah` & `update_mata_kuliah`
- `delete_mata_kuliah` & `delete_any_mata_kuliah`
- `view_tahun_ajaran` & `view_any_tahun_ajaran`
- `create_tahun_ajaran` & `update_tahun_ajaran`
- `delete_tahun_ajaran` & `delete_any_tahun_ajaran`
- `view_kurikulum` & `view_any_kurikulum`
- `create_kurikulum` & `update_kurikulum`
- `delete_kurikulum` & `delete_any_kurikulum`
- `view_mahasiswa` & `view_any_mahasiswa`
- `create_mahasiswa` & `update_mahasiswa`
- `delete_mahasiswa` & `delete_any_mahasiswa`
- `view_dosen` & `view_any_dosen`
- `create_dosen` & `update_dosen`
- `delete_dosen` & `delete_any_dosen`
- `view_periode_krs` & `view_any_periode_krs`
- `create_periode_krs` & `update_periode_krs`
- `delete_periode_krs` & `delete_any_periode_krs`
- `view_krs_mahasiswa` & `view_any_krs_mahasiswa`
- `view_krs_detail` & `view_any_krs_detail`
- `page_MyProfilePage`
- `page_KrsMonitoringPage`
- `page_penetapan_dosen_pa`

---

## 4. Dosen

Peran ini memiliki akses yang relevan dengan fungsi seorang dosen, seperti melihat data akademik, menyetujui Kartu Rencana Studi (KRS), dan memantau kemajuan mahasiswa.

**Izin yang Dimiliki:**
- `view_program_studi` & `view_any_program_studi`
- `view_mata_kuliah` & `view_any_mata_kuliah`
- `view_tahun_ajaran` & `view_any_tahun_ajaran`
- `view_kurikulum` & `view_any_kurikulum`
- `view_mahasiswa` & `view_any_mahasiswa`
- `view_dosen` & `view_any_dosen`
- `view_krs_mahasiswa` & `view_any_krs_mahasiswa`
- `view_krs_detail` & `view_any_krs_detail`
- `page_MyProfilePage`
- `page_KrsApprovalPage`
- `page_KrsMonitoringPage`

---

## 5. Mahasiswa

Peran ini memiliki akses terbatas untuk melihat informasi akademik mereka dan mengelola KRS mereka sendiri.

**Izin yang Dimiliki:**
- `view_program_studi` & `view_any_program_studi`
- `view_mata_kuliah` & `view_any_mata_kuliah`
- `view_tahun_ajaran` & `view_any_tahun_ajaran`
- `view_kurikulum` & `view_any_kurikulum`
- `view_mahasiswa` (hanya data sendiri)
- `view_dosen` & `view_any_dosen`
- `view_krs_mahasiswa` (hanya data sendiri)
- `view_krs_detail` (hanya data sendiri)
- `page_MyProfilePage`
- `page_KrsPage`
- `page_KrsHistoryPage`
