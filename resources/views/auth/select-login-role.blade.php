<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran untuk Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Jika Anda menggunakan Vite, sesuaikan dengan @vite --}}
</head>
<body class="bg-purple-700 flex items-center justify-center min-h-screen">

    <div class="bg-white dark:bg-white rounded-2xl p-8 w-full max-w-sm text-center shadow-lg">
        
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <a href="/">
                <img src="{{ asset('images/Logo FMIPA.png') }}" alt="Logo FMIPA" class="w-24 h-24">
            </a>
        </div>

        <!-- Judul -->
        <h2 class="text-xl font-bold mb-8 text-black">MASUK SEBAGAI</h2>

        <div class="space-y-4">
            <a href="{{ route('login', ['role' => 'standar']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm 
                      hover:bg-purple-600 hover:text-white 
                      transition-colors duration-200 font-semibold">
                Mahasiswa
            </a>
            <a href="{{ route('login', ['role' => 'standar']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm 
                      hover:bg-purple-600 hover:text-white 
                      transition-colors duration-200 font-semibold">
                Mahasiswa Baru
            </a>
            <a href="{{ route('login', ['role' => 'standar']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm 
                      hover:bg-purple-600 hover:text-white 
                      transition-colors duration-200 font-semibold">
                Alumni
            </a>
            <a href="{{ route('login', ['role' => 'dosen']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm 
                      hover:bg-purple-600 hover:text-white 
                      transition-colors duration-200 font-semibold">
                Dosen
            </a>
            <a href="{{ route('login', ['role' => 'standar']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm 
                      hover:bg-purple-600 hover:text-white 
                      transition-colors duration-200 font-semibold">
                Admin
            </a>
        </div>

        <!-- Link Register -->
        <p class="mt-8 text-sm text-gray-600">
            Belum punya akun? 
            <a href="{{ route('register.selection') }}" class="font-bold text-purple-600 hover:underline">Daftar</a>
        </p>
    </div>

</body>
</html>
