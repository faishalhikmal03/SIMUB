<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            Beranda
        </h2>
    </x-slot>

    {{-- Kartu Sambutan dengan Gradien dan Ikon --}}
    <div class="mb-8 p-6 bg-gradient-to-r from-purple-700 to-indigo-800 text-white rounded-2xl shadow-lg flex items-center space-x-4">
        <div>
            <h3 class="text-2xl font-bold">Selamat Datang Kembali!</h3>
            <p class="text-indigo-200">{{ $user->nama ?? 'Pengguna' }}, senang melihat Anda lagi.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="group bg-white rounded-2xl shadow-lg p-6 transition-all duration-300 ease-in-out hover:shadow-2xl hover:-translate-y-2 border-l-4 border-yellow-400 border-yellow-500">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-500">Belum Diisi</h3>
                    {{-- Angka statistik utama --}}
                    <p class="text-6xl font-bold text-gray-800 mt-2">{{ $jumlahBelumDiisi }}</p>
                    <p class="text-sm text-gray-400 mt-1">Kuesioner menunggu</p>
                </div>
                {{-- Ikon dengan latar belakang --}}
                <div class="p-4 rounded-xl  bg-yellow-100 group-hover:bg-yellow-200 transition-colors">
                    <i class="fas fa-file-alt text-yellow-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="group bg-white rounded-2xl shadow-lg p-6 transition-all duration-300 ease-in-out hover:shadow-2xl hover:-translate-y-2 border-l-4 border-green-400 border-green-500">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-500">Sudah Diisi</h3>
                    {{-- Angka statistik utama --}}
                    <p class="text-6xl font-bold text-gray-800 mt-2">{{ $jumlahSudahDiisi }}</p>
                    <p class="text-sm text-gray-400 mt-1">Kuesioner selesai</p>
                </div>
                {{-- Ikon dengan latar belakang --}}
                <div class="p-4 rounded-xl bg-green-100 group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-check-double text-green-500 text-2xl"></i>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
