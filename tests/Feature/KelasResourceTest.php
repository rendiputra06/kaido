<?php

namespace Tests\Feature;

use App\Filament\Resources\KelasResource\Pages\CreateKelas;
use App\Filament\Resources\KelasResource\Pages\EditKelas;
use App\Filament\Resources\KelasResource\Pages\ListKelas;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KelasResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected MataKuliah $mataKuliah;
    protected Dosen $dosen;
    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup user and login
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Setup related data
        ProgramStudi::factory()->create();
        $this->mataKuliah = MataKuliah::factory()->create();
        $this->dosen = Dosen::factory()->create();
        $this->tahunAjaran = TahunAjaran::factory()->create();
    }

    public function test_can_render_list_kelas_page(): void
    {
        $this->get(ListKelas::getUrl())->assertSuccessful();
    }

    public function test_can_list_kelas(): void
    {
        $kelas = Kelas::factory()->count(5)->create();

        Livewire::test(ListKelas::class)
            ->assertCanSeeTableRecords($kelas);
    }

    public function test_can_render_create_kelas_page(): void
    {
        $this->get(CreateKelas::getUrl())->assertSuccessful();
    }

    public function test_can_create_kelas(): void
    {
        $newData = [
            'mata_kuliah_id' => $this->mataKuliah->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'dosen_id' => $this->dosen->id,
            'nama' => 'A-Pagi',
            'kuota' => 35,
        ];

        Livewire::test(CreateKelas::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('kelas', [
            'nama' => 'A-Pagi',
            'kuota' => 35,
            'sisa_kuota' => 35, // Check if sisa_kuota is set correctly
        ]);
    }

    public function test_validate_create_kelas_form(): void
    {
        Livewire::test(CreateKelas::class)
            ->fillForm([
                'nama' => null, // Invalid data
            ])
            ->call('create')
            ->assertHasFormErrors(['nama' => 'required']);
    }

    public function test_can_render_edit_kelas_page(): void
    {
        $kelas = Kelas::factory()->create();
        $this->get(EditKelas::getUrl(['record' => $kelas]))->assertSuccessful();
    }

    public function test_can_retrieve_data_for_edit_kelas_form(): void
    {
        $kelas = Kelas::factory()->create();

        Livewire::test(EditKelas::class, ['record' => $kelas->id])
            ->assertFormSet([
                'nama' => $kelas->nama,
                'kuota' => $kelas->kuota,
            ]);
    }

    public function test_can_update_kelas(): void
    {
        $kelas = Kelas::factory()->create();
        $updatedData = [
            'nama' => 'B-Sore',
            'kuota' => 25,
        ];

        Livewire::test(EditKelas::class, ['record' => $kelas->id])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'nama' => 'B-Sore',
            'kuota' => 25,
        ]);
    }

    public function test_can_delete_kelas(): void
    {
        $kelas = Kelas::factory()->create();

        Livewire::test(EditKelas::class, ['record' => $kelas->id])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertModelMissing($kelas);
    }
}
