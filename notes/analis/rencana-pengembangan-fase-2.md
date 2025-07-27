# Rencana Pengembangan Fase 2: Proses Akademik Inti

## Pendahuluan

Dokumen ini merupakan rencana pengembangan lanjutan untuk Fase 2 dari Sistem Informasi Akademik (SIAKAD) Sekolah Tinggi. Fase 2 berfokus pada implementasi proses akademik inti yang terjadi setiap semester, meliputi manajemen kelas dan jadwal, pengelolaan Kartu Rencana Studi (KRS), serta manajemen nilai mahasiswa.

## Tujuan Fase 2

1. Mengimplementasikan proses bisnis akademik yang terjadi secara rutin setiap semester
2. Membangun sistem pengelolaan kelas dan jadwal kuliah yang fleksibel dan efisien
3. Mengotomatisasi proses pengisian dan persetujuan KRS mahasiswa
4. Menyediakan sistem penilaian yang komprehensif dan transparan
5. Memastikan integrasi yang mulus dengan data master yang telah dibangun pada Fase 1

## Modul dan Fitur

### 1. Manajemen Kelas & Jadwal

#### 1.1 Entitas dan Relasi

**Entitas Utama:**
- `Kelas` (Representasi mata kuliah yang dibuka pada semester tertentu)
- `JadwalKuliah` (Informasi waktu dan tempat pelaksanaan kelas)
- `RuangKuliah` (Data ruangan yang tersedia untuk perkuliahan)

**Relasi:**
- `Kelas` memiliki relasi dengan `MataKuliah` (dari Fase 1)
- `Kelas` memiliki relasi dengan `TahunAjaran` (dari Fase 1)
- `Kelas` memiliki relasi dengan `Dosen` sebagai pengampu (dari Fase 1)
- `JadwalKuliah` memiliki relasi dengan `Kelas` (one-to-many)
- `JadwalKuliah` memiliki relasi dengan `RuangKuliah`

#### 1.2 Struktur Data

