<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Daftar Kuesioner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse ($kuesioners as $kuesioner)
                    @php
                        // Logika untuk menentukan status dan teks tombol
                        $sudahDiisi = isset($kuesionerSudahDiisiIds) && $kuesionerSudahDiisiIds->contains($kuesioner->id);
                        $bisaDiisiUlang = $kuesioner->bisa_diisi_ulang;
                        
                        $tombolTeks = 'Mulai Isi';
                        $showBadge = false;

                        if ($sudahDiisi) {
                            if ($bisaDiisiUlang) {
                                $tombolTeks = 'Isi Ulang';
                                $showBadge = true; // Tetap tunjukkan badge meskipun bisa diisi ulang
                            } else {
                                // Jika sudah diisi dan TIDAK bisa diisi ulang, kuesioner ini seharusnya tidak tampil.
                                // Namun jika tampil, kita pastikan tombolnya disabled.
                                $tombolTeks = 'Selesai';
                            }
                        }
                    @endphp

                    {{-- Kartu Kuesioner dengan Desain Baru --}}
                    <div class="group bg-white rounded-tr-2xl rounded-br-2xl shadow-lg overflow-hidden transition-all duration-300 ease-in-out hover:shadow-2xl hover:-translate-y-1 border-l-4 {{ $sudahDiisi ? 'border-green-500' : 'border-purple-500' }}">
                        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            
                            {{-- Informasi Utama --}}
                            <div class="flex-grow">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-purple-600  transition-colors">
                                        {{ $kuesioner->judul }}
                                    </h3>
                                    @if($showBadge)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100  text-green-800">
                                            <i class="fas fa-check-circle mr-1.5"></i> Sudah Diisi
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-gray-600 max-w-2xl">
                                    {{ $kuesioner->deskripsi }}
                                </p>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="mt-4 sm:mt-0 sm:ml-6 flex-shrink-0">
                                <a href="{{ route('kuesioner.user.show', $kuesioner) }}" 
                                   class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-widest hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 transform group-hover:scale-105">
                                <span>{{ $tombolTeks }}</span>
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    {{-- Kartu "Tidak Ada Kuesioner" dengan Gaya Baru --}}
                    <div class="bg-white rounded-2xl shadow-lg p-10 text-center text-gray-500 border-2 border-dashed border-gray-700">
                        <i class="fas fa-folder-open text-4xl mb-4 text-gray-400"></i>
                        <h3 class="text-lg font-semibold">Tidak Ada Kuesioner</h3>
                        <p>Saat ini tidak ada kuesioner yang tersedia untuk Anda.</p>
                    </div>
                @endforelse

                {{-- Paginasi dengan Styling --}}
                @if($kuesioners->hasPages())
                    <div class="mt-8">
                        {{ $kuesioners->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>