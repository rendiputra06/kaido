<?php

namespace App\Filament\Pages\Mahasiswa;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Facades\Auth;

class EdomPage extends Page
{
    use HasPageShield;

    protected static ?string $permissionName = 'page_edom';
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static string $view = 'filament.pages.mahasiswa.edom-page';
    protected static ?string $title = 'Evaluasi Dosen (EDOM)';
    protected static ?string $slug = 'mahasiswa/edom';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?int $navigationSort = 5;

    public $mahasiswa;
    public $dummyCourses;

    public function mount(): void
    {
        $this->mahasiswa = Auth::user()->mahasiswa;
        $this->loadDummyData();
    }

    public function loadDummyData(): void
    {
        // Dummy data representing courses that can be evaluated.
        $this->dummyCourses = [
            [
                'id' => 1,
                'name' => 'Pemrograman Berbasis Web',
                'lecturer' => 'Dr. Budi Hartono',
                'is_evaluated' => false,
            ],
            [
                'id' => 2,
                'name' => 'Basis Data Lanjut',
                'lecturer' => 'Prof. Dr. Retno Wulandari',
                'is_evaluated' => true,
            ],
            [
                'id' => 3,
                'name' => 'Kecerdasan Buatan',
                'lecturer' => 'Dr. Indah Permatasari',
                'is_evaluated' => false,
            ]
        ];
    }
}
