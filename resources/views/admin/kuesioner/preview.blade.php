<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pratinjau: {{ $kuesioner->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- PERBAIKAN: Menampilkan Header Utama Kuesioner --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $kuesioner->judul }}</h1>
                @if($kuesioner->deskripsi)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $kuesioner->deskripsi }}</p>
                @endif
            </div>

            @foreach ($kuesioner->sections as $section)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 mb-6">
                    {{-- PERBAIKAN: Menampilkan Header Section --}}
                    <h2 class="text-2xl font-semibold text-purple-700 dark:text-purple-400">{{ $section->judul }}</h2>
                    @if($section->deskripsi)
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $section->deskripsi }}</p>
                    @endif
                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    <fieldset disabled>
                        <div class="space-y-8">
                            @foreach ($section->pertanyaans as $pertanyaan)
                                <div>
                                    <label class="block font-medium text-md text-gray-700 dark:text-gray-300">
                                        {{ $pertanyaan->pertanyaan }}
                                    </label>
                                    <div class="mt-4">
                                        @if ($pertanyaan->tipe_jawaban == 'text_singkat')
                                            <input type="text" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm">
                                        @elseif ($pertanyaan->tipe_jawaban == 'paragraf')
                                            <textarea class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" rows="4"></textarea>
                                        @elseif ($pertanyaan->tipe_jawaban == 'single_option')
                                            <div class="space-y-2">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center">
                                                        <input type="radio" name="pertanyaan_{{ $pertanyaan->id }}" class="h-4 w-4 text-purple-600 border-gray-300 dark:border-gray-600">
                                                        <label class="ml-3 text-gray-700 dark:text-gray-300">{{ $pilihan->pilihan }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($pertanyaan->tipe_jawaban == 'checkbox')
                                            <div class="space-y-2">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center">
                                                        <input type="checkbox" class="h-4 w-4 text-purple-600 border-gray-300 dark:border-gray-600 rounded">
                                                        <label class="ml-3 text-gray-700 dark:text-gray-300">{{ $pilihan->pilihan }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            @endforeach

            {{-- Tombol Kembali --}}
            <div class="mt-4 text-center">
                <a href="{{ route('admin.kuesioner.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Kembali ke Manajemen Kuesioner</a>
            </div>
        </div>
    </div>
</x-app-layout>
