<x-filament::page>
    <div class="space-y-6">
        {{-- Progress Summary --}}
        @if($mahasiswaProgress !== null)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Progress Akademik Anda</h3>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $completedSks }}/{{ $totalSks }}</div>
                        <div class="text-sm text-blue-500 dark:text-blue-300">SKS Selesai</div>
                    </div>
                </div>
                
                <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $totalSks > 0 ? ($completedSks / $totalSks) * 100 : 0 }}%"></div>
                </div>
                
                <div class="mt-2 text-sm text-blue-600 dark:text-blue-300">
                    {{ $totalSks > 0 ? round(($completedSks / $totalSks) * 100, 1) : 0 }}% Kurikulum Selesai
                </div>
            </div>
        @endif

    {{-- Semester Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @for($semester = 1; $semester <= 8; $semester++)
            @php
                $semesterMataKuliahs = $mataKuliahsBySemester->get($semester, collect());
                $semesterSks = $semesterMataKuliahs->sum('sks');
            @endphp
            
            <x-semester-card 
                :semester="$semester" 
                :mataKuliahs="$semesterMataKuliahs" 
                :mahasiswaProgress="$mahasiswaProgress"
                :semesterSks="$semesterSks"
            />
        @endfor
    </div>

    {{-- Legend --}}
    @if($mahasiswaProgress !== null)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Keterangan:</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="flex items-center space-x-2">
                    <x-icon.check-circle class="w-4 h-4 text-green-500" />
                    <span class="text-gray-700 dark:text-gray-300">Sudah Lulus</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                    <span class="text-gray-700 dark:text-gray-300">Belum Diambil</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded text-xs font-medium">Wajib</span>
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs font-medium">Pilihan</span>
                </div>
            </div>
        </div>
    @endif
    </div>
</x-filament::page>
