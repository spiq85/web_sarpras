@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 text-white p-4 md:p-8">
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
                <a href="/barang" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
                    <i class="fas fa-boxes"></i> Barang
                </a>
            </li>
            <li>
                <a href="/kategori-barang" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
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
                    Data Barang
                </h2>
                <p class="text-gray-400 mt-1">Mengelola inventaris barang dan sarana prasarana</p>
            </div>
            <a href="{{ route('barang.create') }}"
                class="group bg-gradient-to-r from-blue-500 to-teal-500 hover:from-blue-600 hover:to-teal-600 text-white px-5 py-3 rounded-lg shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-teal-500/20 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Barang</span>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
            </a>
        </div>
    </div>

    {{-- Grid Card Daftar Barang --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($barang as $item)
        <div class="bg-gradient-to-b from-gray-800/90 to-gray-900/90 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-700/40 group hover:border-teal-500/30 transform hover:-translate-y-1">
            <div class="relative w-full h-52 bg-gray-900/80 flex items-center justify-center overflow-hidden">
                @if($item->gambar_barang)
                    <img src="{{ asset('storage/gambar_barang/' . $item->gambar_barang) }}" alt="gambar barang"
                         class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-110" />
                @else
                    <div class="w-full h-full bg-gray-800/80 flex items-center justify-center text-gray-500 text-xl">
                        <i class="fas fa-image text-4xl"></i>
                    </div>
                @endif
                <div class="absolute top-3 right-3 bg-gray-900/70 px-2 py-1 rounded-lg text-xs font-medium text-teal-400 backdrop-blur-sm">
                    {{$item->kode_barang}}
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-gradient-to-r from-blue-500 to-teal-500 w-1 h-6 rounded-full"></span>
                    <h3 class="text-lg font-semibold text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-blue-300 truncate" title="{{ $item->nama_barang }}">
                        {{ $item->nama_barang }}
                    </h3>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tag text-blue-400 w-5"></i>
                        <p class="text-gray-300">{{ $item->kategori->nama_kategori ?? 'Tidak ada kategori' }}</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <i class="fas fa-box-open text-teal-400 w-5"></i>
                        <p class="text-gray-300">Stock: <span class="font-semibold text-white">{{ $item->stock }}</span></p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-purple-400 w-5"></i>
                        <p class="text-gray-300">Kondisi: <span class="capitalize text-white">{{ $item->kondisi_barang }}</span></p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <i class="fas fa-building text-amber-400 w-5"></i>
                        <p class="text-gray-300">Brand: <span class="text-white">{{ $item->brand }}</span></p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400 w-5"></i>
                        <p class="text-gray-300">Status: 
                            <span class="capitalize 
                                {{$item->status == 'tersedia' ? 'text-green-400' : 'text-red-400'}}">
                                {{ $item->status }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-5 flex justify-between gap-3">
                    <a href="{{ route('barang.edit', $item->id_barang) }}"
                       class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-black font-semibold py-2 rounded-lg text-center shadow-md transition transform hover:scale-105 hover:shadow-amber-500/20 flex items-center justify-center gap-1">
                        <i class="fas fa-pencil-alt"></i>
                        <span>Edit</span>
                    </a>

                    <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="show-confirm w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-red-500/20 flex items-center justify-center gap-1"
                                >
                            <i class="fas fa-trash-alt"></i>
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center p-10">
            <div class="flex flex-col items-center justify-center gap-4">
                <div class="bg-gray-800/80 p-5 rounded-full">
                    <i class="fas fa-boxes text-5xl text-gray-500"></i>
                </div>
                <p class="text-gray-400 text-lg">Belum ada data barang.</p>
                <a href="{{ route('barang.create') }}" class="text-teal-400 hover:text-teal-300 font-medium flex items-center gap-2 mt-2">
                    <i class="fas fa-plus-circle"></i> Tambahkan Barang Pertama
                </a>
            </div>
        </div>
        @endforelse
    </section>
    
    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
        {{-- Pagination links would go here if you're using Laravel's pagination --}}
    </div>
    
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
                text: "Data barang yang dihapus tidak dapat dikembalikan!",
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
                        title: 'Barang berhasil dihapus!',
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
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5);
        border-radius: 999px;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.8);
        border-radius: 999px;
    }
    ::-webkit-scrollbar-thumb:hover {
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
    
    /* Hover effects */
    .group:hover {
        box-shadow: 0 10px 25px -5px rgba(20, 184, 166, 0.1);
    }
    
    /* Smooth transitions */
    * {
        transition: background-color 0.2s, transform 0.2s, box-shadow 0.2s, color 0.2s;
    }
</style>
@endpush
@endsection