<?php

namespace App\Filament\Student\Widgets;

use App\Models\JadwalKuliah;
use App\Models\KrsMahasiswa;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TodayScheduleWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.today-schedule';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getTodaySchedule(): array
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            return [];
        }
        
        $mahasiswaId = $user->mahasiswa->id;
        
        // Ambil KRS yang sudah disetujui untuk semester aktif
        $activeKrs = KrsMahasiswa::where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'approved')
            ->with(['krsDetails.kelas.jadwalKuliahs' => function($query) {
                $query->where('hari', Carbon::now()->locale('id')->dayName)
                      ->orderBy('jam_mulai');
            }, 'krsDetails.kelas.mataKuliah', 'krsDetails.kelas.dosen'])
            ->latest('created_at')
            ->first();
            
        if (!$activeKrs) {
            return [];
        }
        
        $schedule = [];
        foreach ($activeKrs->krsDetails as $detail) {
            foreach ($detail->kelas->jadwalKuliahs as $jadwal) {
                $schedule[] = [
                    'mata_kuliah' => $detail->kelas->mataKuliah->nama_mk,
                    'kode_kelas' => $detail->kelas->kode_kelas,
                    'dosen' => $detail->kelas->dosen->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruang' => $jadwal->ruangKuliah->nama_ruang,
                    'hari' => $jadwal->hari,
                ];
            }
        }
        
        return $schedule;
    }
    
    public function getViewData(): array
    {
        return [
            'schedule' => $this->getTodaySchedule(),
            'today' => Carbon::now()->locale('id')->dayName . ', ' . Carbon::now()->format('d F Y'),
        ];
    }
}