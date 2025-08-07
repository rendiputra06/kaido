<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 text-sm text-yellow-800 bg-yellow-50 border-l-4 border-yellow-400 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-600" role="alert">
            <p class="font-bold">Fitur dalam Pengembangan</p>
            <p>Halaman ini masih menggunakan data dummy. Fitur EDOM akan segera terhubung dengan sistem evaluasi yang sesungguhnya.</p>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-800">
            <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                Daftar Mata Kuliah untuk Evaluasi
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Silakan pilih mata kuliah yang ingin Anda evaluasi.
            </p>

            <div class="mt-6 space-y-4">
                @foreach ($dummyCourses as $course)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $course['name'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $course['lecturer'] }}</p>
                        </div>
                        <div>
                            @if ($course['is_evaluated'])
                                <x-filament::button color="gray" disabled icon="heroicon-o-check-circle">
                                    Sudah Dievaluasi
                                </x-filament::button>
                            @else
                                <x-filament::button disabled icon="heroicon-o-pencil-square">
                                    Isi Evaluasi (Segera Hadir)
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Placeholder for the evaluation form modal/section --}}
        <div class="p-6 mt-6 text-center bg-white rounded-xl shadow-md dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Formulir Evaluasi</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Formulir untuk mengisi kuesioner evaluasi akan ditampilkan di sini.
            </p>
            <div class="p-8 mt-4 border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600">
                <p class="text-gray-400">Konten formulir evaluasi...</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