**Model `Kelas`:**
```php
Schema::create('kelas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mata_kuliah_id')->constrained();
    $table->foreignId('tahun_ajaran_id')->constrained();
    $table->foreignId('dosen_id')->constrained();
    $table->string('kode_kelas');
    $table->string('nama_kelas');
    $table->integer('kuota')->default(40);
    $table->integer('sisa_kuota')->default(40);
    $table->boolean('is_active')->default(true);
    $table->text('deskripsi')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

**Model `RuangKuliah`:**
```php
Schema::create('ruang_kuliahs', function (Blueprint $table) {
    $table->id();
    $table->string('kode_ruang')->unique();
    $table->string('nama_ruang');
    $table->string('gedung')->nullable();
    $table->string('lantai')->nullable();
    $table->integer('kapasitas');
    $table->boolean('is_active')->default(true);
    $table->text('fasilitas')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

**Model `JadwalKuliah`:**
```php
Schema::create('jadwal_kuliahs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kelas_id')->constrained();
    $table->foreignId('ruang_kuliah_id')->constrained('ruang_kuliahs');
    $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
    $table->time('jam_mulai');
    $table->time('jam_selesai');
    $table->boolean('is_active')->default(true);
    $table->text('keterangan')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    // Tambahkan indeks untuk optimasi query
    $table->index(['hari', 'jam_mulai', 'jam_selesai']);
});
```

#### 1.3 Fitur dan Implementasi

**1.3.1 Pembukaan Kelas**
- Admin Akademik dapat membuka kelas untuk setiap mata kuliah pada semester berjalan
- Form pembukaan kelas mencakup:
  - Pemilihan mata kuliah (dari data master)
  - Pemilihan tahun ajaran aktif
  - Penentuan dosen pengampu
  - Pengaturan kuota mahasiswa
  - Pengaturan kode dan nama kelas (misalnya Kelas A, B, dst)

**1.3.2 Penjadwalan Kelas**
- Admin dapat menambahkan jadwal untuk setiap kelas yang dibuka
- Sistem melakukan validasi untuk mencegah:
  - Bentrok jadwal untuk ruangan yang sama
  - Bentrok jadwal untuk dosen yang sama
  - Penggunaan ruangan melebihi kapasitas
- Implementasi algoritma pengecekan bentrok jadwal

**1.3.3 Manajemen Ruang Kuliah**
- CRUD untuk data ruang kuliah
- Fitur pencarian dan filter ruangan berdasarkan kapasitas, gedung, atau fasilitas
- Visualisasi penggunaan ruangan dalam bentuk kalender atau timeline

**1.3.4 Laporan dan Ekspor Data**
- Laporan jadwal per program studi
- Laporan jadwal per dosen
- Laporan penggunaan ruangan
- Ekspor jadwal dalam format Excel atau PDF

### 2. Kartu Rencana Studi (KRS)

#### 2.1 Entitas dan Relasi

**Entitas Utama:**
- `PeriodeKrs` (Periode waktu pengisian KRS)
- `KrsMahasiswa` (Header/induk dari KRS per mahasiswa per semester)
- `KrsDetail` (Detail mata kuliah yang diambil dalam KRS)

**Relasi:**
- `PeriodeKrs` memiliki relasi dengan `TahunAjaran`
- `KrsMahasiswa` memiliki relasi dengan `Mahasiswa` (dari Fase 1)
- `KrsMahasiswa` memiliki relasi dengan `PeriodeKrs`
- `KrsMahasiswa` memiliki relasi dengan `Dosen` sebagai pembimbing akademik
- `KrsDetail` memiliki relasi dengan `KrsMahasiswa` (one-to-many)
- `KrsDetail` memiliki relasi dengan `Kelas`

#### 2.2 Struktur Data

**Model `PeriodeKrs`:**
```php
Schema::create('periode_krs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tahun_ajaran_id')->constrained();
    $table->string('nama_periode');
    $table->dateTime('tanggal_mulai');
    $table->dateTime('tanggal_selesai');
    $table->boolean('is_active')->default(false);
    $table->text('keterangan')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

**Model `KrsMahasiswa`:**
```php
Schema::create('krs_mahasiswas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mahasiswa_id')->constrained();
    $table->foreignId('periode_krs_id')->constrained('periode_krs');
    $table->foreignId('dosen_pa_id')->constrained('dosens');
    $table->integer('total_sks')->default(0);
    $table->integer('max_sks')->default(24);
    $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
    $table->text('catatan_dosen')->nullable();
    $table->dateTime('tanggal_submit')->nullable();
    $table->dateTime('tanggal_approval')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    // Unique constraint untuk memastikan satu mahasiswa hanya memiliki satu KRS per periode
    $table->unique(['mahasiswa_id', 'periode_krs_id']);
});
```

**Model `KrsDetail`:**
```php
Schema::create('krs_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('krs_mahasiswa_id')->constrained();
    $table->foreignId('kelas_id')->constrained();
    $table->integer('sks');
    $table->enum('status', ['active', 'canceled'])->default('active');
    $table->text('keterangan')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    // Unique constraint untuk mencegah duplikasi kelas dalam satu KRS
    $table->unique(['krs_mahasiswa_id', 'kelas_id']);
});
```

#### 2.3 Fitur dan Implementasi

**2.3.1 Manajemen Periode KRS**
- Admin dapat membuat dan mengatur periode pengisian KRS
- Pengaturan tanggal mulai dan selesai pengisian KRS
- Aktivasi/deaktivasi periode KRS

**2.3.2 Pengisian KRS oleh Mahasiswa**
- Antarmuka khusus untuk mahasiswa mengisi KRS
- Fitur pencarian dan filter kelas berdasarkan mata kuliah, dosen, atau jadwal
- Validasi otomatis untuk:
  - Batas maksimum SKS yang dapat diambil (berdasarkan IPK sebelumnya)
  - Pengecekan prasyarat mata kuliah
  - Pengecekan bentrok jadwal
  - Pengecekan kuota kelas
- Perhitungan otomatis total SKS yang diambil
- Fitur submit KRS untuk persetujuan dosen PA

**2.3.3 Persetujuan KRS oleh Dosen PA**
- Antarmuka khusus untuk dosen melihat dan menyetujui KRS mahasiswa bimbingan
- Fitur untuk memberikan catatan/komentar pada KRS mahasiswa
- Opsi untuk menyetujui atau menolak KRS
- Notifikasi kepada mahasiswa saat status KRS berubah

**2.3.4 Manajemen KRS oleh Admin**
- Fitur untuk melihat dan mengedit KRS mahasiswa (dalam kasus khusus)
- Laporan status pengisian KRS per program studi
- Fitur reset status KRS jika diperlukan

### 3. Manajemen Nilai

#### 3.1 Entitas dan Relasi

**Entitas Utama:**
- `KomponenNilai` (Komponen penilaian seperti UTS, UAS, Tugas, dll)
- `BorangNilai` (Template penilaian untuk setiap kelas)
- `NilaiMahasiswa` (Nilai mahasiswa per komponen per kelas)

**Relasi:**
- `KomponenNilai` memiliki relasi dengan `MataKuliah` (opsional, jika komponen berbeda per mata kuliah)
- `BorangNilai` memiliki relasi dengan `Kelas`
- `BorangNilai` memiliki relasi dengan `KomponenNilai` (many-to-many)
- `NilaiMahasiswa` memiliki relasi dengan `KrsDetail`
- `NilaiMahasiswa` memiliki relasi dengan `KomponenNilai`

#### 3.2 Struktur Data

**Model `KomponenNilai`:**
```php
Schema::create('komponen_nilais', function (Blueprint $table) {
    $table->id();
    $table->string('nama_komponen');
    $table->string('kode_komponen')->unique();
    $table->text('deskripsi')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

**Model `BorangNilai`:**
```php
Schema::create('borang_nilais', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kelas_id')->constrained();
    $table->string('nama_borang');
    $table->boolean('is_locked')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

**Model `BorangNilaiDetail` (Pivot dengan bobot):**
```php
Schema::create('borang_nilai_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('borang_nilai_id')->constrained();
    $table->foreignId('komponen_nilai_id')->constrained('komponen_nilais');
    $table->decimal('bobot', 5, 2); // Persentase bobot komponen (mis. 30.00 untuk 30%)
    $table->timestamps();
    
    // Unique constraint untuk mencegah duplikasi komponen dalam satu borang
    $table->unique(['borang_nilai_id', 'komponen_nilai_id']);
});
```

**Model `NilaiMahasiswa`:**
```php
Schema::create('nilai_mahasiswas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('krs_detail_id')->constrained();
    $table->foreignId('komponen_nilai_id')->constrained('komponen_nilais');
    $table->decimal('nilai', 5, 2)->nullable(); // Nilai angka (0-100)
    $table->timestamps();
    $table->softDeletes();
    
    // Unique constraint untuk mencegah duplikasi nilai komponen
    $table->unique(['krs_detail_id', 'komponen_nilai_id']);
});
```

**Model `NilaiAkhir`:**
```php
Schema::create('nilai_akhirs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('krs_detail_id')->constrained();
    $table->decimal('nilai_angka', 5, 2)->nullable(); // Nilai akhir dalam bentuk angka (0-100)
    $table->string('nilai_huruf', 2)->nullable(); // Nilai huruf (A, B, C, D, E)
    $table->decimal('bobot_nilai', 3, 2)->nullable(); // Bobot nilai untuk perhitungan IPK (4.0, 3.5, dll)
    $table->boolean('is_final')->default(false); // Status finalisasi nilai
    $table->timestamps();
    $table->softDeletes();
    
    // Unique constraint untuk memastikan satu nilai akhir per KRS detail
    $table->unique(['krs_detail_id']);
});
```

#### 3.3 Fitur dan Implementasi

**3.3.1 Manajemen Komponen Nilai**
- CRUD untuk komponen nilai (UTS, UAS, Tugas, Kuis, dll)
- Pengaturan komponen nilai default untuk seluruh sistem

**3.3.2 Pengaturan Borang Nilai per Kelas**
- Dosen dapat mengatur komponen penilaian untuk kelas yang diampu
- Pengaturan bobot untuk setiap komponen (total harus 100%)
- Fitur kunci borang nilai setelah disepakati dengan mahasiswa

**3.3.3 Input Nilai oleh Dosen**
- Antarmuka untuk input nilai per komponen untuk seluruh mahasiswa dalam kelas
- Opsi input nilai secara massal (batch) atau individual
- Validasi nilai (rentang 0-100)
- Fitur import nilai dari file Excel

**3.3.4 Perhitungan Nilai Akhir**
- Perhitungan otomatis nilai akhir berdasarkan bobot komponen
- Konversi nilai angka ke nilai huruf berdasarkan rentang yang dikonfigurasi
- Perhitungan bobot nilai untuk keperluan IPK

**3.3.5 Finalisasi dan Publikasi Nilai**
- Fitur finalisasi nilai oleh dosen
- Pengaturan waktu publikasi nilai kepada mahasiswa
- Opsi untuk revisi nilai dalam periode tertentu

**3.3.6 Laporan Nilai**
- Laporan nilai per mahasiswa (untuk KHS)
- Laporan nilai per kelas
- Statistik nilai (rata-rata, distribusi nilai huruf, dll)

## Implementasi Best Practices

### 1. Arsitektur dan Struktur Kode

#### 1.1 Penerapan Pattern Repository

Menggunakan pattern repository untuk memisahkan logika bisnis dari model dan controller:

```php
// Contoh interface repository untuk KRS
interface KrsRepositoryInterface
{
    public function getKrsByMahasiswaAndPeriode($mahasiswaId, $periodeId);
    public function createKrs(array $data);
    public function addKrsDetail($krsId, $kelasId);
    public function validateKrsDetail($mahasiswaId, $kelasId);
    // ... method lainnya
}

