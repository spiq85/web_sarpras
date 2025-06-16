@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 text-white p-4 md:p-8">
    {{-- Navbar --}}
    <nav class="flex flex-wrap justify-between items-center py-4 px-5 md:px-8 bg-gradient-to-b from-gray-800 to-gray-900 shadow-xl backdrop-blur-md border-b border-gray-700/50 rounded-xl mb-10">
        <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-teal-200 tracking-wider flex items-center space-x-3">
            <i class="fas fa-users-cog text-3xl text-teal-400"></i>
            <span>SISFO SARPRAS</span>
        </h1>

        <button id="menu-btn" class="md:hidden text-teal-400 focus:outline-none transition duration-300 hover:text-teal-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

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
                <a href="/kategori-barang" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
                    <i class="fas fa-tags"></i> Kategori Barang
                </a>
            </li>
            <li>
                <a href="/users" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
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

    {{-- Header Section --}}
    <div class="mb-8 bg-gradient-to-r from-gray-800/80 to-gray-900/80 p-6 rounded-xl shadow-lg border border-gray-700/50">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-blue-400">
                    Kategori Barang
                </h2>
                <p class="text-gray-400 mt-1">Mengelola daftar kategori untuk klasifikasi barang</p>
            </div>
            <a href="{{ route('kategori.create') }}"
                class="group bg-gradient-to-r from-blue-500 to-teal-500 hover:from-blue-600 hover:to-teal-600 text-white px-5 py-3 rounded-lg shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-teal-500/20 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Kategori</span>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
            </a>
        </div>
    </div>

    {{-- Filter and Search Section (BARU DITAMBAHKAN) --}}
    <div class="mb-8 bg-gradient-to-r from-gray-800/80 to-gray-900/80 p-6 rounded-xl shadow-lg border border-gray-700/50">
        <form action="{{ route('kategori.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-grow">
                <label for="search" class="block text-gray-300 text-sm font-medium mb-2">Cari Nama Kategori</label>
                <input type="text" name="search" id="search" placeholder="Masukkan nama kategori..."
                       class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-300"
                       value="{{ $currentSearch }}">
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('kategori.index') }}" 
                   class="ml-3 bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Data --}}
    <section class="bg-gradient-to-br from-gray-800/90 to-gray-900/90 p-6 rounded-xl shadow-xl border border-gray-700/40 backdrop-blur-sm">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-700/50">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-500 to-teal-500 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-folder-open text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-white">Daftar Kategori</h3>
                    <p class="text-sm text-gray-400">Menampilkan semua kategori barang yang tersedia</p>
                </div>
            </div>
            
            {{-- Search input in header (optional, if you want two search bars) --}}
            {{-- <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" placeholder="Cari kategori..." class="bg-gray-700/70 text-gray-200 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition-all duration-300">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div> --}}
        </div>
        
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-700/80 to-gray-800/80 text-gray-300 uppercase text-xs rounded-lg">
                        <th class="px-4 py-3 rounded-l-lg">No</th>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 rounded-r-lg text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategori as $index => $kat)
                    <tr class="border-b border-gray-700/30 hover:bg-gray-700/30 transition-all duration-200">
                        <td class="px-4 py-3 font-medium text-gray-300">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-teal-300">{{ $kat->nama_kategori }}</td>
                        <td class="px-4 py-3 text-gray-300">{{ $kat->deskripsi }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('kategori.edit', $kat->id_category) }}"
                                    class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium px-3 py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-amber-500/20 flex items-center gap-1">
                                    <i class="fas fa-pencil-alt"></i>
                                    <span>Edit</span>
                                </a>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('kategori.destroy', $kat->id_category) }}" method="POST"
                                    class="inline-block delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium px-3 py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-red-500/20 flex items-center gap-1 show-confirm">
                                        <i class="fas fa-trash-alt"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center p-8">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="bg-gray-800/80 p-4 rounded-full">
                                    <i class="fas fa-folder-open text-4xl text-gray-500"></i>
                                </div>
                                <p class="text-gray-400 text-lg">Belum ada data kategori.</p>
                                <a href="{{ route('kategori.create') }}" class="text-teal-400 hover:text-teal-300 font-medium flex items-center gap-2 mt-2">
                                    <i class="fas fa-plus-circle"></i> Tambahkan Kategori Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-400">
                Menampilkan <span class="font-medium text-white">{{ count($kategori) }}</span> kategori
            </div>
        </div>
    </section>
    
    {{-- Footer --}}
    <footer class="mt-10 text-center text-gray-500 text-sm py-4 border-t border-gray-800">
        <p>© {{ date('Y') }} SISFO SARPRAS - Sistem Informasi Sarana dan Prasarana</p>
    </footer>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.show-confirm').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Data kategori yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                iconColor: '#f87171',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#1f2937',
                confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>Ya, hapus!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                background: '#1e293b',
                color: '#f3f4f6',
                borderRadius: '0.5rem',
                padding: '1.5rem',
                customClass: {
                    confirmButton: 'rounded-lg px-5 py-2',
                    cancelButton: 'rounded-lg px-5 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                    
                    // Tambahkan notifikasi sukses
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Kategori berhasil dihapus!',
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#1e293b',
                        color: '#f3f4f6',
                        toast: true
                    });
                }
            });
        });
    });
    
    // Efek ripple untuk button
    const buttons = document.querySelectorAll('button, a');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const x = e.clientX - e.target.offsetLeft;
            const y = e.clientY - e.target.offsetTop;
            
            const ripple = document.createElement('span');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
</script>

<style>
    /* Custom Scrollbar */
    .scrollbar-thin::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5);
        border-radius: 999px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.8);
        border-radius: 999px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 0.8);
    }
    
    /* Ripple effect */
    button, a {
        position: relative;
        overflow: hidden;
    }
    .ripple {
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    /* Smooth transitions */
    * {
        transition: background-color 0.2s, transform 0.2s, box-shadow 0.2s, color 0.2s;
    }
    
    /* Hover effects for table rows */
    tbody tr:hover td:first-child {
        border-left: 3px solid #2dd4bf;
        padding-left: calc(1rem - 3px);
    }
</style>
@endpush
@endsection