<x-filament-panels::page>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;  
            scrollbar-width: none;  
        }
    </style>

    <div class="bg-gray-50 -m-6 p-6 min-h-screen">
        <div class="space-y-6">
            
            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">KRS Workspace</h1>
                        <p class="text-sm text-gray-500">Periode: {{ $this->activePeriod?->nama_periode ?? 'Tidak Aktif' }} ({{ $this->activePeriod ? $this->activePeriod->tgl_mulai->format('d M') . ' - ' . $this->activePeriod->tgl_selesai->format('d M') : 'N/A' }})</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $this->krs?->status->getColor() ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $this->krs?->status->getLabel() ?? 'Belum Ada' }}
                            </span>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Total SKS</p>
                            <p class="text-xl font-bold text-gray-800">{{ $this->krs?->total_sks ?? 0 }}</p>
                        </div>
                        <div>
                            @if($this->krs)
                                <div class="space-y-2">
                                    @if($this->krs->status->value === 'draft')
                                        <button wire:click="submitKrs" class="bg-blue-600 hover:bg-blue-700 text-black font-semibold py-2 px-4 rounded-lg shadow-md transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 hover:scale-105 active:scale-95 active:shadow-inner">
                                            Submit KRS
                                        </button>
                                    @elseif($this->krs->status->value === 'submitted')
                                        <button wire:click="cancelSubmit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-2 px-4 rounded-lg shadow-md transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 hover:scale-105 active:scale-95 active:shadow-inner">
                                            Batalkan Submit
                                        </button>
                                    @elseif($this->krs->status->value === 'rejected')
                                        <button wire:click="submitKrs" class="bg-blue-600 hover:bg-blue-700 text-black font-semibold py-2 px-4 rounded-lg shadow-md transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 hover:scale-105 active:scale-95 active:shadow-inner">
                                            Submit Ulang
                                        </button>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            {{-- Workspace --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Kolom Kiri: Katalog Kelas --}}
                <div class="bg-white rounded-xl shadow-md border border-gray-200 flex flex-col">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Katalog Kelas</h2>
                        <p class="text-sm text-gray-500">Pilih kelas yang akan Anda ambil semester ini.</p>
                        {{-- TODO: Add search input here --}}
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto hide-scrollbar" style="max-height: 60vh;">
                        @forelse($this->availableClasses as $kelas)
                            <div x-data="{ open: false }" class="border rounded-lg transition-all {{ $kelas['is_taken'] ? 'bg-green-50 border-green-200' : 'hover:border-blue-400 hover:shadow-sm' }}">
                                <div class="p-3">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-grow">
                                            <h3 class="font-semibold text-gray-800">{{ $kelas['mata_kuliah'] }}</h3>
                                            <p class="text-xs text-gray-500">{{ $kelas['dosen'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $kelas['sks'] }} SKS - Kuota: {{ $kelas['sisa_kuota'] }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-2">
                                            @if($kelas['is_taken'])
                                                <span class="text-green-600 font-semibold text-sm">Terpilih</span>
                                            @elseif(!$this->activePeriod || ($this->krs && $this->krs->status->value !== 'draft'))
                                                <button disabled class="p-2 bg-gray-200 text-gray-400 rounded-full cursor-not-allowed">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                            @else
                                                <button wire:click="addClass({{ $kelas['id'] }})" class="p-2 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-full transition-transform hover:scale-110">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                            @endif
                                            <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="border-t border-gray-200 bg-gray-50 px-3 py-2">
                                    <div class="text-xs text-gray-600 space-y-1">
                                        @foreach($kelas['jadwal'] as $jadwal)
                                            <p><span class="font-semibold">{{ $jadwal['hari'] }}:</span> {{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }} ({{ $jadwal['ruang'] }})</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-gray-500">Tidak ada kelas tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom Kanan: Rencana Studi Anda --}}
                <div class="bg-white rounded-xl shadow-md border border-gray-200 flex flex-col">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Rencana Studi Anda</h2>
                        <p class="text-sm text-gray-500">Total SKS yang diambil: {{ $this->krs?->total_sks ?? 0 }}</p>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto hide-scrollbar" style="max-height: 60vh;">
                        @if($this->krs && $this->krs->krsDetails->where('status', 'active')->count() > 0)
                            @foreach($this->krs->krsDetails->where('status', 'active') as $detail)
                                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg bg-gray-50">
                                    <div class="p-3">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-grow">
                                                <h3 class="font-semibold text-gray-800">{{ $detail->kelas->mataKuliah->nama_mk }}</h3>
                                                <p class="text-xs text-gray-500">{{ $detail->kelas->dosen->nama }}</p>
                                                <p class="text-xs text-gray-500">{{ $detail->sks }} SKS</p>
                                            </div>
                                            <div class="flex items-center space-x-2 ml-2">
                                                @if($this->krs->status->value === 'draft')
                                                    <button wire:click="removeClass({{ $detail->id }})" class="p-2 bg-red-100 text-red-600 hover:bg-red-200 rounded-full transition-transform hover:scale-110">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @else
                                                    <span class="p-2 text-gray-400">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    </span>
                                                @endif
                                                <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="open" x-collapse class="border-t border-gray-200 bg-white px-3 py-2">
                                        <div class="text-xs text-gray-600 space-y-1">
                                            @foreach($detail->kelas->jadwalKuliahs as $jadwal)
                                                <p><span class="font-semibold">{{ $jadwal->hari }}:</span> {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }} ({{ $jadwal->ruangKuliah->nama_ruang }})</p>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-10 flex flex-col items-center justify-center h-full">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4M4 7s-1 1-1 4m18-4s1 1 1 4"></path></svg>
                                </div>
                                <p class="text-gray-500 font-medium">KRS Anda Kosong</p>
                                <p class="text-sm text-gray-400 mt-1">Pilih kelas dari katalog di sebelah kiri.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-filament-panels::page>
