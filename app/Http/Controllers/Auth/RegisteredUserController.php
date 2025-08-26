<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Menangani permintaan registrasi yang masuk.
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->role;
        $user = null;

        // 2. Logika khusus untuk pendaftaran Dosen
        if ($role === 'dosen') {
            $request->validate([
                'nama' => ['required', 'string', 'max:255'],
                'nidn' => ['required', 'string', 'max:255', 'unique:'.User::class],
                'role' => ['required', 'string', 'in:dosen'],
            ]);

            $user = User::create([
                'nama' => $request->nama,
                'nidn' => $request->nidn,
                'role' => $request->role,
                // Buat email dummy yang unik berdasarkan NIDN
                'email' => $request->nidn . '@example.com',
                // Buat password acak yang aman karena tidak akan digunakan untuk login
                'password' => Hash::make(Str::random(16)),
            ]);
        } 
        // 3. Logika untuk peran lainnya (tetap sama)
        else {
            $request->validate([
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'in:mahasiswa,mahasiswa_baru,alumni'],
                'npm' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
                'tanggal_yudisium' => ['nullable', 'date'],
            ]);

            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'npm' => $request->npm,
                'tanggal_yudisium' => $request->tanggal_yudisium,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
