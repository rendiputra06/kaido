<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Status Periode KRS --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium">Status Periode KRS</h3>
                    <p class="text-sm text-gray-600">
                        Periode: {{ $this->activePeriod?->nama_periode ?? 'Tidak ada periode aktif' }}
                    </p>
                    @if($this->activePeriod)
                        <p class="text-sm text-gray-600">
                            {{ $this->activePeriod->tgl_mulai->format('d M Y') }} - {{ $this->activePeriod->tgl_selesai->format('d M Y') }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    @if($this->activePeriod)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Periode Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Periode Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>
        </x-filament::card>

        {{-- Ringkasan KRS --}}
        <x-filament::card>
            <h3 class="text-lg font-medium mb-4">Ringkasan KRS</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $this->totalSks }}</div>
                    <div class="text-sm text-gray-600">Total SKS</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $this->krs?->krsDetails->where('status', 'active')->count() ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Mata Kuliah</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $this->maxSks }}</div>
                    <div class="text-sm text-gray-600">Maksimal SKS</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $this->maxSks - $this->totalSks }}</div>
                    <div class="text-sm text-gray-600">Sisa SKS</div>
                </div>
            </div>
            
            {{-- Status KRS --}}
            <div class="mt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    bg-{{ $this->getKrsStatusColor() }}-100 text-{{ $this->getKrsStatusColor() }}-800">
                    {{ $this->getKrsStatusLabel() }}
                </span>
            </div>

            {{-- Catatan PA jika ada --}}
            @if($this->krs?->catatan_pa)
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <h4 class="text-sm font-medium text-yellow-800">Catatan Dosen PA:</h4>
                    <p class="text-sm text-yellow-700 mt-1">{{ $this->krs->catatan_pa }}</p>
                </div>
            @endif
        </x-filament::card>

        {{-- Daftar Kelas yang Diambil --}}
        @if($this->krs && $this->krs->krsDetails->where('status', 'active')->count() > 0)
            <x-filament::card>
                <h3 class="text-lg font-medium mb-4">Kelas yang Diambil</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Kuliah
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dosen
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKS
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jadwal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->krs->krsDetails->where('status', 'active') as $detail)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $detail->kelas->mataKuliah->nama_mk }}</div>
                                        <div class="text-sm text-gray-500">{{ $detail->kelas->nama }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $detail->kelas->dosen->nama }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $detail->sks }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @foreach($detail->kelas->jadwalKuliahs as $jadwal)
                                            <div>{{ $jadwal->hari }}, {{ $jadwal->jam_mulai }}-{{ $jadwal->jam_selesai }}</div>
                                            <div class="text-gray-500">{{ $jadwal->ruangKuliah->nama }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($this->krs->status === 'draft')
                                            <button wire:click="removeClass({{ $detail->id }})" 
                                                    class="text-red-600 hover:text-red-900">
                                                Batalkan
                                            </button>
                                        @else
                                            <span class="text-gray-400">Tidak dapat diubah</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::card>
        @endif

        {{-- Daftar Kelas Tersedia --}}
        @if($this->activePeriod && $this->krs?->status === 'draft')
            <x-filament::card>
                <h3 class="text-lg font-medium mb-4">Kelas Tersedia</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Kuliah
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dosen
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKS
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sisa Kuota
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jadwal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->availableClasses as $kelas)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $kelas['mata_kuliah'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $kelas['nama'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $kelas['dosen'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $kelas['sks'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $kelas['sisa_kuota'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @foreach($kelas['jadwal'] as $jadwal)
                                            <div>{{ $jadwal['hari'] }}, {{ $jadwal['jam'] }}</div>
                                            <div class="text-gray-500">{{ $jadwal['ruang'] }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($kelas['is_taken'])
                                            <span class="text-green-600">Sudah Diambil</span>
                                        @else
                                            <button wire:click="addClass({{ $kelas['id'] }})" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Ambil
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::card>
        @endif

        {{-- Tombol Submit --}}
        @if($this->krs && $this->krs->status === 'draft' && $this->krs->krsDetails->where('status', 'active')->count() > 0)
            <div class="flex justify-end">
                <button wire:click="submitKrs" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Submit KRS
                </button>
            </div>
        @endif

        {{-- Pesan jika tidak ada kelas tersedia --}}
        @if($this->activePeriod && $this->krs?->status === 'draft' && count($this->availableClasses) === 0)
            <x-filament::card>
                <div class="text-center py-8">
                    <div class="text-gray-500 text-lg">Tidak ada kelas tersedia saat ini</div>
                    <div class="text-gray-400 text-sm mt-2">Silakan cek kembali nanti atau hubungi admin</div>
                </div>
            </x-filament::card>
        @endif
    </div>
</x-filament-panels::page> 