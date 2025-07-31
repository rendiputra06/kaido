<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="grid gap-y-2">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                KRS Menunggu Persetujuan
            </h3>
            @if($total_pending > 0)
                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                    {{ $total_pending }}
                </span>
            @endif
        </div>

        @if($total_pending === 0)
            <div class="mt-4 text-center py-8">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada KRS menunggu</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Semua KRS mahasiswa bimbingan Anda sudah diproses.
                </p>
            </div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($pending_krs as $krs)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $krs['mahasiswa']['user']['name'] }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $krs['mahasiswa']['nim'] }} • {{ $krs['krs_details_count'] }} mata kuliah
                                </p>
                                <p class="text-xs text-gray-400">
                                    Diajukan: {{ \Carbon\Carbon::parse($krs['tanggal_submit'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('filament.lecturer.resources.krs-mahasiswas.review', $krs['id']) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 disabled:opacity-25 transition">
                                Review
                            </a>
                        </div>
                    </div>
                @endforeach
                
                @if($total_pending > 5)
                    <div class="text-center mt-4">
                        <a href="{{ route('filament.lecturer.resources.krs-mahasiswas.index', ['tableFilters[status][value]' => 'pending']) }}" 
                           class="text-sm text-primary-600 hover:text-primary-500">
                            Lihat semua KRS menunggu →
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>