<section class="space-y-6">
    <header>
        {{-- Header dengan Ikon Peringatan --}}
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __('Hapus Akun') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Ini adalah tindakan permanen yang tidak dapat dibatalkan.') }}
                </p>
            </div>
        </div>
        
        {{-- Deskripsi Detail --}}
        <p class="mt-4 text-sm text-gray-600">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    {{-- Tombol Aksi Utama --}}
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="!py-3 !px-6"
    >
        <i class="fas fa-trash-alt mr-2"></i>
        {{ __('Hapus Akun Saya Secara Permanen') }}
    </x-danger-button>

    {{-- Modal Konfirmasi dengan Styling Baru --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-whiterounded-lg bg-white">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-gray-900">
                {{ __('Apakah Anda benar-benar yakin?') }}
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                {{ __('Setelah akun Anda dihapus, semua datanya akan hilang selamanya. Mohon masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun secara permanen.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password_delete" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password_delete"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Ketik kata sandi Anda') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="!py-2.5 !px-5">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3 !py-2.5 !px-5">
                    {{ __('Ya, Hapus Akun Saya') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>