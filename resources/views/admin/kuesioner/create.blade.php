{{-- File: resources/views/admin/kuesioner/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Kuesioner Baru') }}
        </h2>
    </x-slot>

    {{-- MODIFIKASI: Tambahkan properti 'dosenList' --}}
    <div class="py-12" x-data="kuesionerBuilder({ 
        isEdit: false, 
        action: '{{ route('admin.kuesioner.store') }}',
        dosenList: {{ Js::from($dosen) }}
    })">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('admin.kuesioner._form')
        </div>
    </div>

    @include('admin.kuesioner._builder-script')
</x-app-layout>