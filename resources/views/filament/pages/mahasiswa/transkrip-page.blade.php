<x-filament-panels::page>
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-800">
        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Transkrip Nilai Sementara
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Program Studi {{ $this->transcriptData['mahasiswa']?->programStudi?->nama_prodi ?? 'N/A' }}
            </p>
        </div>

        {{-- Student Info --}}
        @if ($this->transcriptData['mahasiswa'])
            <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-4 py-5 mt-6 border border-gray-200 rounded-lg sm:px-6 sm:grid-cols-2 dark:border-gray-700">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Mahasiswa</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $this->transcriptData['mahasiswa']->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NIM</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $this->transcriptData['mahasiswa']->nim }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Angkatan</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $this->transcriptData['mahasiswa']->angkatan }}</dd>
                </div>
            </div>
        @endif

        {{-- GPA Summary --}}
        <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2">
            <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Indeks Prestasi Kumulatif (IPK)</p>
                <p class="text-3xl font-semibold text-primary-600 dark:text-primary-500">
                    {{ number_format($this->transcriptData['ipk'], 2) }}
                </p>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total SKS Ditempuh</p>
                <p class="text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ $this->transcriptData['total_sks'] }}
                </p>
            </div>
        </div>

        {{-- Transcript Details --}}
        <div class="mt-8 space-y-8">
            @forelse ($this->transcriptData['riwayat_semester'] as $semester)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Semester {{ $semester['semester'] }} - {{ $semester['tahun_ajaran'] }}
                    </h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Kode MK</th>
                                    <th scope="col" class="px-6 py-3">Nama Mata Kuliah</th>
                                    <th scope="col" class="px-6 py-3 text-center">SKS</th>
                                    <th scope="col" class="px-6 py-3 text-center">Nilai</th>
                                    <th scope="col" class="px-6 py-3 text-center">Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($semester['mata_kuliah'] as $matkul)
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada data nilai yang tersedia untuk ditampilkan.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