// Implementasi repository
class KrsRepository implements KrsRepositoryInterface
{
    // Implementasi method-method di atas
}
```

#### 1.2 Service Layer

Menggunakan service layer untuk menangani logika bisnis kompleks:

```php
// Contoh service untuk KRS
class KrsService
{
    protected $krsRepository;
    protected $kelasRepository;
    
    public function __construct(KrsRepositoryInterface $krsRepository, KelasRepositoryInterface $kelasRepository)
    {
        $this->krsRepository = $krsRepository;
        $this->kelasRepository = $kelasRepository;
    }
    
    public function submitKrs($mahasiswaId, $periodeId, array $kelasIds)
    {
        // Logika validasi dan pemrosesan KRS
        // ...
    }
    
    // Method lainnya
}
```

#### 1.3 Validasi dan Request Classes

Menggunakan Form Request untuk validasi input:

```php
class KrsSubmitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'periode_id' => 'required|exists:periode_krs,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ];
    }
}
```

### 2. Keamanan dan Otorisasi

#### 2.1 Implementasi Policy

Membuat policy untuk setiap model utama:

```php
class KrsPolicy
{
    public function view(User $user, KrsMahasiswa $krs)
    {
        // Mahasiswa hanya bisa melihat KRS miliknya sendiri
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krs->mahasiswa_id;
        }
        
