<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Menggunakan data yang sudah divalidasi dari ProfileUpdateRequest
        $validatedData = $request->validated();

        // Mengisi data ke model pengguna
        $request->user()->fill($validatedData);

        // Jika email diubah, reset status verifikasi
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Simpan perubahan
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
    /**
     * Memperbarui foto profil pengguna.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validasi file foto
        $request->validate([
            'foto_profile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hapus foto lama jika ada
        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        // Simpan foto baru dan simpan path-nya
        $path = $request->file('foto_profile')->store('profile-photos', 'public');
        $user->foto_profile = $path;
        
        // Simpan perubahan ke database
        $user->save();

        // Redirect kembali dengan status spesifik untuk foto
        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }
}
