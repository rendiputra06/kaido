<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\KrsStatusOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        $user = Auth::user();
        $role = $this->getUserRole($user);

        return [
            // Common widgets for all roles
            KrsStatusOverview::class,
            // Role-specific widgets will be added in the view
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getHeaderWidgetsColumns(): int | string | array
    {
        return 2;
    }

    protected function getUserRole($user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        } elseif ($user->hasRole('dosen')) {
            return 'dosen';
        } else {
            return 'mahasiswa';
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // Add any header actions if needed
        ];
    }
}
