@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gray-900 text-white p-6">
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
                <a href="/dashboard" class="flex items-center gap-2 text-gray-300 font-semibold py-2 px-3 rounded-lg hover:bg-gray-800/70 transition duration-300">
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
                <a href="/peminjaman" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
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
                    Daftar Peminjaman
                </h2>
                <p class="text-gray-400 mt-1">Kelola permintaan peminjaman sarana dan prasarana</p>
            </div>
        </div>
    </div>

    {{-- Filter and Search Section (BARU DITAMBAHKAN) --}}
    <div class="mb-8 bg-gradient-to-r from-gray-800/80 to-gray-900/80 p-6 rounded-xl shadow-lg border border-gray-700/50">
        <form action="{{ route('peminjaman.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-grow">
                <label for="search" class="block text-gray-300 text-sm font-medium mb-2">Cari Peminjam (Username/Nama)</label>
                <input type="text" name="search" id="search" placeholder="Masukkan username atau nama peminjam..."
                       class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-300"
                       value="{{ $currentSearch }}">
            </div>
            
            <div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
            </div>
            <div>
                <a href="{{ route('peminjaman.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Peminjaman --}}
    <section class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-xl border border-gray-700/50 mb-10" x-data="{ showModal: false, detail: {} }">
        <h3 class="text-xl font-semibold text-teal-400 mb-6 border-b border-gray-700 pb-3 flex items-center gap-3">
            <i class="fas fa-clipboard-list text-2xl"></i> Riwayat Permintaan Peminjaman
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700/80 text-gray-200 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="p-3 text-left rounded-tl-lg">No</th>
                        <th class="p-3 text-left">Nama Peminjam</th>
                        <th class="p-3 text-left">Barang</th>
                        <th class="p-3 text-left">Keperluan</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjaman as $index => $pinjam)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-all duration-300 ease-in-out">
                        <td class="p-3">{{ $index + 1 }}</td>
                        <td class="p-3 font-medium">{{ optional($pinjam->user)->username ?? '-' }}</td>
                        <td class="p-3">{{ optional($pinjam->detail)->barang->nama_barang ?? '-' }}</td>
                        <td class="p-3">{{ optional($pinjam->detail)->keperluan ?? '-' }}</td>
                        <td class="p-3">
                            @if ($pinjam->status === 'pending')
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-900/50 text-yellow-300 border border-yellow-600/30">
                                Menunggu
                            </span>
                            @elseif ($pinjam->status === 'dipinjam')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-900/50 text-green-300 border border-green-600/30">
                                Disetujui
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900/50 text-red-300 border border-red-600/30">
                                Ditolak
                            </span>
                            @endif
                        </td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-2">
                                {{-- Detail Button --}}
                                <button @click="detail = {
                                    nama_peminjam: '{{ optional($pinjam->user)->username ?? '-' }}',
                                    nama_barang: '{{ optional($pinjam->detail)->barang->nama_barang ?? '-' }}',
                                    keperluan: '{{ optional($pinjam->detail)->keperluan ?? '-' }}',
                                    jumlah: '{{ optional($pinjam->detail)->jumlah ?? '-' }}',
                                    tanggal_pinjam: '{{ optional($pinjam->detail)->tanggal_pinjam ?? '-' }}',
                                    tanggal_kembali: '{{ optional($pinjam->detail)->tanggal_kembali ?? '-' }}',
                                }; showModal = true" 
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-1.5 rounded-lg text-xs transition-all transform hover:scale-105 shadow-md flex items-center gap-1">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                @if ($pinjam->status === 'pending')
                                <form id="approve-form-{{ $pinjam->id_peminjaman }}" action="{{ route('peminjaman.approve', $pinjam->id_peminjaman) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button onclick="confirmApprove('{{ $pinjam->id_peminjaman }}')" 
                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-3 py-1.5 rounded-lg text-xs transition-all transform hover:scale-105 shadow-md flex items-center gap-1">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <form id="reject-form-{{ $pinjam->id_peminjaman }}" action="{{ route('peminjaman.reject', $pinjam->id_peminjaman) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button onclick="confirmReject('{{ $pinjam->id_peminjaman }}')"
                                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-600 text-white px-3 py-1.5 rounded-lg text-xs transition-all transform hover:scale-105 shadow-md flex items-center gap-1">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                @else
                                <span class="text-gray-400 italic text-xs flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i> Sudah diproses
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-6 text-gray-400 bg-gray-800/30 rounded-b-lg">
                            <div class="flex flex-col items-center justify-center py-6">
                                <i class="fas fa-clipboard-check text-5xl text-gray-600 mb-3"></i>
                                <p>Belum ada permintaan peminjaman.</p>
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
                    <i class="fas fa-clipboard-list mr-2"></i> Detail Peminjaman
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-user"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Nama Peminjam</p>
                            <p class="font-medium" x-text="detail.nama_peminjam"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-box"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Barang</p>
                            <p class="font-medium" x-text="detail.nama_barang"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-file-alt"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Keperluan</p>
                            <p class="font-medium" x-text="detail.keperluan"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                        <span class="text-teal-400"><i class="fas fa-sort-numeric-up"></i></span>
                        <div>
                            <p class="text-gray-400 text-xs">Jumlah</p>
                            <p class="font-medium" x-text="detail.jumlah"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                            <span class="text-teal-400"><i class="fas fa-calendar-plus"></i></span>
                            <div>
                                <p class="text-gray-400 text-xs">Tanggal Pinjam</p>
                                <p class="font-medium" x-text="detail.tanggal_pinjam"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-800/50 border border-gray-700/30">
                            <span class="text-teal-400"><i class="fas fa-calendar-check"></i></span>
                            <div>
                                <p class="text-gray-400 text-xs">Tanggal Kembali</p>
                                <p class="font-medium" x-text="detail.tanggal_kembali"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <button @click="showModal = false"
                        class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-500 hover:to-red-600 text-white px-4 py-2 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 flex items-center gap-2 mx-auto">
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
@endsection
@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmApprove(id) {
        Swal.fire({
            title: 'Setujui peminjaman?',
            text: "Pastikan data peminjaman sudah benar.",
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
            title: 'Tolak peminjaman?',
            text: "Permintaan ini akan ditolak.",
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
    @if(session('success'))
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
    @if(session('error'))
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
@endpush