<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Jika Anda menggunakan Vite, sesuaikan dengan @vite --}}
</head>
<body class="bg-purple-700 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-lg">
        @php
            $role = request('role', 'standar');
        @endphp

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            <!-- Judul Form -->
            <h2 class="text-xl font-bold text-center mb-6 text-black">
                MASUK {{ $role === 'dosen' ? 'DOSEN' : '' }}
            </h2>

            @if ($role === 'dosen')
                <!-- NIDN (untuk Dosen) -->
                <div class="text-left">
                    <label for="nidn" class="block font-medium text-sm text-black">NIDN</label>
                    <input id="nidn" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="text" name="nidn" value="{{ old('nidn') }}" required autofocus />
                    <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
                </div>
            @else
                <!-- Email Address (untuk role standar) -->
                <div class="text-left">
                    <label for="email" class="block font-medium text-sm text-black">Email</label>
                    <input id="email" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password (untuk role standar) -->
                <div class="mt-4 text-left">
                    <label for="password" class="block font-medium text-sm text-black">Password</label>
                    <input id="password" class="block mt-1 w-full bg-white border-2 border-purple-600 text-black rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            @endif

            <!-- Remember Me & Lupa Password -->
            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                           class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500"
                           name="remember">
                    <span class="ms-2 text-sm text-black">Remember me</span>
                </label>

                @if ($role !== 'dosen' && Route::has('password.request'))
                    <a class="text-sm text-gray-600 hover:text-purple-600 rounded-md"
                       href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <!-- Tombol Masuk -->
            <div class="mt-6">
                <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 bg-white border-2 border-purple-600 rounded-md font-semibold text-xs text-purple-600 uppercase tracking-widest hover:bg-purple-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Masuk
                </button>
            </div>
        </form>
    </div>

</body>
</html>
