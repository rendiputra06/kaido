<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Biodata extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Biodata Saya';

    protected static ?string $slug = 'biodata';

    protected static string $view = 'filament.pages.biodata';

    // This will hold the profile data (either a Dosen or Mahasiswa model instance)
    public $profile;

    // This will identify the user type to the view
    public $userType;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->hasRole('dosen')) {
            $this->userType = 'dosen';
            $this->profile = $user->dosen;
        } elseif ($user->hasRole('mahasiswa')) {
            $this->userType = 'mahasiswa';
            $this->profile = $user->mahasiswa;
        } else {
            $this->profile = null;
            $this->userType = null;
        }
    }
}
