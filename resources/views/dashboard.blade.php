<x-app-layout>
    <x-slot name="header">
        Beranda
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            {{ __("Selamat datang di dashboard Anda, ") }} {{ $user->nama }}!
        </div>
    </div>

    <!-- Grid untuk Kartu Statistik Pengguna -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Card: Kuesioner Belum Diisi -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col">
            {{-- Header Card --}}
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-file-alt text-yellow-600 dark:text-yellow-300 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kuesioner Belum Diisi</h3>
                </div>
            </div>
            {{-- Konten Card (Angka di tengah) --}}
            <div class="flex-grow flex items-center justify-center">
                <p class="text-5xl font-bold text-gray-800 dark:text-gray-100">{{ $kuesionerBelumDiisi }}</p>
            </div>
        </div>

        <!-- Card: Kuesioner Sudah Diisi -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col">
            {{-- Header Card --}}
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-check-double text-green-600 dark:text-green-300 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kuesioner Sudah Diisi</h3>
                </div>
            </div>
            {{-- Konten Card (Angka di tengah) --}}
            <div class="flex-grow flex items-center justify-center">
                <p class="text-5xl font-bold text-gray-800 dark:text-gray-100">{{ $kuesionerSudahDiisi }}</p>
            </div>
        </div>

    </div>
</x-app-layout>
