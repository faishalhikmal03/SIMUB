<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Models\StatusPengisian;
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

        // Jika yang login adalah admin, arahkan ke dashboard admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Menghitung jumlah kuesioner yang sudah diisi oleh pengguna
        $kuesionerSudahDiisi = StatusPengisian::where('user_id', $user->id)
            ->where('status', 'sudah_diisi')
            ->count();

        // Ambil ID kuesioner yang sudah diisi
        $kuesionerSudahDiisiIds = StatusPengisian::where('user_id', $user->id)
            ->where('status', 'sudah_diisi')
            ->pluck('kuesioner_id');

        // Menghitung kuesioner yang belum diisi (aktif, sesuai role, dan belum diisi)
        $kuesionerBelumDiisi = Kuesioner::where('status', 'aktif')
            ->where('target_user', $user->role)
            ->whereNotIn('id', $kuesionerSudahDiisiIds)
            ->count();

        return view('dashboard', [
            'user' => $user,
            'kuesionerSudahDiisi' => $kuesionerSudahDiisi,
            'kuesionerBelumDiisi' => $kuesionerBelumDiisi,
        ]);
    }
}
