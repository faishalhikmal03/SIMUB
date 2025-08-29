<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $kuesioner->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('kuesioner.user.store', $kuesioner) }}" method="POST">
                @csrf
                <input type="hidden" name="submission_uuid" value="{{ $submissionUuid }}">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 md:p-8 mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $kuesioner->judul }}</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $kuesioner->deskripsi }}</p>
                </div>

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
                                            <input type="text" name="answers[{{ $pertanyaan->id }}]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @elseif ($pertanyaan->tipe_jawaban == 'paragraf')
                                            <textarea name="answers[{{ $pertanyaan->id }}]" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                        @elseif ($pertanyaan->tipe_jawaban == 'single_option')
                                            <div class="space-y-2">
                                                @foreach ($pertanyaan->pilihanJawabans as $pilihan)
                                                    <div class="flex items-center">
                                                        <input type="radio" value="{{ $pilihan->id }}"
                                                               name="answers[{{ $pertanyaan->id }}]"
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

                <div class="flex justify-between mt-6">
                    <x-secondary-button type="button" id="prev-btn" style="display: none;">
                        Kembali
                    </x-secondary-button>
                    <x-primary-button type="button" id="next-btn">
                        Berikutnya
                    </x-primary-button>
                    <x-primary-button type="submit" id="submit-btn" style="display: none;">
                        Kirim Jawaban
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sections = Array.from(document.querySelectorAll('.questionnaire-section'));
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');
            const submitBtn = document.getElementById('submit-btn');

            if (sections.length === 0) return;

            const branchStartSectionIds = new Set();
            document.querySelectorAll('input[data-next-section-id]').forEach(radio => {
                branchStartSectionIds.add(radio.dataset.nextSectionId);
            });

            let currentSectionIndex = 0;
            const navigationHistory = [0];

            function getSectionIndexById(id) {
                return sections.findIndex(section => String(section.dataset.sectionId) === String(id));
            }

            function updateButtonVisibility() {
                prevBtn.style.display = navigationHistory.length > 1 ? 'inline-flex' : 'none';
                const currentSection = sections[currentSectionIndex];
                const hasConditionalChoices = !!currentSection.querySelector('input[type="radio"][data-next-section-id]');
                let hasNextStep = false;
                if (hasConditionalChoices) {
                    hasNextStep = true; 
                } else {
                    const nextSequentialIndex = currentSectionIndex + 1;
                    if (nextSequentialIndex < sections.length) {
                        const nextSectionId = sections[nextSequentialIndex].dataset.sectionId;
                        if (branchStartSectionIds.has(nextSectionId)) {
                            hasNextStep = false;
                        } else {
                            hasNextStep = true;
                        }
                    } else {
                        hasNextStep = false;
                    }
                }
                nextBtn.style.display = hasNextStep ? 'inline-flex' : 'none';
                submitBtn.style.display = !hasNextStep ? 'inline-flex' : 'none';
            }

            // --- FUNGSI YANG DIREFAKTOR ---
            function showSection(indexToShow) {
                if (indexToShow === -1 || indexToShow >= sections.length) return;

                sections.forEach((section, index) => {
                    const inputs = section.querySelectorAll('input, textarea');
                    if (index === indexToShow) {
                        // Tampilkan section dan aktifkan validasi untuk input di dalamnya
                        section.style.display = 'block';
                        inputs.forEach(input => input.required = true);
                    } else {
                        // Sembunyikan section dan nonaktifkan validasi
                        section.style.display = 'none';
                        inputs.forEach(input => input.required = false);
                    }
                });
                currentSectionIndex = indexToShow;
                updateButtonVisibility();
            }
            // --- AKHIR FUNGSI YANG DIREFAKTOR ---
            
            document.querySelector('form').addEventListener('change', function(event) {
                if (event.target.type === 'radio' && event.target.name.startsWith('answers')) {
                    updateButtonVisibility();
                }
            });

            nextBtn.addEventListener('click', function () {
                const currentSection = sections[currentSectionIndex];
                const selectedRadio = currentSection.querySelector('input[type="radio"]:checked');
                const hasConditionalChoices = !!currentSection.querySelector('input[type="radio"][data-next-section-id]');

                if (hasConditionalChoices && !selectedRadio) {
                    alert('Silakan pilih salah satu jawaban untuk melanjutkan.');
                    return;
                }

                let nextSectionIndex = -1;
                
                if (selectedRadio && selectedRadio.hasAttribute('data-next-section-id')) {
                    const nextId = selectedRadio.getAttribute('data-next-section-id');
                    nextSectionIndex = getSectionIndexById(nextId);
                    if (nextSectionIndex === -1) console.error("ERROR: Tidak dapat menemukan section dengan ID:", nextId);
                } 
                else if (currentSectionIndex < sections.length - 1) {
                    nextSectionIndex = currentSectionIndex + 1;
                }
                
                if (nextSectionIndex !== -1) {
                    navigationHistory.push(nextSectionIndex);
                    showSection(nextSectionIndex);
                } else {
                    updateButtonVisibility();
                }
            });

            prevBtn.addEventListener('click', function () {
                if (navigationHistory.length > 1) {
                    navigationHistory.pop();
                    const prevSectionIndex = navigationHistory[navigationHistory.length - 1];
                    showSection(prevSectionIndex);
                }
            });

            // Inisialisasi tampilan awal dengan logika validasi yang benar
            showSection(0);
        });
    </script>
    @endpush
</x-app-layout>

