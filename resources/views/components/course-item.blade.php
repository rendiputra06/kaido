@props(['mataKuliah', 'mahasiswaProgress' => null])

@php
    $isCompleted = $mahasiswaProgress && $mahasiswaProgress->contains($mataKuliah->id);
    $isWajib = $mataKuliah->pivot->jenis === 'wajib';
    
    $statusClasses = [
        'container' => [
            'base' => 'flex items-start space-x-3 p-3 rounded-lg border transition-all duration-200',
            'completed' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
            'default' => 'bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
        ]
    ];
    
    $containerClass = $statusClasses['container']['base'] . ' ' . 
                     ($isCompleted ? $statusClasses['container']['completed'] : $statusClasses['container']['default']);
@endphp

<div class="relative group">
    <div class="{{ $containerClass }}">
        {{-- Status Icon --}}
        <div class="flex-shrink-0 mt-0.5">
            @if($isCompleted)
                <x-icon.check-circle class="w-5 h-5 text-green-500" />
            @elseif($mahasiswaProgress !== null)
                <div class="w-5 h-5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            @else
                <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                </div>
            @endif
        </div>

        {{-- Course Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-tight">
                        {{ $mataKuliah->nama_mk }}
                    </h5>
                    <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="font-mono">{{ $mataKuliah->kode_mk }}</span>
                        <span>•</span>
                        <span>{{ $mataKuliah->sks }} SKS</span>
                        <span>•</span>
                        <span @class([
                            'px-1.5 py-0.5 rounded text-xs font-medium',
                            'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' => $isWajib,
                            'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' => !$isWajib
                        ])>
                            {{ $isWajib ? 'Wajib' : 'Pilihan' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Prerequisites --}}
            @if($mataKuliah->prasyarats->count() > 0)
                <div class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                    <span class="font-medium">Prasyarat:</span>
                    {{ $mataKuliah->prasyarats->pluck('nama_mk')->join(', ') }}
                </div>
            @endif
        </div>
    </div>
</div>
