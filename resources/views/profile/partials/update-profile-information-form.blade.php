<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Perbarui informasi profil akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nama -->
        <div>
            <x-input-label for="nama" :value="__('Nama')" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $user->nama)" required autofocus autocomplete="nama" />
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        {{-- PERBAIKAN: Tampilkan input email hanya jika role BUKAN dosen --}}
        @if ($user->role !== 'dosen')
            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            {{ __('Alamat email Anda belum terverifikasi.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- Input Dinamis Berdasarkan Role -->
        @if ($user->role === 'mahasiswa' || $user->role === 'mahasiswa_baru')
            <!-- NPM -->
            <div class="mt-4">
                <x-input-label for="npm" :value="__('NPM')" />
                <x-text-input id="npm" name="npm" type="text" class="mt-1 block w-full" :value="old('npm', $user->npm)" />
                <x-input-error :messages="$errors->get('npm')" class="mt-2" />
            </div>
        @elseif ($user->role === 'alumni')
            <!-- Tanggal Yudisium -->
            <div class="mt-4">
                <x-input-label for="tanggal_yudisium" :value="__('Tanggal Yudisium')" />
                <x-text-input id="tanggal_yudisium" name="tanggal_yudisium" type="date" class="mt-1 block w-full" :value="old('tanggal_yudisium', $user->tanggal_yudisium)" />
                <x-input-error :messages="$errors->get('tanggal_yudisium')" class="mt-2" />
            </div>
        @elseif ($user->role === 'dosen')
            <!-- NIDN -->
            <div class="mt-4">
                <x-input-label for="nidn" :value="__('NIDN')" />
                <x-text-input id="nidn" name="nidn" type="text" class="mt-1 block w-full" :value="old('nidn', $user->nidn)" />
                <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
            </div>
        @endif


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
