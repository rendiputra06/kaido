@props(['semester', 'mataKuliahs', 'mahasiswaProgress' => null, 'semesterSks' => 0])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Semester Header --}}
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Semester {{ $semester }}</h4>
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $semesterSks }} SKS</div>
        </div>
        
        @if($mahasiswaProgress !== null && $mataKuliahs->count() > 0)
            @php
                $completedInSemester = $mataKuliahs->whereIn('id', $mahasiswaProgress)->count();
                $totalInSemester = $mataKuliahs->count();
            @endphp
            <div class="mt-2">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <span>Progress</span>
                    <span>{{ $completedInSemester }}/{{ $totalInSemester }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                    <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300" 
                         style="width: {{ ($completedInSemester / $totalInSemester) * 100 }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Course List --}}
    <div class="p-4 space-y-3">
        @forelse($mataKuliahs as $mataKuliah)
            <x-course-item :mataKuliah="$mataKuliah" :mahasiswaProgress="$mahasiswaProgress" />
        @empty
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-sm">Tidak ada mata kuliah</p>
            </div>
        @endforelse
    </div>
</div>
