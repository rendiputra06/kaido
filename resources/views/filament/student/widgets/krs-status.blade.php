<div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-2">
            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Status KRS
            </h3>
        </div>

        @if($krs['status'] === 'no_data')
            <div class="mt-4 text-center py-8">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Error</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $krs['message'] }}
                </p>
            </div>
        @elseif($krs['status'] === 'no_period')
            <div class="mt-4 text-center py-8">
                <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Periode KRS Tidak Aktif</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $krs['message'] }}
                </p>
            </div>
        @elseif($krs['status'] === 'not_created')
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Periode:</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $krs['period_name'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Status:</span>
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                        Belum Dibuat
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Maksimal SKS:</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $krs['max_sks'] }} SKS</span>
                </div>
                <div class="mt-4">
                    <a href="{{ route('filament.student.resources.krs.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 disabled:opacity-25 transition">
                        Buat KRS Baru
                    </a>
                </div>
            </div>
        @else
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Periode:</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $krs['period_name'] }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Status:</span>
                    @switch($krs['status'])
                        @case('draft')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                Draft
                            </span>
                            @break
                        @case('pending')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                Menunggu Persetujuan
                            </span>
                            @break
                        @case('approved')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                Disetujui
                            </span>
                            @break
                        @case('rejected')
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                Ditolak
                            </span>
                            @break
                    @endswitch
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Total Mata Kuliah:</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $krs['total_mata_kuliah'] }} MK</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Total SKS:</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $krs['total_sks'] }}/{{ $krs['max_sks'] }} SKS</span>
                </div>

                @if($krs['tanggal_submit'])
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Tanggal Submit:</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($krs['tanggal_submit'])->format('d/m/Y H:i') }}</span>
                    </div>
                @endif

                @if($krs['catatan_dosen'])
                    <div class="mt-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Catatan Dosen:</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $krs['catatan_dosen'] }}</p>
                    </div>
                @endif

                <div class="mt-4 flex space-x-2">
                    @if($krs['status'] === 'draft')
                        <a href="{{ route('filament.student.resources.krs.edit', $krs['id'] ?? 0) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 disabled:opacity-25 transition">
                            Edit KRS
                        </a>
                    @else
                        <a href="{{ route('filament.student.resources.krs.view', $krs['id'] ?? 0) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                            Lihat Detail
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>