<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kuesioner;
use App\Models\Jawaban;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data statistik.
     */
    public function index()
    {
        // 1. Menghitung jumlah pengguna untuk setiap role
        $userCounts = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        // 2. Menghitung total kuesioner yang telah dibuat
        $kuesionerCount = Kuesioner::count();

        // 3. Menghitung jumlah PENGGUNA UNIK (responden) yang sudah mengisi, dikelompokkan per role.
        $submissionCounts = Jawaban::join('users', 'jawabans.user_id', '=', 'users.id')
            ->select('users.role', DB::raw('count(DISTINCT jawabans.user_id) as total'))
            ->groupBy('users.role')
            ->pluck('total', 'users.role');

        // Kirim semua variabel yang dibutuhkan ke view
        return view('admin.dashboard', compact('userCounts', 'kuesionerCount', 'submissionCounts'));
    }
}