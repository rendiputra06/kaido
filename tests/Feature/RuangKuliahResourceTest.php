<?php

namespace Tests\Feature;

use App\Filament\Resources\RuangKuliahResource\Pages\CreateRuangKuliah;
use App\Filament\Resources\RuangKuliahResource\Pages\EditRuangKuliah;
use App\Filament\Resources\RuangKuliahResource\Pages\ListRuangKuliahs;
use App\Models\RuangKuliah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RuangKuliahResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_render_list_ruang_kuliah_page(): void
    {
        $this->get(ListRuangKuliahs::getUrl())->assertSuccessful();
    }

    public function test_can_list_ruang_kuliah(): void
    {
        $ruangan = RuangKuliah::factory()->count(3)->create();

        Livewire::test(ListRuangKuliahs::class)
            ->assertCanSeeTableRecords($ruangan);
    }

    public function test_can_render_create_ruang_kuliah_page(): void
    {
        $this->get(CreateRuangKuliah::getUrl())->assertSuccessful();
    }

    public function test_can_create_ruang_kuliah(): void
    {
        $newData = [
            'nama' => 'Ruang Teori 1',
            'kode' => 'RT-01',
            'kapasitas' => 50,
        ];

        Livewire::test(CreateRuangKuliah::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('ruang_kuliahs', $newData);
    }

    public function test_validate_create_ruang_kuliah_form(): void
    {
        Livewire::test(CreateRuangKuliah::class)
            ->fillForm([
                'nama' => null,
                'kode' => null,
                'kapasitas' => 'bukan-angka',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'nama' => 'required',
                'kode' => 'required',
                'kapasitas' => 'numeric',
            ]);
    }

    public function test_can_render_edit_ruang_kuliah_page(): void
    {
        $ruangan = RuangKuliah::factory()->create();
        $this->get(EditRuangKuliah::getUrl(['record' => $ruangan->id]))->assertSuccessful();
    }

    public function test_can_retrieve_data_for_edit_form(): void
    {
        $ruangan = RuangKuliah::factory()->create();

        Livewire::test(EditRuangKuliah::class, ['record' => $ruangan->id])
            ->assertFormSet([
                'nama' => $ruangan->nama,
                'kode' => $ruangan->kode,
                'kapasitas' => $ruangan->kapasitas,
            ]);
    }

    public function test_can_update_ruang_kuliah(): void
    {
        $ruangan = RuangKuliah::factory()->create();
        $updatedData = [
            'nama' => 'Ruang Laboratorium Komputer',
            'kode' => 'LAB-KOMP',
            'kapasitas' => 30,
        ];

        Livewire::test(EditRuangKuliah::class, ['record' => $ruangan->id])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('ruang_kuliahs', $updatedData);
    }

    public function test_can_delete_ruang_kuliah(): void
    {
        $ruangan = RuangKuliah::factory()->create();

        Livewire::test(EditRuangKuliah::class, ['record' => $ruangan->id])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertModelMissing($ruangan);
    }
}