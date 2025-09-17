<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pratinjau: {{ $kuesioner->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Kuesioner --}}
            <div class="bg-purple-50 shadow-sm sm:rounded-lg p-6 md:p-8 mb-6 border-t-8 border-purple-500">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $kuesioner->judul }}</h3>
                    <p class="mt-2 text-gray-600">{{ $kuesioner->deskripsi }}</p>
                </div>

            {{-- Looping Sections --}}
            @foreach ($kuesioner->sections as $section)
                <div class="questionnaire-section bg-white shadow-sm sm:rounded-lg p-6 md:p-8 mb-6">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-gray-900">{{ $section->judul }}</h2>
                        @if($section->deskripsi)
                            <p class="mt-1 text-gray-600">{{ $section->deskripsi }}</p>
                        @endif
                    </div>

                    {{-- Fieldset disabled ensures no interaction is possible --}}
                    <fieldset disabled>
                        <div class="space-y-8">
                            @foreach ($section->pertanyaans as $pIndex => $pertanyaan)
                                <div class=" border-gray-200 pt-6">
                                    <label class="block text-lg font-semibold text-gray-800 mb-4">
                                        <span class="text-purple-600 mr-2">{{ $pIndex + 1 }}.</span> {{ $pertanyaan->pertanyaan }}
                                    </label>
                                    <div class="mt-2 pl-6">
                                        @if ($pertanyaan->tipe_jawaban == 'text_singkat')
                                            <input type="text" class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-100 shadow-sm" placeholder="Jawaban teks singkat...">
                                        @elseif ($pertanyaan->tipe_jawaban == 'paragraf')
                                            <textarea class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-100 shadow-sm" rows="4" placeholder="Jawaban paragraf..."></textarea>
                                        
                                        @elseif ($pertanyaan->tipe_jawaban == 'single_option' || $pertanyaan->tipe_jawaban == 'checkbox')
                                            <div class="space-y-3">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50">
                                                        @if ($pertanyaan->tipe_jawaban == 'single_option')
                                                            <input type="radio" name="pertanyaan_{{ $pertanyaan->id }}" class="h-5 w-5 text-purple-600 border-gray-300">
                                                        @else
                                                            <input type="checkbox" class="h-5 w-5 text-purple-600 border-gray-300 rounded">
                                                        @endif
                                                        <label class="ml-4 text-gray-700">{{ $pilihan->pilihan }}</label>
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
            <div class="mt-8 text-left">
                <a href="{{ route('admin.kuesioner.index') }}" 
                   class="inline-flex items-center px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Kembali ke Manajemen Kuesioner
                </a>
            </div>
        </div>
    </div>
</x-app-layout>