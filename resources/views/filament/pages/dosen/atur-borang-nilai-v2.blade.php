<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Tampilkan Tahun Ajaran Aktif --}}
        @if ($tahunAjaranAktif)
            <x-filament::section :collapsible="true">
                <x-slot name="heading">
                    Tahun Ajaran Aktif: {{ $tahunAjaranAktif->nama }}
                </x-slot>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pilih kelas dari daftar di sebelah kiri untuk mengatur komposisi penilaian (borang nilai). Pastikan total bobot komponen adalah 100%.
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
                    <div class="space-y-2">
                        @forelse ($kelasList as $kelas)
                            <div wire:click="selectKelas({{ $kelas->id }})"
                                 class="p-3 rounded-lg cursor-pointer transition-all border dark:border-gray-700
                                        {{ $selectedKelasId == $kelas->id
                                            ? 'bg-primary-500 text-white shadow-lg ring-2 ring-primary-600'
                                            : 'bg-gray-50 hover:bg-primary-50 dark:bg-gray-800 dark:hover:bg-gray-700' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-sm">{{ $kelas->mataKuliah->nama_mk }}</p>
                                        <p class="text-xs opacity-90">{{ $kelas->nama }}</p>
                                    </div>
                                    @if($kelas->borang_status === 'Terkunci')
                                        <x-heroicon-s-lock-closed class="w-4 h-4 text-white/70" />
                                    @elseif($kelas->borang_status === 'Terisi')
                                        <x-heroicon-s-check-circle class="w-4 h-4 text-white/70" />
                                    @endif
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

            {{-- Kolom Kanan: Konten Konfigurasi Borang --}}
            <div class="md:col-span-8 lg:col-span-9">
                @if ($selectedKelasId)
                    @php
                        $currentKelas = $kelasList->firstWhere('id', $selectedKelasId);
                    @endphp
                    <x-filament::section>
                        <x-slot name="header">
                             <h3 class="text-base font-semibold">Atur Borang: {{ $currentKelas->mataKuliah->nama_mk }} - {{ $currentKelas->nama }}</h3>
                        </x-slot>

                        @if($isLocked)
                             <div class="p-4 text-yellow-800 bg-yellow-100 border-l-4 border-yellow-500 rounded-md dark:bg-yellow-900/50 dark:text-yellow-300">
                                <p class="font-bold">Borang Nilai Terkunci</p>
                                <p>Komposisi borang nilai untuk kelas ini sudah dikunci dan tidak dapat diubah lagi.</p>
                            </div>
                        @else
                            {{-- Repeater Manual --}}
                            <div class="space-y-4">
                                @foreach ($borang as $index => $item)
                                    <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <div class="flex-1">
                                            <x-filament::input.wrapper>
                                                <x-filament::input.select wire:model="borang.{{ $index }}.komponen_nilai_id">
                                                    <option value="" disabled>Pilih Komponen</option>
                                                    @foreach($komponenOptions as $id => $nama)
                                                        <option value="{{ $id }}">{{ $nama }}</option>
                                                    @endforeach
                                                </x-filament::input.select>
                                            </x-filament::input.wrapper>
                                        </div>
                                        <div class="w-40">
                                            <x-filament::input.wrapper>
                                                <x-filament::input
                                                    type="number"
                                                    wire:model.live="borang.{{ $index }}.bobot"
                                                    placeholder="Bobot %"
                                                />
                                            </x-filament::input.wrapper>
                                        </div>
                                        <div>
                                            <x-filament::icon-button
                                                icon="heroicon-o-trash"
                                                color="danger"
                                                wire:click="removeBorangItem({{ $index }})"
                                                tooltip="Hapus Komponen"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <x-filament::button wire:click="addBorangItem" icon="heroicon-o-plus" outlined>
                                    Tambah Komponen
                                </x-filament::button>
                                <div @class([
                                    'px-4 py-2 rounded-lg font-bold text-lg',
                                    'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400' => $totalBobot == 100,
                                    'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-400' => $totalBobot != 100,
                                ])>
                                    Total: {{ $totalBobot }}%
                                </div>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="flex justify-end space-x-4 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                                <x-filament::button
                                    wire:click="saveBorang"
                                    :disabled="$totalBobot != 100">
                                    Simpan Borang
                                </x-filament::button>
                                <x-filament::button
                                    wire:click="saveAndLockBorang"
                                    color="danger"
                                    :disabled="$totalBobot != 100"
                                    icon="heroicon-s-lock-closed"
                                    onclick="return confirm('Anda yakin ingin menyimpan dan mengunci borang ini? Setelah dikunci, komposisi tidak dapat diubah lagi.')">
                                    Simpan & Kunci
                                </x-filament::button>
                            </div>
                        @endif
                    </x-filament::section>
                @else
                    <x-filament::card class="h-full">
                        <div class="flex flex-col items-center justify-center h-full text-center">
                             <x-heroicon-o-document-chart-bar class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Atur Borang Nilai</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih kelas dari daftar di sebelah kiri untuk mengatur komposisi dan bobot penilaian.</p>
                        </div>
                    </x-filament::card>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
