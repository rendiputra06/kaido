<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Tampilkan Tahun Ajaran Aktif --}}
        @if ($tahunAjaranAktif)
            <x-filament::section>
                <x-slot name="heading">
                    Tahun Ajaran Aktif: {{ $tahunAjaranAktif->nama_tahun_ajaran }} ({{ $tahunAjaranAktif->semester }})
                </x-slot>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Berikut adalah daftar kelas yang Anda ajar pada tahun ajaran ini. Klik "Pilih Kelas" untuk memulai input nilai.
                </p>
            </x-filament::section>
        @endif

        {{-- Tampilan Kartu Kelas --}}
        @if (!$selectedKelasId)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($kelasList as $kelas)
                    <x-filament::card>
                        <div class="flex flex-col h-full">
                            <div class="flex-grow">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $kelas->mataKuliah->nama_matakuliah }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $kelas->nama }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $kelas->mataKuliah->programStudi->nama_prodi }}</p>
                                <div class="mt-2 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-users class="w-4 h-4" />
                                    <span>{{ $kelas->jumlah_mahasiswa }} Mahasiswa</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-filament::button wire:click="selectKelas({{ $kelas->id }})" class="w-full">
                                    Pilih Kelas
                                </x-filament::button>
                            </div>
                        </div>
                    </x-filament::card>
                @empty
                    <div class="md:col-span-2 lg:col-span-3">
                        <x-filament::section>
                            <div class="text-center">
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Tidak ada kelas yang diajar</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Anda tidak memiliki jadwal mengajar pada tahun ajaran aktif saat ini.
                                </p>
                            </div>
                        </x-filament::section>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Tampilkan tabel input nilai hanya jika kelas sudah dipilih --}}
        @if ($selectedKelasId)
            <div>
                <x-filament::button wire:click="selectKelas(null)" color="secondary" icon="heroicon-o-arrow-left" class="mb-4">
                    Kembali ke Daftar Kelas
                </x-filament::button>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">No</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">NIM</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Nama Mahasiswa</th>
                            @foreach ($borangNilai as $borang)
                                <th scope="col" class="px-4 py-3.5 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $borang['komponen_nilai']['nama'] }} ({{ $borang['bobot'] }}%)
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($mahasiswaList as $index => $mahasiswa)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $mahasiswa['nim'] }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $mahasiswa['nama'] }}</td>
                                @foreach ($borangNilai as $borang)
                                    <td class="whitespace-nowrap px-2 py-2">
                                        <input type="number"
                                               wire:model.defer="nilaiInput.{{ $mahasiswa['id'] }}.{{ $borang['id'] }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 sm:text-sm"
                                               min="0" max="100" placeholder="0-100">
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($borangNilai) }}" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada mahasiswa yang mengambil kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tombol Aksi --}}
            @if (!empty($mahasiswaList))
                <div class="flex justify-end space-x-4 pt-4">
                    <x-filament::button wire:click="saveNilai" color="secondary">
                        Simpan Sementara
                    </x-filament::button>
                    <x-filament::button wire:click="finalizeNilai" color="primary">
                        Hitung & Finalisasi Nilai
                    </x-filament::button>
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
