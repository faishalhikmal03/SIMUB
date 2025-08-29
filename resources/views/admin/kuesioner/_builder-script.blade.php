<script>
    function kuesionerBuilder(config) {
        return {
            isEditMode: config.isEdit || false,
            actionUrl: config.action || '',
            kuesioner: {
                judul: '',
                deskripsi: '',
                target_user: 'mahasiswa',
                status: 'nonaktif',
                sections: []
            },
            activeSection: 0,
            
            // Fungsi-fungsi untuk memanipulasi data
            addSection() {
                this.kuesioner.sections.push({
                    id: null, // ID dari DB (untuk edit)
                    clientId: Date.now(), // ID unik di sisi klien
                    judul: `Section ${this.kuesioner.sections.length + 1}`,
                    deskripsi: '',
                    questions: []
                });
                this.activeSection = this.kuesioner.sections.length - 1;
                if(this.kuesioner.sections[this.activeSection].questions.length === 0){
                    this.addQuestion(this.activeSection);
                }
            },
            removeSection(index) {
                this.kuesioner.sections.splice(index, 1);
                if (this.activeSection >= index) {
                    this.activeSection = Math.max(0, this.activeSection - 1);
                }
            },
            addQuestion(sectionIndex, isConditional = false) {
                const question = {
                    id: null,
                    clientId: Date.now(),
                    pertanyaan: '',
                    tipe_jawaban: isConditional ? 'single_option' : 'text_singkat',
                    is_conditional: isConditional,
                    pilihan: []
                };
                this.kuesioner.sections[sectionIndex].questions.push(question);
                // Jika kondisional, otomatis tambah 2 pilihan (misal: Ya/Tidak)
                if(isConditional){
                    this.addOption(sectionIndex, this.kuesioner.sections[sectionIndex].questions.length - 1);
                    this.addOption(sectionIndex, this.kuesioner.sections[sectionIndex].questions.length - 1);
                }
            },
            removeQuestion(sectionIndex, questionIndex) {
                this.kuesioner.sections[sectionIndex].questions.splice(questionIndex, 1);
            },
            addOption(sectionIndex, questionIndex) {
                this.kuesioner.sections[sectionIndex].questions[questionIndex].pilihan.push({ 
                    id: null,
                    clientId: Date.now(),
                    text: '', 
                    next_section_id: null 
                });
            },
            removeOption(sectionIndex, questionIndex, optionIndex) {
                this.kuesioner.sections[sectionIndex].questions[questionIndex].pilihan.splice(optionIndex, 1);
            },

           // Fungsi Inisialisasi
            init() {
                if (this.isEditMode && config.existingData) {
                    this.kuesioner.judul = config.existingData.judul;
                    this.kuesioner.deskripsi = config.existingData.deskripsi;
                    this.kuesioner.target_user = config.existingData.target_user;
                    this.kuesioner.status = config.existingData.status;

                    this.kuesioner.sections = config.existingData.sections.map(section => ({
                        id: section.id,
                        clientId: section.id || Date.now(),
                        judul: section.judul,
                        deskripsi: section.deskripsi,
                        // MODIFIKASI 1: Pastikan 'pertanyaans' ada sebelum di-map
                        questions: (section.pertanyaans || []).map(question => ({
                            id: question.id,
                            clientId: question.id || Date.now(),
                            pertanyaan: question.pertanyaan,
                            tipe_jawaban: question.tipe_jawaban,
                            // MODIFIKASI 2: Cek 'pilihan_jawabans' sebelum digunakan
                            is_conditional: (question.pilihan_jawabans || []).some(p => p.next_section_id !== null),
                            // MODIFIKASI 3: Ganti nama relasi dari 'pilihan_jawabans' ke 'pilihan'
                            pilihan: (question.pilihan_jawabans || []).map(option => ({
                                id: option.id,
                                clientId: option.id || Date.now(),
                                text: option.pilihan, // Nama kolom di DB adalah 'pilihan'
                                next_section_id: option.next_section_id
                            }))
                        }))
                    }));
                }
                
                if (this.kuesioner.sections.length === 0) {
                    this.addSection();
                }
            }
        }
    }
</script>