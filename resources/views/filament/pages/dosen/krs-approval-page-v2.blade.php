<x-filament-panels::page>
    <div class="space-y-6" x-data="{ statusFilter: '' }">
        
        {{-- Header dengan Statistik KRS --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard KRS Mahasiswa Bimbingan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200 p-6 text-center hover:shadow-md transition-shadow">
                    <div class="absolute top-2 right-2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-700 mb-1">
                        {{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::DRAFT) }}
                    </div>
                    <div class="text-sm font-medium text-gray-600">Draft</div>
                    <div class="text-xs text-gray-500 mt-1">Belum disubmit</div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200 p-6 text-center hover:shadow-md transition-shadow">
                    <div class="absolute top-2 right-2">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-yellow-700 mb-1">
                        {{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::SUBMITTED) }}
                    </div>
                    <div class="text-sm font-medium text-yellow-700">Menunggu</div>
                    <div class="text-xs text-yellow-600 mt-1">Perlu persetujuan</div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 p-6 text-center hover:shadow-md transition-shadow">
                    <div class="absolute top-2 right-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-green-700 mb-1">
                        {{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::APPROVED) }}
                    </div>
                    <div class="text-sm font-medium text-green-700">Disetujui</div>
                    <div class="text-xs text-green-600 mt-1">Sudah selesai</div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-red-50 to-red-100 rounded-lg border border-red-200 p-6 text-center hover:shadow-md transition-shadow">
                    <div class="absolute top-2 right-2">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-red-700 mb-1">
                        {{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::REJECTED) }}
                    </div>
                    <div class="text-sm font-medium text-red-700">Ditolak</div>
                    <div class="text-xs text-red-600 mt-1">Perlu revisi</div>
                </div>
            </div>
        </div>

        {{-- Daftar KRS Mahasiswa --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Daftar KRS Mahasiswa</h3>
                    <p class="text-sm text-gray-600 mt-1">Kelola persetujuan KRS mahasiswa bimbingan Anda</p>
                </div>
                
                {{-- Filter Status --}}
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Filter:</label>
                    <select x-model="statusFilter" class="border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Menunggu</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>

            {{-- Cards Layout untuk Mobile/Tablet --}}
            <div class="block lg:hidden space-y-4">
                @forelse($this->krsList as $krs)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow"
                         x-show="statusFilter === '' || statusFilter === '{{ $krs->status->value }}'">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $krs->mahasiswa->nama ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-600">{{ $krs->mahasiswa->nim ?? 'N/A' }}</p>
                            </div>
                            <span @class([
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                                'bg-gray-100 text-gray-800' => $krs->status->getColor() === 'gray',
                                'bg-yellow-100 text-yellow-800' => $krs->status->getColor() === 'yellow',
                                'bg-green-100 text-green-800' => $krs->status->getColor() === 'green',
                                'bg-red-100 text-red-800' => $krs->status->getColor() === 'red',
                            ])>
                                {{ $krs->status->getLabel() }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                            <div>
                                <span class="text-gray-600">Periode:</span>
                                <div class="font-medium">{{ $krs->periodeKrs->nama_periode ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Total SKS:</span>
                                <div class="font-medium">{{ $krs->total_sks }} SKS</div>
                            </div>
                        </div>
                        
                        <div class="text-sm mb-4">
                            <span class="text-gray-600">Tanggal Submit:</span>
                            <div class="font-medium">{{ $krs->tanggal_submit ? $krs->tanggal_submit->format('d M Y H:i') : '-' }}</div>
                        </div>
                        
                        <div class="flex justify-end">
                            <a href="{{ \App\Filament\Pages\Dosen\KrsDetailPage::getUrl(['record' => $krs->id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail & Edit
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada KRS</h3>
                        <p class="text-gray-600">Belum ada KRS mahasiswa bimbingan yang perlu ditinjau.</p>
                    </div>
                @endforelse
            </div>

            {{-- Table Layout untuk Desktop --}}
            <div class="hidden lg:block overflow-x-auto">
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
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-show="statusFilter === '' || statusFilter === '{{ $krs->status->value }}'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ substr($krs->mahasiswa->nama ?? 'N', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $krs->mahasiswa->nama ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $krs->mahasiswa->nim ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs->periodeKrs->nama_periode ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $krs->total_sks }} SKS</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                                        'bg-gray-100 text-gray-800' => $krs->status->getColor() === 'gray',
                                        'bg-yellow-100 text-yellow-800' => $krs->status->getColor() === 'yellow',
                                        'bg-green-100 text-green-800' => $krs->status->getColor() === 'green',
                                        'bg-red-100 text-red-800' => $krs->status->getColor() === 'red',
                                    ])>
                                        {{ $krs->status->getLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs->tanggal_submit ? $krs->tanggal_submit->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ \App\Filament\Pages\Dosen\KrsDetailPage::getUrl(['record' => $krs->id]) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail & Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada KRS</h3>
                                        <p class="text-gray-600">Belum ada KRS mahasiswa bimbingan yang perlu ditinjau.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
