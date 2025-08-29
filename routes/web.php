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

// Halaman login sekarang menerima parameter peran
Route::get('/login/{role?}', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

// Halaman pemilihan peran untuk registrasi
Route::get('/pilih-peran', function () {
    return view('auth.select-role');
})->name('register.selection');


/*
|--------------------------------------------------------------------------
| Rute untuk Pengguna yang Sudah Login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard utama (dengan logika redirect untuk admin)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard', compact('user'));
    })->name('dashboard');

    // Halaman Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman Daftar Kuesioner untuk pengguna biasa
    Route::get('/kuesioner', [KuesionerUserController::class, 'index'])->name('kuesioner.user.index');

    // Menampilkan halaman untuk mengisi kuesioner
    Route::get('/kuesioner/{kuesioner}', [KuesionerUserController::class, 'show'])->name('kuesioner.user.show');
    // Menyimpan jawaban dari kuesioner
    Route::post('/kuesioner/{kuesioner}', [KuesionerUserController::class, 'store'])->name('kuesioner.user.store');
    //statistik Sederhana
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| Rute Khusus Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pratinjau Kuesioner
    Route::get('kuesioner/{kuesioner}/preview', [KuesionerController::class, 'preview'])->name('kuesioner.preview');

     // Clone Kuesioner
    Route::post('kuesioner/{kuesioner}/clone', [KuesionerController::class, 'clone'])->name('kuesioner.clone');

    // Manajemen Kuesioner (CRUD)
    Route::resource('kuesioner', KuesionerController::class);

    
    //--- Manajemen Jawaban (Direfaktor) ---
    Route::prefix('jawaban')->name('jawaban.')->group(function() {
        // Halaman utama yang menampilkan setiap sesi pengisian
        Route::get('/', [JawabanController::class, 'index'])->name('index');

        // Menampilkan detail dari SATU sesi pengisian
        Route::get('/{submissionUuid}', [JawabanController::class, 'show'])->name('show');
        
        // Menghapus SATU sesi pengisian
        Route::delete('/{submissionUuid}', [JawabanController::class, 'destroy'])->name('destroy');
        
        // Mengekspor ringkasan (menggunakan parameter filter dari query string)
        Route::get('/export', [JawabanController::class, 'export'])->name('export');
        
        // Mengekspor jawaban detail dari SATU sesi pengisian spesifik
        Route::get('/export/{submissionUuid}', [JawabanController::class, 'exportDetail'])->name('exportDetail');
    });

});


require __DIR__.'/auth.php';
