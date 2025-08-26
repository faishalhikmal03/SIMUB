<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Jika Anda menggunakan Vite, sesuaikan dengan @vite --}}
</head>
<body class="bg-purple-600 flex items-center justify-center min-h-screen py-12">

    <div class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-lg">
        @php
            $role = request('role', 'mahasiswa');
        @endphp

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            <!-- Judul Form -->
            <h2 class="text-xl font-bold text-center mb-6 text-black">
                DAFTAR SEBAGAI {{ Str::upper(str_replace('_', ' ', $role)) }}
            </h2>

            <!-- Nama Lengkap -->
            <div class="text-left">
                <label for="nama" class="block font-medium text-sm text-black">Nama Lengkap</label>
                <input id="nama" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="text" name="nama" value="{{ old('nama') }}" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            {{-- Input Dinamis Berdasarkan Role --}}
            @if ($role === 'mahasiswa' || $role === 'mahasiswa_baru')
                <!-- NPM -->
                <div class="mt-4 text-left">
                    <label for="npm" class="block font-medium text-sm text-black">NPM</label>
                    <input id="npm" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="text" name="npm" value="{{ old('npm') }}" required />
                    <x-input-error :messages="$errors->get('npm')" class="mt-2" />
                </div>
            @elseif ($role === 'alumni')
                <!-- Tanggal Yudisium -->
                <div class="mt-4 text-left">
                    <label for="tanggal_yudisium" class="block font-medium text-sm text-black">Tanggal Yudisium</label>
                    <input id="tanggal_yudisium" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="date" name="tanggal_yudisium" value="{{ old('tanggal_yudisium') }}" required />
                    <x-input-error :messages="$errors->get('tanggal_yudisium')" class="mt-2" />
                </div>
            @elseif ($role === 'dosen')
                <!-- NIDN -->
                <div class="mt-4 text-left">
                    <label for="nidn" class="block font-medium text-sm text-black">NIDN</label>
                    <input id="nidn" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="text" name="nidn" value="{{ old('nidn') }}" required />
                    <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
                </div>
            @endif

            {{-- Sembunyikan Email & Password untuk Dosen --}}
            @if ($role !== 'dosen')
                <!-- Email -->
                <div class="mt-4 text-left">
                    <label for="email" class="block font-medium text-sm text-black">Email</label>
                    <input id="email" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4 text-left">
                    <label for="password" class="block font-medium text-sm text-black">Password</label>
                    <input id="password" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Konfirmasi Password -->
                <div class="mt-4 text-left">
                    <label for="password_confirmation" class="block font-medium text-sm text-black">Konfirmasi Password</label>
                    <input id="password_confirmation" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center justify-end mt-6">
                <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md hover:text-purple-600" href="{{ route('login.selection') }}">
                    {{ __('Sudah punya akun?') }}
                </a>

                <button type="submit" class="ms-4 w-auto justify-center inline-flex items-center px-4 py-2 bg-white border-2 border-purple-600 rounded-md font-semibold text-xs text-purple-600 uppercase tracking-widest hover:bg-purple-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Daftar
                </button>
            </div>
        </form>
    </div>

</body>
</html>
