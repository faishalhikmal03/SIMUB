<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-lg font-semibold mb-4">Informasi Data {{ ucfirst($user->role) }}</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-purple-600 text-white">
                <tr>
                    @if($user->role === 'mahasiswa')
                        <th class="px-4 py-2 text-left">NPM</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Email</th>
                    @elseif($user->role === 'alumni')
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Tahun Angkatan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr class="border-b hover:bg-gray-50">
                    @if($user->role === 'mahasiswa')
                        <td class="px-4 py-2">{{ $user->npm ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                    @elseif($user->role === 'alumni')
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->tahun_angkatan ?? '-' }}</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>
