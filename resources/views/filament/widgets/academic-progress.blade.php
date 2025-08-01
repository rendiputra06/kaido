<div>
    @php
        $data = $this->getViewData();
    @endphp

    @if($data['isStudent'])
        <x-filament-widgets::widget>
            <x-slot name="heading">
                Progress Akademik & Notifikasi
            </x-slot>

            <div class="space-y-4">
                {{-- Progress Summary --}}
                @if(isset($data['progressData']))
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Progress Keseluruhan</h3>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    {{ $data['progressData']['completed_sks'] }}/{{ $data['progressData']['total_sks'] }}
                                </div>
                                <div class="text-xs text-blue-500 dark:text-blue-300">SKS</div>
                            </div>
                        </div>
                        
                        <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2 mb-2">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ $data['progressData']['percentage'] }}%"></div>
                        </div>
                        
                        <div class="text-xs text-blue-600 dark:text-blue-300">
                            {{ $data['progressData']['percentage'] }}% Kurikulum Selesai
                        </div>
                    </div>
                @endif

                {{-- Notifications --}}
                @if($data['notifications']->count() > 0)
                    <div class="space-y-3">
                        @foreach($data['notifications'] as $notification)
                            <div class="flex items-start space-x-3 p-3 rounded-lg border
                                {{ $notification['type'] === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : '' }}
                                {{ $notification['type'] === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : '' }}
                                {{ $notification['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}
                                {{ $notification['type'] === 'info' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}">
                                
                                {{-- Icon --}}
                                <div class="flex-shrink-0 mt-0.5">
                                    @if($notification['type'] === 'success')
                                        <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'warning')
                                        <div class="w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'danger')
                                        <div class="w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium
                                        {{ $notification['type'] === 'success' ? 'text-green-900 dark:text-green-100' : '' }}
                                        {{ $notification['type'] === 'warning' ? 'text-amber-900 dark:text-amber-100' : '' }}
                                        {{ $notification['type'] === 'danger' ? 'text-red-900 dark:text-red-100' : '' }}
                                        {{ $notification['type'] === 'info' ? 'text-blue-900 dark:text-blue-100' : '' }}">
                                        {{ $notification['title'] }}
                                    </h4>
                                    <p class="mt-1 text-xs
                                        {{ $notification['type'] === 'success' ? 'text-green-700 dark:text-green-300' : '' }}
                                        {{ $notification['type'] === 'warning' ? 'text-amber-700 dark:text-amber-300' : '' }}
                                        {{ $notification['type'] === 'danger' ? 'text-red-700 dark:text-red-300' : '' }}
                                        {{ $notification['type'] === 'info' ? 'text-blue-700 dark:text-blue-300' : '' }}">
                                        {{ $notification['message'] }}
                                    </p>
                                    
                                    @if($notification['action'])
                                        <div class="mt-2">
                                            <a href="{{ $notification['action']['url'] }}" 
                                               class="inline-flex items-center text-xs font-medium
                                                   {{ $notification['type'] === 'success' ? 'text-green-600 hover:text-green-500 dark:text-green-400' : '' }}
                                                   {{ $notification['type'] === 'warning' ? 'text-amber-600 hover:text-amber-500 dark:text-amber-400' : '' }}
                                                   {{ $notification['type'] === 'danger' ? 'text-red-600 hover:text-red-500 dark:text-red-400' : '' }}
                                                   {{ $notification['type'] === 'info' ? 'text-blue-600 hover:text-blue-500 dark:text-blue-400' : '' }}">
                                                {{ $notification['action']['label'] }}
                                                <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">Tidak ada notifikasi akademik saat ini</p>
                        <p class="text-xs mt-1">Progress akademik Anda berjalan lancar!</p>
                    </div>
                @endif
            </div>
        </x-filament-widgets::widget>
    @endif
</div>
