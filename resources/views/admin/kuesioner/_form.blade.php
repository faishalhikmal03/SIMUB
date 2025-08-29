{{-- !! PERBAIKAN: Menambahkan blok untuk menampilkan error validasi !! --}}
@if ($errors->any())
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
    <strong class="font-bold">Oops! Ada yang salah.</strong>
    <ul class="mt-3 list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" :action="actionUrl">
    @csrf
    {{-- MODIFIKASI: Tambahkan method spoofing untuk form edit --}}
    <template x-if="isEditMode">
        @method('PUT')
    </template>

    {{-- Bagian Detail Kuesioner --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Kuesioner</h3>
        {{-- Input Judul, Deskripsi, Target, Status (TETAP SAMA) --}}
        <div>
            <x-input-label for="judul" :value="__('Judul Kuesioner')" />
            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" x-model="kuesioner.judul" required autofocus />
        </div>
        <div>
            <x-input-label for="deskripsi" :value="__('Deskripsi')" />
            <textarea id="deskripsi" name="deskripsi" x-model="kuesioner.deskripsi" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="target_user" :value="__('Target Responden')" />
                <select name="target_user" id="target_user" x-model="kuesioner.target_user" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="mahasiswa_baru">Mahasiswa Baru</option>
                    <option value="alumni">Alumni</option>
                    <option value="dosen">Dosen</option>
                </select>
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select name="status" id="status" x-model="kuesioner.status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="nonaktif">Nonaktif (Draft)</option>
                    <option value="aktif">Aktif</option>
                </select>
            </div>
            </div>
            <div class="mt-4">
    <label for="bisa_diisi_ulang" class="inline-flex items-center">
        {{-- Logika untuk mengatasi nilai checkbox saat form dikirim --}}
        <input type="hidden" name="bisa_diisi_ulang" value="0">
        <input id="bisa_diisi_ulang" type="checkbox" name="bisa_diisi_ulang" value="1"
               class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500"
               @if(isset($kuesioner) && $kuesioner->bisa_diisi_ulang) checked @endif>
        <span class="ml-2 text-sm text-gray-600">Izinkan kuesioner ini untuk diisi ulang oleh pengguna yang sama</span>
    </label>
</div>
        </div>
    </div>

    {{-- Builder untuk Sections dan Questions --}}
    <div class="mt-8">
        <template x-for="(section, sectionIndex) in kuesioner.sections" :key="section.clientId">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 border-l-4" :class="activeSection === sectionIndex ? 'border-purple-500' : 'border-transparent'">
                {{-- Header Section (TETAP SAMA) --}}
                <div class="p-6 cursor-pointer" @click="activeSection = sectionIndex">
                    <div class="flex justify-between items-center">
                        {{-- MODIFIKASI: Tambah input hidden untuk ID section (penting untuk edit) --}}
                        <input type="hidden" :name="`sections[${sectionIndex}][id]`" x-model="section.id">
                        <input type="hidden" :name="`sections[${sectionIndex}][clientId]`" x-model="section.clientId">
                        <input type="text" :name="`sections[${sectionIndex}][judul]`" x-model="section.judul" placeholder="Judul Section" class="text-lg font-bold w-full border-0 focus:ring-0 p-0 bg-transparent dark:text-white dark:placeholder-gray-400">
                        <button type="button" @click.stop="removeSection(sectionIndex)" class="text-gray-400 hover:text-red-500 ml-4">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <textarea :name="`sections[${sectionIndex}][deskripsi]`" x-model="section.deskripsi" placeholder="Deskripsi Section (opsional)" class="mt-2 w-full border-0 focus:ring-0 p-0 text-sm bg-transparent dark:text-gray-400 dark:placeholder-gray-500" rows="1"></textarea>
                </div>

                {{-- Pertanyaan di dalam Section --}}
                <div x-show="activeSection === sectionIndex" class="p-6 border-t dark:border-gray-700 space-y-4" x-cloak>
                    <template x-for="(question, questionIndex) in section.questions" :key="question.clientId">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg relative">
                            {{-- MODIFIKASI: Tambah input hidden untuk ID pertanyaan (penting untuk edit) --}}
                            <input type="hidden" :name="`sections[${sectionIndex}][questions][${questionIndex}][id]`" x-model="question.id">
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- Input Teks Pertanyaan & Tipe Jawaban (TETAP SAMA) --}}
                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Teks Pertanyaan</label>
                                    <input type="text" :name="`sections[${sectionIndex}][questions][${questionIndex}][pertanyaan]`" x-model="question.pertanyaan" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tipe Jawaban</label>
                                    <select :name="`sections[${sectionIndex}][questions][${questionIndex}][tipe_jawaban]`" x-model="question.tipe_jawaban" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="text_singkat">Teks Singkat</option>
                                        <option value="paragraf">Paragraf</option>
                                        <option value="single_option">Pilihan Ganda</option>
                                        <option value="checkbox">Kotak Centang</option>
                                        <option value="dropdown">Dropdown</option>
                                        <option value="skala_linear">Skala Linear</option>
                                    </select>
                                </div>
                            </div>

                            {{-- TAMBAHKAN: Opsi untuk menjadikan pertanyaan kondisional --}}
                            <div class="mt-4" x-show="question.tipe_jawaban === 'single_option' || question.tipe_jawaban === 'dropdown'">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="question.is_conditional" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Jadikan Pertanyaan Kondisional (Lompat Section)</span>
                                </label>
                            </div>

                            {{-- MODIFIKASI: Tampilkan pilihan jawaban jika tipe-nya sesuai --}}
                            <div x-show="['single_option', 'checkbox', 'dropdown'].includes(question.tipe_jawaban)" class="mt-4 space-y-2">
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pilihan Jawaban</label>
                                <template x-for="(option, optionIndex) in question.pilihan" :key="option.clientId">
                                    <div class="flex items-center space-x-2">
                                        {{-- MODIFIKASI: Tambah input hidden untuk ID pilihan (penting untuk edit) --}}
                                        <input type="hidden" :name="`sections[${sectionIndex}][questions][${questionIndex}][pilihan][${optionIndex}][id]`" x-model="option.id">

                                        <input type="text" :name="`sections[${sectionIndex}][questions][${questionIndex}][pilihan][${optionIndex}][text]`" x-model="option.text" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Tulis pilihan..."/>
                                        
                                        {{-- TAMBAHKAN: Dropdown untuk logika lompat section --}}
                                        <div x-show="question.is_conditional" class="w-full">
                                            <select :name="`sections[${sectionIndex}][questions][${questionIndex}][pilihan][${optionIndex}][next_section_id]`" 
            x-model="option.next_section_id" 
            class="w-full ... rounded-md shadow-sm">
        
        <option :value="null">Lompat ke: Section Berikutnya (Default)</option>
        
        {{-- 1. Filter menggunakan clientId --}}
        <template x-for="targetSection in kuesioner.sections.filter(s => s.clientId !== section.clientId)">
            {{-- 2. Value dari option adalah clientId --}}
            <option :value="targetSection.clientId" x-text="targetSection.judul"></option>
        </template>
    </select>
                                        </div>

                                        <button type="button" @click="removeOption(sectionIndex, questionIndex, optionIndex)" class="text-red-500 hover:text-red-700 p-2">&times;</button>
                                    </div>
                                </template>
                                <button type="button" @click="addOption(sectionIndex, questionIndex)" class="text-sm text-purple-600 hover:text-purple-800">+ Tambah Pilihan</button>
                            </div>

                            <button type="button" @click="removeQuestion(sectionIndex, questionIndex)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                    {{-- TAMBAHKAN: Tombol untuk pertanyaan kondisi (Sesuai ide Anda) --}}
                    <button type="button" @click="addQuestion(sectionIndex, false)" class="mt-4 text-sm text-purple-600 hover:text-purple-800">+ Tambah Pertanyaan</button>
                    <button type="button" @click="addQuestion(sectionIndex, true)" class="mt-4 ml-4 text-sm text-indigo-600 hover:text-indigo-800">+ Pertanyaan Kondisional</button>
                </div>
            </div>
        </template>
    </div>

    {{-- Tombol Aksi Utama (TETAP SAMA) --}}
    <div class="mt-6 flex justify-between items-center">
        <button type="button" @click="addSection()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300">
            + Tambah Section
        </button>
        <div class="flex items-center">
            <a href="{{ route('admin.kuesioner.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 mr-4">Batal</a>
            <x-primary-button x-text="isEditMode ? 'Update Kuesioner' : 'Simpan Kuesioner'"></x-primary-button>
        </div>
    </div>
</form>