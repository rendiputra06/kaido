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
    
    public function test_cannot_add_course_with_unmet_prerequisites(): void
    {
        // Buat KRS
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );
        
        // Buat mata kuliah prasyarat
        $programStudi = ProgramStudi::first();
        $prasyarat = MataKuliah::factory()->create([
            'program_studi_id' => $programStudi->id,
            'kode_mk' => 'MK001',
            'nama_mk' => 'Mata Kuliah Prasyarat',
            'sks' => 3,
            'semester' => 1
        ]);
        
        // Buat mata kuliah lanjutan dengan prasyarat
        $mataKuliahLanjutan = MataKuliah::factory()->create([
            'program_studi_id' => $programStudi->id,
            'kode_mk' => 'MK002',
            'nama_mk' => 'Mata Kuliah Lanjutan',
            'sks' => 3,
            'semester' => 2
        ]);
        
        // Tambahkan relasi prasyarat
        $mataKuliahLanjutan->prasyarats()->attach($prasyarat->id);
        
        // Buat kelas untuk mata kuliah lanjutan
        $tahunAjaran = TahunAjaran::factory()->create();
        $dosen = Dosen::factory()->create();
        $kelasLanjutan = Kelas::factory()->create([
            'nama' => 'Kelas Lanjutan',
            'kuota' => 30,
            'sisa_kuota' => 30,
            'mata_kuliah_id' => $mataKuliahLanjutan->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'dosen_id' => $dosen->id
        ]);
        
        // Coba tambahkan mata kuliah lanjutan ke KRS
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Anda belum lulus mata kuliah prasyarat: Mata Kuliah Prasyarat');
        
        $this->krsService->addMataKuliah($krs->id, $kelasLanjutan->id);
    }
    
    public function test_can_add_course_with_met_prerequisites(): void
    {
        // Buat KRS
        $krs = $this->krsService->createKrs(
            $this->mahasiswa->id,
            $this->periodeKrs->id,
            $this->dosenPa->id
        );
        
        // Buat mata kuliah prasyarat
        $programStudi = ProgramStudi::first();
        $prasyarat = MataKuliah::factory()->create([
            'program_studi_id' => $programStudi->id,
            'kode_mk' => 'MK001',
            'nama_mk' => 'Mata Kuliah Prasyarat',
            'sks' => 3,
            'semester' => 1
        ]);
        
        // Buat mata kuliah lanjutan dengan prasyarat
        $mataKuliahLanjutan = MataKuliah::factory()->create([
            'program_studi_id' => $programStudi->id,
            'kode_mk' => 'MK002',
            'nama_mk' => 'Mata Kuliah Lanjutan',
            'sks' => 3,
            'semester' => 2
        ]);
        
        // Tambahkan relasi prasyarat
        $mataKuliahLanjutan->prasyarats()->attach($prasyarat->id);
        
        // Buat kelas untuk mata kuliah prasyarat dan lanjutan
        $tahunAjaran = TahunAjaran::factory()->create();
        $dosen = Dosen::factory()->create();
        
        $kelasPrereq = Kelas::factory()->create([
            'nama' => 'Kelas Prasyarat',
            'kuota' => 30,
            'sisa_kuota' => 30,
            'mata_kuliah_id' => $prasyarat->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'dosen_id' => $dosen->id
        ]);
        
        $kelasLanjutan = Kelas::factory()->create([
            'nama' => 'Kelas Lanjutan',
            'kuota' => 30,
            'sisa_kuota' => 30,
            'mata_kuliah_id' => $mataKuliahLanjutan->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'dosen_id' => $dosen->id
        ]);
        
        // Buat KRS detail untuk mata kuliah prasyarat
        $krsDetail = \App\Models\KrsDetail::factory()->create([
            'krs_mahasiswa_id' => $krs->id,
            'kelas_id' => $kelasPrereq->id,
            'sks' => $prasyarat->sks,
            'status' => 'active'
        ]);
        
        // Buat nilai lulus untuk mata kuliah prasyarat
        \App\Models\NilaiAkhir::create([
            'mahasiswa_id' => $this->mahasiswa->id,
            'krs_detail_id' => $krsDetail->id,
            'nilai_angka' => 80.0,
            'nilai_huruf' => 'A',
            'bobot' => 4.0
        ]);
        
        // Coba tambahkan mata kuliah lanjutan ke KRS
        $result = $this->krsService->addMataKuliah($krs->id, $kelasLanjutan->id);
        
        // Pastikan berhasil ditambahkan
        $this->assertInstanceOf(\App\Models\KrsDetail::class, $result);
        $this->assertEquals($kelasLanjutan->id, $result->kelas_id);
        $this->assertEquals('active', $result->status);
    }
}
