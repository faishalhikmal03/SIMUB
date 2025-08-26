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
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $kuesioner->judul }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $kuesioner->deskripsi }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                               <a href="{{ route('kuesioner.user.show', $kuesioner) }}" class="inline-flex ...">
    Mulai Isi
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
