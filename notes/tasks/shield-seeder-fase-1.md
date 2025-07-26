# Pembaruan ShieldSeeder untuk Resource Fase 1

Dokumen ini berisi daftar tugas untuk memperbarui ShieldSeeder dengan permission untuk resource-resource yang dibuat pada Fase 1.

## 1. Pembaruan ShieldSeeder

- [x] Tambahkan permission untuk model `ProgramStudi` ke role super_admin
- [x] Tambahkan permission untuk model `MataKuliah` ke role super_admin
- [x] Tambahkan permission untuk model `TahunAjaran` ke role super_admin
- [x] Tambahkan permission untuk model `Kurikulum` ke role super_admin
- [x] Tambahkan permission untuk model `Mahasiswa` ke role super_admin
- [x] Tambahkan permission untuk model `Dosen` ke role super_admin

## 2. Pembuatan Role Baru

- [x] Buat role `admin_akademik` dengan permission untuk mengelola semua resource fase 1
- [x] Buat role `dosen` dengan permission untuk melihat semua resource fase 1
- [x] Buat role `mahasiswa` dengan permission untuk melihat resource fase 1 yang relevan

## 3. Detail Permission untuk Setiap Role

### Role admin_akademik

Permission yang diberikan:
- Melihat, membuat, mengubah, dan menghapus `ProgramStudi`
- Melihat, membuat, mengubah, dan menghapus `MataKuliah`
- Melihat, membuat, mengubah, dan menghapus `TahunAjaran`
- Melihat, membuat, mengubah, dan menghapus `Kurikulum`
- Melihat, membuat, mengubah, dan menghapus `Mahasiswa`
- Melihat, membuat, mengubah, dan menghapus `Dosen`
- Akses ke halaman `MyProfilePage`

### Role dosen

Permission yang diberikan:
- Melihat `ProgramStudi`
- Melihat `MataKuliah`
- Melihat `TahunAjaran`
- Melihat `Kurikulum`
- Melihat `Mahasiswa`
- Melihat `Dosen`
- Akses ke halaman `MyProfilePage`

### Role mahasiswa

Permission yang diberikan:
- Melihat `ProgramStudi`
- Melihat `MataKuliah`
- Melihat `TahunAjaran`
- Melihat `Kurikulum`
- Melihat `Mahasiswa` (hanya data diri sendiri)
- Melihat `Dosen`
- Akses ke halaman `MyProfilePage`

## 4. Langkah Selanjutnya

- [ ] Jalankan perintah `php artisan db:seed --class=ShieldSeeder` untuk menerapkan perubahan pada database
- [ ] Verifikasi bahwa semua role dan permission telah dibuat dengan benar
- [ ] Uji akses untuk setiap role untuk memastikan permission berfungsi dengan benar