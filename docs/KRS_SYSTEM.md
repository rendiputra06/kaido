# Sistem KRS (Kartu Rencana Studi)

## Overview

Sistem KRS adalah modul untuk pengelolaan perencanaan studi mahasiswa yang memungkinkan mahasiswa untuk membuat, mengubah, dan mengajukan KRS secara online, serta memungkinkan dosen untuk menyetujui atau menolak KRS mahasiswa bimbingannya.

## Fitur Utama

### 1. Mahasiswa
- **Pembuatan KRS**: Mahasiswa dapat membuat KRS untuk periode yang aktif
- **Penambahan Mata Kuliah**: Menambahkan mata kuliah ke dalam KRS
- **Validasi Otomatis**: Sistem otomatis memvalidasi:
  - Prasyarat mata kuliah
  - Konflik jadwal
  - Batas maksimal SKS
  - Kuota kelas
- **Pengajuan KRS**: Mengajukan KRS untuk disetujui oleh dosen pembimbing
- **Dashboard**: Menampilkan jadwal kuliah hari ini dan status KRS

### 2. Dosen Pembimbing
- **Review KRS**: Melihat detail KRS mahasiswa bimbingan
- **Persetujuan/Tolak**: Menyetujui atau menolak KRS dengan catatan
- **Dashboard**: Menampilkan KRS yang menunggu persetujuan dan jadwal mengajar

### 3. Admin
- **Manajemen Periode KRS**: Membuat dan mengelola periode KRS
- **Manajemen Kelas**: Mengelola kelas dan kuota
- **Monitoring**: Melihat status KRS seluruh mahasiswa

## Alur Kerja

### Alur KRS Mahasiswa

1. **Pembuatan KRS**
   - Mahasiswa membuat KRS untuk periode aktif
   - Sistem menentukan batas SKS berdasarkan IP semester lalu

2. **Penambahan Mata Kuliah**
   - Mahasiswa menambahkan mata kuliah ke KRS
   - Validasi prasyarat dan konflik jadwal
   - Pengecekan kuota kelas (hanya pengecekan, tidak mengurangi kuota)

3. **Pengajuan KRS**
   - Mahasiswa mengajukan KRS untuk disetujui
   - KRS berubah status menjadi 'pending'

4. **Persetujuan Dosen**
   - Dosen pembimbing menerima notifikasi
   - Dosen mereview KRS mahasiswa
   - Dosen menyetujui atau menolak dengan catatan

5. **Finalisasi**
   - Jika disetujui: kuota kelas berkurang secara atomik
   - Jika ditolak: mahasiswa dapat memperbaiki dan mengajukan ulang

### Alur Kuota Kelas

- **Draft Phase**: Pengecekan kuota hanya untuk validasi, tidak mengurangi kuota
- **Approval Phase**: Kuota berkurang secara atomik ketika KRS disetujui
- **Atomic Transaction**: Menggunakan database transaction untuk menjaga konsistensi kuota

## Teknis

### Model Database

#### KrsMahasiswa
- `id`: Primary key
- `mahasiswa_id`: Foreign key ke tabel mahasiswa
- `periode_krs_id`: Foreign key ke tabel periode_krs
- `max_sks`: Batas maksimal SKS untuk periode ini
- `total_sks`: Total SKS yang diambil
- `status`: Enum ['draft', 'pending', 'approved', 'rejected']
- `tanggal_submit`: Waktu pengajuan
- `tanggal_approval`: Waktu persetujuan
- `catatan_dosen`: Catatan dari dosen pembimbing

#### KrsDetail
- `id`: Primary key
- `krs_mahasiswa_id`: Foreign key ke KrsMahasiswa
- `kelas_id`: Foreign key ke tabel kelas
- `status`: Status detail KRS

#### Kelas
- `id`: Primary key
- `mata_kuliah_id`: Foreign key ke mata kuliah
- `dosen_id`: Foreign key ke dosen pengajar
- `kode_kelas`: Kode unik kelas
- `kuota`: Jumlah maksimal mahasiswa
- `sisa_kuota`: Sisa kuota yang tersedia

### Validasi

#### Validasi SKS
- IP ≥ 3.00: Maksimal 24 SKS
- IP 2.50 - 2.99: Maksimal 21 SKS
- IP 2.00 - 2.49: Maksimal 18 SKS
- IP < 2.00: Maksimal 15 SKS

#### Validasi Prasyarat
- Memeriksa apakah mahasiswa sudah lulus prasyarat
- Validasi berdasarkan nilai akhir mahasiswa

#### Validasi Konflik Jadwal
- Memeriksa tumpang tindih jadwal antar mata kuliah
- Validasi berdasarkan jadwal kuliah setiap kelas

### Dashboard Widgets

#### Mahasiswa Widgets
1. **TodayScheduleWidget**: Menampilkan jadwal kuliah hari ini
2. **KrsStatusWidget**: Menampilkan status KRS periode aktif

#### Dosen Widgets
1. **PendingKrsWidget**: Menampilkan KRS yang menunggu persetujuan
2. **TeachingScheduleWidget**: Menampilkan jadwal mengajar hari ini

## API Endpoints

### Mahasiswa
- `GET /api/krs/current`: Mendapatkan KRS aktif
- `POST /api/krs`: Membuat KRS baru
- `PUT /api/krs/{id}`: Update KRS
- `POST /api/krs/{id}/submit`: Submit KRS untuk persetujuan

### Dosen
- `GET /api/krs/pending`: Mendapatkan KRS menunggu persetujuan
- `PUT /api/krs/{id}/approve`: Menyetujui KRS
- `PUT /api/krs/{id}/reject`: Menolak KRS

## Keamanan

- **Role-based Access**: Setiap role memiliki akses yang terbatas
- **Data Validation**: Validasi input di client dan server
- **Rate Limiting**: Pembatasan jumlah request
- **Audit Trail**: Pencatatan semua aktivitas perubahan KRS

## Monitoring

- **Real-time Notifications**: Notifikasi untuk dosen saat ada KRS baru
- **Email Alerts**: Email untuk mahasiswa saat KRS disetujui/ditolak
- **Dashboard Analytics**: Statistik penggunaan KRS

## Troubleshooting

### Masalah Umum

1. **KRS tidak bisa disubmit**
   - Periksa apakah periode KRS masih aktif
   - Pastikan semua validasi terpenuhi
   - Cek total SKS tidak melebihi batas

2. **Kuota kelas penuh**
   - Kuota hanya berkurang saat KRS disetujui
   - Sistem akan memberikan pesan error jika kuota habis saat approval

3. **Konflik jadwal tidak terdeteksi**
   - Pastikan jadwal kuliah sudah benar di database
   - Cek timezone yang digunakan

## Best Practices

1. **Backup**: Lakukan backup sebelum periode KRS baru
2. **Testing**: Test alur KRS di environment staging
3. **Documentation**: Update dokumentasi untuk perubahan fitur
4. **Training**: Berikan training untuk dosen dan mahasiswa baru