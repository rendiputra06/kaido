<?php

namespace App\Filament\Pages\Mahasiswa;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Facades\Auth;

class PresensiPage extends Page
{
    use HasPageShield;

    protected static ?string $permissionName = 'page_presensi';
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static string $view = 'filament.pages.mahasiswa.presensi-page';
    protected static ?string $title = 'Presensi';
    protected static ?string $slug = 'mahasiswa/presensi';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?int $navigationSort = 4;

    public $mahasiswa;
    public $dummyCourses;

    public function mount(): void
    {
        $this->mahasiswa = Auth::user()->mahasiswa;
        $this->loadDummyData();
    }

    public function loadDummyData(): void
    {
        // This is dummy data and should be replaced with real data later.
        $this->dummyCourses = [
            [
                'name' => 'Pemrograman Berbasis Web',
                'lecturer' => 'Dr. Budi Hartono',
                'total_meetings' => 16,
                'attended' => 14,
                'absent' => 2,
                'percentage' => (14 / 16) * 100,
                'details' => [
                    ['date' => '2023-09-05', 'status' => 'Hadir'],
                    ['date' => '2023-09-12', 'status' => 'Hadir'],
                    ['date' => '2023-09-19', 'status' => 'Absen'],
                    ['date' => '2023-09-26', 'status' => 'Hadir'],
                    // ... more dummy details
                ]
            ],
            [
                'name' => 'Basis Data Lanjut',
                'lecturer' => 'Prof. Dr. Retno Wulandari',
                'total_meetings' => 16,
                'attended' => 15,
                'absent' => 1,
                'percentage' => (15 / 16) * 100,
                'details' => [
                    ['date' => '2023-09-06', 'status' => 'Hadir'],
                    ['date' => '2023-09-13', 'status' => 'Hadir'],
                    ['date' => '2023-09-20', 'status' => 'Hadir'],
                    // ... more dummy details
                ]
            ],
        ];
    }
}
