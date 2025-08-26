<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Kuesioner Baru') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="kuesionerBuilder()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- !! PERBAIKAN: Menambahkan blok untuk menampilkan error validasi !! --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <strong class="font-bold">Oops! Ada yang salah dengan input Anda.</strong>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.kuesioner.store') }}">
                @csrf
                {{-- Bagian Detail Kuesioner --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Kuesioner</h3>
                    <div>
                        <x-input-label for="judul" :value="__('Judul Kuesioner')" />
                        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul')" required autofocus />
                    </div>
                    <div>
                        <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                        <textarea id="deskripsi" name="deskripsi" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="target_user" :value="__('Target Responden')" />
                            <select name="target_user" id="target_user" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="mahasiswa_baru">Mahasiswa Baru</option>
                                <option value="alumni">Alumni</option>
                                <option value="dosen">Dosen</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="nonaktif">Nonaktif (Draft)</option>
                                <option value="aktif">Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Builder untuk Sections dan Questions --}}
                <div class="mt-8">
                    <template x-for="(section, sectionIndex) in sections" :key="sectionIndex">
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 border-l-4" :class="activeSection === sectionIndex ? 'border-purple-500' : 'border-transparent'">
                            {{-- Header Section --}}
                            <div class="p-6 cursor-pointer" @click="activeSection = sectionIndex">
                                <div class="flex justify-between items-center">
                                    <input type="text" :name="`sections[${sectionIndex}][judul]`" x-model="section.judul" placeholder="Judul Section" class="text-lg font-bold w-full border-0 focus:ring-0 p-0 bg-transparent dark:text-white dark:placeholder-gray-400">
                                    <button type="button" @click.stop="removeSection(sectionIndex)" class="text-gray-400 hover:text-red-500 ml-4">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <textarea :name="`sections[${sectionIndex}][deskripsi]`" x-model="section.deskripsi" placeholder="Deskripsi Section (opsional)" class="mt-2 w-full border-0 focus:ring-0 p-0 text-sm bg-transparent dark:text-gray-400 dark:placeholder-gray-500" rows="1"></textarea>
                            </div>

                            {{-- Pertanyaan di dalam Section (hanya tampil jika section aktif) --}}
                            <div x-show="activeSection === sectionIndex" class="p-6 border-t dark:border-gray-700 space-y-4" x-cloak>
                                <template x-for="(question, questionIndex) in section.questions" :key="questionIndex">
                                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg relative">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="md:col-span-2">
                                                <label :for="`pertanyaan_${sectionIndex}_${questionIndex}`" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Teks Pertanyaan</label>
                                                <input type="text" :id="`pertanyaan_${sectionIndex}_${questionIndex}`" :name="`sections[${sectionIndex}][questions][${questionIndex}][pertanyaan]`" x-model="question.pertanyaan" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                            </div>
                                            <div>
                                                <label :for="`tipe_jawaban_${sectionIndex}_${questionIndex}`" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tipe Jawaban</label>
                                                <select :id="`tipe_jawaban_${sectionIndex}_${questionIndex}`" :name="`sections[${sectionIndex}][questions][${questionIndex}][tipe_jawaban]`" x-model="question.tipe_jawaban" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                    <option value="text_singkat">Teks Singkat</option>
                                                    <option value="paragraf">Paragraf</option>
                                                    <option value="single_option">Pilihan Ganda</option>
                                                    <option value="checkbox">Kotak Centang</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div x-show="question.tipe_jawaban === 'single_option' || question.tipe_jawaban === 'checkbox'" class="mt-4 space-y-2">
                                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pilihan Jawaban</label>
                                            <template x-for="(option, optionIndex) in question.pilihan" :key="optionIndex">
                                                <div class="flex items-center space-x-2">
                                                    <input type="text" :name="`sections[${sectionIndex}][questions][${questionIndex}][pilihan][${optionIndex}]`" x-model="option.text" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Tulis pilihan..."/>
                                                    <button type="button" @click="removeOption(sectionIndex, questionIndex, optionIndex)" class="text-red-500 hover:text-red-700">&times;</button>
                                                </div>
                                            </template>
                                            <button type="button" @click="addOption(sectionIndex, questionIndex)" class="text-sm text-purple-600 hover:text-purple-800">+ Tambah Pilihan</button>
                                        </div>
                                        <button type="button" @click="removeQuestion(sectionIndex, questionIndex)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addQuestion(sectionIndex)" class="mt-4 text-sm text-purple-600 hover:text-purple-800">+ Tambah Pertanyaan</button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Tombol Aksi Utama --}}
                <div class="mt-6 flex justify-between items-center">
                    <button type="button" @click="addSection()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300">
                        + Tambah Section
                    </button>
                    <div class="flex items-center">
                        <a href="{{ route('admin.kuesioner.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 mr-4">Batal</a>
                        <x-primary-button>Simpan Kuesioner</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function kuesionerBuilder() {
            return {
                sections: [],
                activeSection: 0,
                addSection() {
                    this.sections.push({
                        judul: `Section Tanpa Judul`,
                        deskripsi: '',
                        questions: []
                    });
                    this.activeSection = this.sections.length - 1;
                    this.addQuestion(this.activeSection);
                },
                removeSection(index) {
                    this.sections.splice(index, 1);
                    if (this.activeSection >= index) {
                        this.activeSection = Math.max(0, this.activeSection - 1);
                    }
                },
                addQuestion(sectionIndex) {
                    this.sections[sectionIndex].questions.push({
                        pertanyaan: '',
                        tipe_jawaban: 'text_singkat',
                        pilihan: []
                    });
                },
                removeQuestion(sectionIndex, questionIndex) {
                    this.sections[sectionIndex].questions.splice(questionIndex, 1);
                },
                addOption(sectionIndex, questionIndex) {
                    this.sections[sectionIndex].questions[questionIndex].pilihan.push({ text: '' });
                },
                removeOption(sectionIndex, questionIndex, optionIndex) {
                    this.sections[sectionIndex].questions[questionIndex].pilihan.splice(optionIndex, 1);
                },
                init() {
                    this.addSection();
                }
            }
        }
    </script>
</x-app-layout>
