<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekapitulasi Penilaian Dosen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi Error Validasi --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded-lg" role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan validasi:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded-lg" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Kartu Filter --}}
            <div class="bg-white shadow-lg sm:rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Filter Data Rekapitulasi</h3>
                <form method="GET" action="{{ route('admin.jawaban.rekap.dosen.index') }}">
                    <div class="flex flex-col sm:flex-row items-end space-y-4 sm:space-y-0 sm:space-x-4">
                        <div class="w-full sm:flex-grow">
                            <x-input-label for="kuesioner_id" value="Pilih Kuesioner Penilaian" />
                            <select name="kuesioner_id" id="kuesioner_id" class="mt-1 p-2 block w-full rounded-lg border-2 border-gray-300 bg-white shadow-sm transition duration-150 ease-in-out focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                <option value="">-- Pilih Satu Kuesioner --</option>
                                @foreach ($kuesioners as $kuesioner)
                                    <option value="{{ $kuesioner->id }}" {{ request('kuesioner_id') == $kuesioner->id ? 'selected' : '' }}>
                                        {{ $kuesioner->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-auto">
                            <x-primary-button type="submit" class="w-full justify-center !py-2.5">
                                <i class="fas fa-eye mr-2"></i> Tampilkan
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel hanya akan muncul setelah kuesioner dipilih --}}
            @if($selectedKuesioner)
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6 border-b">
                         <h3 class="text-xl font-bold text-gray-800">Daftar Dosen</h3>
                         <p class="text-gray-500">Menampilkan hasil rekapitulasi untuk kuesioner: <span class="font-semibold">{{ $selectedKuesioner->judul }}</span></p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-purple-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NIDN</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($dosens as $dosen)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $dosen->nama }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $dosen->nidn ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.jawaban.rekap.dosen.export', ['kuesioner' => $selectedKuesioner->id, 'user' => $dosen->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition ease-in-out duration-150">
                                                <i class="fas fa-file-excel mr-2"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 whitespace-nowrap text-center text-gray-500">
                                            <i class="fas fa-info-circle text-3xl mb-2"></i>
                                            <p>Tidak ada data penilaian untuk dosen pada kuesioner ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                {{-- Pesan jika belum ada kuesioner yang dipilih --}}
                <div class="bg-white shadow-lg rounded-2xl p-10 text-center text-gray-500 border-2 border-dashed">
                    <i class="fas fa-filter text-4xl mb-4 text-gray-400"></i>
                    <h3 class="text-lg font-semibold">Pilih Kuesioner</h3>
                    <p>Silakan pilih salah satu kuesioner di atas untuk menampilkan data rekapitulasi.</p>
                </div>
            @endif
            <a href="{{ route('admin.jawaban.index') }}" 
   class="mt-6 inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 transform hover:scale-105">
    Kembali
</a>
        </div>
    </div>
</x-app-layout>