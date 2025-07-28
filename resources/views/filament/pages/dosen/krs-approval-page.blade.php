<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Statistik KRS --}}
        <x-filament::card>
            <h3 class="text-lg font-medium mb-4">Statistik KRS Mahasiswa Bimbingan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $this->getTotalKrsByStatus('draft') }}</div>
                    <div class="text-sm text-gray-600">Draft</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $this->getTotalKrsByStatus('submitted') }}</div>
                    <div class="text-sm text-gray-600">Menunggu Persetujuan</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $this->getTotalKrsByStatus('approved') }}</div>
                    <div class="text-sm text-gray-600">Disetujui</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $this->getTotalKrsByStatus('rejected') }}</div>
                    <div class="text-sm text-gray-600">Ditolak</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Daftar KRS Mahasiswa --}}
        <x-filament::card>
            <h3 class="text-lg font-medium mb-4">Daftar KRS Mahasiswa Bimbingan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mahasiswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total SKS
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Submit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->krsList as $krs)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $krs['mahasiswa']['nama'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $krs['mahasiswa']['nim'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs['periode_krs']['nama_periode'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs['total_sks'] }} SKS
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        bg-{{ $this->getKrsStatusColor($krs['status']) }}-100 text-{{ $this->getKrsStatusColor($krs['status']) }}-800">
                                        {{ $this->getKrsStatusLabel($krs['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs['tanggal_submit'] ? \Carbon\Carbon::parse($krs['tanggal_submit'])->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="viewKrsDetail({{ $krs['id'] }})" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Detail
                                    </button>
                                    @if($krs['status'] === 'submitted')
                                        <button wire:click="approveKrs({{ $krs['id'] }})" 
                                                class="text-green-600 hover:text-green-900 mr-2">
                                            Setujui
                                        </button>
                                        <button wire:click="rejectKrs({{ $krs['id'] }})" 
                                                class="text-red-600 hover:text-red-900">
                                            Tolak
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada KRS mahasiswa bimbingan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        {{-- Modal Detail KRS --}}
        @if($this->selectedKrs)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modal">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Detail KRS - {{ $this->selectedKrs->mahasiswa->nama }}</h3>
                            <button wire:click="$set('selectedKrs', null)" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Informasi KRS --}}
                        <div class="mb-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Mahasiswa:</span> {{ $this->selectedKrs->mahasiswa->nama }}
                                </div>
                                <div>
                                    <span class="font-medium">NIM:</span> {{ $this->selectedKrs->mahasiswa->nim }}
                                </div>
                                <div>
                                    <span class="font-medium">Periode:</span> {{ $this->selectedKrs->periodeKrs->nama_periode }}
                                </div>
                                <div>
                                    <span class="font-medium">Total SKS:</span> {{ $this->selectedKrs->total_sks }}
                                </div>
                                <div>
                                    <span class="font-medium">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        bg-{{ $this->getKrsStatusColor($this->selectedKrs->status) }}-100 text-{{ $this->getKrsStatusColor($this->selectedKrs->status) }}-800">
                                        {{ $this->getKrsStatusLabel($this->selectedKrs->status) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Tanggal Submit:</span> 
                                    {{ $this->selectedKrs->tanggal_submit ? \Carbon\Carbon::parse($this->selectedKrs->tanggal_submit)->format('d M Y H:i') : '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Daftar Mata Kuliah --}}
                        <div class="mb-4">
                            <h4 class="font-medium mb-2">Mata Kuliah yang Diambil:</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mata Kuliah</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dosen</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKS</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($this->selectedKrs->krsDetails->where('status', 'active') as $detail)
                                            <tr>
                                                <td class="px-3 py-2 text-sm">
                                                    <div class="font-medium text-gray-900">{{ $detail->kelas->mataKuliah->nama_mk }}</div>
                                                    <div class="text-gray-500">{{ $detail->kelas->nama }}</div>
                                                </td>
                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $detail->kelas->dosen->nama }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $detail->sks }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900">
                                                    @foreach($detail->kelas->jadwalKuliahs as $jadwal)
                                                        <div>{{ $jadwal->hari }}, {{ $jadwal->jam_mulai }}-{{ $jadwal->jam_selesai }}</div>
                                                        <div class="text-gray-500">{{ $jadwal->ruangKuliah->nama }}</div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Catatan PA --}}
                        @if($this->selectedKrs->catatan_pa)
                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <h4 class="text-sm font-medium text-yellow-800">Catatan PA:</h4>
                                <p class="text-sm text-yellow-700 mt-1">{{ $this->selectedKrs->catatan_pa }}</p>
                            </div>
                        @endif

                        {{-- Form Catatan dan Aksi --}}
                        @if($this->selectedKrs->status === 'submitted')
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional):</label>
                                <textarea wire:model="catatan" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Berikan catatan untuk mahasiswa..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button wire:click="approveKrs({{ $this->selectedKrs->id }})" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Setujui KRS
                                </button>
                                <button wire:click="rejectKrs({{ $this->selectedKrs->id }})" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Tolak KRS
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page> 