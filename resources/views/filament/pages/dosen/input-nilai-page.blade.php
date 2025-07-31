<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Dropdown untuk memilih kelas --}}
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>

        {{-- Tampilkan hanya jika kelas sudah dipilih --}}
        @if ($selectedKelasId)
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                                No
                            </th>
                            <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                                NIM
                            </th>
                            <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Nama Mahasiswa
                            </th>
                            {{-- Header dinamis berdasarkan borang nilai --}}
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
                                
                                {{-- Input nilai dinamis --}}
                                @foreach ($borangNilai as $borang)
                                    <td class="whitespace-nowrap px-2 py-2">
                                        <input type="number"
                                               wire:model.defer="nilaiInput.{{ $mahasiswa['id'] }}.{{ $borang['id'] }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 sm:text-sm"
                                               min="0"
                                               max="100"
                                               placeholder="0-100">
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($borangNilai) }}" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Pilih kelas untuk menampilkan mahasiswa atau tidak ada mahasiswa yang mengambil kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tombol Aksi --}}
            @if (!empty($mahasiswaList))
                <div class="flex justify-end space-x-4">
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
