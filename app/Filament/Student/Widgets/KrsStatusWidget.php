<?php

namespace App\Filament\Student\Widgets;

use App\Models\KrsMahasiswa;
use App\Models\PeriodeKrs;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class KrsStatusWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.krs-status';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getKrsStatus(): array
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            return [
                'status' => 'no_data',
                'message' => 'Data mahasiswa tidak ditemukan'
            ];
        }
        
        $mahasiswaId = $user->mahasiswa->id;
        
        // Cek periode KRS aktif
        $activePeriod = PeriodeKrs::where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();
            
        if (!$activePeriod) {
            return [
                'status' => 'no_period',
                'message' => 'Periode KRS belum dibuka atau sudah ditutup'
            ];
        }
        
        // Cari KRS mahasiswa untuk periode aktif
        $krs = KrsMahasiswa::where('mahasiswa_id', $mahasiswaId)
            ->where('periode_krs_id', $activePeriod->id)
            ->withCount('krsDetails')
            ->first();
            
        if (!$krs) {
            return [
                'status' => 'not_created',
                'period_name' => $activePeriod->nama,
                'max_sks' => 24,
                'total_sks' => 0,
                'total_mata_kuliah' => 0
            ];
        }
        
        return [
            'status' => $krs->status,
            'period_name' => $activePeriod->nama,
            'max_sks' => $krs->max_sks,
            'total_sks' => $krs->total_sks,
            'total_mata_kuliah' => $krs->krs_details_count,
            'tanggal_submit' => $krs->tanggal_submit,
            'tanggal_approval' => $krs->tanggal_approval,
            'catatan_dosen' => $krs->catatan_dosen
        ];
    }
    
    public function getViewData(): array
    {
        return [
            'krs' => $this->getKrsStatus()
        ];
    }
    
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('mahasiswa');
    }
}