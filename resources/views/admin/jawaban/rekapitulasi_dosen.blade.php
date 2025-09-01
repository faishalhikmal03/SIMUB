<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rekapitulasi Penilaian Dosen') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan validasi:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="GET" action="{{ route('admin.jawaban.rekap.dosen.index') }}" class="mb-6">
                        <label for="kuesioner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Kuesioner Penilaian</label>
                        <div class="flex items-center mt-1">
                            <select name="kuesioner_id" id="kuesioner_id" class="block w-full md:w-1/2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Satu Kuesioner --</option>
                                @foreach ($kuesioners as $kuesioner)
                                    <option value="{{ $kuesioner->id }}" {{ request('kuesioner_id') == $kuesioner->id ? 'selected' : '' }}>
                                        {{ $kuesioner->judul }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 bg-purple-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-purple-700">Tampilkan</button>
                        </div>
                    </form>

                    {{-- Tabel hanya akan muncul setelah kuesioner dipilih --}}
                    @if($selectedKuesioner)
                    <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700 pt-4">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nama Dosen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">NIDN</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($dosens as $dosen)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $dosen->nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $dosen->nidn ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.jawaban.rekap.dosen.export', ['kuesioner' => $selectedKuesioner->id, 'user' => $dosen->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">
                                                <i class="fas fa-file-excel mr-2"></i> Download Rekap
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data penilaian untuk dosen pada kuesioner ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>