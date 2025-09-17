<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\Section;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\StatusPengisian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class KuesionerController extends Controller
{
    // MENAMPILKAN DAFTAR SEMUA KUESIONER
    public function index()
    {
        $kuesioners = Kuesioner::withCount('pertanyaans')->latest()->paginate(10);
        return view('admin.kuesioner.index', compact('kuesioners'));
    }

    // MENAMPILKAN FORM UNTUK MEMBUAT KUESIONER BARU
    public function create()
    {
        $dosen = User::where('role', 'dosen')->orderBy('nama')->get(['id', 'nama']);
        return view('admin.kuesioner.create', compact('dosen'));
    }

    // MENYIMPAN KUESIONER KE DATABASE
    public function store(Request $request)
    {
        // 1. Validasi input utama yang berisi string JSON dari form
        $request->validate([
            'kuesioner_data' => 'required|json',
        ]);

        // 2. Decode string JSON menjadi array PHP
        $data = json_decode($request->input('kuesioner_data'), true);
        if (!$data) {
            return back()->with('error', 'Data form tidak valid atau kosong.');
        }

        // 3. Lakukan validasi terhadap isi dari data JSON
        $validated = validator($data, [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_user' => 'required|in:mahasiswa,mahasiswa_baru,alumni,dosen',
            'status' => 'required|in:aktif,nonaktif',
            'bisa_diisi_ulang' => 'required|boolean',
            'sections' => 'required|array|min:1',
            'sections.*.clientId' => 'required|string',
            'sections.*.judul' => 'required|string',
            'sections.*.deskripsi' => 'nullable|string',
            'sections.*.questions' => 'nullable|array',
            'sections.*.questions.*.pertanyaan' => 'required|string',
            'sections.*.questions.*.tipe_jawaban' => 'required|in:text_singkat,paragraf,single_option,checkbox,pilihan_dosen',
            'sections.*.questions.*.pilihan' => 'nullable|array',
            'sections.*.questions.*.pilihan.*.text' => 'required_if:sections.*.questions.*.tipe_jawaban,single_option,checkbox|string|max:255',
            'sections.*.questions.*.pilihan.*.value' => 'nullable',
        ])->validate();

        DB::beginTransaction();
        try {
            // 4. Buat entitas Kuesioner utama
            $kuesioner = Kuesioner::create(Arr::only($validated, ['judul', 'deskripsi', 'target_user', 'status', 'bisa_diisi_ulang']));

            $clientIdToDbIdMap = [];

            // 5. Looping untuk membuat setiap Section
            foreach ($validated['sections'] as $sIndex => $sData) {
                $section = $kuesioner->sections()->create([
                    'judul' => $sData['judul'],
                    'deskripsi' => $sData['deskripsi'],
                    'urutan' => $sIndex + 1,
                ]);
                $clientIdToDbIdMap[$sData['clientId']] = $section->id;
            }

            // 6. Looping kedua untuk membuat pertanyaan dan pilihan (setelah semua section ID diketahui)
            foreach ($validated['sections'] as $sData) {
                $sectionDbId = $clientIdToDbIdMap[$sData['clientId']];
                
                if (empty($sData['questions'])) continue;

                foreach ($sData['questions'] as $qData) {
                    $pertanyaan = Pertanyaan::create([
                        'section_id' => $sectionDbId,
                        'pertanyaan' => $qData['pertanyaan'],
                        'tipe_jawaban' => $qData['tipe_jawaban'],
                    ]);

                    // Jika ada pilihan jawaban, buat juga
                    if (in_array($qData['tipe_jawaban'], ['single_option', 'checkbox','pilihan_dosen']) && !empty($qData['pilihan'])) {
                        foreach ($qData['pilihan'] as $oData) {
                            $nextSectionDbId = isset($oData['next_section_clientId']) ? ($clientIdToDbIdMap[$oData['next_section_clientId']] ?? null) : null;
                            
                            $pertanyaan->pilihanJawabans()->create([
                                'pilihan' => $oData['text'],
                                'value' => $oData['value'] ?? null,
                                'next_section_id' => $nextSectionDbId
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner baru berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat kuesioner baru: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return back()->withInput()->with('error', 'Terjadi kesalahan teknis saat menyimpan kuesioner. ' . $e->getMessage());
        }
    }

    // PREVIEW KUESIONER
    public function preview(Kuesioner $kuesioner)
    {
        $kuesioner->load('sections.pertanyaans.pilihanJawabans');
        return view('admin.kuesioner.preview', compact('kuesioner'));
    }

    // MENAMPILKAN FORM EDIT KUESIONER
    public function edit(Kuesioner $kuesioner)
    {
        $dosen = User::where('role', 'dosen')->orderBy('nama')->get(['id', 'nama']);

        $kuesioner->load('sections.pertanyaans.pilihanJawabans');

        $kuesionerData = [
            'id' => $kuesioner->id,
            'judul' => $kuesioner->judul,
            'deskripsi' => $kuesioner->deskripsi,
            'target_user' => $kuesioner->target_user,
            'status' => $kuesioner->status,
            'bisa_diisi_ulang' => (bool) $kuesioner->bisa_diisi_ulang,
            'sections' => $kuesioner->sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'clientId' => $section->id,
                    'judul' => $section->judul,
                    'deskripsi' => $section->deskripsi,
                    'questions' => $section->pertanyaans->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'clientId' => $question->id,
                            'pertanyaan' => $question->pertanyaan,
                            'tipe_jawaban' => $question->tipe_jawaban,
                            'pilihan' => $question->pilihanJawabans->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'clientId' => $option->id,
                                    'text' => $option->pilihan,
                                    'value' => $option->value,
                                    'next_section_clientId' => $option->next_section_id,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];

        return view('admin.kuesioner.edit', compact('kuesioner', 'kuesionerData', 'dosen'));
    }

    // MEMPERBARUI KUESIONER
    public function update(Request $request, Kuesioner $kuesioner)
    {
        $data = json_decode($request->input('kuesioner_data'), true);
        if (!$data) {
            return back()->with('error', 'Data form tidak valid.');
        }

        $validated = validator($data, [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_user' => 'required|in:mahasiswa,mahasiswa_baru,alumni,dosen',
            'status' => 'required|in:aktif,nonaktif',
            'bisa_diisi_ulang' => 'required|boolean',
            'sections' => 'required|array|min:1',
            'sections.*.id' => 'nullable|exists:sections,id,kuesioner_id,' . $kuesioner->id,
            'sections.*.clientId' => 'required',
            'sections.*.judul' => 'required|string',
            'sections.*.deskripsi' => 'nullable|string',
            'sections.*.questions' => 'nullable|array',
            'sections.*.questions.*.id' => 'nullable|exists:pertanyaans,id',
            'sections.*.questions.*.pertanyaan' => 'required|string',
            'sections.*.questions.*.tipe_jawaban' => 'required|in:text_singkat,paragraf,single_option,checkbox,pilihan_dosen',
            'sections.*.questions.*.pilihan' => 'nullable|array',
            'sections.*.questions.*.pilihan.*.id' => 'nullable|exists:pilihan_jawabans,id',
            'sections.*.questions.*.pilihan.*.text' => 'required_with:sections.*.questions.*.pilihan|string|max:255',
            'sections.*.questions.*.pilihan.*.value' => 'nullable',
            'sections.*.questions.*.pilihan.*.next_section_clientId' => 'nullable',
        ])->validate();

        DB::beginTransaction();
        try {
            $kuesioner->update(Arr::only($validated, ['judul', 'deskripsi', 'target_user', 'status', 'bisa_diisi_ulang']));

            $incomingSectionIds = [];
            $clientIdToDbIdMap = [];

            foreach ($validated['sections'] as $sIndex => $sData) {
                $section = Section::updateOrCreate(
                    ['id' => $sData['id'] ?? null],
                    ['kuesioner_id' => $kuesioner->id, 'judul' => $sData['judul'], 'deskripsi' => $sData['deskripsi'], 'urutan' => $sIndex + 1]
                );
                $incomingSectionIds[] = $section->id;
                $clientIdToDbIdMap[$sData['clientId']] = $section->id;
            }

            foreach ($validated['sections'] as $sData) {
                $sectionDbId = $clientIdToDbIdMap[$sData['clientId']];

                $incomingQuestionIds = [];
                if (!empty($sData['questions'])) {
                    foreach ($sData['questions'] as $qData) {
                        $pertanyaan = Pertanyaan::updateOrCreate(
                            ['id' => $qData['id'] ?? null],
                            ['section_id' => $sectionDbId, 'pertanyaan' => $qData['pertanyaan'], 'tipe_jawaban' => $qData['tipe_jawaban']]
                        );
                        $incomingQuestionIds[] = $pertanyaan->id;

                        if (in_array($qData['tipe_jawaban'], ['single_option', 'checkbox', 'pilihan_dosen']) && !empty($qData['pilihan'])) {
                            $incomingOptionIds = [];
                            foreach ($qData['pilihan'] as $oData) {
                                $nextSectionDbId = null;
                                if (isset($oData['next_section_clientId']) && !empty($oData['next_section_clientId'])) {
                                    $nextSectionDbId = $clientIdToDbIdMap[$oData['next_section_clientId']] ?? null;
                                }

                                $option = PilihanJawaban::updateOrCreate(
                                    ['id' => $oData['id'] ?? null],
                                    ['pertanyaan_id' => $pertanyaan->id, 'pilihan' => $oData['text'], 'value' => $oData['value'] ?? null, 'next_section_id' => $nextSectionDbId]
                                );
                                $incomingOptionIds[] = $option->id;
                            }
                            $pertanyaan->pilihanJawabans()->whereNotIn('id', $incomingOptionIds)->delete();
                        } else {
                            $pertanyaan->pilihanJawabans()->delete();
                        }
                    }
                }
                Section::find($sectionDbId)->pertanyaans()->whereNotIn('id', $incomingQuestionIds)->delete();
            }
            $kuesioner->sections()->whereNotIn('id', $incomingSectionIds)->delete();

            DB::commit();
            return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update kuesioner: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui kuesioner. ' . $e->getMessage());
        }
    }

    // HAPUS KUESIONER DARI DATABASE
    public function destroy(Kuesioner $kuesioner)
    {
        try {
            $kuesioner->delete();
            return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus kuesioner: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus kuesioner.');
        }
    }

    // SALIN KUESIONER
    public function clone(Kuesioner $kuesioner)
    {
        DB::beginTransaction();
        try {
            $kuesioner->load('sections.pertanyaans.pilihanJawabans');

            $cloneKuesioner = $kuesioner->replicate();
            $cloneKuesioner->judul = $kuesioner->judul . ' (Salinan)';
            $cloneKuesioner->status = 'nonaktif';
            $cloneKuesioner->created_at = now();
            $cloneKuesioner->updated_at = now();
            $cloneKuesioner->save();

            foreach ($kuesioner->sections as $section) {
                $cloneSection = $section->replicate();
                $cloneSection->kuesioner_id = $cloneKuesioner->id;
                $cloneSection->save();

                foreach ($section->pertanyaans as $pertanyaan) {
                    $clonePertanyaan = $pertanyaan->replicate();
                    $clonePertanyaan->section_id = $cloneSection->id;
                    $clonePertanyaan->save();

                    foreach ($pertanyaan->pilihanJawabans as $pilihan) {
                        $clonePilihan = $pilihan->replicate();
                        $clonePilihan->pertanyaan_id = $clonePertanyaan->id;
                        $clonePilihan->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil diduplikasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menduplikasi kuesioner: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menduplikasi kuesioner.');
        }
    }
}