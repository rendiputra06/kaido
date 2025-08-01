<?php

namespace App\Filament\Widgets;

use App\Models\Kurikulum;
use App\Models\NilaiAkhir;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AcademicProgressWidget extends Widget
{
    protected static string $view = 'filament.widgets.academic-progress';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $user = auth()->user();

        // Only show for students
        if (!$user->hasRole('mahasiswa') || !$user->mahasiswa) {
            return [
                'isStudent' => false,
                'notifications' => collect(),
            ];
        }

        $mahasiswa = $user->mahasiswa;

        // Get student's active curriculum
        $kurikulum = $mahasiswa->programStudi->kurikulums()
            // ->where('status', 'aktif')
            ->first();

        if (!$kurikulum) {
            return [
                'isStudent' => true,
                'notifications' => collect([
                    [
                        'type' => 'warning',
                        'title' => 'Kurikulum Tidak Ditemukan',
                        'message' => 'Kurikulum aktif tidak ditemukan untuk program studi Anda.',
                        'action' => null,
                    ]
                ]),
            ];
        }

        // Get completed courses
        $completedCourses = NilaiAkhir::where('mahasiswa_id', $mahasiswa->id)
            ->where('nilai_huruf', '!=', 'E')
            ->with('krsDetail.kelas.mataKuliah')
            ->get()
            ->pluck('krsDetail.kelas.mataKuliah.id')
            ->unique();

        // Get all curriculum courses grouped by semester
        $allCourses = $kurikulum->mataKuliahs()
            ->get()
            ->groupBy('pivot.semester_ditawarkan');

        $notifications = collect();

        // Check for missing required courses from previous semesters
        $currentSemester = $this->getCurrentSemester($mahasiswa);

        for ($semester = 1; $semester < $currentSemester; $semester++) {
            $semesterCourses = $allCourses->get($semester, collect());
            $requiredCourses = $semesterCourses->where('pivot.jenis', 'wajib');

            $missingRequired = $requiredCourses->whereNotIn('id', $completedCourses);

            if ($missingRequired->count() > 0) {
                $notifications->push([
                    'type' => 'danger',
                    'title' => "Mata Kuliah Wajib Semester {$semester} Belum Diambil",
                    'message' => "Anda memiliki {$missingRequired->count()} mata kuliah wajib yang belum diambil: " .
                        $missingRequired->pluck('nama_mk')->take(3)->implode(', ') .
                        ($missingRequired->count() > 3 ? ', dan lainnya.' : '.'),
                    'action' => [
                        'label' => 'Lihat Detail',
                        'url' => route('filament.admin.resources.kurikulums.view', $kurikulum->id),
                    ],
                ]);
            }
        }

        // Check for courses with unmet prerequisites
        $currentSemesterCourses = $allCourses->get($currentSemester, collect());
        $availableCourses = $currentSemesterCourses->whereNotIn('id', $completedCourses);

        foreach ($availableCourses as $course) {
            if ($course->prasyarats->count() > 0) {
                $unmetPrerequisites = $course->prasyarats->whereNotIn('id', $completedCourses);

                if ($unmetPrerequisites->count() > 0) {
                    $notifications->push([
                        'type' => 'warning',
                        'title' => "Prasyarat Belum Terpenuhi",
                        'message' => "Untuk mengambil '{$course->nama_mk}', Anda perlu menyelesaikan: " .
                            $unmetPrerequisites->pluck('nama_mk')->implode(', '),
                        'action' => null,
                    ]);
                }
            }
        }

        // Progress summary
        $totalSks = $kurikulum->mataKuliahs()->sum('sks');
        $completedSks = $kurikulum->mataKuliahs()
            ->whereIn('mata_kuliahs.id', $completedCourses)
            ->sum('sks');

        // Motivational message based on progress
        $progressPercentage = $totalSks > 0 ? ($completedSks / $totalSks) * 100 : 0;

        if ($progressPercentage >= 75) {
            $notifications->prepend([
                'type' => 'success',
                'title' => 'Hampir Selesai!',
                'message' => "Anda telah menyelesaikan {$progressPercentage}% dari kurikulum. Tetap semangat!",
                'action' => [
                    'label' => 'Lihat Progress',
                    'url' => route('filament.admin.resources.kurikulums.view', $kurikulum->id),
                ],
            ]);
        } elseif ($progressPercentage >= 50) {
            $notifications->prepend([
                'type' => 'info',
                'title' => 'Progress Baik',
                'message' => "Anda telah menyelesaikan {$progressPercentage}% dari kurikulum. Lanjutkan!",
                'action' => null,
            ]);
        }

        return [
            'isStudent' => true,
            'notifications' => $notifications->take(5), // Limit to 5 notifications
            'progressData' => [
                'completed_sks' => $completedSks,
                'total_sks' => $totalSks,
                'percentage' => round($progressPercentage, 1),
            ],
        ];
    }

    /**
     * Estimate current semester based on student's enrollment
     */
    private function getCurrentSemester($mahasiswa): int
    {
        // This is a simplified logic. You might want to implement more sophisticated logic
        // based on when the student first enrolled and current academic period

        // For now, we'll estimate based on completed courses
        $completedSemesters = NilaiAkhir::where('mahasiswa_id', $mahasiswa->id)
            ->where('nilai_huruf', '!=', 'E')
            ->with('krsDetail.kelas.mataKuliah.kurikulums')
            ->get()
            ->flatMap(function ($nilai) {
                return $nilai->krsDetail->kelas->mataKuliah->kurikulums
                    ->pluck('pivot.semester_ditawarkan');
            })
            ->unique()
            ->max();

        return min(($completedSemesters ?? 0) + 1, 8);
    }
}
