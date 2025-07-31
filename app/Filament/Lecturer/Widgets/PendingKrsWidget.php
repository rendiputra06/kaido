<?php

namespace App\Filament\Lecturer\Widgets;

use App\Models\KrsMahasiswa;
use App\Models\Dosen;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PendingKrsWidget extends Widget
{
    protected static string $view = 'filament.lecturer.widgets.pending-krs';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getPendingKrs(): array
    {
        $user = Auth::user();
        if (!$user || !$user->dosen) {
            return [];
        }
        
        $dosenId = $user->dosen->id;
        
        // Ambil KRS yang menunggu persetujuan untuk mahasiswa bimbingan
        $pendingKrs = KrsMahasiswa::where('status', 'pending')
            ->whereHas('mahasiswa', function($query) use ($dosenId) {
                $query->where('dosen_pa_id', $dosenId);
            })
            ->with(['mahasiswa.user', 'periodeKrs'])
            ->withCount('krsDetails')
            ->latest('tanggal_submit')
            ->limit(5)
            ->get();
            
        return $pendingKrs->toArray();
    }
    
    public function getTotalPending(): int
    {
        $user = Auth::user();
        if (!$user || !$user->dosen) {
            return 0;
        }
        
        $dosenId = $user->dosen->id;
        
        return KrsMahasiswa::where('status', 'pending')
            ->whereHas('mahasiswa', function($query) use ($dosenId) {
                $query->where('dosen_pa_id', $dosenId);
            })
            ->count();
    }
    
    public function getViewData(): array
    {
        return [
            'pending_krs' => $this->getPendingKrs(),
            'total_pending' => $this->getTotalPending()
        ];
    }
    
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('dosen');
    }
}