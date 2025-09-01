<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // MENAMPILKAN HALAMAN LOGIN
    public function create(string $role = 'standar'): View
    {
        return view('auth.login', ['role' => $role]);
    }

    // MENANGANI PERMINTAAN LOGIN
    public function store(Request $request): RedirectResponse
    {
        // Logika khusus untuk login Dosen via NIDN
        if ($request->role === 'dosen') {
            $request->validate(['nidn' => 'required|string']);

            $user = User::where('nidn', $request->nidn)->where('role', 'dosen')->first();

            if (! $user) {
                return back()->withErrors([
                    'nidn' => 'NIDN tidak ditemukan atau tidak terdaftar sebagai Dosen.',
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Logika default Breeze untuk login via Email & Password
        $loginRequest = LoginRequest::createFrom($request);
        $loginRequest->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    // DESTROY OTENTIFIKASI
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
