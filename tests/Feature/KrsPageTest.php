<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\PeriodeKrs;
use App\Models\KrsMahasiswa;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KrsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup data yang diperlukan
        $this->setupTestData();
    }

    private function setupTestData(): void
    {
        // Buat Program Studi
        $programStudi = ProgramStudi::factory()->create();

        // Buat Tahun Ajaran
        $tahunAjaran = TahunAjaran::factory()->create(['is_active' => true]);

        // Buat Periode KRS aktif
        $periodeKrs = PeriodeKrs::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'status' => 'aktif',
            'tgl_mulai' => now()->subDays(1),
            'tgl_selesai' => now()->addDays(7),
        ]);

        // Buat Mata Kuliah
        $mataKuliah = MataKuliah::factory()->create([
            'program_studi_id' => $programStudi->id,
            'sks' => 3,
        ]);

        // Buat Dosen
        $dosen = Dosen::factory()->create();

        // Buat Kelas
        $kelas = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'dosen_id' => $dosen->id,
            'sisa_kuota' => 10,
            'kuota' => 50,
        ]);

        // Buat User untuk Mahasiswa
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        // Buat Mahasiswa dengan user_id yang sudah terasosiasi
        $mahasiswa = Mahasiswa::factory()->create([
            'program_studi_id' => $programStudi->id,
            'dosen_pa_id' => $dosen->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_mahasiswa_can_access_krs_page(): void
    {
        $user = $this->getMahasiswaUser();

        $response = $this->actingAs($user)
            ->get('/admin/mahasiswa/krs');

        $response->assertStatus(200);
    }

    public function test_dosen_cannot_access_mahasiswa_krs_page(): void
    {
        // Buat user dosen baru untuk memastikan test ini independen
        $user = User::factory()->create();
        $user->assignRole('dosen');

        $dosen = Dosen::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/mahasiswa/krs');

        $response->assertStatus(403);
    }

    public function test_mahasiswa_can_see_available_classes(): void
    {
        $user = $this->getMahasiswaUser();

        $response = $this->actingAs($user)
            ->get('/admin/mahasiswa/krs');

        $response->assertStatus(200);
        $response->assertSee('Kelas Tersedia');
    }

    public function test_mahasiswa_can_add_class_to_krs(): void
    {
        $user = $this->getMahasiswaUser();
        $kelas = Kelas::first();

        $response = $this->actingAs($user)
            ->post('/admin/mahasiswa/krs/add-class', [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertStatus(200);

        // Cek apakah KRS dibuat
        $mahasiswa = $user->mahasiswa;
        $periodeKrs = PeriodeKrs::where('status', 'aktif')->first();

        $krs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode_krs_id', $periodeKrs->id)
            ->first();

        $this->assertNotNull($krs, 'KRS tidak berhasil dibuat');
        $this->assertEquals(1, $krs->krsDetails->count(), 'Detail KRS tidak sesuai');
        $this->assertEquals($kelas->id, $krs->krsDetails->first()->kelas_id, 'Kelas yang ditambahkan tidak sesuai');
    }

    public function test_mahasiswa_can_submit_krs(): void
    {
        $user = $this->getMahasiswaUser();
        $kelas = Kelas::first();

        // Tambah kelas ke KRS
        $this->actingAs($user)
            ->post('/admin/mahasiswa/krs/add-class', [
                'kelas_id' => $kelas->id,
            ]);

        // Submit KRS
        $response = $this->actingAs($user)
            ->post('/admin/mahasiswa/krs/submit');

        $response->assertStatus(200);

        // Cek status KRS
        $mahasiswa = $user->mahasiswa;
        $periodeKrs = PeriodeKrs::where('status', 'aktif')->first();

        $krs = KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->where('periode_krs_id', $periodeKrs->id)
            ->first();

        $this->assertNotNull($krs, 'KRS tidak ditemukan');
        $this->assertEquals('submitted', $krs->status, 'Status KRS tidak berubah menjadi submitted');
    }

    public function test_mahasiswa_cannot_submit_empty_krs(): void
    {
        $user = $this->getMahasiswaUser();

        $response = $this->actingAs($user)
            ->post('/admin/mahasiswa/krs/submit');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['krs_details']);
    }

    public function test_mahasiswa_cannot_access_when_no_active_period(): void
    {
        // Nonaktifkan periode KRS
        PeriodeKrs::where('status', 'aktif')->update(['status' => 'tidak_aktif']);

        $user = $this->getMahasiswaUser();

        $response = $this->actingAs($user)
            ->get('/admin/mahasiswa/krs');

        $response->assertStatus(200);
        $response->assertSee('Periode KRS Tidak Aktif');
    }

    /**
     * Helper method untuk mendapatkan user mahasiswa
     * 
     * @return \App\Models\User
     * @throws \Exception jika tidak ada user mahasiswa
     */
    private function getMahasiswaUser(): User
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'mahasiswa');
        })->whereHas('mahasiswa')->first();

        if (!$user) {
            $this->fail('Tidak ada user mahasiswa yang ditemukan');
        }

        return $user;
    }
}
