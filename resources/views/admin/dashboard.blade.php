<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Beranda Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Kartu Sambutan dengan Gradien --}}
            <div class="mb-8 p-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl shadow-lg">
                <h3 class="text-2xl font-bold">Selamat Datang di Dashboard Admin!</h3>
                <p class="text-indigo-200">Kelola pengguna, kuesioner, dan lihat statistik di sini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <div class="group bg-white rounded-2xl shadow-lg p-6 transition-all duration-300 ease-in-out hover:shadow-2xl hover:-translate-y-2 border-l-4 border-purple-400">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500">Pengguna Terdaftar</h3>
                            <p class="text-6xl font-bold text-gray-800 mt-2">{{ $userCounts->sum() }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-purple-100 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-users text-purple-500 text-2xl"></i>
                        </div>
                    </div>
                    {{-- Detail Pengguna per Role --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                        @foreach(['mahasiswa', 'mahasiswa_baru', 'alumni', 'dosen', 'admin'] as $role)
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>{{ Str::title(str_replace('_', ' ', $role)) }}</span>
                            <span class="font-semibold text-gray-800">{{ $userCounts[$role] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="group bg-white rounded-2xl shadow-lg p-6 transition-all duration-300 ease-in-out hover:shadow-2xl hover:-translate-y-2 border-l-4 border-blue-400">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500">Total Kuesioner</h3>
                            <p class="text-6xl font-bold text-gray-800 mt-2">{{ $kuesionerCount }}</p>
                            <p class="text-sm text-gray-400 mt-1">Kuesioner dibuat</p>
                        </div>
                        <div class="p-4 rounded-xl bg-blue-100 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-file-alt text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="group bg-white rounded-2xl shadow-lg p-6 transition-all duration-300 ease-in-out hover:shadow-2xl hover:-translate-y-2 border-l-4 border-green-400">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500">Total Responden</h3>
                            <p class="text-6xl font-bold text-gray-800 mt-2">{{ $submissionCounts->sum() }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-green-100 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                    </div>
                     {{-- Detail Responden per Role --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                        @foreach(['mahasiswa', 'mahasiswa_baru', 'alumni', 'dosen'] as $role)
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>{{ Str::title(str_replace('_', ' ', $role)) }}</span>
                            <span class="font-semibold text-gray-800">{{ $submissionCounts[$role] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>