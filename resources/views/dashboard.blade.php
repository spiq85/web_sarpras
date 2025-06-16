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
                <a href="/dashboard" class="flex items-center gap-2 text-teal-400 font-semibold py-2 px-3 rounded-lg bg-gray-800/70 border-l-2 border-teal-400 transition duration-300">
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
                <a href="/laporan" class="flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300">
                    <i class="fas fa-file-alt"></i> Laporan
                </a>
            </li>

            {{-- === NOTIFICATION ICON AND DROPDOWN === --}}
            <li class="relative z-40">
                <button id="notification-btn" class="relative flex items-center gap-2 text-gray-300 hover:text-teal-400 py-2 px-3 rounded-lg hover:bg-gray-800/50 transition duration-300 focus:outline-none">
                    <i class="fas fa-bell"></i> Notifikasi
                    @if($unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                            {{ $unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-50 hidden max-h-96 overflow-y-auto">
                    <div class="px-4 py-3 border-b border-gray-700 flex justify-between items-center">
                        <h4 class="text-white font-semibold">Notifikasi</h4>
                        <span class="text-sm text-gray-400">{{ $unreadNotifications->count() }} belum dibaca</span>
                    </div>
                    
                    @if($allNotifications->isEmpty())
                        <div class="px-4 py-6 text-center text-gray-400">Tidak ada notifikasi.</div>
                    @else
                        <ul>
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
                                            @if(array_key_exists('url', $notification->data)) {{-- Pemeriksaan yang lebih aman --}}
                                                <a href="{{ url($notification->data['url']) }}" class="ml-2 text-xs text-blue-400 hover:text-blue-300 font-semibold px-2 py-1 rounded-full bg-blue-900/30 hover:bg-blue-900/50 transition-colors" target="_blank">
                                                    Detail
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
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

    {{-- Hero Section --}}
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-white mb-2">Dashboard Admin</h1>
        <p class="text-gray-300">Selamat datang di Sistem Informasi Sarana dan Prasarana</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @php
        $gradients = [
            'from-purple-600 to-purple-950',
            'from-rose-600 to-rose-950',
            'from-yellow-600 to-yellow-950',
            'from-indigo-600 to-indigo-950'
        ];
        
        $icons = [
            '<i class="fas fa-box-open text-4xl opacity-80"></i>',
            '<i class="fas fa-hand-holding text-4xl opacity-80"></i>',
            '<i class="fas fa-undo-alt text-4xl opacity-80"></i>',
            '<i class="fas fa-users text-4xl opacity-80"></i>'
        ];
        @endphp

        @foreach ($summaryData as $index => $data)
        <div
            class="p-6 rounded-xl shadow-xl bg-gradient-to-br {{ $gradients[$index % count($gradients)] }} relative overflow-hidden group transition duration-300 hover:scale-105 hover:shadow-lg hover:shadow-white/20">
            <div
                class="absolute -inset-1 bg-gradient-to-r from-white/20 to-transparent blur-xl opacity-20 group-hover:opacity-40 transition duration-300">
            </div>

            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h2 class="text-sm text-gray-200 font-semibold uppercase tracking-wider">{{ $data['title'] }}</h2>
                    <p class="text-4xl font-extrabold text-white mt-2">{{ $data['value'] }}</p>
                </div>
                <div class="text-white/70">
                    {!! $icons[$index % count($icons)] !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        {{-- Chart Section (Line Chart) --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-xl shadow-2xl border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-teal-400 tracking-wide">📈 Statistik Peminjaman Bulanan</h3>
                <div class="bg-gray-700 text-xs text-gray-300 px-3 py-1 rounded-full">
                    {{ count($lineChartData['labels']) }} bulan terakhir
                </div>
            </div>
            <div class="h-72">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        {{-- Pie Chart Section --}}
        <div class="bg-gray-800 p-6 rounded-xl  shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-teal-400">📊 Barang Paling Banyak Dipinjam</h3>
                {{-- <a href="/barang" class="text-xs bg-teal-800 hover:bg-teal-700 text-white px-3 py-1 rounded-full transition-colors">
                    Lihat Semua
                </a> --}}
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        {{-- Recent Peminjaman --}}
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-teal-400">Peminjaman Terbaru</h3>
                <a href="/peminjaman" class="text-xs bg-teal-800 hover:bg-teal-700 text-white px-3 py-1 rounded-full transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="space-y-3">
                @foreach ($recentPeminjaman as $item)
                <div class="bg-gray-700 p-4 rounded-lg border border-gray-600 shadow-md">
                    <div class="flex justify-between">
                        <strong class="text-white">{{ $item['judul'] }}</strong>
                        <span class="text-xs bg-teal-900 text-teal-300 rounded-full px-2 py-1">{{ $item['jumlah'] }}</span>
                    </div>
                    <div class="flex justify-between mt-1 text-sm">
                        <span class="text-gray-300">{{ $item['oleh'] }}</span>
                        <span class="text-gray-400">{{ $item['tanggal'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Pengembalian --}}
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-teal-400">Pengembalian Terbaru</h3>
                <a href="/detail-pengembalian" class="text-xs bg-teal-800 hover:bg-teal-700 text-white px-3 py-1 rounded-full transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="space-y-3">
                @foreach ($recentPengembalian as $item)
                <div class="bg-gray-700 p-4 rounded-lg border border-gray-600 shadow-md">
                    <div class="flex justify-between">
                        <strong class="text-white">{{ $item['judul'] }}</strong>
                        <span class="text-xs bg-teal-900 text-teal-300 rounded-full px-2 py-1">{{ $item['jumlah'] }}</span>
                    </div>
                    <div class="flex justify-between mt-1 text-sm">
                        <span class="text-gray-300">{{ $item['oleh'] }}</span>
                        <span class="text-gray-400">{{ $item['tanggal'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    {{-- User Terbaru --}}
    <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-teal-400">User Terbaru</h3>
            <a href="/users" class="text-xs bg-teal-800 hover:bg-teal-700 text-white px-3 py-1 rounded-full transition-colors">
                Lihat Semua
            </a>
        </div>
        <div class="space-y-3">
            @php
            $recentUsers = \App\Models\User::latest()->take(5)->get();
            @endphp
            
            @foreach ($recentUsers as $user)
            <div class="bg-gray-700 p-4 rounded-lg border border-gray-600 shadow-md">
                <div class="flex justify-between">
                    <strong class="text-white">{{ $user->username }}</strong>
                    <span class="text-xs bg-{{ $user->role == 'admin' ? 'purple' : 'blue' }}-900 text-{{ $user->role == 'admin' ? 'purple' : 'blue' }}-300 rounded-full px-2 py-1 capitalize">{{ $user->role }}</span>
                </div>
                <div class="flex justify-between mt-1 text-sm">
                    <span class="text-gray-300">{{ $user->email }}</span>
                    <span class="text-gray-400">{{ $user->class }} {{ $user->major }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    {{-- Inventaris Terbaru --}}
    <div class="bg-gray-800 p-6 rounded-xl shadow-lg overflow-hidden mb-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-teal-400">Inventaris Terbaru</h3>
            <a href="/barang" class="text-xs bg-teal-800 hover:bg-teal-700 text-white px-3 py-1 rounded-full transition-colors">
                Lihat Semua
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm table-auto">
                <thead>
                    <tr class="text-left border-b border-gray-600">
                        <th class="py-3 px-2">Gambar</th>
                        <th class="py-3 px-2">Kode</th>
                        <th class="py-3 px-2">Nama</th>
                        <th class="py-3 px-2">Kategori</th>
                        <th class="py-3 px-2">Jumlah</th>
                        <th class="py-3 px-2">Kondisi</th>
                        <th class="py-3 px-2">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventoryData as $item)
                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                        <td class="py-3 px-2">
                            @if(!empty($item['gambar']))
                            <img src="{{ asset('storage/gambar_barang/' . $item['gambar']) }}" alt="Gambar"
                                class="w-10 h-10 object-cover rounded border border-gray-600">
                            @else
                            <div class="w-10 h-10 flex items-center justify-center bg-gray-600 rounded">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-2 whitespace-nowrap">{{ $item['kode'] }}</td>
                        <td class="py-3 px-2 whitespace-nowrap">{{ $item['nama'] }}</td>
                        <td class="py-3 px-2 whitespace-nowrap">{{ $item['kategori'] }}</td>
                        <td class="py-3 px-2 whitespace-nowrap">{{ $item['jumlah'] }}</td>
                        <td class="py-3 px-2 whitespace-nowrap">
                            @if($item['kondisi'] == 'Baik')
                            <span class="px-2 py-1 bg-green-900 text-green-300 rounded-full text-xs">{{ $item['kondisi'] }}</span>
                            @elseif($item['kondisi'] == 'Rusak Ringan')
                            <span class="px-2 py-1 bg-yellow-900 text-yellow-300 rounded-full text-xs">{{ $item['kondisi'] }}</span>
                            @else
                            <span class="px-2 py-1 bg-red-900 text-red-300 rounded-full text-xs">{{ $item['kondisi'] }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 whitespace-nowrap">{{ $item['tanggal'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-auto pt-8">
        <div class="border-t border-gray-700 pt-6 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} SISFO SARPRAS - Sistem Informasi Sarana dan Prasarana</p>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // LINE CHART
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChartData = @json($lineChartData);
        const lineLabels = lineChartData.labels;
        const lineValues = lineChartData.values;

        const lineGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
        lineGradient.addColorStop(0, 'rgba(0, 255, 187, 0.91)');
        lineGradient.addColorStop(1, 'rgba(2, 73, 68, 0.42)');

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: 'Peminjaman Bulanan',
                    data: lineValues,
                    backgroundColor: lineGradient,
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

        // PIE CHART
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChartData = @json($pieChartData);
        const pieLabels = pieChartData.labels;
        const pieValues = pieChartData.values;

        new Chart(pieCtx, {
            type: 'doughnut', // Atau 'pie'
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieValues,
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
                        position: 'right', // Posisi legenda
                        labels: {
                            color: '#fff', // Warna teks legenda
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