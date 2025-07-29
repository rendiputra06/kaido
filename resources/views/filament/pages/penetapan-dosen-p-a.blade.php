<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Kolom Kiri: Daftar Dosen --}}
        <div class="lg:col-span-1">
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Daftar Dosen</h2>
                <ul class="space-y-2">
                    @forelse ($dosens as $dosen)
                        <li wire:click="selectDosen({{ $dosen->id }})"
                            class="p-3 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 {{ $selectedDosenId == $dosen->id ? 'bg-primary-500 text-white' : '' }}">
                            <div class="flex items-center justify-between">
                                <span>{{ $dosen->nama }}</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 dark:bg-gray-600">
                                    {{ $dosen->mahasiswa_bimbingan_count }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li>Tidak ada data dosen.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Kolom Kanan: Tabel Mahasiswa --}}
        <div class="lg:col-span-2">
            @if ($selectedDosenId)
                {{-- Tabel Mahasiswa Bimbingan --}}
                <div class="p-4 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-bold">Mahasiswa Bimbingan: {{ $dosens->find($selectedDosenId)->nama }}</h2>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">NIM</th>
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mahasiswaBimbingan as $mhs)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $mhs->nim }}</td>
                                    <td class="px-6 py-4">{{ $mhs->nama }}</td>
                                    <td class="px-6 py-4">
                                        <x-filament::button wire:click="lepaskan({{ $mhs->id }})" color="danger" size="sm">
                                            Lepaskan
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center">Tidak ada mahasiswa bimbingan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Tabel Mahasiswa Tanpa PA --}}
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Mahasiswa Tanpa Dosen PA</h2>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">NIM</th>
                            <th scope="col" class="px-6 py-3">Nama</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswaTanpaPA as $mhs)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $mhs->nim }}</td>
                                <td class="px-6 py-4">{{ $mhs->nama }}</td>
                                <td class="px-6 py-4">
                                    <x-filament::button wire:click="jadikanBimbingan({{ $mhs->id }})" color="success" size="sm" :disabled="!$selectedDosenId">
                                        Jadikan Bimbingan
                                    </x-filament::button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center">Semua mahasiswa sudah memiliki Dosen PA.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
