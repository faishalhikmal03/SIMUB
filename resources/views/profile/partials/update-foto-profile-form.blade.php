<section>
    <header>
        <h2 class="text-xl font-bold text-gray-900">
            {{ __('Foto Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Perbarui foto profil Anda. Ini akan terlihat oleh semua pengguna lain.") }}
        </p>
    </header>

    {{-- Form ini hanya untuk update foto --}}
    <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center gap-x-4">
            {{-- Foto Profil Saat Ini --}}
            <img class="w-24 h-24 rounded-full object-cover" 
                 src="{{ Auth::user()->foto_profile ? asset('storage/'.Auth::user()->foto_profile) : 'https://placehold.co/96x96/E2E8F0/A0AEC0?text=U' }}" 
                 alt="Foto Profil Saat Ini">
            
            {{-- Input untuk memilih file baru --}}
            <div>
                 <input id="foto_profile" name="foto_profile" type="file" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-lg file:border-0
                    file:text-sm file:font-semibold
                    file:bg-purple-100 file:text-purple-700
                    hover:file:bg-purple-200
                  "/>
                 <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Maks. 2MB).</p>
                 <x-input-error class="mt-2" :messages="$errors->get('foto_profile')" />
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Foto') }}</x-primary-button>

            @if (session('status') === 'photo-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-semibold"
                ><i class="fas fa-check-circle mr-1"></i>{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>