<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-2">
            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Jadwal Kuliah Hari Ini
            </h3>
        </div>
        
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $today }}
        </div>

        @if(empty($schedule))
            <div class="mt-4 text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada jadwal</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Anda tidak memiliki jadwal kuliah hari ini.
                </p>
            </div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($schedule as $item)
                    <div class="flex items-start space-x-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item['mata_kuliah'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $item['kode_kelas'] }} • {{ $item['dosen'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }} • {{ $item['ruang'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>