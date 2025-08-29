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
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex justify-between items-center">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $kuesioner->judul }}
                                    </h3>
                                    {{-- PERUBAHAN 1: Tambahkan badge jika sudah diisi --}}
                                    @if(isset($kuesionerSudahDiisiIds) && $kuesionerSudahDiisiIds->contains($kuesioner->id))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sudah Diisi
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $kuesioner->deskripsi }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <a href="{{ route('kuesioner.user.show', $kuesioner) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{-- PERUBAHAN 2: Ubah teks tombol secara dinamis --}}
                                    @if(isset($kuesionerSudahDiisiIds) && $kuesionerSudahDiisiIds->contains($kuesioner->id))
                                        Isi Ulang
                                    @else
                                        Mulai Isi
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            <p>Tidak ada kuesioner yang tersedia untuk Anda saat ini.</p>
                        </div>
                    </div>
                @endforelse

                @if($kuesioners->hasPages())
                    <div class="mt-4">
                        {{ $kuesioners->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
