<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran untuk Login</title>
    {{-- Menggunakan Font Inter dari Google Fonts untuk tampilan yang lebih modern --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Jika Anda menggunakan Vite, sesuaikan dengan @vite --}}
    <style>
        /* Menetapkan font default ke Inter */
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center min-h-screen p-4">
    
    {{-- Kartu Utama dengan Layout Dua Kolom --}}
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex overflow-hidden">
        
        <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
            <div class="flex justify-start mb-6">
                <a href="/">
                    <img src="{{ asset('images/Logo FMIPA.png') }}" alt="Logo FMIPA" class="w-20 h-20">
                </a>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang!</h1>
            <p class="text-gray-600 mb-8">Silakan pilih peran Anda untuk masuk.</p>

            <div class="space-y-4">
                <a href="{{ route('login', ['role' => 'mahasiswa']) }}" 
                   class="block w-full text-center py-3 px-4 rounded-lg font-semibold text-purple-700 bg-purple-100 hover:bg-purple-600 hover:text-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                    Mahasiswa
                </a>
                <a href="{{ route('login', ['role' => 'mahasiswa_baru']) }}" 
                   class="block w-full text-center py-3 px-4 rounded-lg font-semibold text-purple-700 bg-purple-100 hover:bg-purple-600 hover:text-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                    Mahasiswa Baru
                </a>
                <a href="{{ route('login', ['role' => 'alumni']) }}" 
                   class="block w-full text-center py-3 px-4 rounded-lg font-semibold text-purple-700 bg-purple-100 hover:bg-purple-600 hover:text-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                    Alumni
                </a>
                <a href="{{ route('login', ['role' => 'dosen']) }}" 
                   class="block w-full text-center py-3 px-4 rounded-lg font-semibold text-purple-700 bg-purple-100 hover:bg-purple-600 hover:text-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                    Dosen
                </a>
                <a href="{{ route('login', ['role' => 'admin']) }}" 
                   class="block w-full text-center py-3 px-4 rounded-lg font-semibold text-purple-700 bg-purple-100 hover:bg-purple-600 hover:text-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                    Admin
                </a>
            </div>

            <p class="mt-8 text-sm text-center text-gray-600">
                Belum punya akun? 
                <a href="{{ route('register.selection') }}" class="font-bold text-purple-600 hover:underline">Daftar di sini</a>
            </p>
        </div>

        <div class="hidden md:block w-1/2 bg-white p-12 flex flex-col justify-center items-center text-black text-center">
            <img src="{{ asset('images/deco.png') }}" alt="Ilustrasi Kuesioner" class="w-full max-w-xs mb-8">
            <h2 class="text-3xl font-bold mb-2">Sistem Kuesioner Online</h2>
            <p class="text-gray-800">Partisipasi Anda sangat berarti untuk kemajuan kami.</p>
        </div>
        
    </div>

</body>
</html>