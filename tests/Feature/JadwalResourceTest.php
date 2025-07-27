<?php

namespace Tests\Feature;

use App\Filament\Resources\JadwalKuliahResource;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\RuangKuliah;
use App\Models\TahunAjaran;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Pest\Laravel as Pest;

class JadwalResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Dosen $dosen;
    protected RuangKuliah $ruangKuliah;
    protected Kelas $kelas;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['name' => 'admin', 'email' => 'admin@admin.com']);
        $this->actingAs($this->admin);

        // Setup data dummy
        TahunAjaran::factory()->create();
        ProgramStudi::factory()->create();
        MataKuliah::factory()->create();
        $this->dosen = Dosen::factory()->create();
        $this->ruangKuliah = RuangKuliah::factory()->create(['kapasitas' => 30]);
        $this->kelas = Kelas::factory()->create(['dosen_id' => $this->dosen->id, 'kuota' => 30]);
    }

    public function test_cannot_create_schedule_with_lecturer_conflict()
    {
        // Jadwal yang sudah ada
        JadwalKuliah::factory()->create([
            'kelas_id' => $this->kelas->id,
            'ruang_kuliah_id' => $this->ruangKuliah->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ]);

        // Kelas lain dengan dosen yang sama
        $kelasLain = Kelas::factory()->create(['dosen_id' => $this->dosen->id]);

        Livewire::test(JadwalKuliahResource\Pages\CreateJadwalKuliah::class)
            ->fillForm([
                'kelas_id' => $kelasLain->id,
                'ruang_kuliah_id' => RuangKuliah::factory()->create()->id, // Ruangan berbeda
                'hari' => 'Senin',
                'jam_mulai' => '09:00:00', // Waktu overlap
                'jam_selesai' => '11:00:00',
            ])
            ->call('create')
            ->assertHasFormErrors();
    }

    public function test_cannot_create_schedule_with_room_conflict()
    {
        // Jadwal yang sudah ada
        JadwalKuliah::factory()->create([
            'kelas_id' => $this->kelas->id,
            'ruang_kuliah_id' => $this->ruangKuliah->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
        ]);

        Livewire::test(JadwalKuliahResource\Pages\CreateJadwalKuliah::class)
            ->fillForm([
                'kelas_id' => Kelas::factory()->create()->id, // Kelas berbeda
                'ruang_kuliah_id' => $this->ruangKuliah->id, // Ruangan sama
                'hari' => 'Selasa',
                'jam_mulai' => '11:00:00', // Waktu overlap
                'jam_selesai' => '13:00:00',
            ])
            ->call('create')
            ->assertHasFormErrors();
    }

    public function test_cannot_create_schedule_if_room_capacity_is_insufficient()
    {
        $kelasBesar = Kelas::factory()->create(['kuota' => 40]);
        $ruanganKecil = RuangKuliah::factory()->create(['kapasitas' => 30]);

        Livewire::test(JadwalKuliahResource\Pages\CreateJadwalKuliah::class)
            ->fillForm([
                'kelas_id' => $kelasBesar->id,
                'ruang_kuliah_id' => $ruanganKecil->id,
                'hari' => 'Rabu',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:00:00',
            ])
            ->call('create')
            ->assertHasFormErrors(['ruang_kuliah_id' => 'Kapasitas ruangan (30) tidak mencukupi untuk kuota kelas (40).']);
    }
}
