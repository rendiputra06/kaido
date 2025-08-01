# Analisis Relasi Kelas, Angkatan, dan Kurikulum

Dokumen ini menganalisis pendekatan terbaik untuk menerapkan aturan atau batasan pengambilan kelas berdasarkan angkatan mahasiswa.

## Pertanyaan Awal

"Apakah kita perlu menambahkan field `angkatan` pada model `Kelas` untuk menentukan kelas mana yang bisa diambil oleh angkatan tertentu?"

## Analisis Pendekatan

Ada dua cara utama untuk mengatasi ini, namun hanya satu yang direkomendasikan untuk sistem informasi akademik yang fleksibel dan scalable.

### 1. Pendekatan Buruk: Menambahkan `angkatan` ke Model `Kelas`

Pendekatan ini melibatkan penambahan kolom `angkatan` secara langsung ke tabel `kelas`.

- **Kelemahan Utama:**
    - **Tidak Fleksibel:** Mahasiswa yang perlu mengulang mata kuliah dari angkatan di bawahnya tidak akan bisa mendaftar ke kelas tersebut.
    - **Duplikasi Data:** Untuk mata kuliah pilihan yang bisa diambil oleh beberapa angkatan, admin harus membuat beberapa data kelas yang identik hanya untuk membedakan angkatan. Ini sangat tidak efisien.
    - **Tidak Realistis:** Tidak mencerminkan cara kerja kurikulum di dunia nyata, di mana aturan pengambilan mata kuliah lebih kompleks daripada sekadar batasan angkatan.

### 2. Pendekatan Tepat: Memanfaatkan Model `Kurikulum` (Direkomendasikan)

Pendekatan ini menggunakan model `Kurikulum` yang sudah ada di dalam proyek sebagai pusat aturan akademik.

- **Konsep Alur Data:**
    1.  Seorang **Mahasiswa** memiliki **Angkatan**.
    2.  Setiap **Angkatan** terikat pada sebuah **Kurikulum** spesifik (misal, Angkatan 2023 mengikuti Kurikulum 2023).
    3.  **Kurikulum** mendefinisikan daftar **MataKuliah** yang harus/bisa diambil, seringkali dikelompokkan per semester.
    4.  **Kelas** adalah sebuah instansi atau jadwal penawaran dari sebuah **MataKuliah** pada periode tertentu.

- **Logika Validasi:**
    Pengecekan tidak terjadi di level database `Kelas`, melainkan pada **logika bisnis aplikasi** (misalnya, di dalam `KrsService` saat mahasiswa akan menambahkan mata kuliah ke KRS).
    
    Sistem akan bertanya: *"Apakah Mata Kuliah dari Kelas yang dipilih ini ada di dalam Kurikulum yang berlaku untuk Mahasiswa ini?"*

- **Kelebihan:**
    - **Sangat Fleksibel:** Mendukung mahasiswa yang mengulang, mengambil mata kuliah lintas semester, dan mata kuliah pilihan.
    - **Struktur yang Benar:** Mencerminkan proses bisnis akademik yang sesungguhnya.
    - **Terpusat:** Semua aturan akademik terpusat di dalam manajemen Kurikulum, bukan tersebar di setiap data Kelas.

## Kesimpulan

**Jangan menambahkan `angkatan` ke model `Kelas`.** Implementasi yang benar adalah dengan membangun dan menegakkan aturan melalui relasi antara **Mahasiswa -> Kurikulum -> MataKuliah**. Validasi dilakukan pada saat proses pengisian KRS.
