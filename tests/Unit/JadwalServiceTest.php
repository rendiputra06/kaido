<?php

namespace Tests\Unit;

use App\Interfaces\JadwalServiceInterface;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\RuangKuliah;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalServiceTest extends TestCase
{
    use RefreshDatabase;

    private JadwalServiceInterface $jadwalService;
    private Dosen $dosen;
    private RuangKuliah $ruangKuliah;
    private Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        // Ambil service dari container, sesuai dengan best practice
        $this->jadwalService = $this->app->make(JadwalServiceInterface::class);

        // Setup data dummy
        TahunAjaran::factory()->create();
        ProgramStudi::factory()->create();
        MataKuliah::factory()->create();
        $this->dosen = Dosen::factory()->create();
        $this->ruangKuliah = RuangKuliah::factory()->create();
        $this->kelas = Kelas::factory()->create(['dosen_id' => $this->dosen->id]);
    }

    public function test_no_conflict_for_new_schedule(): void
    {
        // Panggil method dari service, bukan repository
        $isConflict = $this->jadwalService->isScheduleConflict(
            ruangKuliahId: $this->ruangKuliah->id,
            dosenId: $this->dosen->id,
            hari: 'Senin',
            jamMulai: '08:00',
            jamSelesai: '10:00'
        );

        $this->assertFalse($isConflict);
    }

    public function test_detects_room_conflict(): void
    {
        JadwalKuliah::factory()->create([
            'kelas_id' => $this->kelas->id,
            'ruang_kuliah_id' => $this->ruangKuliah->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ]);

        $isConflict = $this->jadwalService->isScheduleConflict(
            ruangKuliahId: $this->ruangKuliah->id,
            dosenId: Dosen::factory()->create()->id, // Dosen berbeda
            hari: 'Senin',
            jamMulai: '09:00', // Waktu overlap
            jamSelesai: '11:00'
        );

        $this->assertTrue($isConflict);
    }

    public function test_detects_lecturer_conflict(): void
    {
        JadwalKuliah::factory()->create([
            'kelas_id' => $this->kelas->id,
            'ruang_kuliah_id' => $this->ruangKuliah->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
        ]);

        $isConflict = $this->jadwalService->isScheduleConflict(
            ruangKuliahId: RuangKuliah::factory()->create()->id, // Ruangan berbeda
            dosenId: $this->dosen->id, // Dosen yang sama
            hari: 'Selasa',
            jamMulai: '10:30', // Waktu overlap
            jamSelesai: '11:30'
        );

        $this->assertTrue($isConflict);
    }

    public function test_no_conflict_when_editing_same_schedule(): void
    {
        $existingSchedule = JadwalKuliah::factory()->create([
            'kelas_id' => $this->kelas->id,
            'ruang_kuliah_id' => $this->ruangKuliah->id,
            'hari' => 'Rabu',
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '15:00:00',
        ]);

        $isConflict = $this->jadwalService->isScheduleConflict(
            ruangKuliahId: $this->ruangKuliah->id,
            dosenId: $this->dosen->id,
            hari: 'Rabu',
            jamMulai: '13:00',
            jamSelesai: '15:00',
            exceptJadwalId: $existingSchedule->id // Kecualikan diri sendiri
        );

        $this->assertFalse($isConflict);
    }
}