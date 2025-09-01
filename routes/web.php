<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KuesionerController;
use App\Http\Controllers\Admin\JawabanController;
use App\Http\Controllers\KuesionerUserController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.select-login-role');
})->name('login.selection');

Route::get('/login/{role?}', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/pilih-peran', function () {
    return view('auth.select-role');
})->name('register.selection');


/*
|--------------------------------------------------------------------------
| Rute untuk Pengguna yang Sudah Login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- Dashboard Pengguna ---
    // Mengarahkan ke dashboard yang sesuai berdasarkan peran pengguna.
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        // Menggunakan UserDashboardController untuk pengguna non-admin
        return app(UserDashboardController::class)->index();
    })->name('dashboard');

    // --- Manajemen Profil ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Pengisian Kuesioner (untuk Pengguna) ---
    Route::get('/kuesioner', [KuesionerUserController::class, 'index'])->name('kuesioner.user.index');
    Route::get('/kuesioner/{kuesioner}', [KuesionerUserController::class, 'show'])->name('kuesioner.user.show');
    Route::post('/kuesioner/{kuesioner}', [KuesionerUserController::class, 'store'])->name('kuesioner.user.store');
});


/*
|--------------------------------------------------------------------------
| Rute Khusus Admin (/admin/...)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // --- Dashboard Admin ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Manajemen Kuesioner ---
    Route::post('kuesioner/{kuesioner}/clone', [KuesionerController::class, 'clone'])->name('kuesioner.clone');
    Route::get('kuesioner/{kuesioner}/preview', [KuesionerController::class, 'preview'])->name('kuesioner.preview');
    Route::resource('kuesioner', KuesionerController::class);

    // --- Manajemen Jawaban ---
    Route::prefix('jawaban')->name('jawaban.')->group(function() {
        // Halaman utama dan CRUD untuk sesi jawaban
        Route::get('/', [JawabanController::class, 'index'])->name('index');
        Route::get('/{submissionUuid}', [JawabanController::class, 'show'])->name('show');
        Route::delete('/{submissionUuid}', [JawabanController::class, 'destroy'])->name('destroy');
        
        // Ekspor jawaban (ringkasan & detail)
        Route::get('/export', [JawabanController::class, 'export'])->name('export');
        Route::get('/export/{submissionUuid}', [JawabanController::class, 'exportDetail'])->name('exportDetail');

        // --- Rekapitulasi Penilaian Dosen (DIKELOMPOKKAN & DIPERBAIKI) ---
        Route::prefix('rekapitulasi/dosen')->name('rekap.dosen.')->group(function() {
            Route::get('/', [JawabanController::class, 'rekapDosenIndex'])->name('index');
            
            // !!! INI ADALAH PERBAIKAN UTAMA !!!
            // Menambahkan {kuesioner} agar Route Model Binding berfungsi dengan benar.
            Route::get('/{kuesioner}/{user}/export', [JawabanController::class, 'exportRekapitulasiPerDosen'])->name('export');
        });
    });
});


require __DIR__.'/auth.php';