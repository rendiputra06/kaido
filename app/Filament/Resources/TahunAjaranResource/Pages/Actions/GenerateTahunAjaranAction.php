<?php

namespace App\Filament\Resources\TahunAjaranResource\Pages\Actions;

use App\Models\TahunAjaran;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class GenerateTahunAjaranAction
{
    public static function handleGenerate(array $data)
    {
        $startYear = (int) $data['start_year'];
        $endYear = (int) $data['end_year'];
        $generated = 0;
        $skipped = 0;

        DB::beginTransaction();
        
        try {
            for ($year = $startYear; $year <= $endYear; $year++) {
                // Generate Ganjil (1) and Genap (2) semesters
                foreach (['1' => 'Ganjil', '2' => 'Genap'] as $semesterCode => $semesterName) {
                    $kode = $year . $semesterCode;
                    
                    // Skip if already exists
                    if (TahunAjaran::where('kode', $kode)->exists()) {
                        $skipped++;
                        continue;
                    }
                    
                    // Calculate dates based on semester
                    if ($semesterCode === '1') {
                        // Ganjil: March - July
                        $tglMulai = "$year-03-01";
                        $tglSelesai = "$year-07-31";
                        $tahunAkademik = "$year/" . ($year + 1);
                    } else {
                        // Genap: September - December
                        $tglMulai = "$year-09-01";
                        $tglSelesai = "$year-12-31";
                        $tahunAkademik = ($year - 1) . "/$year";
                    }
                    
                    TahunAjaran::create([
                        'kode' => $kode,
                        'nama' => "Tahun Ajaran $tahunAkademik Semester $semesterName",
                        'semester' => $semesterName,
                        'tahun_akademik' => $tahunAkademik,
                        'tgl_mulai' => $tglMulai,
                        'tgl_selesai' => $tglSelesai,
                        'is_active' => false,
                    ]);
                    
                    $generated++;
                }
            }
            
            DB::commit();
            
            Notification::make()
                ->title('Generate Tahun Ajaran Berhasil')
                ->body("Berhasil menambahkan $generated tahun ajaran baru. " . 
                       ($skipped > 0 ? "$skipped tahun ajaran sudah ada dan dilewati." : ''))
                ->success()
                ->send();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Gagal menambahkan tahun ajaran: ' . $e->getMessage())
                ->danger()
                ->send();
            
            return false;
        }
    }
    
    public static function getYearOptions(): array
    {
        $currentYear = now()->year;
        $years = [];
        
        // Generate 10 years before and after current year
        for ($i = $currentYear - 10; $i <= $currentYear + 10; $i++) {
            $years[$i] = $i;
        }
        
        return $years;
    }
}
