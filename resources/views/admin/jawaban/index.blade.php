<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Manajemen Jawaban') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.jawaban.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Filter Kuesioner -->
                        <div>
                            <label for="kuesioner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter Kuesioner</label>
                            <select name="kuesioner_id" id="kuesioner_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">Semua Kuesioner</option>
                                @foreach ($kuesioners as $kuesioner)
                                    <option value="{{ $kuesioner->id }}" {{ ($filters['kuesioner_id'] ?? '') == $kuesioner->id ? 'selected' : '' }}>
                                        {{ $kuesioner->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Filter Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter Responden</label>
                            <select name="role" id="role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">Semua Responden</option>
                                <option value="mahasiswa" {{ ($filters['role'] ?? '') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="mahasiswa_baru" {{ ($filters['role'] ?? '') == 'mahasiswa_baru' ? 'selected' : '' }}>Mahasiswa Baru</option>
                                <option value="alumni" {{ ($filters['role'] ?? '') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                                <option value="dosen" {{ ($filters['role'] ?? '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            </select>
                        </div>
                        <!-- Tombol Aksi Filter -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-purple-700">Filter</button>
                            <a href="{{ route('admin.jawaban.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-400">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Hasil Jawaban -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Pengisian Kuesioner</h3>
                    
                    {{-- ====================================================== --}}
                    {{--               TAMBAHAN TOMBOL BARU DI SINI             --}}
                    {{-- ====================================================== --}}
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.jawaban.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">
                            <i class="fas fa-file-excel mr-2"></i> Download Ringkasan
                        </a>
                        {{-- Tombol baru yang mengarah ke halaman rekapitulasi dosen --}}
                        <a href="{{ route('admin.jawaban.rekap.dosen.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">
                            <i class="fas fa-chart-bar mr-2"></i> Rekapitulasi Dosen
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">NPM/NIDN/Yudisium</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nama Responden</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Judul Kuesioner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Waktu Pengisian</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($hasilPengisian as $hasil)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if(in_array($hasil->user->role, ['mahasiswa', 'mahasiswa_baru']))
                                            {{ $hasil->user->npm ?? 'N/A' }}
                                        @elseif($hasil->user->role == 'dosen')
                                            {{ $hasil->user->nidn ?? 'N/A' }}
                                        @elseif($hasil->user->role == 'alumni')
                                            {{ $hasil->user->tanggal_yudisium ? \Carbon\Carbon::parse($hasil->user->tanggal_yudisium)->format('d M Y') : 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $hasil->user->nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $hasil->kuesioner->judul }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($hasil->waktu_pengisian)->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.jawaban.exportDetail', $hasil->submission_uuid) }}" class="text-green-600 hover:text-green-900">Download</a>
                                        <form action="{{ route('admin.jawaban.destroy', $hasil->submission_uuid) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi jawaban ini? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data jawaban yang cocok dengan filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t">
                    {{ $hasilPengisian->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>