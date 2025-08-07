<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 text-sm text-yellow-800 bg-yellow-50 border-l-4 border-yellow-400 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-600" role="alert">
            <p class="font-bold">Fitur dalam Pengembangan</p>
            <p>Halaman ini masih menggunakan data dummy. Fitur presensi akan segera tersedia dengan data yang sesungguhnya.</p>
        </div>

        @foreach ($dummyCourses as $course)
            <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-800">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $course['name'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Dosen: {{ $course['lecturer'] }}</p>

                <div class="mt-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-primary-700 dark:text-white">Kehadiran</span>
                        <span class="text-sm font-medium text-primary-700 dark:text-white">{{ number_format($course['percentage'], 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $course['percentage'] }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 mt-4 text-center sm:grid-cols-3">
                    <div class="p-3 border border-gray-200 rounded-lg dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pertemuan</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $course['total_meetings'] }}</p>
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hadir</p>
                        <p class="text-2xl font-semibold text-green-600">{{ $course['attended'] }}</p>
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Absen</p>
                        <p class="text-2xl font-semibold text-red-600">{{ $course['absent'] }}</p>
                    </div>
                </div>

                {{-- Placeholder for detailed attendance records --}}
                <div class="mt-4 text-sm text-center">
                    <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-500">
                        Lihat Detail Presensi (Segera Hadir)
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
