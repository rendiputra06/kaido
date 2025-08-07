<x-filament-panels::page>
    <div class="space-y-6">
        {{-- GPA Summary --}}
        <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-800">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Indeks Prestasi Kumulatif (IPK)
            </h2>
            <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">IPK</p>
                    <p class="text-3xl font-semibold text-primary-600 dark:text-primary-500">
                        {{ number_format($this->gpa['ipk'], 2) }}
                    </p>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total SKS Ditempuh</p>
                    <p class="text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ $this->gpa['total_sks'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- KHS History per Semester --}}
        <div class="space-y-8">
            @forelse ($this->khsHistory as $khs)
                <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                Semester {{ $khs['semester'] }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tahun Ajaran {{ $khs['tahun_ajaran'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Indeks Prestasi Semester (IPS)</p>
                            <p class="text-2xl font-semibold text-primary-600 dark:text-primary-500">
                                {{ number_format($khs['ips'], 2) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Kode MK</th>
                                    <th scope="col" class="px-6 py-3">Nama Mata Kuliah</th>
                                    <th scope="col" class="px-6 py-3 text-center">SKS</th>
                                    <th scope="col" class="px-6 py-3 text-center">Nilai</th>
                                    <th scope="col" class="px-6 py-3 text-center">Bobot</th>
                                    <th scope="col" class="px-6 py-3 text-center">Mutu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($khs['mata_kuliah'] as $matkul)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $matkul['kode_mk'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $matkul['nama_mk'] }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ $matkul['sks'] }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-semibold">
                                            {{ $matkul['nilai_huruf'] }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ number_format($matkul['bobot'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ number_format($matkul['mutu'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-semibold text-gray-900 dark:text-white">
                                    <th scope="row" colspan="2" class="px-6 py-3 text-base text-right">Total</th>
                                    <td class="px-6 py-3 text-center">{{ $khs['total_sks'] }}</td>
                                    <td class="px-6 py-3"></td>
                                    <td class="px-6 py-3"></td>
                                    <td class="px-6 py-3 text-center">{{ number_format($khs['total_mutu'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center bg-white rounded-xl shadow-md dark:bg-gray-800">
                    <p class="text-gray-500 dark:text-gray-400">
                        Belum ada data Kartu Hasil Studi yang tersedia.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
