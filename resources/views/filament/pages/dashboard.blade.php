<x-filament-panels::page>
    {{-- Common Header for All Roles --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight">
            Selamat Datang, {{ auth()->user()->name }}!
        </h2>
        <p class="text-gray-500 dark:text-gray-400">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Role-Based Content --}}
    @if(auth()->user()->hasRole('admin'))
        {{-- Admin Dashboard --}}
        <div class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {{-- Admin Stats --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Admin Dashboard</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Selamat datang di panel administrasi. Anda dapat mengelola seluruh sistem dari sini.
                    </p>
                </div>
                {{-- Widgets will be automatically injected here --}}
                {{ $this->table }}
            </div>
        </div>

    @elseif(auth()->user()->hasRole('dosen'))
        {{-- Dosen Dashboard --}}
        <div class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
                {{-- Quick Actions --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aksi Cepat</h3>
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('filament.dosen.resources.krs-approvals.index') }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Persetujuan KRS
                        </a>
                        <a href="{{ route('filament.dosen.resources.input-nilai.index') }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Input Nilai
                        </a>
                    </div>
                </div>
                {{-- Widgets will be automatically injected here --}}
                {{ $this->table }}
            </div>
        </div>

    @else
        {{-- Mahasiswa Dashboard --}}
        <div class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
                {{-- KRS Status --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status KRS</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Periksa status pengajuan KRS Anda.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('filament.mahasiswa.pages.krs') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Lihat KRS Saya
                        </a>
                    </div>
                </div>

                {{-- Academic Info --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Akademik</h3>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Program Studi</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->mahasiswa->programStudi->nama_prodi ?? '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Dosen PA</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->mahasiswa->dosenPa->nama ?? 'Belum ditentukan' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Widgets will be automatically injected here --}}
                {{ $this->table }}
            </div>
        </div>
    @endif
</x-filament-panels::page>
