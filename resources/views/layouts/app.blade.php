<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Menghindari 'flickering' pada elemen Alpine.js --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">
        
        {{-- Komponen Sidebar sekarang dikontrol oleh div di atas --}}
        <x-sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header -->
            <header class="flex justify-between items-center p-4 bg-white border-b">
                <div class="flex items-center">
                    <!-- Tombol Hamburger (hanya muncul di mobile) -->
                    <button class="md:hidden mr-4 text-gray-600" @click="sidebarOpen = !sidebarOpen">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    {{-- Slot untuk Judul Halaman --}}
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ $header ?? 'Dashboard' }}
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 hidden sm:block">Hallo, {{ Auth::user()->nama ?? Auth::user()->email }}!</span>
                    <img class="w-10 h-10 rounded-full object-cover" 
                         src="{{ Auth::user()->foto_profile ? asset('storage/'.Auth::user()->foto_profile) : 'https://placehold.co/40x40/E2E8F0/A0AEC0?text=U' }}" 
                         alt="Foto Profil">
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                <div class="container mx-auto px-6 py-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
     @stack('scripts')
</body>
</html>