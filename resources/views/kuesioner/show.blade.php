<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $kuesioner->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('kuesioner.user.store', $kuesioner) }}" method="POST" id="kuesioner-form">
                @csrf
                <input type="hidden" name="submission_uuid" value="{{ $submissionUuid }}">

                {{-- Header Kuesioner --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 md:p-8 mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $kuesioner->judul }}</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $kuesioner->deskripsi }}</p>
                </div>

                {{-- Looping Sections --}}
                @foreach ($kuesioner->sections as $index => $section)
                    <div id="section-{{ $section->id }}"
                         class="questionnaire-section bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 md:p-8 mb-6"
                         data-section-id="{{ $section->id }}"
                         style="{{ $index > 0 ? 'display: none;' : '' }}">

                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $section->judul }}</h4>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $section->deskripsi }}</p>
                        </div>

                        <div class="space-y-8">
                            @foreach ($section->pertanyaans as $pertanyaan)
                                <div class="question-block">
                                    <label class="block text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">{{ $pertanyaan->pertanyaan }}</label>
                                    <div class="mt-2">
                                        @if ($pertanyaan->tipe_jawaban == 'text_singkat')
                                            <input type="text" name="answers[{{ $pertanyaan->id }}][jawaban]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @elseif ($pertanyaan->tipe_jawaban == 'paragraf')
                                            <textarea name="answers[{{ $pertanyaan->id }}][jawaban]" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                        
                                        {{-- Logika Gabungan untuk 'single_option' dan 'pilihan_dosen' --}}
                                       @elseif ($pertanyaan->tipe_jawaban == 'single_option' || $pertanyaan->tipe_jawaban == 'pilihan_dosen')
    
    @if($pertanyaan->tipe_jawaban == 'pilihan_dosen')
    <div class="mb-4">
        <input type="text" 
               id="dosen-search-{{ $pertanyaan->id }}" 
               onkeyup="filterDosen({{ $pertanyaan->id }})" 
               placeholder="Cari nama dosen..."
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-purple-500 focus:ring-purple-500">
    </div>
    @endif
    
    <div class="space-y-2" id="dosen-list-{{ $pertanyaan->id }}">
        @foreach ($pertanyaan->pilihanJawabans as $pilihan)
            <div class="flex items-center dosen-option">
                <input type="radio" 
                       {{-- PERBAIKAN KUNCI: Selalu kirim ID dari tabel pilihan_jawabans --}}
                       value="{{ $pilihan->id }}"
                       name="answers[{{ $pertanyaan->id }}][pilihan_id]"
                       class="h-4 w-4 text-purple-600 border-gray-300 dark:border-gray-600"
                       @if(!empty($pilihan->next_section_id))
                           data-next-section-id="{{ $pilihan->next_section_id }}"
                       @endif
                       >
                <label class="ml-3 text-gray-700 dark:text-gray-300">{{ $pilihan->pilihan }}</label>
            </div>
        @endforeach
    </div>

                                        @elseif ($pertanyaan->tipe_jawaban == 'checkbox')
                                            <div class="space-y-2">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center">
                                                        <input type="checkbox" value="{{ $pilihan->id }}" name="answers[{{ $pertanyaan->id }}][pilihan_id][]" class="h-4 w-4 text-purple-600 border-gray-300 dark:border-gray-600 rounded">
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

                <div class="flex justify-between mt-6">
                    <x-secondary-button type="button" id="prev-btn" style="display: none;">
                        Kembali
                    </x-secondary-button>
                    <div>
                        <x-primary-button type="button" id="next-btn">
                            Berikutnya
                        </x-primary-button>
                        <x-primary-button type="submit" id="submit-btn" style="display: none;">
                            Kirim Jawaban
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('kuesioner-form');
        const sections = Array.from(document.querySelectorAll('.questionnaire-section'));
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const submitBtn = document.getElementById('submit-btn');

        if (sections.length === 0) return;
        if (sections.length === 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-flex';
            return;
        }

        let currentSectionIndex = 0;
        const navigationHistory = [0]; // Lacak histori index section

        function getSectionIndexById(id) {
            return sections.findIndex(section => String(section.dataset.sectionId) === String(id));
        }

        function updateButtonVisibility() {
            prevBtn.style.display = navigationHistory.length > 1 ? 'inline-flex' : 'none';

            const currentSection = sections[currentSectionIndex];
            const selectedRadio = currentSection.querySelector('input[type="radio"]:checked');
            
            // Dapatkan section SEBELUMNYA dari histori
            const prevIndex = navigationHistory.length > 1 ? navigationHistory[navigationHistory.length - 2] : -1;
            const prevSection = prevIndex !== -1 ? sections[prevIndex] : null;
            
            let hasNextStep = false; // Asumsikan ini adalah akhir dari alur

            // Cek 1: Apakah section saat ini punya pilihan kondisional?
            const currentHasConditionalChoices = !!currentSection.querySelector('input[type="radio"][data-next-section-id]');
            if (currentHasConditionalChoices) {
                // Jika ya, maka ada langkah berikutnya HANYA JIKA salah satu pilihan sudah dipilih.
                if (selectedRadio && selectedRadio.dataset.nextSectionId) {
                    hasNextStep = true;
                }
            } else {
                // Cek 2: Jika section saat ini BUKAN kondisional, cek section SEBELUMNYA.
                const prevHadConditionalChoices = prevSection ? !!prevSection.querySelector('input[type="radio"][data-next-section-id]') : false;
                
                if (prevHadConditionalChoices) {
                    // Jika kita sampai di sini dari sebuah LOMPATAN, maka ini adalah AKHIR.
                    hasNextStep = false;
                } else {
                    // Jika kita sampai di sini secara SEKUENSIAL, cek apakah ada section berikutnya.
                    if (currentSectionIndex < sections.length - 1) {
                        hasNextStep = true;
                    }
                }
            }
            
            nextBtn.style.display = hasNextStep ? 'inline-flex' : 'none';
            submitBtn.style.display = !hasNextStep ? 'inline-flex' : 'none';
        }

        function showSection(indexToShow) {
            if (indexToShow === -1 || indexToShow >= sections.length) return;

            sections.forEach((section, index) => {
                section.style.display = index === indexToShow ? 'block' : 'none';
            });
            
            currentSectionIndex = indexToShow;
            updateButtonVisibility();
        }
        
        form.addEventListener('change', function(event) {
            if (event.target.type === 'radio' && event.target.name.startsWith('answers')) {
                updateButtonVisibility();
            }
        });

        nextBtn.addEventListener('click', function () {
            const currentSection = sections[currentSectionIndex];
            const selectedRadio = currentSection.querySelector('input[type="radio"]:checked');

            if (currentSection.querySelector('input[type="radio"]') && !selectedRadio) {
                alert('Silakan pilih salah satu jawaban untuk melanjutkan.');
                return;
            }

            let nextSectionIndex = -1;
            
            if (selectedRadio && selectedRadio.dataset.nextSectionId) {
                nextSectionIndex = getSectionIndexById(selectedRadio.dataset.nextSectionId);
            } else if (currentSectionIndex < sections.length - 1) {
                nextSectionIndex = currentSectionIndex + 1;
            }
            
            if (nextSectionIndex !== -1) {
                navigationHistory.push(nextSectionIndex);
                showSection(nextSectionIndex);
            }
        });

        prevBtn.addEventListener('click', function () {
            if (navigationHistory.length > 1) {
                navigationHistory.pop();
                const prevSectionIndex = navigationHistory[navigationHistory.length - 1];
                showSection(prevSectionIndex);
            }
        });

        showSection(0);
    })

        // Fungsi filterDosen tidak perlu diubah, fungsionalitasnya tetap ada.
        function filterDosen(pertanyaanId) {
            let input = document.getElementById('dosen-search-' + pertanyaanId);
            let filter = input.value.toUpperCase();
            let listContainer = document.getElementById('dosen-list-' + pertanyaanId);
            let options = listContainer.getElementsByClassName('dosen-option');
            for (let i = 0; i < options.length; i++) {
                let label = options[i].getElementsByTagName("label")[0];
                if (label) {
                    let txtValue = label.textContent || label.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>