<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="grid gap-y-2">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Jadwal Mengajar Hari Ini
            </h3>
            @if($total_classes > 0)
                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                    {{ $total_classes }} kelas
                </span>
            @endif
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
                    Anda tidak memiliki jadwal mengajar hari ini.
                </p>
            </div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($schedule as $item)
                    <div class="flex items-start space-x-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item['mata_kuliah'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Kelas {{ $item['kode_kelas'] }} • {{ $item['sks'] }} SKS
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }} • {{ $item['ruang'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Kuota: {{ $item['kuota'] }} | Tersedia: {{ $item['sisa_kuota'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>