        // Dosen hanya bisa melihat KRS mahasiswa bimbingannya
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $krs->dosen_pa_id;
        }
        
        // Admin akademik bisa melihat semua KRS
        return $user->hasPermissionTo('krs.view');
    }
    
    // Method policy lainnya
}
```

#### 2.2 Middleware Khusus

Membuat middleware untuk validasi periode KRS:

```php
class CheckKrsPeriodeMiddleware
{
    public function handle($request, Closure $next)
    {
        $periodeKrs = PeriodeKrs::where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();
            
        if (!$periodeKrs) {
            return redirect()->route('dashboard')
                ->with('error', 'Periode pengisian KRS belum dibuka atau sudah ditutup.');
        }
        
        $request->merge(['periode_krs' => $periodeKrs]);
        return $next($request);
    }
}
```

### 3. Optimasi Performa

#### 3.1 Eager Loading

Menggunakan eager loading untuk mengurangi N+1 query problem:

```php
// Contoh di controller
public function index()
{
    $kelas = Kelas::with(['mataKuliah', 'dosen', 'jadwalKuliah.ruangKuliah'])
        ->where('tahun_ajaran_id', $this->getCurrentTahunAjaran()->id)
        ->get();
        
    return view('kelas.index', compact('kelas'));
}
```

#### 3.2 Caching

Menerapkan caching untuk data yang jarang berubah:

```php
// Contoh caching jadwal kuliah
public function getJadwalDosen($dosenId)
{
    $cacheKey = "jadwal_dosen_{$dosenId}";
    
    return Cache::remember($cacheKey, now()->addHours(12), function () use ($dosenId) {
        return JadwalKuliah::whereHas('kelas', function ($query) use ($dosenId) {
            $query->where('dosen_id', $dosenId);
        })->with(['kelas.mataKuliah', 'ruangKuliah'])
          ->get();
    });
}
```

#### 3.3 Database Indexing

Menambahkan indeks pada kolom yang sering digunakan dalam query:

```php
// Contoh pada migration
Schema::table('krs_details', function (Blueprint $table) {
    $table->index(['krs_mahasiswa_id']);
    $table->index(['kelas_id']);
});
```

### 4. Testing

#### 4.1 Unit Testing

Membuat unit test untuk logika bisnis kompleks:

```php
class KrsServiceTest extends TestCase
{
    public function test_can_submit_krs_within_valid_period()
    {
        // Setup test data
        // ...
        
        // Execute service method
        $result = $this->krsService->submitKrs($mahasiswaId, $periodeId, $kelasIds);
        
        // Assert results
        $this->assertTrue($result);
        $this->assertDatabaseHas('krs_mahasiswas', [
            'mahasiswa_id' => $mahasiswaId,
            'periode_krs_id' => $periodeId,
            'status' => 'submitted'
        ]);
    }
    
