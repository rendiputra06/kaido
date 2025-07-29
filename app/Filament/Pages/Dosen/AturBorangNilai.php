<?php

namespace App\Filament\Pages\Dosen;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\KomponenNilai;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\BorangNilai;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class AturBorangNilai extends Page implements HasForms
{
        use HasPageShield;
    use InteractsWithForms;

        protected static string $permissionName = 'page_AturBorangNilai';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.dosen.atur-borang-nilai';
    protected static ?string $title = 'Pengaturan Borang Nilai';
    protected static ?string $slug = 'dosen/atur-borang-nilai';
    protected static ?string $navigationGroup = 'Dosen';
    protected static ?int $navigationSort = 2;

    public $kelasOptions = [];
    public ?int $selectedKelasId = null;
    public $komponenItems = [];
    public $isLocked = false;

    public function mount(): void
    {
        $this->loadKelasOptions();
        $this->form->fill();
    }

    protected function loadKelasOptions(): void
    {
        $dosenId = Auth::user()->dosen->id ?? null;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$dosenId || !$tahunAjaranAktif) {
            $this->kelasOptions = [];
            return;
        }

        $this->kelasOptions = Kelas::where('dosen_id', $dosenId)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->with('mataKuliah')
            ->get()
            ->mapWithKeys(fn($kelas) => [$kelas->id => $kelas->mataKuliah->nama . ' - ' . $kelas->nama])
            ->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedKelasId')
                    ->label('Pilih Kelas')
                    ->options($this->kelasOptions)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->updatedSelectedKelasId($state))
                    ->required(),
                Repeater::make('komponenItems')
                    ->label('Komponen Penilaian')
                    ->schema([
                        Select::make('komponen_nilai_id')
                            ->label('Komponen')
                            ->options(KomponenNilai::where('is_aktif', true)->pluck('nama', 'id'))
                            ->required(),
                        TextInput::make('bobot')
                            ->label('Bobot (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                                        ->addActionLabel('Tambah Komponen')
                    ->visible(fn ($get) => $get('selectedKelasId'))
                    ->disabled($this->isLocked),
            ])->statePath('data');
    }

    public function updatedSelectedKelasId(?int $kelasId): void
    {
        if (!$kelasId) {
            $this->komponenItems = [];
            $this->isLocked = false;
            return;
        }

        $kelas = Kelas::with('borangNilais')->find($kelasId);
        $this->isLocked = $kelas->borangNilais->first()->is_locked ?? false;

        $this->komponenItems = $kelas->borangNilais->map(fn ($borang) => [
            'komponen_nilai_id' => $borang->komponen_nilai_id,
            'bobot' => $borang->bobot,
        ])->toArray();

        // Fill the form with the loaded data
        $this->form->fill(['selectedKelasId' => $kelasId, 'komponenItems' => $this->komponenItems]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action('saveBorang')
                ->visible(fn () => $this->selectedKelasId && !$this->isLocked),
            Action::make('lock')
                ->label('Kunci Borang Nilai')
                ->action('lockBorang')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Kunci Borang Nilai')
                ->modalDescription('Setelah dikunci, komposisi borang nilai tidak dapat diubah. Pastikan total bobot adalah 100%.')
                ->visible(fn () => $this->selectedKelasId && !$this->isLocked),
        ];
    }

    public function saveBorang(): void
    {
        $data = $this->form->getState();
        $totalBobot = collect($data['komponenItems'])->sum('bobot');

        if ($totalBobot != 100) {
            Notification::make()
                ->title('Validasi Gagal')
                ->body('Total bobot semua komponen harus tepat 100%. Saat ini totalnya adalah ' . $totalBobot . '%.')
                ->danger()
                ->send();
            return;
        }

        $dosenId = Auth::user()->dosen->id;
        BorangNilai::where('kelas_id', $data['selectedKelasId'])->delete();

        foreach ($data['komponenItems'] as $item) {
            BorangNilai::create([
                'kelas_id' => $data['selectedKelasId'],
                'komponen_nilai_id' => $item['komponen_nilai_id'],
                'bobot' => $item['bobot'],
                'dosen_id' => $dosenId,
                'is_locked' => false,
            ]);
        }

        Notification::make()
            ->title('Borang Nilai Berhasil Disimpan')
            ->success()
            ->send();
    }

    public function lockBorang(): void
    {
        $this->saveBorang(); // Save first to ensure data is valid and stored

        // Re-check validation before locking
        $data = $this->form->getState();
        $totalBobot = collect($data['komponenItems'])->sum('bobot');
        if ($totalBobot != 100) {
            return; // Notification is already sent from saveBorang
        }

        BorangNilai::where('kelas_id', $data['selectedKelasId'])->update(['is_locked' => true]);
        $this->isLocked = true;

        Notification::make()
            ->title('Borang Nilai Berhasil Dikunci')
            ->body('Komposisi borang nilai tidak dapat diubah lagi.')
            ->success()
            ->send();
    }

}
