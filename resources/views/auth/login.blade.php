<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    {{-- Menggunakan Font Inter dari Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
{{-- Latar belakang dengan gradasi yang sama seperti halaman daftar --}}
<body class="bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-2xl p-8 sm:p-10 w-full max-w-md shadow-2xl">
        @php
            // Mengambil dan memformat nama peran untuk ditampilkan
            $role = request('role', 'standar');
            $roleFormatted = ucwords(str_replace('-', ' ', $role));
             if (str_contains($roleFormatted, 'Mahasiswa Baru')) {
                $roleFormatted = 'Mahasiswa Baru';
            }
        @endphp

        <div class="text-center mb-8">
             <a href="/" class="inline-block">
                 <img src="{{ asset('images/Logo FMIPA.png') }}" alt="Logo FMIPA" class="w-20 h-20 mx-auto mb-4">
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali!</h1>
            <p class="text-gray-500">Masuk sebagai <span class="font-semibold text-purple-600">{{ $roleFormatted }}</span></p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            @if ($role === 'dosen')
                <div>
                    <x-input-label for="nidn" value="NIDN (Nomor Induk Dosen Nasional)" />
                    <x-text-input id="nidn" class="block mt-1 w-full" type="text" name="nidn" :value="old('nidn')" required autofocus />
                    <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
                </div>
            @else
                <div>
                    <x-input-label for="email" value="Alamat Email" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="Kata Sandi" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                </label>

                @if ($role !== 'dosen' && Route::has('password.request'))
                    <a class="text-sm font-medium text-purple-600 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <div>
                <button type="submit" 
                        class="w-full justify-center inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 transform hover:scale-105">
                    Masuk
                </button>
            </div>
            
             <div class="text-center pt-4">
                 <a class="text-sm font-medium text-gray-500 hover:text-purple-600" href="{{ route('login.selection') }}">
                    Kembali
                </a>
            </div>
        </form>
    </div>

</body>
</html>