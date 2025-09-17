<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kuesionerBuilder', (config) => ({
        // --- PROPERTI ---
        isEditMode: config.isEdit || false,
        actionUrl: config.action || '',
        dosenList: config.dosenList || [],
        activeSection: 0,
        kuesioner: {
            judul: '',
            deskripsi: '',
            target_user: 'mahasiswa',
            status: 'nonaktif',
            bisa_diisi_ulang: false,
            sections: []
        },

        // --- FUNGSI INISIALISASI (SUDAH DIPERBAIKI) ---
        init() {
            // Periksa 'kuesionerData' yang dikirim dari controller
            if (this.isEditMode && config.kuesionerData) {
                this.kuesioner = config.kuesionerData;

                // Pastikan setiap elemen memiliki clientId untuk reaktivitas Alpine
                this.kuesioner.sections.forEach(s => {
                    // Jika elemen baru (belum punya ID DB), clientId sudah ada dari Date.now()
                    // Jika elemen lama, kita pastikan clientId diisi dari ID DB
                    s.clientId = s.clientId || s.id; 
                    s.questions.forEach(q => {
                        q.clientId = q.clientId || q.id;
                        q.is_conditional = (q.pilihan || []).some(p => p.next_section_clientId !== null);
                        (q.pilihan || []).forEach(p => {
                            p.clientId = p.clientId || p.id;
                        });
                    });
                });
            }

            // Jika form baru dan belum ada section, buat satu default
            if (this.kuesioner.sections.length === 0) {
                this.addSection();
            }
        },

        // --- MANAJEMEN SECTION ---
        addSection() {
            this.kuesioner.sections.push({
                id: null,
                clientId: Date.now().toString(),
                judul: `Section ${this.kuesioner.sections.length + 1}`,
                deskripsi: '',
                questions: []
            });
            this.activeSection = this.kuesioner.sections.length - 1;
            if (this.kuesioner.sections[this.activeSection].questions.length === 0) {
                this.addQuestion(this.activeSection);
            }
        },
        removeSection(index) {
            if (confirm('Anda yakin ingin menghapus section ini?')) {
                this.kuesioner.sections.splice(index, 1);
                if (this.activeSection >= index) {
                    this.activeSection = Math.max(0, this.activeSection - 1);
                }
            }
        },

        // --- MANAJEMEN PERTANYAAN ---
        addQuestion(sectionIndex) {
            this.kuesioner.sections[sectionIndex].questions.push({
                id: null,
                clientId: Date.now().toString(),
                pertanyaan: '',
                tipe_jawaban: 'text_singkat',
                is_conditional: false,
                pilihan: []
            });
        },
        removeQuestion(sectionIndex, questionIndex) {
            this.kuesioner.sections[sectionIndex].questions.splice(questionIndex, 1);
        },

        // --- MANAJEMEN OPSI JAWABAN ---
        addOption(sectionIndex, questionIndex) {
            this.kuesioner.sections[sectionIndex].questions[questionIndex].pilihan.push({
                id: null,
                clientId: Date.now().toString(),
                text: '',
                value: null,
                next_section_clientId: null
            });
        },
        removeOption(sectionIndex, questionIndex, optionIndex) {
            this.kuesioner.sections[sectionIndex].questions[questionIndex].pilihan.splice(optionIndex, 1);
        },

        // --- LOGIKA BARU UNTUK TIPE JAWABAN DINAMIS ---
        handleTipeJawabanChange(question, newType) {
            if (newType === 'pilihan_dosen') {
                question.pilihan = this.dosenList.map(dosen => ({
                    id: null,
                    clientId: (Date.now() + dosen.id).toString(),
                    text: dosen.nama,
                    value: dosen.id
                }));
            } else if (['single_option', 'checkbox'].includes(newType)) {
                const isCurrentlyDosenOptions = question.pilihan.length > 0 && question.pilihan[0].value;
                if (isCurrentlyDosenOptions) {
                    question.pilihan = [];
                }
            }
        },
        
        // --- FUNGSI UNTUK SUBMIT FORM ---
        saveKuesioner(event) {
            const form = event.target;
            const input = form.querySelector('input[name="kuesioner_data"]');
            if(input){
                input.value = JSON.stringify(this.kuesioner);
                form.submit();
            } else {
                console.error('Error: Hidden input "kuesioner_data" tidak ditemukan.');
                alert('Terjadi kesalahan teknis saat menyimpan.');
            }
        }
    }));
});
</script>