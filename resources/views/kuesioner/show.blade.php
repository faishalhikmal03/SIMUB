<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $kuesioner->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- PERBAIKAN: Form sekarang fungsional --}}
            <form method="POST" action="{{ route('kuesioner.user.store', $kuesioner) }}">
                @csrf
                
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $kuesioner->judul }}</h1>
                    @if($kuesioner->deskripsi)
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $kuesioner->deskripsi }}</p>
                    @endif
                </div>

                {{-- PERBAIKAN: Menggunakan data dinamis dari controller --}}
                @foreach ($kuesioner->sections as $section)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 mb-6">
                        <h2 class="text-2xl font-semibold text-purple-700 dark:text-purple-400">{{ $section->judul }}</h2>
                        @if($section->deskripsi)
                            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $section->deskripsi }}</p>
                        @endif
                        <hr class="my-6 border-gray-200 dark:border-gray-700">

                        <div class="space-y-8">
                            @foreach ($section->pertanyaans as $pertanyaan)
                                <div>
                                    <label class="block font-medium text-md text-gray-700 dark:text-gray-300">
                                        {{ $pertanyaan->pertanyaan }}
                                    </label>
                                    <div class="mt-4">
                                        @if ($pertanyaan->tipe_jawaban == 'text_singkat')
                                            <input type="text" name="answers[{{ $pertanyaan->id }}]" maxlength="100" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm">
                                        @elseif ($pertanyaan->tipe_jawaban == 'paragraf')
                                            <textarea name="answers[{{ $pertanyaan->id }}]" maxlength="500" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" rows="4"></textarea>
                                        @elseif ($pertanyaan->tipe_jawaban == 'single_option')
                                            <div class="space-y-2">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center">
                                                        <input type="radio" value="{{ $pilihan->id }}" name="answers[{{ $pertanyaan->id }}]" class="h-4 w-4 text-purple-600 border-gray-300 dark:border-gray-600">
                                                        <label class="ml-3 text-gray-700 dark:text-gray-300">{{ $pilihan->pilihan }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($pertanyaan->tipe_jawaban == 'checkbox')
                                            <div class="space-y-2">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center">
                                                        <input type="checkbox" value="{{ $pilihan->id }}" name="answers[{{ $pertanyaan->id }}][]" class="h-4 w-4 text-purple-600 border-gray-300 dark:border-gray-600 rounded">
                                                        <label class="ml-3 text-gray-700 dark:text-gray-300">{{ $pilihan->pilihan }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end mt-6">
                    <x-primary-button>
                        Kirim Jawaban
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
