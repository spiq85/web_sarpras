@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                <a href="/detail-pengembalian" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
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
                    Daftar Pengembalian Barang
                </h2>
                <p class="text-gray-400 mt-1">Kelola permintaan pengembalian sarana dan prasarana</p>
            </div>
        </div>
    </div>

    {{-- Tabel Pengembalian --}}
    <section class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-xl border border-gray-700/50 mb-10" x-data="{ showModal: false, detail: {} }">
        <h3 class="text-xl font-semibold text-teal-400 mb-6 border-b border-gray-700 pb-3 flex items-center gap-3">
            <i class="fas fa-clipboard-list text-2xl"></i> Data Pengembalian
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700/80 text-gray-200 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="p-3 text-left rounded-tl-lg">No</th>
                        <th class="p-3 text-left">Nama Barang</th>
                        <th class="p-3 text-left">Jumlah</th>
                        <th class="p-3 text-left">Tanggal Pengembalian</th>
                        <th class="p-3 text-left">Kondisi</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-all duration-300 ease-in-out">
                        <td class="p-3">{{ $index + 1 }}</td>
                        <td class="p-3 font-medium">{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td class="p-3">{{ $item->jumlah }}</td>
                        <td class="p-3">{{ $item->tanggal_pengembalian }}</td>
                        <td class="p-3">{{ $item->kondisi ?? '-' }}</td>
                        <td class="p-3">
                            @if ($item->status === 'approve')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-900/50 text-green-300 border border-green-600/30">
                                Disetujui
                            </span>
                            @elseif ($item->status === 'not approve')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900/50 text-red-300 border border-red-600/30">
                                Ditolak
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-900/50 text-yellow-300 border border-yellow-600/30">
                                Menunggu
                            </span>
                            @endif
                        </td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-2">
                                {{-- Tombol Detail --}}
                                <button @click="detail = {
                                    barang: '{{ $item->barang->nama_barang ?? '-' }}',
                                    jumlah: '{{ $item->jumlah }}',
                                    kondisi: '{{ $item->kondisi ?? '-' }}',
                                    tanggal_pengembalian: '{{ $item->tanggal_pengembalian }}',
                                    keterangan: '{{ $item->keterangan ?? '-' }}',
                                    image: '{{ $item->item_image ?? null }}'
                                }; showModal = true" 
                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-1.5 rounded-lg text-xs transition-all transform hover:scale-105 shadow-md flex items-center gap-1">
                                    <i class="fas fa-eye"></i> Detail
                                </button>

                                {{-- Approve / Reject --}}
                                @if ($item->status === 'pending')
                                <form id="approve-form-{{ $item->id_detail_pengembalian }}"
                                    action="{{ route('detail-pengembalian.approve', $item->id_detail_pengembalian) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button onclick="confirmApprove('{{ $item->id_detail_pengembalian }}')"
                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-3 py-1.5 rounded-lg text-xs transition-all transform hover:scale-105 shadow-md flex items-center gap-1">
                                    <i class="fas fa-check"></i> Approve
                                </button>

                                <form id="reject-form-{{ $item->id_detail_pengembalian }}"
                                    action="{{ route('detail-pengembalian.reject', $item->id_detail_pengembalian) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button onclick="confirmReject('{{ $item->id_detail_pengembalian }}')"
                                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-3 py-1.5 rounded-lg text-xs transition-all transform hover:scale-105 shadow-md flex items-center gap-1">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-6 text-gray-400 bg-gray-800/30 rounded-b-lg">
                            <div class="flex flex-col items-center justify-center py-6">
                                <i class="fas fa-clipboard-check text-5xl text-gray-600 mb-3"></i>
                                <p>Belum ada data pengembalian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal Detail --}}
        <div x-show="showModal"
            class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
            style="display: none;">
            <div class="bg-gradient-to-b from-gray-800 to-gray-900 p-8 rounded-xl w-full max-w-md shadow-2xl border border-gray-700/70 text-white relative"
                @click.away="showModal = false">
                <h2 class="text-xl font-semibold mb-4 text-center text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-blue-400 border-b border-gray-700 pb-2">
                    <i class="fas fa-clipboard-list mr-2"></i> Detail Pengembalian
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-box"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Barang</p>
                            <p class="font-medium" x-text="detail.barang"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-sort-numeric-up"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Jumlah</p>
                            <p class="font-medium" x-text="detail.jumlah"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-clipboard-check"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Kondisi</p>
                            <p class="font-medium" x-text="detail.kondisi"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-calendar-check"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Tanggal Pengembalian</p>
                            <p class="font-medium" x-text="detail.tanggal_pengembalian"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-comment-alt"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Keterangan</p>
                            <p class="font-medium" x-text="detail.keterangan"></p>
                        </div>
                    </div>
                    
                    <template x-if="detail.image">
                        <div class="mt-3 rounded-lg bg-gray-800/50 border border-gray-700/30 p-3">
                            <p class="text-gray-400 text-xs mb-2 flex items-center gap-2">
                                <i class="fas fa-camera text-teal-400"></i> Foto Barang
                            </p>
                            <img :src="detail.image" alt="Gambar Barang"
                                class="rounded-md border border-gray-700 max-h-48 mx-auto">
                        </div>
                    </template>
                </div>
                
                <div class="mt-6 text-center">
                    <button @click="showModal = false" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 flex items-center gap-2 mx-auto">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    {{-- Footer --}}
    <div class="mt-auto pt-8">
        <div class="border-t border-gray-700 pt-6 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} SISFO SARPRAS - Sistem Informasi Sarana dan Prasarana</p>
        </div>
    </div>
</div>

<script>
    function confirmApprove(id) {
        Swal.fire({
            title: 'Setujui pengembalian?',
            text: "Pastikan barang telah diterima dengan baik.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            background: '#1f2937',
            color: '#fff',
            iconColor: '#34d399'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approve-form-' + id).submit();
            }
        });
    }

    function confirmReject(id) {
        Swal.fire({
            title: 'Tolak pengembalian?',
            text: "Pastikan Anda yakin ingin menolak pengembalian ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            background: '#1f2937',
            color: '#fff',
            iconColor: '#f87171'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reject-form-' + id).submit();
            }
        });
    }

    // ✅ Flash Message Success
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff',
            iconColor: '#34d399'
        });
    @endif

    // ❌ Flash Message Error
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            timer: 2500,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff',
            iconColor: '#f87171'
        });
    @endif
</script>

@endsection