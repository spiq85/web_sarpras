@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white p-6">
    {{-- Navbar --}}
    <nav class="relative flex flex-wrap justify-between items-center py-4 px-5 md:px-8 bg-gradient-to-b from-gray-800 to-gray-900 shadow-xl backdrop-blur-md border-b border-gray-700/50 rounded-xl mb-10 z-50 overflow-visible">
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

        <ul id="menu" class="hidden md:flex flex-col md:flex-row w-full md:w-auto mt-4 md:mt-0 gap-1 md:gap-5 text-base font-medium items-center overflow-visible">
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
                <a href="/detail-pengembalian" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-undo-alt"></i> Pengembalian
                </a>
            </li>
            <li>
                <a href="/laporan" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
                    <i class="fas fa-file-alt"></i> Laporan
                </a>
            </li>

            {{-- === NOTIFICATION ICON AND DROPDOWN === --}}
            <li class="relative z-40">
                <button id="notification-btn" class="relative flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300 focus:outline-none">
                    <i class="fas fa-bell"></i> Notifikasi
                    {{-- Check if $unreadNotifications is set and not empty before trying to count --}}
                    @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                            {{ $unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-50 hidden max-h-96 overflow-y-auto">
                    <div class="px-4 py-3 border-b border-gray-700 flex justify-between items-center">
                        <h4 class="text-white font-semibold">Notifikasi</h4>
                        @if(isset($unreadNotifications))
                            <span class="text-sm text-gray-400">{{ $unreadNotifications->count() }} belum dibaca</span>
                        @endif
                    </div>

                    {{-- Check if $allNotifications is set before trying to check if it's empty or iterate --}}
                    @if(isset($allNotifications) && $allNotifications->isEmpty())
                        <div class="px-4 py-6 text-center text-gray-400">Tidak ada notifikasi.</div>
                    @else
                        <ul>
                            @if(isset($allNotifications))
                                @foreach($allNotifications as $notification)
                                    <li class="border-b border-gray-700 last:border-b-0">
                                        <div class="block px-4 py-3 hover:bg-gray-700 transition-colors duration-200
                                                @if(!$notification->read_at) bg-gray-700/50 @endif">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 text-teal-400 mt-1">
                                                    {{-- Menggunakan `array_key_exists` untuk pemeriksaan yang lebih aman --}}
                                                    @if(array_key_exists('type', $notification->data))
                                                        @if($notification->data['type'] == 'peminjaman_baru')
                                                            <i class="fas fa-hand-holding"></i>
                                                        @elseif($notification->data['type'] == 'pengembalian_baru')
                                                            <i class="fas fa-undo-alt"></i>
                                                        @else
                                                            <i class="fas fa-info-circle"></i>
                                                        @endif
                                                    @else
                                                        <i class="fas fa-info-circle"></i>
                                                    @endif
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    {{-- Menggunakan `??` untuk fallback default jika key tidak ada --}}
                                                    <p class="text-white font-medium">{{ $notification->data['message'] ?? 'Notifikasi tanpa pesan' }}</p>
                                                    <p class="text-gray-400 text-xs mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                        @if(!$notification->read_at)
                                                            <span class="text-red-400 font-semibold ml-2">Baru!</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex justify-end mt-2">
                                                @if(!$notification->read_at)
                                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-xs text-teal-400 hover:text-teal-300 font-semibold px-2 py-1 rounded-full bg-teal-900/30 hover:bg-teal-900/50 transition-colors">
                                                            Tandai Dibaca
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(array_key_exists('url', $notification->data))
                                                    <a href="{{ url($notification->data['url']) }}" class="ml-2 text-xs text-blue-400 hover:text-blue-300 font-semibold px-2 py-1 rounded-full bg-blue-900/30 hover:bg-blue-900/50 transition-colors" target="_blank">
                                                        Detail
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    @endif
                    <div class="px-4 py-3 text-center border-t border-gray-700">
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-teal-400 hover:text-teal-200 text-sm focus:outline-none">Tandai Semua Dibaca</button>
                        </form>
                    </div>
                </div>
            </li>
            {{-- === END NOTIFICATION ICON AND DROPDOWN === --}}

            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 text-red-400 hover:text-red-300 py-2 px-3 rounded-lg hover:bg-red-900/20 transition duration-300 ml-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>

    <script>
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Dropdown Notifikasi
        const notificationBtn = document.getElementById('notification-btn');
        const notificationDropdown = document.getElementById('notification-dropdown');

        if (notificationBtn && notificationDropdown) {
            notificationBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });

            // Menutup dropdown jika klik di luar
            document.addEventListener('click', (event) => {
                if (!notificationDropdown.contains(event.target) && !notificationBtn.contains(event.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }
    </script>

    {{-- Header Section --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-white mb-2">Laporan</h1>
        <p class="text-gray-300">Manajemen dan Ekspor Laporan Sistem Sarana dan Prasarana</p>
    </div>
    {{-- Laporan Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        {{-- Laporan Barang --}}
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-xl overflow-hidden border border-gray-700/50 group hover:shadow-teal-900/20 hover:shadow-lg transition duration-300">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-teal-500 to-teal-700 p-3 rounded-lg">
                        <i class="fas fa-boxes text-white text-2xl"></i>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-teal-400">Laporan Barang</h3>
                </div>
                <p class="text-gray-300 mb-6">Daftar inventaris barang dan status kondisinya saat ini</p>
                <div class="flex gap-3">
                    <a href="{{ route('laporan.barang') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white py-2 px-4 rounded-lg hover:from-teal-500 hover:to-teal-600 transition duration-300 w-full text-center">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="{{ route('laporan.barang.pdf') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 text-white py-2 px-4 rounded-lg hover:from-red-500 hover:to-red-600 transition duration-300">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('laporan.barang.excel') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 text-white py-2 px-4 rounded-lg hover:from-green-500 hover:to-green-600 transition duration-300">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
        </div>
        {{-- Laporan Peminjaman --}}
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-xl overflow-hidden border border-gray-700/50 group hover:shadow-teal-900/20 hover:shadow-lg transition duration-300">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-3 rounded-lg">
                        <i class="fas fa-hand-holding text-white text-2xl"></i>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-purple-400">Laporan Peminjaman</h3>
                </div>
                <p class="text-gray-300 mb-6">Data peminjaman barang serta status peminjaman saat ini</p>
                <div class="flex gap-3">
                    <a href="{{ route('laporan.peminjaman') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white py-2 px-4 rounded-lg hover:from-purple-500 hover:to-purple-600 transition duration-300 w-full text-center">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="{{ route('laporan.peminjaman.pdf') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 text-white py-2 px-4 rounded-lg hover:from-red-500 hover:to-red-600 transition duration-300">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('laporan.peminjaman.excel') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 text-white py-2 px-4 rounded-lg hover:from-green-500 hover:to-green-600 transition duration-300">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
        </div>
        {{-- Laporan Pengembalian --}}
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-xl overflow-hidden border border-gray-700/50 group hover:shadow-teal-900/20 hover:shadow-lg transition duration-300">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-3 rounded-lg">
                        <i class="fas fa-undo-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-blue-400">Laporan Pengembalian</h3>
                </div>
                <p class="text-gray-300 mb-6">Riwayat pengembalian barang dan status kondisinya</p>
                <div class="flex gap-3">
                    <a href="{{ route('laporan.pengembalian') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 px-4 rounded-lg hover:from-blue-500 hover:to-blue-600 transition duration-300 w-full text-center">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <a href="{{ route('laporan.pengembalian.pdf') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 text-white py-2 px-4 rounded-lg hover:from-red-500 hover:to-red-600 transition duration-300">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('laporan.pengembalian.excel') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 text-white py-2 px-4 rounded-lg hover:from-green-500 hover:to-green-600 transition duration-300">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- Laporan Ringkasan Section --}}
    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-xl border border-gray-700/50 mb-10">
        <h3 class="text-xl font-semibold text-teal-400 mb-6 flex items-center">
            <i class="fas fa-chart-line mr-3"></i> Ringkasan Laporan
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800/50 p-5 rounded-lg border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Total Barang</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $totalBarang }}</h4>
                    </div>
                    <div class="bg-teal-900/50 p-3 rounded-full">
                        <i class="fas fa-boxes text-teal-400"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Barang Tersedia</span>
                        <span class="text-teal-400 font-semibold">{{ $barangTersedia }}</span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800/50 p-5 rounded-lg border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Total Peminjaman</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $totalPeminjaman }}</h4>
                    </div>
                    <div class="bg-purple-900/50 p-3 rounded-full">
                        <i class="fas fa-hand-holding text-purple-400"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Peminjaman Aktif</span>
                        <span class="text-purple-400 font-semibold">{{ $peminjamanAktif }}</span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800/50 p-5 rounded-lg border border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Total Pengembalian</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $totalPengembalian }}</h4>
                    </div>
                    <div class="bg-blue-900/50 p-3 rounded-full">
                        <i class="fas fa-undo-alt text-blue-400"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Terlambat</span>
                        <span class="text-red-400 font-semibold">{{ $terlambat }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section (Line Chart and Pie Chart) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        {{-- Line Chart --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-xl shadow-2xl border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-teal-400 tracking-wide">📈 Statistik Peminjaman Bulanan</h3>
                <div class="bg-gray-700 text-xs text-gray-300 px-3 py-1 rounded-full">
                    {{ count($lineChartData['labels']) }} bulan terakhir
                </div>
            </div>
            <div class="h-72">
                <canvas id="lineChartLaporan"></canvas>
            </div>
        </div>

        {{-- Pie Chart Section --}}
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-teal-400">📊 Barang Paling Banyak Dipinjam</h3>
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas id="pieChartLaporan"></canvas>
            </div>
        </div>
    </div>

    {{-- Filter & Date Range --}}
    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-xl border border-gray-700/50 mb-10">
        <h3 class="text-xl font-semibold text-teal-400 mb-6 flex items-center">
            <i class="fas fa-filter mr-3"></i> Filter Laporan
        </h3>
        <form action="{{ route('laporan.filter') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-300 mb-2">Rentang Tanggal</label>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="date" name="start_date" class="w-full py-2 px-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 text-white" value="{{ request('start_date') }}">
                        </div>
                        <div class="flex items-center text-gray-400">hingga</div>
                        <div class="flex-1">
                            <input type="date" name="end_date" class="w-full py-2 px-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 text-white" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">Kategori Laporan</label>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <select name="kategori" class="w-full py-2 px-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 text-white">
                                <option value="">Semua Kategori</option>
                                <option value="barang" {{ isset($kategori) && $kategori == 'barang' ? 'selected' : '' }}>Barang</option>
                                <option value="peminjaman" {{ isset($kategori) && $kategori == 'peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                                <option value="pengembalian" {{ isset($kategori) && $kategori == 'pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white py-2 px-6 rounded-lg hover:from-teal-500 hover:to-teal-600 transition duration-300">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Conditional rendering for filtered results --}}
    @if(isset($peminjaman) && (($kategori != 'all' && $peminjaman->count() > 0) || ($kategori == 'all' && ($peminjaman['barang']->count() > 0 || $peminjaman['peminjaman']->count() > 0 || $peminjaman['pengembalian']->count() > 0))))
    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-xl shadow-xl border border-gray-700/50 mb-10">
        <h3 class="text-xl font-semibold text-teal-400 mb-6 flex items-center">
            <i class="fas fa-table mr-3"></i>
            @if($kategori == 'barang')
                Data Barang
            @elseif($kategori == 'peminjaman')
                Data Peminjaman
            @elseif($kategori == 'pengembalian')
                Data Pengembalian
            @elseif($kategori == 'all')
                Semua Data
            @else
                Data Hasil Filter
            @endif
            @if($startDate && $endDate)
             ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
            @endif
        </h3>

        @if($kategori != 'all')
        {{-- Display single category data --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-700 text-left">
                        <th class="p-3 text-white font-medium">No</th>
                        @if($kategori == 'barang')
                            <th class="p-3 text-white font-medium">Nama Barang</th>
                            <th class="p-3 text-white font-medium">Kategori</th>
                            <th class="p-3 text-white font-medium">Stok</th>
                            <th class="p-3 text-white font-medium">Kondisi</th>
                            <th class="p-3 text-white font-medium">Tanggal</th>
                        @elseif($kategori == 'peminjaman')
                            <th class="p-3 text-white font-medium">Peminjam</th>
                            <th class="p-3 text-white font-medium">Tanggal Pinjam</th>
                            <th class="p-3 text-white font-medium">Jumlah Item</th>
                            <th class="p-3 text-white font-medium">Status</th>
                        @elseif($kategori == 'pengembalian')
                            <th class="p-3 text-white font-medium">Peminjam</th>
                            <th class="p-3 text-white font-medium">Nama Barang</th>
                            <th class="p-3 text-white font-medium">Tanggal Kembali</th>
                            <th class="p-3 text-white font-medium">Jumlah</th>
                            <th class="p-3 text-white font-medium">Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $index => $item)
                    <tr class="{{ $index % 2 == 0 ? 'bg-gray-800' : 'bg-gray-800/50' }} border-b border-gray-700">
                        <td class="p-3 text-gray-300">{{ $index + 1 }}</td>
                        @if($kategori == 'barang')
                            <td class="p-3 text-white">{{ $item->nama_barang }}</td>
                            <td class="p-3 text-gray-300">{{ optional($item->kategori)->nama_kategori ?? '-' }}</td>
                            <td class="p-3 text-gray-300">{{ $item->stock }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $item->kondisi_barang == 'baik' ? 'bg-green-900/50 text-green-400' :
                                        ($item->kondisi_barang == 'rusak' ? 'bg-red-900/50 text-red-400' : 'bg-yellow-900/50 text-yellow-400') }}">
                                        {{ ucfirst($item->kondisi_barang) }}
                                </span>
                            </td>
                            <td class="p-3 text-gray-300">{{ $item->created_at->format('d/m/Y') }}</td>
                        @elseif($kategori == 'peminjaman')
                            <td class="p-3 text-white">{{ optional($item->user)->username ?? '-' }}</td>
                            <td class="p-3 text-gray-300">
                                @if($item->detail->count() > 0)
                                    {{ \Carbon\Carbon::parse($item->detail->first()->tanggal_pinjam)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            {{-- Assuming 'peminjaman' data refers to the Peminjaman model, sum its details' quantities --}}
                            <td class="p-3 text-gray-300">{{ $item->detail->sum('jumlah') }} Item</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $item->status == 'pending' ? 'bg-yellow-900/50 text-yellow-400' :
                                        ($item->status == 'approve' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400') }}">
                                        {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        @elseif($kategori == 'pengembalian')
                            <td class="p-3 text-white">{{ optional(optional($item->peminjaman)->user)->username ?? '-' }}</td>
                            <td class="p-3 text-gray-300">{{ optional($item->barang)->nama_barang ?? '-' }}</td>
                            <td class="p-3 text-gray-300">{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                            <td class="p-3 text-gray-300">{{ $item->jumlah }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $item->status == 'pending' ? 'bg-yellow-900/50 text-yellow-400' :
                                        ($item->status == 'approve' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400') }}">
                                        {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        @endif
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-gray-400 text-center">Tidak ada data yang tersedia untuk kategori ini dalam rentang tanggal yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else {{-- This 'else' correctly belongs to the `if($kategori != 'all')` above --}}
        {{-- Display all categories data (tabs) --}}
        <div class="mb-6 border-b border-gray-700">
            <div class="flex flex-wrap">
                <button id="tab-barang" class="tab-btn active px-6 py-3 text-teal-400 border-b-2 border-teal-400">
                    Barang <span class="ml-2 bg-teal-900/50 text-teal-400 text-xs px-2 py-1 rounded-full">{{ $peminjaman['barang']->count() }}</span>
                </button>
                <button id="tab-peminjaman" class="tab-btn px-6 py-3 text-gray-400 hover:text-teal-400">
                    Peminjaman <span class="ml-2 bg-gray-800 text-gray-400 text-xs px-2 py-1 rounded-full">{{ $peminjaman['peminjaman']->count() }}</span>
                </button>
                <button id="tab-pengembalian" class="tab-btn px-6 py-3 text-gray-400 hover:text-teal-400">
                    Pengembalian <span class="ml-2 bg-gray-800 text-gray-400 text-xs px-2 py-1 rounded-full">{{ $peminjaman['pengembalian']->count() }}</span>
                </button>
            </div>
        </div>
        {{-- Barang Table --}}
        <div id="content-barang" class="tab-content">
            @if($peminjaman['barang']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-700 text-left">
                            <th class="p-3 text-white font-medium">No</th>
                            <th class="p-3 text-white font-medium">Kode</th>
                            <th class="p-3 text-white font-medium">Nama Barang</th>
                            <th class="p-3 text-white font-medium">Kategori</th>
                            <th class="p-3 text-white font-medium">Stok</th>
                            <th class="p-3 text-white font-medium">Kondisi</th>
                            <th class="p-3 text-white font-medium">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman['barang'] as $index => $item)
                        <tr class="{{ $index % 2 == 0 ? 'bg-gray-800' : 'bg-gray-800/50' }} border-b border-gray-700">
                            <td class="p-3 text-gray-300">{{ $index + 1 }}</td>
                            <td class="p-3 text-gray-300">{{ $item->kode_barang }}</td>
                            <td class="p-3 text-white">{{ $item->nama_barang }}</td>
                            <td class="p-3 text-gray-300">{{ optional($item->kategori)->nama_kategori ?? '-' }}</td>
                            <td class="p-3 text-gray-300">{{ $item->stock }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $item->kondisi_barang == 'baik' ? 'bg-green-900/50 text-green-400' :
                                        ($item->kondisi_barang == 'rusak' ? 'bg-red-900/50 text-red-400' : 'bg-yellow-900/50 text-yellow-400') }}">
                                        {{ ucfirst($item->kondisi_barang) }}
                                </span>
                            </td>
                            <td class="p-3 text-gray-300">{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-3 text-gray-400 text-center">Tidak ada data barang yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-400">Tidak ada data barang yang tersedia.</p>
            @endif
        </div>
        {{-- Peminjaman Table --}}
        <div id="content-peminjaman" class="tab-content hidden">
            @if($peminjaman['peminjaman']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-700 text-left">
                            <th class="p-3 text-white font-medium">No</th>
                            <th class="p-3 text-white font-medium">Peminjam</th>
                            <th class="p-3 text-white font-medium">Tanggal Pinjam</th>
                            <th class="p-3 text-white font-medium">Jumlah Item</th>
                            <th class="p-3 text-white font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman['peminjaman'] as $index => $item)
                        <tr class="{{ $index % 2 == 0 ? 'bg-gray-800' : 'bg-gray-800/50' }} border-b border-gray-700">
                            <td class="p-3 text-gray-300">{{ $index + 1 }}</td>
                            <td class="p-3 text-white">{{ optional($item->user)->username ?? '-' }}</td>
                            <td class="p-3 text-gray-300">
                                @if($item->detail->count() > 0)
                                    {{ \Carbon\Carbon::parse($item->detail->first()->tanggal_pinjam)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3 text-gray-300">{{ $item->detail->sum('jumlah') }} Item</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $item->status == 'pending' ? 'bg-yellow-900/50 text-yellow-400' :
                                        ($item->status == 'approve' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400') }}">
                                        {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-3 text-gray-400 text-center">Tidak ada data peminjaman yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-400">Tidak ada data peminjaman yang tersedia.</p>
            @endif
        </div>
        {{-- Pengembalian Table --}}
        <div id="content-pengembalian" class="tab-content hidden">
            @if($peminjaman['pengembalian']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-700 text-left">
                            <th class="p-3 text-white font-medium">No</th>
                            <th class="p-3 text-white font-medium">Peminjam</th>
                            <th class="p-3 text-white font-medium">Nama Barang</th>
                            <th class="p-3 text-white font-medium">Tanggal Kembali</th>
                            <th class="p-3 text-white font-medium">Jumlah</th>
                            <th class="p-3 text-white font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman['pengembalian'] as $index => $item)
                        <tr class="{{ $index % 2 == 0 ? 'bg-gray-800' : 'bg-gray-800/50' }} border-b border-gray-700">
                            <td class="p-3 text-gray-300">{{ $index + 1 }}</td>
                            <td class="p-3 text-white">{{ optional(optional($item->peminjaman)->user)->username ?? '-' }}</td>
                            <td class="p-3 text-gray-300">{{ optional($item->barang)->nama_barang ?? '-' }}</td>
                            <td class="p-3 text-gray-300">{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                            <td class="p-3 text-gray-300">{{ $item->jumlah }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $item->status == 'pending' ? 'bg-yellow-900/50 text-yellow-400' :
                                        ($item->status == 'approve' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400') }}">
                                        {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-3 text-gray-400 text-center">Tidak ada data pengembalian yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-400">Tidak ada data pengembalian yang tersedia.</p>
            @endif
        </div>
        @endif {{-- THIS IS THE CRUCIAL MISSING @ENDIF FOR THE OUTER CONDITIONAL BLOCK --}}
    </div>
    @endif {{-- This @endif closes the initial @if(isset($peminjaman) && ...) for the entire filtered results section --}}
</div>

<script>
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => btn.classList.remove('active', 'text-teal-400', 'border-teal-400'));
            // Remove the specific active tab styling
            tabButtons.forEach(btn => {
                btn.classList.remove('border-b-2', 'border-teal-400');
                btn.classList.add('text-gray-400', 'hover:text-teal-400'); // Re-add default hover
            });

            tabContents.forEach(content => content.classList.add('hidden'));

            button.classList.add('active', 'text-teal-400', 'border-b-2', 'border-teal-400'); // Add active styles
            button.classList.remove('text-gray-400', 'hover:text-teal-400'); // Remove default hover
            const contentId = `content-${button.id.split('-')[1]}`;
            document.getElementById(contentId).classList.remove('hidden');

            // Update tab counter background
            tabButtons.forEach(btn => {
                const span = btn.querySelector('span');
                if (span) {
                    span.classList.remove('bg-teal-900/50', 'text-teal-400');
                    span.classList.add('bg-gray-800', 'text-gray-400');
                }
            });
            const activeSpan = button.querySelector('span');
            if (activeSpan) {
                activeSpan.classList.remove('bg-gray-800', 'text-gray-400');
                activeSpan.classList.add('bg-teal-900/50', 'text-teal-400');
            }
        });
    });

    // Initialize the first tab as active on page load if 'all' category is selected
    // Or if no specific category is selected and showing all data.
    document.addEventListener('DOMContentLoaded', () => {
        // Find the 'barang' tab button and click it to activate it by default
        const defaultTabButton = document.getElementById('tab-barang');
        if (defaultTabButton) {
            defaultTabButton.click();
        }
    });

</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // LINE CHART for Laporan
        const lineCtxLaporan = document.getElementById('lineChartLaporan').getContext('2d');
        const lineChartDataLaporan = @json($lineChartData);
        const lineLabelsLaporan = lineChartDataLaporan.labels;
        const lineValuesLaporan = lineChartDataLaporan.values;

        const lineGradientLaporan = lineCtxLaporan.createLinearGradient(0, 0, 0, 400);
        lineGradientLaporan.addColorStop(0, 'rgba(0, 255, 187, 0.91)');
        lineGradientLaporan.addColorStop(1, 'rgba(2, 73, 68, 0.42)');

        new Chart(lineCtxLaporan, {
            type: 'line',
            data: {
                labels: lineLabelsLaporan,
                datasets: [{
                    label: 'Peminjaman Bulanan',
                    data: lineValuesLaporan,
                    backgroundColor: lineGradientLaporan,
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#5bbfba',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(20, 20, 20, 0.9)',
                        titleColor: '#5bbfba',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 6,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });

        // PIE CHART for Laporan
        const pieCtxLaporan = document.getElementById('pieChartLaporan').getContext('2d');
        const pieChartDataLaporan = @json($pieChartData);
        const pieLabelsLaporan = pieChartDataLaporan.labels;
        const pieValuesLaporan = pieChartDataLaporan.values;

        new Chart(pieCtxLaporan, {
            type: 'doughnut',
            data: {
                labels: pieLabelsLaporan,
                datasets: [{
                    data: pieValuesLaporan,
                    backgroundColor: [
                        'rgba(20, 184, 166, 0.85)',
                        'rgba(124, 58, 237, 0.85)',
                        'rgba(219, 39, 119, 0.85)',
                        'rgba(217, 119, 6, 0.85)',
                        'rgba(79, 70, 229, 0.85)',
                        'rgba(30, 64, 175, 0.85)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 0.5)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#fff',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(20, 20, 20, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 6,
                        displayColors: true
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection