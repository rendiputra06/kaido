<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KrsMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeKrs;
use App\Models\User;
use App\Models\JadwalKuliah;
use App\Models\RuangKuliah;
use App\Services\KrsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class KrsSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswaUser;
    private User $dosenUser;
    private Mahasiswa $mahasiswa;
    private Dosen $dosen;
    private PeriodeKrs $periodeKrs;
    private KrsService $krsService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->krsService = app(KrsService::class);
        
        // Setup mahasiswa
        $this->mahasiswaUser = User::factory()->create();
        $this->mahasiswaUser->assignRole('mahasiswa');
        $this->mahasiswa = Mahasiswa::factory()->create([
            'user_id' => $this->mahasiswaUser->id,
            'dosen_pa_id' => null,
            'ipk' => 3.5,
        ]);
        
        // Setup dosen
        $this->dosenUser = User::factory()->create();
        $this->dosenUser->assignRole('dosen');
        $this->dosen = Dosen::factory()->create(['user_id' => $this->dosenUser->id]);
        
        // Update mahasiswa dengan dosen PA
        $this->mahasiswa->update(['dosen_pa_id' => $this->dosen->id]);
        
        // Setup periode KRS aktif
        $this->periodeKrs = PeriodeKrs::factory()->create([
            'is_active' => true,
            'tanggal_mulai' => Carbon::now()->subDays(7),
            'tanggal_selesai' => Carbon::now()->addDays(7),
        ]);
    }

    /** @test */
    public function mahasiswa_can_create_krs()
    {
        $response = $this->actingAs($this->mahasiswaUser)
            ->postJson('/api/krs', [
                'periode_krs_id' => $this->periodeKrs->id,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('krs_mahasiswas', [
            'mahasiswa_id' => $this->mahasiswa->id,
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function mahasiswa_can_add_mata_kuliah_to_krs()
    {
        // Create mata kuliah and kelas
        $mataKuliah = MataKuliah::factory()->create(['sks' => 3]);
        $kelas = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah->id,
            'dosen_id' => $this->dosen->id,
            'kuota' => 30,
            'sisa_kuota' => 30,
        ]);

        // Create jadwal kuliah
        $ruang = RuangKuliah::factory()->create();
        JadwalKuliah::factory()->create([
            'kelas_id' => $kelas->id,
            'ruang_kuliah_id' => $ruang->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ]);

        // Create KRS
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'max_sks' => 24,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->postJson("/api/krs/{$krs->id}/detail", [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('krs_details', [
            'krs_mahasiswa_id' => $krs->id,
            'kelas_id' => $kelas->id,
        ]);
        
        // Kuota tidak berkurang saat draft
        $kelas->refresh();
        $this->assertEquals(30, $kelas->sisa_kuota);
    }

    /** @test */
    public function krs_validation_prevents_exceeding_sks_limit()
    {
        // Create KRS dengan batas SKS rendah
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'max_sks' => 3, // Batas sangat rendah untuk testing
            'status' => 'draft',
        ]);

        // Create mata kuliah 4 SKS
        $mataKuliah = MataKuliah::factory()->create(['sks' => 4]);
        $kelas = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah->id,
            'dosen_id' => $this->dosen->id,
            'kuota' => 30,
            'sisa_kuota' => 30,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->postJson("/api/krs/{$krs->id}/detail", [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kelas_id');
    }

    /** @test */
    public function krs_validation_prevents_schedule_conflict()
    {
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'max_sks' => 24,
            'status' => 'draft',
        ]);

        $ruang = RuangKuliah::factory()->create();
        
        // Create first class
        $mataKuliah1 = MataKuliah::factory()->create(['sks' => 3]);
        $kelas1 = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah1->id,
            'dosen_id' => $this->dosen->id,
            'kuota' => 30,
            'sisa_kuota' => 30,
        ]);
        
        JadwalKuliah::factory()->create([
            'kelas_id' => $kelas1->id,
            'ruang_kuliah_id' => $ruang->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ]);

        // Add first class to KRS
        $this->actingAs($this->mahasiswaUser)
            ->postJson("/api/krs/{$krs->id}/detail", [
                'kelas_id' => $kelas1->id,
            ])->assertStatus(200);

        // Create second class with same schedule
        $mataKuliah2 = MataKuliah::factory()->create(['sks' => 3]);
        $kelas2 = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah2->id,
            'dosen_id' => $this->dosen->id,
            'kuota' => 30,
            'sisa_kuota' => 30,
        ]);
        
        JadwalKuliah::factory()->create([
            'kelas_id' => $kelas2->id,
            'ruang_kuliah_id' => $ruang->id,
            'hari' => 'Senin',
            'jam_mulai' => '09:00:00', // Bentrok dengan kelas 1
            'jam_selesai' => '11:00:00',
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->postJson("/api/krs/{$krs->id}/detail", [
                'kelas_id' => $kelas2->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kelas_id');
    }

    /** @test */
    public function mahasiswa_can_submit_krs_for_approval()
    {
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'max_sks' => 24,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->postJson("/api/krs/{$krs->id}/submit");

        $response->assertStatus(200);
        $krs->refresh();
        $this->assertEquals('pending', $krs->status);
        $this->assertNotNull($krs->tanggal_submit);
    }

    /** @test */
    public function dosen_can_approve_krs()
    {
        $mataKuliah = MataKuliah::factory()->create(['sks' => 3]);
        $kelas = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah->id,
            'dosen_id' => $this->dosen->id,
            'kuota' => 30,
            'sisa_kuota' => 30,
        ]);

        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'status' => 'pending',
            'tanggal_submit' => now(),
        ]);

        // Add detail
        $krs->krsDetails()->create(['kelas_id' => $kelas->id]);

        $response = $this->actingAs($this->dosenUser)
            ->putJson("/api/krs/{$krs->id}/approve", [
                'catatan' => 'KRS disetujui',
            ]);

        $response->assertStatus(200);
        $krs->refresh();
        $kelas->refresh();
        
        $this->assertEquals('approved', $krs->status);
        $this->assertEquals(29, $kelas->sisa_kuota); // Kuota berkurang 1
        $this->assertNotNull($krs->tanggal_approval);
    }

    /** @test */
    public function dosen_can_reject_krs()
    {
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'status' => 'pending',
            'tanggal_submit' => now(),
        ]);

        $response = $this->actingAs($this->dosenUser)
            ->putJson("/api/krs/{$krs->id}/reject", [
                'catatan' => 'KRS ditolak karena SKS melebihi batas',
            ]);

        $response->assertStatus(200);
        $krs->refresh();
        $this->assertEquals('rejected', $krs->status);
        $this->assertEquals('KRS ditolak karena SKS melebihi batas', $krs->catatan_dosen);
    }

    /** @test */
    public function quota_reduction_is_atomic_on_approval()
    {
        $mataKuliah = MataKuliah::factory()->create(['sks' => 3]);
        $kelas = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah->id,
            'dosen_id' => $this->dosen->id,
            'kuota' => 1, // Hanya 1 kuota
            'sisa_kuota' => 1,
        ]);

        $krs1 = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'status' => 'pending',
            'tanggal_submit' => now(),
        ]);
        $krs1->krsDetails()->create(['kelas_id' => $kelas->id]);

        // Create second mahasiswa
        $mahasiswa2 = Mahasiswa::factory()->create([
            'user_id' => User::factory()->create(['role' => 'mahasiswa'])->id,
            'dosen_pa_id' => $this->dosen->id,
        ]);
        
        $krs2 = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $mahasiswa2->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'status' => 'pending',
            'tanggal_submit' => now(),
        ]);
        $krs2->krsDetails()->create(['kelas_id' => $kelas->id]);

        // Approve first KRS
        $this->actingAs($this->dosenUser)
            ->putJson("/api/krs/{$krs1->id}/approve")
            ->assertStatus(200);

        // Try to approve second KRS - should fail
        $response = $this->actingAs($this->dosenUser)
            ->putJson("/api/krs/{$krs2->id}/approve");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Kuota kelas tidak mencukupi']);
    }

    /** @test */
    public function student_dashboard_shows_correct_schedule()
    {
        // Create test data for today's schedule
        $mataKuliah = MataKuliah::factory()->create(['nama_mk' => 'Algoritma']);
        $kelas = Kelas::factory()->create([
            'mata_kuliah_id' => $mataKuliah->id,
            'dosen_id' => $this->dosen->id,
            'kode_kelas' => 'A',
        ]);

        $ruang = RuangKuliah::factory()->create(['nama_ruang' => 'R101']);
        
        $jadwal = JadwalKuliah::factory()->create([
            'kelas_id' => $kelas->id,
            'ruang_kuliah_id' => $ruang->id,
            'hari' => Carbon::now()->locale('id')->dayName,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ]);

        // Create approved KRS
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'status' => 'approved',
        ]);
        $krs->krsDetails()->create(['kelas_id' => $kelas->id]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->getJson('/api/dashboard/schedule/today');

        $response->assertStatus(200);
        $response->assertJson([
            'schedule' => [
                [
                    'mata_kuliah' => 'Algoritma',
                    'kode_kelas' => 'A',
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:00:00',
                    'ruang' => 'R101',
                ]
            ]
        ]);
    }

    /** @test */
    public function lecturer_dashboard_shows_pending_krs()
    {
        // Create KRS pending for this dosen's student
        $krs = KrsMahasiswa::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'periode_krs_id' => $this->periodeKrs->id,
            'status' => 'pending',
            'tanggal_submit' => now(),
        ]);

        $response = $this->actingAs($this->dosenUser)
            ->getJson('/api/dashboard/krs/pending');

        $response->assertStatus(200);
        $response->assertJson([
            'pending_krs' => [
                [
                    'mahasiswa_id' => $this->mahasiswa->id,
                    'status' => 'pending',
                ]
            ],
            'total_pending' => 1
        ]);
    }
}