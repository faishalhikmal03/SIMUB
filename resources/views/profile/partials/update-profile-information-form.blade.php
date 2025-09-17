<section>
    <header>
        {{-- Header dengan Ikon --}}
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                <i class="fas fa-user-edit text-purple-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __('Informasi Profil') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __("Perbarui informasi profil dan detail kontak akun Anda.") }}
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="nama" :value="__('Nama Lengkap')" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $user->nama)" required autofocus autocomplete="nama" />
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        {{-- Tampilkan input email hanya jika role BUKAN dosen --}}
        @if ($user->role !== 'dosen')
            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                {{-- Notifikasi Verifikasi Email dengan Gaya Baru --}}
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ __('Alamat email Anda belum terverifikasi.') }}

                            <button form="send-verification" class="underline font-semibold hover:text-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 rounded">
                                {{ __('Kirim ulang email verifikasi.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-semibold text-sm text-green-600">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ __('Tautan verifikasi baru telah berhasil dikirim ke email Anda.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if ($user->role === 'mahasiswa' || $user->role === 'mahasiswa_baru')
            <div>
                <x-input-label for="npm" :value="__('NPM (Nomor Pokok Mahasiswa)')" />
                <x-text-input id="npm" name="npm" type="text" class="mt-1 block w-full" :value="old('npm', $user->npm)" />
                <x-input-error :messages="$errors->get('npm')" class="mt-2" />
            </div>
        @elseif ($user->role === 'alumni')
            <div>
                <x-input-label for="tanggal_yudisium" :value="__('Tanggal Yudisium')" />
                <x-text-input id="tanggal_yudisium" name="tanggal_yudisium" type="date" class="mt-1 block w-full" :value="old('tanggal_yudisium', $user->tanggal_yudisium)" />
                <x-input-error :messages="$errors->get('tanggal_yudisium')" class="mt-2" />
            </div>
        @elseif ($user->role === 'dosen')
            <div>
                <x-input-label for="nidn" :value="__('NIDN (Nomor Induk Dosen Nasional)')" />
                <x-text-input id="nidn" name="nidn" type="text" class="mt-1 block w-full" :value="old('nidn', $user->nidn)" />
                <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
            </div>
        @endif


        <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
            <x-primary-button class="!py-2.5 !px-6">{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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