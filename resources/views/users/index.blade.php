@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white p-6">
    {{-- Navbar --}}
    <nav class="flex flex-wrap justify-between items-center py-4 px-5 md:px-8 bg-gradient-to-b from-gray-800 to-gray-900 shadow-xl backdrop-blur-md border-b border-gray-700/50 rounded-xl mb-10">
        <!-- Logo -->
        <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-teal-200 tracking-wider flex items-center space-x-3">
            <i class="fas fa-users-cog text-3xl text-teal-400"></i>
            <span>SISFO SARPRAS</span>
        </h1>

        <!-- Hamburger (mobile only) -->
        <button id="menu-btn" class="md:hidden text-teal-400 focus:outline-none transition duration-300 hover:text-teal-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Navigation Links -->
        <ul id="menu" class="hidden md:flex flex-col md:flex-row w-full md:w-auto mt-4 md:mt-0 gap-1 md:gap-5 text-base font-medium items-center">
            <li>
                <a href="/dashboard" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/barang" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-boxes"></i> Barang
                </a>
            </li>
            <li>
                <a href="/kategori-barang" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-tags"></i> Kategori Barang
                </a>
            </li>
            <li>
                <a href="/users" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
                    <i class="fas fa-user-plus"></i> Tambah User
                </a>
            </li>
            <li>
                <a href="/peminjaman" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-hand-holding"></i> Peminjaman
                </a>
            </li>
            <li>
                <a href="/detail-pengembalian" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-undo-alt"></i> Pengembalian
                </a>
            </li>
            <li>
                <a href="/laporan" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-file-alt"></i> Laporan
                </a>
            </li>
            <li>
                <a href="/logout" class="flex items-center gap-2 text-red-400 hover:text-red-300 py-2 px-3 rounded-lg hover:bg-red-900/20 transition duration-300 ml-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>

    {{-- Header --}}
     <div class="mb-8 bg-gradient-to-r from-gray-800/80 to-gray-900/80 p-6 rounded-xl shadow-lg border border-gray-700/50">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-blue-400">
                    Data User
                </h2>
                <p class="text-gray-400 mt-1">Kelola pengguna sistem informasi sarana dan prasarana</p>
            </div>
            <a href="{{ route('users.create') }}"
                class="group bg-gradient-to-r from-blue-500 to-teal-500 hover:from-blue-600 hover:to-teal-600 text-white px-5 py-3 rounded-lg shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-teal-500/20 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah User</span>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
            </a>
        </div>
    </div>

    {{-- Tabel Data --}}
    <section class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-xl border border-gray-700/50 mb-10">
        <h3 class="text-xl font-semibold text-teal-400 mb-6 border-b border-gray-700 pb-3 flex items-center gap-3">
            <i class="fas fa-users text-2xl"></i> Daftar Pengguna
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700/80 text-gray-200 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="p-3 text-left rounded-tl-lg">Nama</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Role</th>
                        <th class="p-3 text-left">Kelas</th>
                        <th class="p-3 text-left">Jurusan</th>
                        <th class="p-3 text-left rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-all duration-300 ease-in-out">
                        <td class="p-3 font-medium">{{ $user->username }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full capitalize 
                                @if($user->role == 'admin') 
                                    bg-purple-900/50 text-purple-300 border border-purple-600/30
                                @else 
                                    bg-blue-900/50 text-blue-300 border border-blue-600/30
                                @endif">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="p-3">{{ $user->class }}</td>
                        <td class="p-3">{{ $user->major }}</td>
                        <td class="p-3 space-x-2">
                            <div class="flex flex-wrap gap-2">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('users.edit', $user->users_id) }}"
                                    class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium px-4 py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg flex items-center gap-1">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('users.destroy', $user->users_id) }}" method="POST"
                                    class="inline-block delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium px-4 py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg flex items-center gap-1 show-confirm">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-6 text-gray-400 bg-gray-800/30 rounded-b-lg">
                            <div class="flex flex-col items-center justify-center py-6">
                                <i class="fas fa-users-slash text-5xl text-gray-600 mb-3"></i>
                                <p>Belum ada data user dalam sistem.</p>
                                <a href="{{ route('users.create') }}" class="mt-3 text-teal-400 hover:text-teal-300 transition">
                                    <i class="fas fa-plus-circle"></i> Tambah user baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Footer --}}
    <div class="mt-auto pt-8">
        <div class="border-t border-gray-700 pt-6 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} SISFO SARPRAS - Sistem Informasi Sarana dan Prasarana</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.show-confirm').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: '#1f2937',
                color: '#fff',
                iconColor: '#fbbf24',
                heightAuto: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection