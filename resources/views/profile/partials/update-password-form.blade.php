<section>
    <header>
        {{-- Header dengan Ikon Kunci --}}
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                <i class="fas fa-key text-purple-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __('Ubah Kata Sandi') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('put')

        {{-- Input Kata Sandi Saat Ini --}}
        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- Input Kata Sandi Baru --}}
        <div>
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- Input Konfirmasi Kata Sandi Baru --}}
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Tombol Aksi dan Pesan Konfirmasi --}}
        <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
            <x-primary-button class="!py-2.5 !px-6">{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-semibold text-green-600"
                >
                    <i class="fas fa-check-circle mr-1"></i>
                    {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>