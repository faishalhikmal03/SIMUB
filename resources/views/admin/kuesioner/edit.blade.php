<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kuesioner') }}
        </h2>
    </x-slot>

    {{-- 
        MODIFIKASI KUNCI:
        - Mengganti 'existingData' menjadi 'kuesionerData' agar sesuai dengan nama variabel 
          yang dikirim dari controller yang sudah direfaktor.
        - Menambahkan properti 'dosenList' untuk menyediakan daftar dosen ke Alpine.js, 
          sama seperti di halaman create.
    --}}
    <div class="py-12" x-data="kuesionerBuilder({
        isEdit: true,
        action: '{{ route('admin.kuesioner.update', $kuesioner) }}',
        kuesionerData: {{ Js::from($kuesionerData) }},
        dosenList: {{ Js::from($dosen) }}
    })">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Form partial tetap sama, karena logikanya sudah ditangani oleh Alpine.js --}}
            @include('admin.kuesioner._form')
        </div>
    </div>

    {{-- Script partial juga tidak perlu diubah --}}
    @include('admin.kuesioner._builder-script')
</x-app-layout>