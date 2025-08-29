<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Beranda Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Selamat datang di dashboard Anda!") }}
                </div>
            </div>

            <!-- Grid untuk Kartu Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Card: Pengguna Terdaftar -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col">
                    {{-- Header Card --}}
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                            <i class="fas fa-users text-purple-600 dark:text-purple-300 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pengguna Terdaftar</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $userCounts->sum() }}</p>
                        </div>
                    </div>
                    {{-- Konten Card --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                        @foreach(['mahasiswa', 'mahasiswa_baru', 'alumni', 'dosen', 'admin'] as $role)
                        <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                            <span>{{ Str::title(str_replace('_', ' ', $role)) }}</span>
                            <span class="font-semibold">{{ $userCounts[$role] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Card: Total Kuesioner -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col">
                    {{-- Header Card --}}
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <i class="fas fa-file-alt text-blue-600 dark:text-blue-300 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Kuesioner</h3>
                        </div>
                    </div>
                    {{-- Konten Card (Angka di tengah) --}}
                    <div class="flex-grow flex items-center justify-center">
                        <p class="text-5xl font-bold text-gray-800 dark:text-gray-100">{{ $kuesionerCount }}</p>
                    </div>
                </div>

                <!-- Card: Responden Kuesioner -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col">
                    {{-- Header Card --}}
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-300 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Responden</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Responden: {{ $submissionCounts->sum() }}</p>
                        </div>
                    </div>
                    {{-- Konten Card --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                        @foreach(['mahasiswa', 'mahasiswa_baru', 'alumni', 'dosen'] as $role)
                        <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                            <span>{{ Str::title(str_replace('_', ' ', $role)) }}</span>
                            <span class="font-semibold">{{ $submissionCounts[$role] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>