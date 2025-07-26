# Penambahan Policies untuk Resource Fase 1

Dokumen ini berisi daftar tugas untuk menambahkan policies dan mengimplementasikan interface HasShieldPermissions pada resource Filament yang dibuat pada Fase 1.

## 1. Pembuatan File Policies

- [x] Buat policy untuk model `ProgramStudi`
- [x] Buat policy untuk model `MataKuliah`
- [x] Buat policy untuk model `TahunAjaran`
- [x] Buat policy untuk model `Kurikulum`
- [x] Buat policy untuk model `Mahasiswa`
- [x] Buat policy untuk model `Dosen`

## 2. Implementasi HasShieldPermissions pada Resource Filament

- [x] Implementasikan interface HasShieldPermissions pada `ProgramStudiResource`
- [x] Implementasikan interface HasShieldPermissions pada `MataKuliahResource`
- [x] Implementasikan interface HasShieldPermissions pada `TahunAjaranResource`
- [x] Implementasikan interface HasShieldPermissions pada `KurikulumResource`
- [x] Implementasikan interface HasShieldPermissions pada `MahasiswaResource`
- [x] Implementasikan interface HasShieldPermissions pada `DosenResource`

## 3. Langkah Selanjutnya

- [ ] Jalankan perintah `php artisan shield:generate` untuk menghasilkan permissions berdasarkan resource yang telah diimplementasikan
- [ ] Jalankan perintah `php artisan shield:super-admin` untuk memberikan semua permissions kepada role super_admin
- [ ] Buat role tambahan seperti admin_akademik, dosen, dan mahasiswa dengan permissions yang sesuai

## 4. Catatan Implementasi

Setiap policy yang dibuat mengikuti struktur yang sama dengan `BookPolicy` yang sudah ada, dengan metode-metode berikut:

- `viewAny`: Mengecek apakah user dapat melihat daftar semua record
- `view`: Mengecek apakah user dapat melihat detail record tertentu
- `create`: Mengecek apakah user dapat membuat record baru
- `update`: Mengecek apakah user dapat mengubah record tertentu
- `delete`: Mengecek apakah user dapat menghapus record tertentu
- `deleteAny`: Mengecek apakah user dapat menghapus banyak record sekaligus
- `forceDelete`: Mengecek apakah user dapat menghapus permanen record tertentu
- `forceDeleteAny`: Mengecek apakah user dapat menghapus permanen banyak record sekaligus
- `restore`: Mengecek apakah user dapat mengembalikan record tertentu yang telah dihapus
- `restoreAny`: Mengecek apakah user dapat mengembalikan banyak record sekaligus yang telah dihapus
- `replicate`: Mengecek apakah user dapat menduplikasi record tertentu
- `reorder`: Mengecek apakah user dapat mengubah urutan record

Setiap resource Filament yang mengimplementasikan interface HasShieldPermissions telah ditambahkan metode `getPermissionPrefixes()` yang mengembalikan array berisi prefix-prefix permission yang digunakan oleh resource tersebut.