<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Informasi Mahasiswa dan KRS --}}
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium">Informasi Mahasiswa</h3>
                    <dl class="mt-2 border-t border-gray-200">
                        <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $record->mahasiswa->nama }}</dd>
                        </div>
                        <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">NIM</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $record->mahasiswa->nim }}</dd>
                        </div>
                        <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Program Studi</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $record->mahasiswa->programStudi->nama_prodi }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-lg font-medium">Informasi KRS</h3>
                    <dl class="mt-2 border-t border-gray-200">
                        <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Periode</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $record->periodeKrs->nama_periode }}</dd>
                        </div>
                        <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span @class([
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    'bg-gray-100 text-gray-800' => $record->status->getColor() === 'gray',
                                    'bg-yellow-100 text-yellow-800' => $record->status->getColor() === 'yellow',
                                    'bg-green-100 text-green-800' => $record->status->getColor() === 'green',
                                    'bg-red-100 text-red-800' => $record->status->getColor() === 'red',
                                ])>
                                    {{ $record->status->getLabel() }}
                                </span>
                            </dd>
                        </div>
                        <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Total SKS</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $record->total_sks }} SKS</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </x-filament::card>

        {{-- Relation Manager untuk Edit Mata Kuliah --}}
        <div>
            @foreach ($this->getRelationManagers() as $manager)
                @livewire($manager, ['ownerRecord' => $this->record, 'pageClass' => static::class])
                @endforeach
            </div>
        </div>
</x-filament-panels::page>