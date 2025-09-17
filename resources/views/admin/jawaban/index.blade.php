<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Jawaban') }}
        </h2>
    </x-slot>

    {{-- 
        LANGKAH 1: Tambahkan x-data untuk state modal konfirmasi DAN modal sukses.
    --}}
    <div x-data="{
        showConfirmModal: false,
        modalTitle: 'Konfirmasi Hapus',
        modalMessage: '',
        modalActionUrl: '',
        showSuccessModal: {{ session()->has('success') ? 'true' : 'false' }},
        successMessage: '{{ session('success') }}'
    }" 
         x-init="if(showSuccessModal) { setTimeout(() => showSuccessModal = false, 2500) }"
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter Section --}}
            <div class="bg-white shadow-lg sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.jawaban.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="kuesioner_id" value="Filter Berdasarkan Kuesioner" />
                            <select name="kuesioner_id" id="kuesioner_id" class="mt-1 p-2 block w-full rounded-lg border-2 border-gray-300 bg-white shadow-sm transition duration-150 ease-in-out focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                <option value="">Semua Kuesioner</option>
                                @foreach ($kuesioners as $kuesioner)
                                    <option value="{{ $kuesioner->id }}" {{ ($filters['kuesioner_id'] ?? '') == $kuesioner->id ? 'selected' : '' }}>
                                        {{ $kuesioner->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="role" value="Filter Berdasarkan Responden" />
                            <select name="role" id="role" class="mt-1 p-2 block w-full rounded-lg border-2 border-gray-300 bg-white shadow-sm transition duration-150 ease-in-out focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                <option value="">Semua Responden</option>
                                <option value="mahasiswa" {{ ($filters['role'] ?? '') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="mahasiswa_baru" {{ ($filters['role'] ?? '') == 'mahasiswa_baru' ? 'selected' : '' }}>Mahasiswa Baru</option>
                                <option value="alumni" {{ ($filters['role'] ?? '') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                                <option value="dosen" {{ ($filters['role'] ?? '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <x-primary-button type="submit" class="w-full justify-center !py-2.5">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </x-primary-button>
                            <a href="{{ route('admin.jawaban.index') }}" 
                               class="w-full justify-center inline-flex items-center !py-2.5 px-4 bg-white border border-purple-500 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Kontainer Tabel --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Hasil Pengisian Kuesioner</h3>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.jawaban.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-700 transition ease-in-out duration-150">
                            <i class="fas fa-file-excel mr-2"></i> Download Ringkasan
                        </a>
                        <a href="{{ route('admin.jawaban.rekap.dosen.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 transition ease-in-out duration-150">
                            <i class="fas fa-chart-bar mr-2"></i> Rekapitulasi Dosen
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-purple-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Responden</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kuesioner & Waktu</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($hasilPengisian as $hasil)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $hasil->user->nama }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if(in_array($hasil->user->role, ['mahasiswa', 'mahasiswa_baru']))
                                                NPM: {{ $hasil->user->npm ?? 'N/A' }}
                                            @elseif($hasil->user->role == 'dosen')
                                                NIDN: {{ $hasil->user->nidn ?? 'N/A' }}
                                            @elseif($hasil->user->role == 'alumni')
                                                Yudisium: {{ $hasil->user->tanggal_yudisium ? \Carbon\Carbon::parse($hasil->user->tanggal_yudisium)->format('d M Y') : 'N/A' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $hasil->kuesioner->judul }}</div>
                                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($hasil->waktu_pengisian)->format('d M Y, H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                        <a href="{{ route('admin.jawaban.exportDetail', $hasil->submission_uuid) }}" class="text-gray-500 hover:text-green-600" title="Download Detail">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        {{-- 
                                            LANGKAH 2: Ganti <form> dengan <button> yang memiliki @click.
                                        --}}
                                        <button type="button" 
                                                @click="
                                                    modalMessage = 'Apakah Anda yakin ingin menghapus sesi jawaban dari \'{{ addslashes($hasil->user->nama) }}\'? Tindakan ini tidak dapat dibatalkan.';
                                                    modalActionUrl = '{{ route('admin.jawaban.destroy', $hasil->submission_uuid) }}';
                                                    showConfirmModal = true;
                                                "
                                                class="text-gray-500 hover:text-red-600" title="Hapus Jawaban">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 whitespace-nowrap text-center text-gray-500">
                                        <i class="fas fa-folder-open text-3xl mb-2"></i>
                                        <p>Tidak ada data jawaban yang cocok dengan filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($hasilPengisian->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $hasilPengisian->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- 
            LANGKAH 3: Tambahkan Modal Konfirmasi dan Modal Sukses.
        --}}
        <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-10 overflow-y-auto bg-gray-500 bg-opacity-75" style="display: none;">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showConfirmModal" x-transition @click.away="showConfirmModal = false" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                    <form :action="modalActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                                <i class="fa fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" x-text="modalTitle"></h3>
                                <div class="mt-2"><p class="text-sm text-gray-500" x-text="modalMessage"></p></div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 bg-red-600 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">Konfirmasi Hapus</button>
                            <button @click="showConfirmModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showSuccessModal" x-transition:enter="ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-20 overflow-y-auto bg-gray-500 bg-opacity-75" style="display: none;">
            <div class="flex items-center justify-center min-h-screen">
                <div @click.away="showSuccessModal = false" class="relative bg-white rounded-lg shadow-xl text-center p-8 max-w-sm mx-auto">
                    <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center bg-green-100 rounded-full">
                        <i class="fas fa-check-double text-green-500 text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Sukses!</h3>
                    <p class="text-gray-600 mt-2" x-text="successMessage"></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>