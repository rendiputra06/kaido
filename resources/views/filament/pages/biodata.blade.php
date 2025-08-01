<x-filament-panels::page>
    @if ($profile)
        <x-filament::card>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                {{-- Avatar and Basic Info Column --}}
                <div class="flex flex-col items-center text-center md:col-span-1">
                    <img class="object-cover w-32 h-32 rounded-full mb-4"
                         src="{{ filament()->getUserAvatarUrl(Auth::user()) }}"
                         alt="Avatar">
                    <h2 class="text-xl font-bold">{{ $profile->nama }}</h2>
                    <p class="text-md text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                </div>

                {{-- Detailed Info Column --}}
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium border-b pb-2 mb-4">
                        Detail Informasi
                    </h3>
                    <dl class="grid grid-cols-1 gap-y-4 sm:grid-cols-2">
                        {{-- Common Fields --}}
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->nama }}</dd>
                        </div>

                        {{-- Specific for Mahasiswa --}}
                        @if ($userType === 'mahasiswa')
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NIM</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->nim ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Angkatan</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->angkatan ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Program Studi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->programStudi->nama_prodi ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dosen Pembimbing Akademik</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->dosenPa->nama ?? 'Belum ditentukan' }}</dd>
                            </div>

                        @endif

                        {{-- Specific for Dosen --}}
                        @if ($userType === 'dosen')
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NIDN</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->nidn ?? '-' }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jabatan</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $profile->jabatan ?? 'Tidak ada data' }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </x-filament::card>
    @else
        <x-filament::card>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Biodata Tidak Ditemukan</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Informasi biodata tidak terhubung dengan akun Anda. Silakan hubungi administrator.
                </p>
            </div>
        </x-filament::card>
    @endif
</x-filament-panels::page>
