<x-filament-panels::page>
    {{-- Header --}}
    <x-filament-panels::header
        :heading="__('Input Nilai Mahasiswa')"
        :description="__('Masukkan nilai mahasiswa berdasarkan komponen penilaian yang telah ditentukan')"
    />

    {{-- Form for selecting class --}}
    <div class="p-6 bg-white rounded-lg shadow mb-6">
        {{ $this->form }}
    </div>
    
    {{-- Import Wizard --}}
    @if($selectedKelasId && $importStep === 1)
        <div class="p-6 bg-white rounded-lg shadow mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    Impor Nilai dari Excel
                </h3>
            </div>
            
            <div class="space-y-4">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <input 
                        type="file" 
                        wire:model="importFile"
                        accept=".xlsx,.xls,.csv"
                        class="hidden"
                        id="importFileInput"
                    >
                    <label for="importFileInput" class="cursor-pointer">
                        <x-heroicon-o-arrow-up-tray class="mx-auto h-12 w-12 text-gray-400" />
                        <p class="mt-1 text-sm text-gray-600">
                            <span class="font-medium text-indigo-600 hover:text-indigo-500">
                                Unggah file Excel
                            </span>
                            atau drag and drop
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Format file: .xlsx, .xls, atau .csv (Maks: 2MB)
                        </p>
                    </label>
                    
                    @if($importFile)
                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                            <div class="flex items-center">
                                <x-heroicon-o-document-text class="h-5 w-5 text-gray-400" />
                                <div class="ml-2 text-sm text-gray-700 truncate">
                                    {{ $importFile->getClientOriginalName() }}
                                    <div class="text-xs text-gray-500">
                                        {{ number_format($importFile->getSize() / 1024, 2) }} KB
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    wire:click="$set('importFile', null)"
                                    class="ml-auto text-gray-400 hover:text-gray-600"
                                >
                                    <x-heroicon-o-x-mark class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button
                                type="button"
                                wire:click="importGrades"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                                <span wire:loading.remove>Proses File</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    @endif
                </div>
                
                <div class="text-sm text-gray-500">
                    <p class="font-medium">Petunjuk:</p>
                    <ol class="list-decimal pl-5 space-y-1 mt-1">
                        <li>Unduh template terlebih dahulu untuk memastikan format sesuai</li>
                        <li>Pastikan file Excel memiliki kolom NIM dan kolom untuk setiap komponen nilai</li>
                        <li>Nilai harus berupa angka antara 0-100</li>
                        <li>Simpan file dalam format .xlsx, .xls, atau .csv</li>
                    </ol>
                    <div class="mt-2">
                        <button
                            type="button"
                            wire:click="exportToExcel"
                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
                        >
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 inline-block mr-1" />
                            Unduh Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Import Mapping --}}
    @if($importStep === 2 && !empty($importPreview) && !empty($importMapping))
        <div class="p-6 bg-white rounded-lg shadow mb-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    Konfirmasi Pemetaan Kolom
                </h3>
                <p class="text-sm text-gray-500">
                    Pastikan kolom pada file Excel sudah sesuai dengan field yang diminta
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIM
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Mahasiswa
                            </th>
                            @foreach($nilaiData['komponen_nilai'] as $komponen)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $komponen['kode'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($importPreview as $row)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ isset($importMapping['nim']) && isset($row[$importMapping['nim']]) ? 'text-gray-900' : 'text-gray-400 italic' }}">
                                    {{ isset($importMapping['nim']) && isset($row[$importMapping['nim']]) ? $row[$importMapping['nim']] : '(tidak ada data)' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ isset($importMapping['nama']) && isset($row[$importMapping['nama']]) ? 'text-gray-900' : 'text-gray-400 italic' }}">
                                    {{ isset($importMapping['nama']) && isset($row[$importMapping['nama']]) ? $row[$importMapping['nama']] : '(tidak ada data)' }}
                                </td>
                                @foreach($nilaiData['komponen_nilai'] as $komponen)
                                    @php $colKey = 'nilai_' . $komponen['id']; @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ isset($importMapping[$colKey]) && isset($row[$importMapping[$colKey]]]) ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ isset($importMapping[$colKey]) && isset($row[$importMapping[$colKey]]]) ? $row[$importMapping[$colKey]] : '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button
                    type="button"
                    wire:click="$set('importStep', 1)"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <x-heroicon-o-arrow-uturn-left class="w-4 h-4 mr-2" />
                    Kembali
                </button>
                <button
                    type="button"
                    wire:click="confirmImport"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                    <span wire:loading.remove>Konfirmasi & Impor</span>
                    <span wire:loading>Mengimpor...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Grade input table --}}
    @if($selectedKelasId && !empty($nilaiData['mahasiswas']))
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            Daftar Mahasiswa dan Nilai
                        </h3>
                        @if($selectedKelasId)
                            <p class="text-sm text-gray-500">
                                {{ $kelas->mataKuliah->nama }} - {{ $kelas->nama }}
                            </p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button 
                            wire:click="exportToExcel"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                            Ekspor
                        </button>
                        @if($selectedKelasId && $importStep === 1)
                            <button 
                                wire:click="$set('importStep', 1)"
                                type="button"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-2" />
                                Impor
                            </button>
                        @endif
                        @if($selectedKelasId && count($nilaiData['mahasiswas'] ?? []) > 0)
                            <button 
                                wire:click="finalizeAllGrades"
                                wire:loading.attr="disabled"
                                wire:confirm="Apakah Anda yakin ingin memfinalisasi semua nilai? Tindakan ini tidak dapat dibatalkan."
                                type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                            >
                                <x-heroicon-o-lock-closed class="w-4 h-4 mr-2" />
                                <span wire:loading.remove>Finalisasi Semua Nilai</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIM
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Mahasiswa
                            </th>
                            @foreach($nilaiData['komponen_nilai'] as $komponen)
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex flex-col items-center">
                                        <span>{{ $komponen['kode'] }}</span>
                                        <span class="text-xs text-gray-400">({{ $komponen['bobot'] }}%)</span>
                                        @if(!$komponen['is_locked'])
                                            <button 
                                                wire:click="lockBorangNilai({{ $komponen['id'] }})"
                                                type="button"
                                                class="mt-1 text-gray-400 hover:text-gray-600"
                                                title="Kunci komponen nilai"
                                            >
                                                <x-heroicon-o-lock-open class="w-4 h-4" />
                                            </button>
                                        @else
                                            <span class="mt-1 text-red-500" title="Komponen terkunci">
                                                <x-heroicon-o-lock-closed class="w-4 h-4" />
                                            </span>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($nilaiData['mahasiswas'] as $index => $mahasiswa)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $mahasiswa['nim'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $mahasiswa['nama'] }}
                                </td>
                                
                                @foreach($nilaiData['komponen_nilai'] as $komponen)
                                    @php
                                        $nilai = $mahasiswa['nilai'][$komponen['id']] ?? null;
                                        $isLocked = $komponen['is_locked'];
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center">
                                            @if($isLocked)
                                                <span class="text-sm text-gray-900">
                                                    {{ $nilai['nilai'] ?? '-' }}
                                                </span>
                                            @else
                                                <input 
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    wire:model.debounce.500ms="nilaiData.mahasiswas.{{ $index }}.nilai.{{ $komponen['id'] }}.nilai"
                                                    wire:change="saveNilai({{ $mahasiswa['id'] }}, {{ $komponen['id'] }}, $event.target.value)"
                                                    class="w-20 px-2 py-1 text-sm text-center border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                                    {{ $isLocked ? 'disabled' : '' }}
                                                >
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button 
                                        wire:click="calculateNilaiAkhir({{ $mahasiswa['id'] }})"
                                        type="button"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="Hitung nilai akhir"
                                    >
                                        <x-heroicon-o-calculator class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Total {{ count($nilaiData['mahasiswas'] ?? []) }} mahasiswa
                    </p>
                    <div class="text-xs text-gray-500">
                        <span class="inline-flex items-center">
                            <span class="w-3 h-3 inline-block rounded-full bg-red-500 mr-1"></span>
                            Komponen Terkunci
                        </span>
                        <span class="inline-flex items-center ml-4">
                            <x-heroicon-o-lock-open class="w-4 h-4 text-gray-400 mr-1" />
                            Dapat Diubah
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($selectedKelasId)
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-center">
                <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data mahasiswa</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada mahasiswa yang terdaftar pada kelas ini atau belum melakukan KRS.</p>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-center">
                <x-heroicon-o-academic-cap class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Pilih Kelas</h3>
                <p class="mt-1 text-sm text-gray-500">Silakan pilih kelas untuk menampilkan daftar mahasiswa dan input nilai.</p>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // Auto-resize input fields
        document.addEventListener('input', function(event) {
            if (event.target.tagName === 'INPUT' && event.target.type === 'number') {
                event.target.style.width = Math.min(80, Math.max(40, event.target.value.length * 10)) + 'px';
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
