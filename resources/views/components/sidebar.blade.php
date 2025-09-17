{{-- Overlay untuk mobile (muncul saat sidebar terbuka) --}}
<div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" @click="sidebarOpen = false" x-cloak></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed z-30 inset-y-0 left-0 w-68 bg-gradient-to-b from-purple-700 dark:to-indigo-800 text-white flex flex-col justify-between 
              transform transition-transform duration-300 ease-in-out 
              md:relative md:translate-x-0">
    <div>
        <div class="p-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">SIMUB</h1>
            <!-- Tombol tutup (hanya muncul di mobile) -->
            <button class="md:hidden" @click="sidebarOpen = false">
                <i class="fas fa-times text-white text-xl"></i>
            </button>
        </div>
       <nav class="mt-4">
    {{-- Beranda --}}
    {{-- PERBAIKAN: Memeriksa 'dashboard' ATAU 'admin.dashboard' --}}
    <a href="{{ route(Auth::user()->role == 'admin' ? 'admin.dashboard' : 'dashboard') }}" 
       class="flex items-center px-6 py-3 transition-colors duration-200 {{ request()->routeIs('dashboard', 'admin.dashboard') ? 'bg-white text-purple-700' : 'hover:bg-purple-600' }}">
        <i class="fas fa-home w-5"></i>
        <span class="mx-4 font-medium">Beranda</span>
    </a>

            {{-- Logika Dinamis untuk Kuisioner --}}
            @if(Auth::check() && Auth::user()->role == 'admin')
                {{-- Manajemen Kuesioner --}}
                <a href="{{ route('admin.kuesioner.index') }}"
                   class="flex items-center px-6 py-3 transition-colors duration-200 {{ request()->routeIs('admin.kuesioner.*') ? 'bg-white text-purple-700' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-edit w-5"></i>
                    <span class="mx-4 font-medium">Manajemen Kuesioner</span>
                </a>

                {{-- !! PERUBAHAN DI SINI: Menambahkan Menu Manajemen Jawaban !! --}}
                <a href="{{ route('admin.jawaban.index') }}"
   class="flex items-center px-6 py-3 transition-colors duration-200 {{ request()->routeIs('admin.jawaban.*') ? 'bg-white text-purple-700' : 'hover:bg-purple-600' }}">
    <i class="fas fa-poll-h w-5"></i>
    <span class="mx-4 font-medium">Manajemen Jawaban</span>
</a>

            @else
                {{-- Daftar Kuesioner (User) --}}
                <a href="{{ route('kuesioner.user.index') }}"
                   class="flex items-center px-6 py-3 transition-colors duration-200 {{ request()->routeIs('kuesioner.user.*') ? 'bg-white text-purple-700' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="mx-4 font-medium">Daftar Kuisioner</span>
                </a>
            @endif

            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center px-6 py-3 transition-colors duration-200 {{ request()->routeIs('profile.edit') ? 'bg-white text-purple-700' : 'hover:bg-purple-600' }}">
                <i class="fas fa-user w-5"></i>
                <span class="mx-4 font-medium">Profile</span>
            </a>
        </nav>
    </div>
    
    <!-- Tombol Logout -->
    <div class="p-6 border-t border-white">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center w-full px-4 py-2 text-left text-white transition-colors duration-200 hover:bg-white hover:text-purple-600 rounded-md">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="mx-4 font-medium">KELUAR</span>
            </button>
        </form>
    </div>
</aside>
