<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\StatusPengisian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KuesionerUserController extends Controller
{
    /**
     * Menampilkan daftar kuesioner yang tersedia untuk pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role;

        // Ambil ID kuesioner yang sudah diisi oleh pengguna
        $kuesionerSudahDiisiIds = StatusPengisian::where('user_id', $user->id)
            ->where('status', 'sudah_diisi')
            ->pluck('kuesioner_id');

        $kuesioners = Kuesioner::where('status', 'aktif')
            ->where('target_user', $userRole)
            // Kecualikan kuesioner yang sudah diisi
            ->whereNotIn('id', $kuesionerSudahDiisiIds)
            ->latest()
            ->paginate(10);

        return view('kuesioner.index', compact('kuesioners'));
    }

    /**
     * Menampilkan halaman untuk mengisi kuesioner.
     */
    public function show(Kuesioner $kuesioner)
    {
        // Pastikan pengguna berhak mengisi kuesioner ini
        if ($kuesioner->status !== 'aktif' || $kuesioner->target_user !== Auth::user()->role) {
            abort(403, 'Anda tidak diizinkan mengakses kuesioner ini.');
        }

        $kuesioner->load('sections.pertanyaans.pilihanJawabans');
        return view('kuesioner.show', compact('kuesioner'));
    }

    /**
     * Menyimpan jawaban kuesioner ke database.
     */
    public function store(Request $request, Kuesioner $kuesioner)
    {
        // Validasi sederhana, bisa diperketat jika perlu
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            foreach ($validated['answers'] as $pertanyaanId => $jawabanData) {
                // Untuk checkbox, jawabanData adalah array. Untuk lainnya, string.
                $jawabans = is_array($jawabanData) ? $jawabanData : [$jawabanData];

                foreach ($jawabans as $jawaban) {
                    Jawaban::create([
                        'user_id' => $user->id,
                        'kuesioner_id' => $kuesioner->id,
                        'pertanyaan_id' => $pertanyaanId,
                        // Cek apakah jawaban adalah ID (dari radio/checkbox) atau teks bebas
                        'pilihan_jawaban_id' => is_numeric($jawaban) ? $jawaban : null,
                        'jawaban_text' => !is_numeric($jawaban) ? $jawaban : null,
                    ]);
                }
            }

            // Tandai bahwa pengguna ini sudah selesai mengisi kuesioner
            StatusPengisian::updateOrCreate(
                ['user_id' => $user->id, 'kuesioner_id' => $kuesioner->id],
                ['status' => 'sudah_diisi']
            );

            DB::commit();

            return redirect()->route('kuesioner.user.index')
                             ->with('success', 'Terima kasih telah mengisi kuesioner "' . $kuesioner->judul . '"!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan jawaban kuesioner: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban Anda. Silakan coba lagi.');
        }
    }
}
