<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kuesioner') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="kuesionerBuilder({
        isEdit: true,
        action: '{{ route('admin.kuesioner.update', $kuesioner) }}',
        existingData: {{ Js::from($kuesioner->load('sections.pertanyaans.pilihanJawabans')) }}
    })">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Memanggil form yang SAMA dari file partial --}}
            @include('admin.kuesioner._form')
        </div>
    </div>

    @include('admin.kuesioner._builder-script')
</x-app-layout>