    // Test case lainnya
}
```

#### 4.2 Feature Testing

Membuat feature test untuk endpoint API atau halaman web:

```php
class KrsControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_mahasiswa_can_view_own_krs()
    {
        // Setup user dan data test
        $user = User::factory()->create();
        $mahasiswa = Mahasiswa::factory()->create(['user_id' => $user->id]);
        $krs = KrsMahasiswa::factory()->create(['mahasiswa_id' => $mahasiswa->id]);
        
        // Simulasi request
        $response = $this->actingAs($user)
                         ->get(route('krs.show', $krs->id));
        
        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('krs', $krs);
    }
    
    // Test case lainnya
}
```

## Rencana Implementasi

### Sprint 1: Manajemen Kelas & Jadwal (1 Minggu)

**Hari 1-2:**
- Implementasi model dan migrasi untuk `Kelas`, `RuangKuliah`, dan `JadwalKuliah`
- Implementasi repository dan service layer

**Hari 3-4:**
- Implementasi Filament resources untuk manajemen kelas
- Implementasi fitur pembukaan kelas dan validasi

**Hari 5-7:**
- Implementasi fitur penjadwalan dan validasi bentrok
- Implementasi laporan jadwal
- Testing dan bug fixing

### Sprint 2: Kartu Rencana Studi (1 Minggu)

**Hari 1-2:**
- Implementasi model dan migrasi untuk `PeriodeKrs`, `KrsMahasiswa`, dan `KrsDetail`
- Implementasi repository dan service layer

**Hari 3-4:**
- Implementasi antarmuka pengisian KRS untuk mahasiswa
- Implementasi validasi KRS (prasyarat, bentrok, kuota)

**Hari 5-7:**
- Implementasi antarmuka persetujuan KRS untuk dosen
- Implementasi fitur notifikasi
- Testing dan bug fixing

### Sprint 3: Manajemen Nilai (1 Minggu)

**Hari 1-2:**
- Implementasi model dan migrasi untuk `KomponenNilai`, `BorangNilai`, dan `NilaiMahasiswa`
- Implementasi repository dan service layer

**Hari 3-4:**
- Implementasi antarmuka pengaturan borang nilai
- Implementasi antarmuka input nilai

**Hari 5-7:**
- Implementasi perhitungan nilai akhir dan konversi
- Implementasi laporan nilai
- Testing dan bug fixing

### Sprint 4: Integrasi dan Finalisasi (1 Minggu)

**Hari 1-3:**
- Integrasi antar modul
- Implementasi dashboard untuk mahasiswa dan dosen

**Hari 4-5:**
- User acceptance testing (UAT)
- Perbaikan bug dan optimasi

**Hari 6-7:**
- Dokumentasi
- Persiapan deployment

## Kesimpulan

Fase 2 merupakan inti dari sistem akademik yang akan mengimplementasikan proses bisnis utama dalam kegiatan perkuliahan. Dengan implementasi yang baik, fase ini akan menjadi fondasi yang kuat untuk pengembangan fitur-fitur lanjutan pada fase berikutnya.

Rencana pengembangan ini bersifat dinamis dan dapat disesuaikan seiring dengan berjalannya implementasi dan feedback dari pengguna. Prioritas utama adalah memastikan sistem dapat menangani proses akademik dengan efisien, akurat, dan user-friendly.