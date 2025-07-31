<?php

namespace App\Filament\Lecturer\Widgets;

use App\Models\JadwalKuliah;
use App\Models\Kelas;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TeachingScheduleWidget extends Widget
{
    protected static string $view = 'filament.lecturer.widgets.teaching-schedule';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getTodaySchedule(): array
    {
        $user = Auth::user();
        if (!$user || !$user->dosen) {
            return [];
        }
        
        $dosenId = $user->dosen->id;
        
        // Ambil jadwal mengajar hari ini
        $schedules = JadwalKuliah::where('hari', Carbon::now()->locale('id')->dayName)
            ->whereHas('kelas', function($query) use ($dosenId) {
                $query->where('dosen_id', $dosenId);
            })
            ->with(['kelas.mataKuliah', 'kelas', 'ruangKuliah'])
            ->orderBy('jam_mulai')
            ->get();
            
        $schedule = [];
        foreach ($schedules as $jadwal) {
            $schedule[] = [
                'mata_kuliah' => $jadwal->kelas->mataKuliah->nama_mk,
                'kode_kelas' => $jadwal->kelas->kode_kelas,
                'jam_mulai' => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
                'ruang' => $jadwal->ruangKuliah->nama_ruang,
                'hari' => $jadwal->hari,
                'sks' => $jadwal->kelas->mataKuliah->sks,
                'kuota' => $jadwal->kelas->kuota,
                'sisa_kuota' => $jadwal->kelas->sisa_kuota,
            ];
        }
        
        return $schedule;
    }
    
    public function getTotalClassesToday(): int
    {
        $user = Auth::user();
        if (!$user || !$user->dosen) {
            return 0;
        }
        
        $dosenId = $user->dosen->id;
        
        return JadwalKuliah::where('hari', Carbon::now()->locale('id')->dayName)
            ->whereHas('kelas', function($query) use ($dosenId) {
                $query->where('dosen_id', $dosenId);
            })
            ->count();
    }
    
    public function getViewData(): array
    {
        return [
            'schedule' => $this->getTodaySchedule(),
            'total_classes' => $this->getTotalClassesToday(),
            'today' => Carbon::now()->locale('id')->dayName . ', ' . Carbon::now()->format('d F Y'),
        ];
    }
    
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('dosen');
    }
}