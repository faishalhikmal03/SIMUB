<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\Pertanyaan;
use App\Models\StatusPengisian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Import Str untuk menggunakan UUID

class KuesionerUserController extends Controller
{
    /**
     * Menampilkan daftar kuesioner yang tersedia untuk pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role;

        $kuesionerSudahDiisiIds = StatusPengisian::where('user_id', $user->id)
            ->where('status', 'sudah_diisi')
            ->pluck('kuesioner_id');

        $kuesioners = Kuesioner::where('status', 'aktif')
            ->where('target_user', $userRole)
            ->where(function ($query) use ($kuesionerSudahDiisiIds) {
                $query->where('bisa_diisi_ulang', true)
                      ->orWhereNotIn('id', $kuesionerSudahDiisiIds);
            })
            ->latest()
            ->paginate(10);

        return view('kuesioner.index', compact('kuesioners', 'kuesionerSudahDiisiIds'));
    }

    /**
     * Menampilkan halaman untuk mengisi kuesioner.
     */
    public function show(Kuesioner $kuesioner)
    {
        if ($kuesioner->status !== 'aktif' || $kuesioner->target_user !== Auth::user()->role) {
            abort(403, 'Anda tidak diizinkan mengakses kuesioner ini.');
        }

        if (!$kuesioner->bisa_diisi_ulang) {
            $sudahDiisi = StatusPengisian::where('user_id', Auth::id())
                ->where('kuesioner_id', $kuesioner->id)
                ->where('status', 'sudah_diisi')
                ->exists();
            
            if ($sudahDiisi) {
                return redirect()->route('kuesioner.user.index')->with('info', 'Anda sudah pernah mengisi kuesioner ini.');
            }
        }

        // --- PERUBAHAN KUNCI ---
        // Buat ID unik untuk sesi pengisian ini
        $submissionUuid = Str::uuid();

        $kuesioner->load('sections.pertanyaans.pilihanJawabans');
        
        // Kirim ID ke view
        return view('kuesioner.show', compact('kuesioner', 'submissionUuid'));
    }

    /**
     * Menyimpan jawaban kuesioner ke database.
     */
    public function store(Request $request, Kuesioner $kuesioner)
    {
        // --- PERUBAHAN KUNCI ---
        $validated = $request->validate([
            'answers' => 'required|array',
            'submission_uuid' => 'required|uuid', // Validasi ID sesi pengisian
        ]);

        $user = Auth::user();
        $answers = array_filter($validated['answers']);

        if (empty($answers)) {
            return back()->with('error', 'Anda harus mengisi setidaknya satu jawaban.');
        }

        DB::beginTransaction();
        try {
            $pertanyaanIds = array_keys($answers);
            $pertanyaans = Pertanyaan::whereIn('id', $pertanyaanIds)->get()->keyBy('id');

            foreach ($answers as $pertanyaanId => $jawabanData) {
                if (!isset($pertanyaans[$pertanyaanId])) {
                    continue; 
                }
                
                $pertanyaan = $pertanyaans[$pertanyaanId];
                
                // --- PERUBAHAN KUNCI ---
                $baseData = [
                    'submission_uuid' => $validated['submission_uuid'], // Gunakan ID dari form
                    'user_id' => $user->id,
                    'kuesioner_id' => $kuesioner->id,
                    'section_id' => $pertanyaan->section_id,
                    'pertanyaan_id' => $pertanyaanId,
                ];

                if (in_array($pertanyaan->tipe_jawaban, ['text_singkat', 'paragraf'])) {
                    Jawaban::create(array_merge($baseData, [
                        'jawaban_text' => $jawabanData,
                        'pilihan_jawaban_id' => null,
                    ]));
                } else { 
                    $pilihanIds = is_array($jawabanData) ? $jawabanData : [$jawabanData];
                    foreach ($pilihanIds as $pilihanId) {
                        Jawaban::create(array_merge($baseData, [
                            'pilihan_jawaban_id' => $pilihanId,
                            'jawaban_text' => null,
                        ]));
                    }
                }
            }

            // Logika StatusPengisian tetap relevan untuk kuesioner yang TIDAK BISA diisi ulang
            if (!$kuesioner->bisa_diisi_ulang) {
                StatusPengisian::updateOrCreate(
                    ['user_id' => $user->id, 'kuesioner_id' => $kuesioner->id],
                    ['status' => 'sudah_diisi']
                );
            }

            DB::commit();

            return redirect()->route('kuesioner.user.index')
                             ->with('success', 'Terima kasih telah mengisi kuesioner "' . $kuesioner->judul . '"!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan jawaban kuesioner: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban Anda. Silakan coba lagi.');
        }
    }
}

