<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Models\StatusPengisian;
use App\Models\Jawaban; // Import model Jawaban
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard untuk pengguna non-admin.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // --- LOGIKA PERHITUNGAN BARU ---

        // 1. Ambil semua ID kuesioner yang tersedia untuk peran pengguna ini
        $kuesionerTersediaIds = Kuesioner::where('status', 'aktif')
            ->where('target_user', $user->role)
            ->pluck('id');

        // 2. Ambil ID kuesioner yang sudah diisi (dari kedua sumber)
        $filledFromStatus = StatusPengisian::where('user_id', $user->id)
            ->where('status', 'sudah_diisi')
            ->pluck('kuesioner_id');

        $filledFromAnswers = Jawaban::where('user_id', $user->id)
            ->distinct()
            ->pluck('kuesioner_id');

        // Gabungkan keduanya untuk mendapatkan daftar lengkap kuesioner yang pernah diisi
        $totalFilledIds = $filledFromStatus->merge($filledFromAnswers)->unique();
        
        // 3. Hitung jumlahnya
        $jumlahSudahDiisi = $totalFilledIds->count();
        $jumlahBelumDiisi = $kuesionerTersediaIds->diff($totalFilledIds)->count();

        // --- AKHIR LOGIKA PERHITUNGAN BARU ---

        // PERBAIKAN: Menggunakan compact() untuk memastikan nama variabel di view sudah benar.
        return view('dashboard', compact('user', 'jumlahSudahDiisi', 'jumlahBelumDiisi'));
    }
}

