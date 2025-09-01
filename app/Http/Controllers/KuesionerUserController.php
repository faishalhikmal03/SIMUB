<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\StatusPengisian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KuesionerUserController extends Controller
{
    /**
     * Menampilkan daftar kuesioner yang tersedia.
     */
    public function index()
    {
        // ... (Logika index Anda sudah benar, tidak perlu diubah) ...
        $user = Auth::user();
        $userRole = $user->role;
        $kuesionerYangTidakBisaDiisiLagiIds = Kuesioner::where('bisa_diisi_ulang', false)
            ->whereHas('statusPengisian', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'sudah_diisi');
            })
            ->pluck('id');
        $kuesioners = Kuesioner::where('status', 'aktif')
            ->where('target_user', $userRole)
            ->whereNotIn('id', $kuesionerYangTidakBisaDiisiLagiIds)
            ->latest()
            ->paginate(10);
        $kuesionerSudahDiisiIds = StatusPengisian::where('user_id', $user->id)
            ->where('status', 'sudah_diisi')
            ->pluck('kuesioner_id');
        return view('kuesioner.index', compact('kuesioners', 'kuesionerSudahDiisiIds'));
    }

    /**
     * Menampilkan halaman untuk mengisi kuesioner.
     * Kita tidak perlu lagi $dosen_id_to_evaluate di sini.
     */
    public function show(Kuesioner $kuesioner)
    {
        // ... (Logika otorisasi Anda sudah benar) ...
        $submissionUuid = Str::uuid();
        $kuesioner->load('sections.pertanyaans.pilihanJawabans');
        return view('kuesioner.show', compact('kuesioner', 'submissionUuid'));
    }

    /**
     * Menyimpan jawaban kuesioner ke database. (DIREFAKTOR TOTAL)
     */
    /**
     * Menyimpan jawaban kuesioner ke database. (DIREFAKTOR TOTAL)
     */
 public function store(Request $request, Kuesioner $kuesioner)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'submission_uuid' => 'required|uuid',
        ]);

        $user = Auth::user();
        $answers = $validated['answers'];
        $dosenIdYangDinilai = null;
        $pilihanJawabanDosenId = null; // Simpan ID pilihan jawaban untuk dosen

        DB::beginTransaction();
        try {
            $pertanyaanIds = array_keys($answers);
            $pertanyaans = Pertanyaan::whereIn('id', $pertanyaanIds)->get()->keyBy('id');

            // --- LANGKAH 1 (DIPERBAIKI): Cari ID Dosen dan ID Pilihan Jawabannya ---
            foreach ($answers as $pertanyaanId => $jawabanData) {
                if (isset($pertanyaans[$pertanyaanId]) && $pertanyaans[$pertanyaanId]->tipe_jawaban == 'pilihan_dosen') {
                    if (isset($jawabanData['pilihan_id'])) {
                        $pilihanJawabanDosenId = $jawabanData['pilihan_id'];
                        // Cari baris pilihan jawaban untuk mendapatkan 'value' (ID dosen)
                        $pilihanRow = PilihanJawaban::find($pilihanJawabanDosenId);
                        if ($pilihanRow) {
                            $dosenIdYangDinilai = $pilihanRow->value;
                        }
                    }
                    break;
                }
            }

            // --- LANGKAH 2 (DIPERBAIKI): Simpan semua jawaban ---
            foreach ($answers as $pertanyaanId => $jawabanData) {
                if (!isset($pertanyaans[$pertanyaanId]) || empty($jawabanData)) continue;
                
                $pertanyaan = $pertanyaans[$pertanyaanId];
                
                $baseData = [
                    'submission_uuid' => $validated['submission_uuid'],
                    'user_id' => $user->id,
                    'kuesioner_id' => $kuesioner->id,
                    'section_id' => $pertanyaan->section_id,
                    'pertanyaan_id' => $pertanyaanId,
                    'dosen_id' => $dosenIdYangDinilai,
                ];

                if (isset($jawabanData['jawaban'])) {
                    Jawaban::create(array_merge($baseData, ['jawaban_text' => $jawabanData['jawaban']]));
                
                } else if (isset($jawabanData['pilihan_id'])) {
                    $pilihanIds = is_array($jawabanData['pilihan_id']) ? $jawabanData['pilihan_id'] : [$jawabanData['pilihan_id']];
                    
                    foreach ($pilihanIds as $pilihanId) {
                        // Untuk semua jenis pertanyaan (dosen, kondisional, biasa),
                        // $pilihanId sekarang adalah ID dari tabel pilihan_jawabans.
                        Jawaban::create(array_merge($baseData, [
                            'pilihan_jawaban_id' => $pilihanId,
                        ]));
                    }
                }
            }

            if (!$kuesioner->bisa_diisi_ulang) {
                StatusPengisian::updateOrCreate(
                    ['user_id' => $user->id, 'kuesioner_id' => $kuesioner->id],
                    ['status' => 'sudah_diisi']
                );
            }

            DB::commit();
            return redirect()->route('kuesioner.user.index')->with('success', 'Terima kasih telah mengisi kuesioner!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan jawaban: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan teknis saat menyimpan jawaban Anda.');
        }
    }}