<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Kartu Foto Profil (selalu tampil) --}}
            <div class="p-4 sm:p-8 bg-purple-50 shadow sm:rounded-lg border-l-4 border-purple-500">
                <div class="max-w-xl">
                    @include('profile.partials.update-foto-profile-form')
                </div>
            </div>
            {{-- Kartu Informasi Profil (selalu tampil) --}}
            <div class="p-4 sm:p-8 bg-purple-50 shadow sm:rounded-lg border-l-4 border-purple-500">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- PERBAIKAN: Tampilkan kartu ini hanya jika role BUKAN dosen --}}
            @if (Auth::user()->role !== 'dosen')
                <div class="p-4 sm:p-8 bg-purple-50 shadow sm:rounded-lg border-l-4 border-purple-500">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-purple-50 shadow sm:rounded-lg border-l-4 border-red-500">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
