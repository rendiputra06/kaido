<?php

namespace App\Filament\Widgets;

use App\Models\KrsMahasiswa;
use App\Models\PeriodeKrs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class KrsStatusOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    /**
     * Get statistics overview for KRS status
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $activePeriod = $this->getActivePeriod();
        
        if (!$activePeriod) {
            return $this->getNoActivePeriodStats();
        }
        
        $krsStats = $this->getKrsStats($activePeriod->id);
        
        if (empty($krsStats)) {
            return $this->getNoKrsDataStats($activePeriod);
        }
        
        return $this->generateStats($activePeriod, $krsStats);
    }
    
    /**
     * Get active KRS period
     */
    private function getActivePeriod(): ?PeriodeKrs
    {
        return PeriodeKrs::where('status', 'aktif')->first();
    }
    
    /**
     * Get statistics when there's no active period
     */
    private function getNoActivePeriodStats(): array
    {
        $message = 'Tidak ada periode aktif';
        
        return [
            Stat::make('Periode KRS', $message)
                ->description('Tidak ada data KRS untuk ditampilkan')
                ->color('danger'),
            Stat::make('Total KRS', '0')
                ->description($message)
                ->color('danger'),
            Stat::make('Status KRS', '0%')
                ->description($message)
                ->color('danger'),
        ];
    }
    
    /**
     * Get statistics when there's no KRS data
     */
    private function getNoKrsDataStats(PeriodeKrs $activePeriod): array
    {
        $periodRange = $this->formatDateRange(
            $activePeriod->tanggal_mulai, 
            $activePeriod->tanggal_selesai
        );
        
        return [
            Stat::make('Periode KRS Aktif', $activePeriod->nama)
                ->description('Periode: ' . $periodRange)
                ->color('primary'),
            Stat::make('Total KRS', '0')
                ->description('Belum ada data KRS')
                ->color('warning'),
            Stat::make('Status KRS', '0%')
                ->description('Belum ada data KRS')
                ->color('warning')
        ];
    }
    
    /**
     * Get KRS statistics grouped by status
     */
    private function getKrsStats(int $periodeId): array
    {
        return KrsMahasiswa::where('periode_krs_id', $periodeId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
    
    /**
     * Generate statistics array
     */
    private function generateStats(PeriodeKrs $activePeriod, array $krsStats): array
    {
        $totalKrs = array_sum($krsStats);
        
        // Initialize all possible statuses with 0
        $statusCounts = array_merge(
            ['draft' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0],
            $krsStats
        );
        
        $submissionRate = $this->calculateSubmissionRate($statusCounts, $totalKrs);
        $approvalRate = $this->calculateApprovalRate($statusCounts['approved'], $totalKrs);
        
        $periodRange = $this->formatDateRange(
            $activePeriod->tgl_mulai, 
            $activePeriod->tgl_selesai
        );
        
        return [
            $this->createPeriodStat($activePeriod->nama_periode ?? 'Periode Tanpa Nama', $periodRange),
            $this->createTotalKrsStat($statusCounts, $totalKrs),
            $this->createSubmissionRateStat($submissionRate),
            $this->createApprovalRateStat($approvalRate),
        ];
    }
    
    /**
     * Format date range for display
     */
    private function formatDateRange(?Carbon $startDate, ?Carbon $endDate): string
    {
        $start = $startDate ? $startDate->format('d M Y') : 'N/A';
        $end = $endDate ? $endDate->format('d M Y') : 'N/A';
        
        return sprintf('%s - %s', $start, $end);
    }
    
    /**
     * Calculate submission rate
     */
    private function calculateSubmissionRate(array $statusCounts, int $totalKrs): int
    {
        if ($totalKrs === 0) {
            return 0;
        }
        
        $submittedTotal = $statusCounts['submitted'] + 
                         $statusCounts['approved'] + 
                         $statusCounts['rejected'];
        
        return (int) round(($submittedTotal / $totalKrs) * 100);
    }
    
    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate(int $approvedCount, int $totalKrs): int
    {
        return $totalKrs > 0 ? (int) round(($approvedCount / $totalKrs) * 100) : 0;
    }
    
    /**
     * Create period stat card
     */
    private function createPeriodStat(string $periodName, string $dateRange): Stat
    {
        return Stat::make('Periode KRS Aktif', $periodName)
            ->description('Periode: ' . $dateRange)
            ->color('primary');
    }
    
    /**
     * Create total KRS stat card
     */
    private function createTotalKrsStat(array $statusCounts, int $totalKrs): Stat
    {
        $description = sprintf(
            'Draft: %d | Submitted: %d | Approved: %d | Rejected: %d',
            $statusCounts['draft'],
            $statusCounts['submitted'],
            $statusCounts['approved'],
            $statusCounts['rejected']
        );
        
        return Stat::make('Total KRS', (string) $totalKrs)
            ->description($description)
            ->color('gray');
    }
    
    /**
     * Create submission rate stat card
     */
    private function createSubmissionRateStat(int $rate): Stat
    {
        return Stat::make('Tingkat Pengisian', $rate . '%')
            ->description($rate . '% KRS telah disubmit')
            ->color($this->getRateColor($rate));
    }
    
    /**
     * Create approval rate stat card
     */
    private function createApprovalRateStat(int $rate): Stat
    {
        return Stat::make('Tingkat Persetujuan', $rate . '%')
            ->description($rate . '% KRS telah disetujui')
            ->color($this->getRateColor($rate));
    }
    
    /**
     * Get color based on rate value
     */
    private function getRateColor(int $rate): string
    {
        if ($rate < 30) return 'danger';
        if ($rate < 70) return 'warning';
        return 'success';
    }
}