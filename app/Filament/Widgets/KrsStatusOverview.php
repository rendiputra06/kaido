<?php

namespace App\Filament\Widgets;

use App\Models\KrsMahasiswa;
use App\Models\PeriodeKrs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class KrsStatusOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        // Ambil periode KRS aktif
        $activePeriod = PeriodeKrs::where('status', 'active')->first();
        
        if (!$activePeriod) {
            return [
                Stat::make('Periode KRS', 'Tidak ada periode aktif')
                    ->description('Tidak ada data KRS untuk ditampilkan')
                    ->color('danger'),
                Stat::make('Total KRS', '0')
                    ->description('Tidak ada periode aktif')
                    ->color('danger'),
                Stat::make('Status KRS', '0%')
                    ->description('Tidak ada periode aktif')
                    ->color('danger'),
            ];
        }
        
        // Hitung jumlah KRS berdasarkan status untuk periode aktif
        $krsStats = KrsMahasiswa::where('periode_krs_id', $activePeriod->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        
        $totalKrs = array_sum($krsStats);
        $draftCount = $krsStats['draft'] ?? 0;
        $submittedCount = $krsStats['submitted'] ?? 0;
        $approvedCount = $krsStats['approved'] ?? 0;
        $rejectedCount = $krsStats['rejected'] ?? 0;
        
        // Hitung persentase KRS yang sudah disubmit
        $submissionRate = $totalKrs > 0 
            ? round((($submittedCount + $approvedCount + $rejectedCount) / $totalKrs) * 100) 
            : 0;
        
        // Hitung persentase KRS yang sudah diapprove
        $approvalRate = $totalKrs > 0 
            ? round(($approvedCount / $totalKrs) * 100) 
            : 0;
        
        return [
            Stat::make('Periode KRS Aktif', $activePeriod->nama)
                ->description('Periode: ' . $activePeriod->tanggal_mulai->format('d M Y') . ' - ' . $activePeriod->tanggal_selesai->format('d M Y'))
                ->color('primary'),
                
            Stat::make('Total KRS', $totalKrs)
                ->description('Draft: ' . $draftCount . ' | Submitted: ' . $submittedCount . ' | Approved: ' . $approvedCount . ' | Rejected: ' . $rejectedCount)
                ->color('gray'),
                
            Stat::make('Tingkat Pengisian', $submissionRate . '%')
                ->description($submissionRate . '% KRS telah disubmit')
                ->color(function() use ($submissionRate) {
                    if ($submissionRate < 30) return 'danger';
                    if ($submissionRate < 70) return 'warning';
                    return 'success';
                }),
                
            Stat::make('Tingkat Persetujuan', $approvalRate . '%')
                ->description($approvalRate . '% KRS telah disetujui')
                ->color(function() use ($approvalRate) {
                    if ($approvalRate < 30) return 'danger';
                    if ($approvalRate < 70) return 'warning';
                    return 'success';
                }),
        ];
    }
}