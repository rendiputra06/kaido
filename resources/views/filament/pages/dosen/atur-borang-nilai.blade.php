<x-filament-panels::page>
        @if (empty($kelasOptions))
        <div class="p-4 text-center bg-gray-100 rounded-lg dark:bg-gray-800">
            <p class="text-gray-500 dark:text-gray-400">Anda tidak memiliki kelas yang aktif pada semester ini.</p>
        </div>
    @else
        <form wire:submit.prevent="saveBorang">
            {{ $this->form }}
        </form>

        @if ($isLocked)
            <div class="p-4 mt-4 text-yellow-800 bg-yellow-100 border-l-4 border-yellow-500 rounded-md dark:bg-yellow-900/50 dark:text-yellow-300">
                <p class="font-bold">Borang Nilai Terkunci</p>
                <p>Komposisi borang nilai untuk kelas ini sudah dikunci dan tidak dapat diubah lagi.</p>
            </div>
        @endif
    @endif
</x-filament-panels::page>
