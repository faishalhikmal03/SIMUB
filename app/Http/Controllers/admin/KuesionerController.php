<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kuesioner;
use App\Models\Section;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class KuesionerController extends Controller
{
    /**
     * Menampilkan daftar semua kuesioner.
     */
    public function index()
    {
        $kuesioners = Kuesioner::withCount('pertanyaans')->latest()->paginate(10);
        return view('admin.kuesioner.index', compact('kuesioners'));
    }

    /**
     * Menampilkan form untuk membuat kuesioner baru.
     */
    public function create()
    {
        return view('admin.kuesioner.create');
    }

    /**
     * Menyimpan kuesioner baru beserta pertanyaannya ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_user' => 'required|in:mahasiswa,mahasiswa_baru,alumni,dosen',
            'status' => 'required|in:aktif,nonaktif',
            'sections' => 'required|array|min:1',
            'sections.*.judul' => 'required|string',
            'sections.*.deskripsi' => 'nullable|string',
            'sections.*.questions' => 'required|array|min:1',
            'sections.*.questions.*.pertanyaan' => 'required|string|max:255',
            'sections.*.questions.*.tipe_jawaban' => 'required|in:text_singkat,paragraf,single_option,checkbox',
            'sections.*.questions.*.pilihan' => 'nullable|array',
            'sections.*.questions.*.pilihan.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $kuesioner = Kuesioner::create(Arr::only($validated, ['judul', 'deskripsi', 'target_user', 'status']));

            foreach ($validated['sections'] as $sIndex => $sData) {
                $section = $kuesioner->sections()->create([
                    'judul' => $sData['judul'],
                    'deskripsi' => $sData['deskripsi'],
                    'urutan' => $sIndex + 1,
                ]);

                foreach ($sData['questions'] as $qData) {
                    $pertanyaan = $section->pertanyaans()->create([
                        'pertanyaan' => $qData['pertanyaan'],
                        'tipe_jawaban' => $qData['tipe_jawaban'],
                    ]);

                    if (in_array($qData['tipe_jawaban'], ['single_option', 'checkbox']) && isset($qData['pilihan'])) {
                        $pilihanData = collect($qData['pilihan'])->filter()->map(fn ($p) => ['pilihan' => $p])->all();
                        if (!empty($pilihanData)) {
                           $pertanyaan->pilihanJawabans()->createMany($pilihanData);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan kuesioner: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan internal saat menyimpan.');
        }
    }

    /**
     * Menampilkan pratinjau kuesioner.
     */
    public function preview(Kuesioner $kuesioner)
    {
        $kuesioner->load('sections.pertanyaans.pilihanJawabans');
        return view('admin.kuesioner.preview', compact('kuesioner'));
    }

    /**
     * Menampilkan form untuk mengedit kuesioner.
     */
    public function edit(Kuesioner $kuesioner)
    {
        $kuesioner->load('sections.pertanyaans.pilihanJawabans');

        $kuesionerData = [
            'sections' => $kuesioner->sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'judul' => $section->judul,
                    'deskripsi' => $section->deskripsi,
                    'questions' => $section->pertanyaans->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'pertanyaan' => $question->pertanyaan,
                            'tipe_jawaban' => $question->tipe_jawaban,
                            'pilihan' => $question->pilihanJawabans->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'text' => $option->pilihan,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];

        return view('admin.kuesioner.edit', compact('kuesioner', 'kuesionerData'));
    }

    /**
     * Memperbarui data kuesioner di database.
     */
    public function update(Request $request, Kuesioner $kuesioner)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_user' => 'required|in:mahasiswa,mahasiswa_baru,alumni,dosen',
            'status' => 'required|in:aktif,nonaktif',
            'sections' => 'required|array|min:1',
            'sections.*.id' => 'nullable|exists:sections,id',
            'sections.*.judul' => 'required|string',
            'sections.*.deskripsi' => 'nullable|string',
            'sections.*.questions' => 'required|array|min:1',
            'sections.*.questions.*.id' => 'nullable|exists:pertanyaans,id',
            'sections.*.questions.*.pertanyaan' => 'required|string',
            'sections.*.questions.*.tipe_jawaban' => 'required|in:text_singkat,paragraf,single_option,checkbox',
            'sections.*.questions.*.pilihan' => 'nullable|array',
            'sections.*.questions.*.pilihan.*.id' => 'nullable|exists:pilihan_jawabans,id',
            'sections.*.questions.*.pilihan.*.text' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $kuesioner->update(Arr::only($validated, ['judul', 'deskripsi', 'target_user', 'status']));

            $incomingSectionIds = [];
            foreach ($validated['sections'] as $sIndex => $sData) {
                $section = Section::updateOrCreate(
                    ['id' => $sData['id'] ?? null, 'kuesioner_id' => $kuesioner->id],
                    ['judul' => $sData['judul'], 'deskripsi' => $sData['deskripsi'], 'urutan' => $sIndex + 1]
                );
                $incomingSectionIds[] = $section->id;

                $incomingQuestionIds = [];
                foreach ($sData['questions'] as $qData) {
                    $pertanyaan = Pertanyaan::updateOrCreate(
                        ['id' => $qData['id'] ?? null, 'section_id' => $section->id],
                        ['pertanyaan' => $qData['pertanyaan'], 'tipe_jawaban' => $qData['tipe_jawaban']]
                    );
                    $incomingQuestionIds[] = $pertanyaan->id;

                    if (in_array($qData['tipe_jawaban'], ['single_option', 'checkbox']) && isset($qData['pilihan'])) {
                        $incomingOptionIds = [];
                        foreach ($qData['pilihan'] as $oData) {
                            if (isset($oData['text'])) {
                                $option = PilihanJawaban::updateOrCreate(
                                    ['id' => $oData['id'] ?? null, 'pertanyaan_id' => $pertanyaan->id],
                                    ['pilihan' => $oData['text']]
                                );
                                $incomingOptionIds[] = $option->id;
                            }
                        }
                        $pertanyaan->pilihanJawabans()->whereNotIn('id', $incomingOptionIds)->delete();
                    } else {
                        $pertanyaan->pilihanJawabans()->delete();
                    }
                }
                $section->pertanyaans()->whereNotIn('id', $incomingQuestionIds)->delete();
            }
            $kuesioner->sections()->whereNotIn('id', $incomingSectionIds)->delete();

            DB::commit();
            return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update kuesioner: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui kuesioner.');
        }
    }

    /**
     * Menghapus kuesioner dari database.
     */
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

    /**
     * Menduplikasi kuesioner beserta semua relasinya.
     */
    public function clone(Kuesioner $kuesioner)
    {
        DB::beginTransaction();
        try {
            // 1. Muat semua relasi dari kuesioner asli
            $kuesioner->load('sections.pertanyaans.pilihanJawabans');

            // 2. Duplikasi data kuesioner utama
            $cloneKuesioner = $kuesioner->replicate();
            $cloneKuesioner->judul = $kuesioner->judul . ' (Salinan)';
            $cloneKuesioner->status = 'nonaktif'; // Set status default menjadi draft
            $cloneKuesioner->created_at = now();
            $cloneKuesioner->updated_at = now();
            $cloneKuesioner->save();

            // 3. Loop dan duplikasi setiap section
            foreach ($kuesioner->sections as $section) {
                $cloneSection = $section->replicate();
                $cloneSection->kuesioner_id = $cloneKuesioner->id;
                $cloneSection->save();

                // 4. Loop dan duplikasi setiap pertanyaan di dalam section
                foreach ($section->pertanyaans as $pertanyaan) {
                    $clonePertanyaan = $pertanyaan->replicate();
                    $clonePertanyaan->section_id = $cloneSection->id;
                    $clonePertanyaan->save();

                    // 5. Loop dan duplikasi setiap pilihan jawaban
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
