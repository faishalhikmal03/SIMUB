<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran Pendaftaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Jika Anda menggunakan Vite, sesuaikan dengan @vite --}}
</head>
<body class="bg-purple-700 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-2xl p-8 w-full max-w-sm text-center shadow-lg">
        
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/Logo FMIPA.png') }}" alt="Logo" class="w-24 h-24">
        </div>

        <!-- Judul -->
        <h2 class="text-xl font-bold mb-8">DAFTAR SEBAGAI</h2>

        <div class="space-y-4">
            <!-- Tombol Mahasiswa -->
            <a href="{{ route('register', ['role' => 'mahasiswa']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm hover:bg-purple-600 hover:text-white transition-colors duration-200 font-semibold">
                Mahasiswa
            </a>

            <!-- Tombol Mahasiswa Baru -->
            <a href="{{ route('register', ['role' => 'mahasiswa_baru']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm hover:bg-purple-600 hover:text-white transition-colors duration-200 font-semibold">
                Mahasiswa Baru
            </a>

            <!-- Tombol Alumni -->
            <a href="{{ route('register', ['role' => 'alumni']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm hover:bg-purple-600 hover:text-white transition-colors duration-200 font-semibold">
                Alumni
            </a>

            <!-- Tombol Dosen -->
            <a href="{{ route('register', ['role' => 'dosen']) }}" 
               class="block w-full border-2 border-purple-600 text-black py-3 rounded-lg shadow-sm hover:bg-purple-600 hover:text-white transition-colors duration-200 font-semibold">
                Dosen
            </a>
        </div>

        <!-- Link Login -->
        <p class="mt-8 text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login.selection') }}" class="font-bold text-purple-600 hover:underline">Masuk</a>
        </p>
    </div>

</body>
</html>
