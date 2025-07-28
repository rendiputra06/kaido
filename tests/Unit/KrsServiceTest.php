<?php

namespace Tests\Unit;

use App\Interfaces\KrsRepositoryInterface;
use App\Interfaces\PeriodeKrsRepositoryInterface;
use App\Interfaces\JadwalServiceInterface;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KrsMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PeriodeKrs;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Services\KrsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KrsServiceTest extends TestCase
{
    use RefreshDatabase;

    private KrsService $krsService;
    private Mahasiswa $mahasiswa;
    private PeriodeKrs $periodeKrs;
    private Dosen $dosenPa;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup data dummy
        ProgramStudi::factory()->create();
        $this->mahasiswa = Mahasiswa::factory()->create();
        $this->dosenPa = Dosen::factory()->create();
        $this->periodeKrs = PeriodeKrs::factory()->aktif()->create();

        // Mock repositories
        $this->krsService = new KrsService(
            $this->app->make(KrsRepositoryInterface::class),
            $this->app->make(PeriodeKrsRepositoryInterface::class),
            $this->app->make(JadwalServiceInterface::class)
        );
    }

    public function test_can_create_krs(): void
    {
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );

        $this->assertInstanceOf(KrsMahasiswa::class, $krs);
        $this->assertEquals($this->mahasiswa->id, $krs->mahasiswa_id);
        $this->assertEquals($this->periodeKrs->id, $krs->periode_krs_id);
        $this->assertEquals($this->dosenPa->id, $krs->dosen_pa_id);
        $this->assertEquals('draft', $krs->status);
        $this->assertEquals(0, $krs->total_sks);
    }

    public function test_cannot_create_krs_when_period_not_active(): void
    {
        $inactivePeriod = PeriodeKrs::factory()->tidakAktif()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Periode KRS tidak aktif atau tidak ditemukan');

        $this->krsService->createKrs(
            $this->mahasiswa->id,
            $inactivePeriod->id,
            $this->dosenPa->id
        );
    }

    public function test_cannot_create_duplicate_krs(): void
    {
        // Buat KRS pertama
        $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );

        // Coba buat KRS kedua
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Mahasiswa sudah memiliki KRS di periode ini');

        $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );
    }

    public function test_can_submit_krs(): void
    {
        // Buat KRS
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );

        // Tambahkan mata kuliah
        $kelas = Kelas::factory()->create();
        $this->krsService->addMataKuliah($krs->id, $kelas->id);

        // Submit KRS
        $submittedKrs = $this->krsService->submitKrs($krs->id);

        $this->assertEquals('submitted', $submittedKrs->status);
        $this->assertNotNull($submittedKrs->tanggal_submit);
    }

    public function test_cannot_submit_empty_krs(): void
    {
        // Buat KRS tanpa mata kuliah
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('KRS harus memiliki minimal satu mata kuliah');

        $this->krsService->submitKrs($krs->id);
    }

    public function test_can_approve_krs(): void
    {
        // Buat dan submit KRS
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );

        $kelas = Kelas::factory()->create();
        $this->krsService->addMataKuliah($krs->id, $kelas->id);
        $this->krsService->submitKrs($krs->id);

        // Approve KRS
        $approvedKrs = $this->krsService->approveKrs($krs->id, 'KRS disetujui');

        $this->assertEquals('approved', $approvedKrs->status);
        $this->assertNotNull($approvedKrs->tanggal_approval);
        $this->assertEquals('KRS disetujui', $approvedKrs->catatan_pa);
    }

    public function test_can_reject_krs(): void
    {
        // Buat dan submit KRS
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );

        $kelas = Kelas::factory()->create();
        $this->krsService->addMataKuliah($krs->id, $kelas->id);
        $this->krsService->submitKrs($krs->id);

        // Reject KRS
        $rejectedKrs = $this->krsService->rejectKrs($krs->id, 'KRS perlu perbaikan');

        $this->assertEquals('rejected', $rejectedKrs->status);
        $this->assertNotNull($rejectedKrs->tanggal_approval);
        $this->assertEquals('KRS perlu perbaikan', $rejectedKrs->catatan_pa);
    }
}
