<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Statistik KRS --}}
        <x-filament::card>
            <h3 class="text-lg font-medium mb-4">Statistik KRS Mahasiswa Bimbingan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::DRAFT) }}</div>
                    <div class="text-sm text-gray-600">Draft</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::SUBMITTED) }}</div>
                    <div class="text-sm text-gray-600">Menunggu Persetujuan</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::APPROVED) }}</div>
                    <div class="text-sm text-gray-600">Disetujui</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $this->getTotalKrsByStatus(\App\Enums\KrsStatusEnum::REJECTED) }}</div>
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
                                    <div class="text-sm font-medium text-gray-900">{{ $krs->mahasiswa->nama ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $krs->mahasiswa->nim ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs->periodeKrs->nama_periode ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $krs->total_sks }} SKS
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
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
                                       class="text-indigo-600 hover:text-indigo-900">
                                        Detail & Edit
                                    </a>
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
    </div>
</x-filament-panels::page>