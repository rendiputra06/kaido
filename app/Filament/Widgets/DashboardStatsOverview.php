<?php

namespace App\Filament\Widgets;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        if ($user->hasRole('admin')) {
            $stats = $this->getAdminStats();
        }

        if ($user->hasRole('dosen')) {
            $stats = $this->getDosenStats($user);
        }

        if ($user->hasRole('mahasiswa')) {
            $stats = $this->getMahasiswaStats($user);
        }

        return $stats;
    }

    protected function getAdminStats(): array
    {
        return [
            Stat::make('Total Mahasiswa', Mahasiswa::count())
                ->description('Jumlah seluruh mahasiswa aktif')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('success'),
            Stat::make('Total Dosen', Dosen::count())
                ->description('Jumlah seluruh dosen pengajar')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),
            Stat::make('Total Program Studi', ProgramStudi::count())
                ->description('Jumlah program studi')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('warning'),
        ];
    }

    protected function getDosenStats($user): array
    {
        $dosen = $user->dosen;
        if (!$dosen) return [];

        // Note: Logic for 'mata_kuliah_diampu' and 'kelas_diajar' depends on your specific database schema.
        // This is a placeholder and might need adjustment.
        $mahasiswaBimbingan = Mahasiswa::where('dosen_pa_id', $dosen->id)->count();

        return [
            Stat::make('Mahasiswa Bimbingan', $mahasiswaBimbingan)
                ->description('Jumlah mahasiswa di bawah bimbingan Anda')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('success'),
            Stat::make('Mata Kuliah Diampu', 'N/A') // Placeholder
                ->description('Jumlah mata kuliah yang Anda ajar semester ini')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('info'),
            Stat::make('Kelas Diajar', 'N/A') // Placeholder
                ->description('Jumlah kelas yang Anda masuki semester ini')
                ->descriptionIcon('heroicon-o-presentation-chart-line')
                ->color('warning'),
        ];
    }

    protected function getMahasiswaStats($user): array
    {
        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) return [];

        // Note: Logic for 'sks_diambil' and 'ipk' depends on your specific database schema.
        // This is a placeholder and might need adjustment.
        return [
            Stat::make('Total SKS Diambil', 'N/A') // Placeholder
                ->description('Jumlah SKS pada semester berjalan')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('success'),
            Stat::make('IPK (Semester Lalu)', 'N/A') // Placeholder
                ->description('Indeks Prestasi Kumulatif terakhir')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('info'),
            Stat::make('Status KRS', 'Disetujui') // Placeholder
                ->description('Status Kartu Rencana Studi Anda')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('primary'),
        ];
    }
}
