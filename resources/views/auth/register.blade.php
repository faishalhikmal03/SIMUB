<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    {{-- Menggunakan Font Inter dari Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
{{-- Latar belakang dengan gradasi halus --}}
<body class="bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-2xl p-8 sm:p-10 w-full max-w-md shadow-2xl">
        @php
            $role = request('role', 'mahasiswa');
            $roleFormatted = ucwords(str_replace('_', ' ', $role));
        @endphp

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h1>
            <p class="text-gray-500">Daftar sebagai <span class="font-semibold text-purple-600">{{ $roleFormatted }}</span></p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            <div>
                <x-input-label for="nama" value="Nama Lengkap" />
                <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            {{-- Input Dinamis Berdasarkan Role --}}
            @if ($role === 'mahasiswa' || $role === 'mahasiswa_baru')
                <div>
                    <x-input-label for="npm" value="NPM (Nomor Pokok Mahasiswa)" />
                    <x-text-input id="npm" class="block mt-1 w-full" type="text" name="npm" :value="old('npm')" required />
                    <x-input-error :messages="$errors->get('npm')" class="mt-2" />
                </div>
            @elseif ($role === 'alumni')
                <div>
                    <x-input-label for="tanggal_yudisium" value="Tanggal Yudisium" />
                    <x-text-input id="tanggal_yudisium" class="block mt-1 w-full" type="date" name="tanggal_yudisium" :value="old('tanggal_yudisium')" required />
                    <x-input-error :messages="$errors->get('tanggal_yudisium')" class="mt-2" />
                </div>
            @elseif ($role === 'dosen')
                <div>
                    <x-input-label for="nidn" value="NIDN (Nomor Induk Dosen Nasional)" />
                    <x-text-input id="nidn" class="block mt-1 w-full" type="text" name="nidn" :value="old('nidn')" required />
                    <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
                </div>
            @endif

            {{-- Sembunyikan Email & Password untuk Dosen --}}
            @if ($role !== 'dosen')
                <div>
                    <x-input-label for="email" value="Alamat Email" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="Kata Sandi" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center justify-between pt-4">
                <a class="text-sm font-medium text-purple-600 hover:underline" href="{{ route('login.selection') }}">
                    {{ __('Sudah punya akun?') }}
                </a>

                <button type="submit" 
                        class="ms-4 inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 transform hover:scale-105">
                    Daftar
                </button>
            </div>
        </form>
    </div>

</body>
</html>