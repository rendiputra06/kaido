<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Tampilkan Tahun Ajaran Aktif --}}
        @if ($tahunAjaranAktif)
            <x-filament::section :collapsible="true">
                <x-slot name="heading">
                    Tahun Ajaran Aktif: {{ $tahunAjaranAktif->nama }}
                </x-slot>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Silakan pilih kelas dari daftar di bawah untuk memulai proses input nilai.
                </p>
            </x-filament::section>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            {{-- Kolom Kiri: Daftar Kelas --}}
            <div class="md:col-span-4 lg:col-span-3">
                <x-filament::card class="h-full">
                    <x-slot name="header">
                        <h3 class="text-base font-semibold">Kelas yang Diajar</h3>
                    </x-slot>
                    <div class="space-y-3">
                        @forelse ($kelasList as $kelas)
                            <div wire:click="selectKelas({{ $kelas->id }})"
                                 class="p-4 rounded-lg cursor-pointer transition-all border dark:border-gray-700
                                        {{ $selectedKelasId == $kelas->id
                                            ? 'bg-primary-500 text-white shadow-lg'
                                            : 'bg-gray-50 hover:bg-primary-50 hover:ring-2 hover:ring-primary-500 dark:bg-gray-800 dark:hover:bg-gray-700' }}">
                                <p class="font-bold text-md">{{ $kelas->mataKuliah->nama_mk }}</p>
                                <p class="text-sm opacity-90">Kelas : {{ $kelas->nama }}</p>
                                <hr class="my-2 dark:border-gray-600">
                                <div class="space-y-1 text-xs opacity-80">
                                    <p><strong>Kode:</strong> {{ $kelas->mataKuliah->kode_mk }}</p>
                                    <p><strong>SKS:</strong> {{ $kelas->mataKuliah->sks }}</p>
                                    <p><strong>Prodi:</strong> {{ $kelas->mataKuliah->programStudi->nama_prodi }}</p>
                                </div>
                                <div class="text-sm opacity-90 mt-3 flex items-center justify-between bg-black/10 dark:bg-white/10 px-2 py-1 rounded-md">
                                    <span class="font-medium">Mahasiswa</span>
                                    <span class="font-bold">{{ $kelas->jumlah_mahasiswa }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada kelas yang diajar.
                            </div>
                        @endforelse
                    </div>
                </x-filament::card>
            </div>

            {{-- Kolom Kanan: Konten Input Nilai --}}
            <div class="md:col-span-8 lg:col-span-9">
                @if ($selectedKelasId)
                    @php
                        $currentKelas = $kelasList->firstWhere('id', $selectedKelasId);
                    @endphp
                    <x-filament::section>
                        <x-slot name="header">
                             <h3 class="text-base font-semibold">Input Nilai: {{ $currentKelas->mataKuliah->nama_matakuliah }} - {{ $currentKelas->nama }}</h3>
                        </x-slot>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th class="p-2 text-left font-medium">No</th>
                                        <th class="p-2 text-left font-medium">NIM</th>
                                        <th class="p-2 text-left font-medium">Nama Mahasiswa</th>
                                        @foreach ($borangNilai as $borang)
                                            <th class="p-2 text-center font-medium">
                                                {{ $borang['komponen_nilai']['nama'] }}
                                                <span class="block text-xs font-normal">({{ $borang['bobot'] }}%)</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($mahasiswaList as $index => $mahasiswa)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="p-2">{{ $index + 1 }}</td>
                                            <td class="p-2">{{ $mahasiswa['nim'] }}</td>
                                            <td class="p-2 font-medium">{{ $mahasiswa['nama'] }}</td>
                                            @foreach ($borangNilai as $borang)
                                                <td class="p-2 w-28">
                                                    <input type="number"
                                                           wire:model.defer="nilaiInput.{{ $mahasiswa['id'] }}.{{ $borang['id'] }}"
                                                           class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500"
                                                           min="0" max="100" placeholder="0-100">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 3 + count($borangNilai) }}" class="py-6 text-center">
                                                Tidak ada mahasiswa di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (!empty($mahasiswaList))
                            <div class="flex justify-end space-x-4 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-filament::button wire:click="saveNilai" color="secondary">
                                    Simpan Sementara
                                </x-filament::button>
                                <x-filament::button wire:click="finalizeNilai" color="primary">
                                    Hitung & Finalisasi Nilai
                                </x-filament::button>
                            </div>
                        @endif
                    </x-filament::section>
                @else
                    <x-filament::card class="h-full">
                        <div class="flex flex-col items-center justify-center h-full text-center">
                             <x-heroicon-o-document-text class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Selamat Datang di Halaman Input Nilai</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Untuk memulai, silakan pilih salah satu kelas yang Anda ajar dari daftar di sebelah kiri.</p>
                        </div>
                    </x-filament::card>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
