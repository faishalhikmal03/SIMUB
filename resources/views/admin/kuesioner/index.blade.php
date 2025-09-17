<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kuesioner') }}
        </h2>
    </x-slot>

    <div x-data="{
        showConfirmModal: false,
        modalTitle: '',
        modalMessage: '',
        modalActionUrl: '',
        modalMethod: 'POST',
        showSuccessModal: {{ session()->has('success') ? 'true' : 'false' }},
        successMessage: '{{ session('success') }}'
    }" 
         x-init="if(showSuccessModal) { setTimeout(() => showSuccessModal = false, 2500) }"
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-600">Daftar semua kuesioner yang telah Anda buat.</p>
                <a href="{{ route('admin.kuesioner.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest shadow-lg transition-all duration-150 ease-in-out hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Kuesioner
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        {{-- Isi tabel Anda tetap sama persis seperti sebelumnya --}}
                         <thead class="bg-purple-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Kuesioner</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Target Responden</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($kuesioners as $kuesioner)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900">{{ $kuesioner->judul }}</div></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::title(str_replace('_', ' ', $kuesioner->target_user)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $kuesioner->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $kuesioner->status == 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                        <a href="{{ route('admin.kuesioner.preview', $kuesioner) }}" class="text-gray-500 hover:text-green-600" title="Preview"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.kuesioner.edit', $kuesioner) }}" class="text-gray-500 hover:text-indigo-600" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                        <button type="button" @click=" modalTitle = 'Konfirmasi Duplikasi'; modalMessage = 'Apakah Anda yakin ingin menduplikasi kuesioner \'{{ addslashes($kuesioner->judul) }}\'?'; modalActionUrl = '{{ route('admin.kuesioner.clone', $kuesioner) }}'; modalMethod = 'POST'; showConfirmModal = true; " class="text-gray-500 hover:text-yellow-600" title="Clone"><i class="fas fa-clone"></i></button>
                                        <button type="button" @click=" modalTitle = 'Konfirmasi Hapus'; modalMessage = 'Apakah Anda yakin ingin menghapus kuesioner \'{{ addslashes($kuesioner->judul) }}\'? Tindakan ini tidak dapat dibatalkan.'; modalActionUrl = '{{ route('admin.kuesioner.destroy', $kuesioner) }}'; modalMethod = 'DELETE'; showConfirmModal = true; " class="text-gray-500 hover:text-red-600" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 whitespace-nowrap text-center text-gray-500">
                                        <i class="fas fa-folder-open text-3xl mb-2"></i>
                                        <p>Belum ada kuesioner yang dibuat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($kuesioners->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">{{ $kuesioners->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Modal Konfirmasi (Tidak Berubah) --}}
        <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-10 overflow-y-auto bg-gray-500 bg-opacity-75" style="display: none;">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showConfirmModal" x-transition @click.away="showConfirmModal = false" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                    <form :action="modalActionUrl" method="POST">
                        @csrf
                        <template x-if="modalMethod === 'DELETE'"><input type="hidden" name="_method" value="DELETE"></template>
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full" :class="{ 'bg-red-100': modalMethod === 'DELETE', 'bg-yellow-100': modalMethod === 'POST' }">
                                <i class="fa" :class="{ 'fa-exclamation-triangle text-red-600': modalMethod === 'DELETE', 'fa-clone text-yellow-600': modalMethod === 'POST' }"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" x-text="modalTitle"></h3>
                                <div class="mt-2"><p class="text-sm text-gray-500" x-text="modalMessage"></p></div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" :class="{ 'bg-red-600 hover:bg-red-700 focus:ring-red-500': modalMethod === 'DELETE', 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': modalMethod === 'POST' }">Konfirmasi</button>
                            <button @click="showConfirmModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Sukses (Versi Ringan dengan Ikon Font Awesome) --}}
        <div x-show="showSuccessModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-20 overflow-y-auto bg-gray-500 bg-opacity-75" style="display: none;">
            <div class="flex items-center justify-center min-h-screen">
                <div @click.away="showSuccessModal = false" class="relative bg-white rounded-lg shadow-xl text-center p-8 max-w-sm mx-auto">
                    {{-- PERUBAHAN DI SINI: Ikon Font Awesome menggantikan SVG --}}
                    <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center bg-green-100 rounded-full">
                        <i class="fas fa-check-double text-green-500 text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Sukses!</h3>
                    <p class="text-gray-600 mt-2" x-text="successMessage"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS untuk animasi sudah tidak diperlukan, bisa dihapus dari @push('styles') --}}
</x-app-layout>