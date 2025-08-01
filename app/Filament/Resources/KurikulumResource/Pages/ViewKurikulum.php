<?php

namespace App\Filament\Resources\KurikulumResource\Pages;

use App\Filament\Resources\KurikulumResource;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\NilaiAkhir;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;

class ViewKurikulum extends ViewRecord
{
    protected static string $resource = KurikulumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Kurikulum')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama_kurikulum')
                                    ->label('Nama Kurikulum'),
                                TextEntry::make('tahun_berlaku')
                                    ->label('Tahun Berlaku'),
                                TextEntry::make('programStudi.nama')
                                    ->label('Program Studi'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'nonaktif' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),
                    
                Section::make('Struktur Kurikulum')
                    ->schema([
                        ViewEntry::make('curriculum_structure')
                            ->label('')
                            ->view('filament.curriculum.structure', array_merge(
                                [
                                    'kurikulum' => $this->record,
                                    'mahasiswaProgress' => $mahasiswaProgress = $this->getMahasiswaProgress(),
                                ],
                                $this->getCurriculumStructureData($this->record, $mahasiswaProgress)
                            )),
                    ]),
            ]);
    }

    /**
     * Get student progress if viewing as a student
     */
    /**
     * Get student progress if viewing as a student
     */
    private function getMahasiswaProgress(): ?Collection
    {
        $user = auth()->user();
        
        if (!$user->hasRole('mahasiswa') || !$user->mahasiswa) {
            return null;
        }

        $mahasiswa = $user->mahasiswa;
        
        // Get all completed courses for this student
        return NilaiAkhir::where('mahasiswa_id', $mahasiswa->id)
            ->where('nilai_huruf', '!=', 'E') // Exclude failed courses
            ->with('krsDetail.kelas.mataKuliah')
            ->get()
            ->pluck('krsDetail.kelas.mataKuliah.id')
            ->unique();
    }

    /**
     * Get mata kuliah grouped by semester for the curriculum
     */
    private function getMataKuliahsBySemester(Kurikulum $kurikulum): Collection
    {
        return $kurikulum->mataKuliahs()
            ->orderBy('kurikulum_matakuliah.semester_ditawarkan')
            ->orderBy('kurikulum_matakuliah.jenis')
            ->get()
            ->groupBy('pivot.semester_ditawarkan');
    }

    /**
     * Get total SKS for the curriculum
     */
    private function getTotalSks(Kurikulum $kurikulum): int
    {
        return $kurikulum->mataKuliahs()->sum('sks');
    }

    /**
     * Get completed SKS for the student
     */
    private function getCompletedSks(Kurikulum $kurikulum, ?Collection $mahasiswaProgress): int
    {
        if (!$mahasiswaProgress) {
            return 0;
        }

        return $kurikulum->mataKuliahs()
            ->whereIn('mata_kuliahs.id', $mahasiswaProgress)
            ->sum('sks');
    }

    /**
     * Get view data for the curriculum structure
     */
    private function getCurriculumStructureData(Kurikulum $kurikulum, ?Collection $mahasiswaProgress): array
    {
        $mataKuliahsBySemester = $this->getMataKuliahsBySemester($kurikulum);
        $totalSks = $this->getTotalSks($kurikulum);
        $completedSks = $this->getCompletedSks($kurikulum, $mahasiswaProgress);

        return [
            'mataKuliahsBySemester' => $mataKuliahsBySemester,
            'totalSks' => $totalSks,
            'completedSks' => $completedSks,
        ];
    }
}
