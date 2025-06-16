<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang; // Pastikan ini diimpor jika diperlukan
use App\Models\DetailPeminjaman; // Pastikan ini diimpor
use App\Models\Peminjaman; // Pastikan ini diimpor
use App\Models\DetailPengembalian; // Pastikan ini diimpor
use App\Models\User; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Carbon\Carbon; // Pastikan ini diimpor
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk DB raw expressions

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalAset = Barang::sum('stock');
            $peminjamanAktif = Peminjaman::where('status', 'dipinjam')->count();
            $pengajuanPengembalianPending = DetailPengembalian::where('status', 'pending')->count();
            $totalUser = User::count();

            $summaryData = [
                ['title' => 'Total Aset', 'value' => number_format($totalAset)],
                ['title' => 'Peminjaman Aktif', 'value' => number_format($peminjamanAktif)],
                ['title' => 'Pengajuan Pengembalian', 'value' => number_format($pengajuanPengembalianPending)],
                ['title' => 'Total User', 'value' => number_format($totalUser)],
            ];

            // === DATA UNTUK LINE CHART: Peminjaman Per Bulan ===
            $monthsToShow = 6; // Menampilkan data 6 bulan terakhir
            $monthlyLabels = [];
            $monthlyValues = [];

            for ($i = $monthsToShow - 1; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthName = $month->translatedFormat('M Y'); // Contoh: Jan 2023
                $monthlyLabels[] = $monthName;

                // Hitung total peminjaman untuk bulan ini
                $count = DetailPeminjaman::whereYear('tanggal_pinjam', $month->year)
                                        ->whereMonth('tanggal_pinjam', $month->month)
                                        ->count(); // Menghitung jumlah detail peminjaman per bulan
                $monthlyValues[] = $count;
            }

            $lineChartData = [
                'labels' => $monthlyLabels,
                'values' => $monthlyValues,
            ];
            // === END DATA UNTUK LINE CHART ===


            // === DATA UNTUK PIE CHART: Barang Paling Banyak Dipinjam ===
            $mostBorrowedItems = DetailPeminjaman::select('id_barang', DB::raw('COUNT(*) as total_borrowed'))
                ->groupBy('id_barang')
                ->orderByDesc('total_borrowed')
                ->with('barang') // Eager load relasi barang
                ->take(5) // Ambil 5 barang teratas
                ->get();

            $pieChartLabels = [];
            $pieChartValues = [];
            foreach ($mostBorrowedItems as $item) {
                $pieChartLabels[] = optional($item->barang)->nama_barang ?? 'Unknown';
                $pieChartValues[] = $item->total_borrowed;
            }
            $pieChartData = [
                'labels' => $pieChartLabels,
                'values' => $pieChartValues,
            ];
            // === END DATA UNTUK PIE CHART ===


            $recentPeminjaman = DetailPeminjaman::with('user', 'barang')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'judul' => 'Peminjaman ' . optional($item->user)->username . ' - ' . optional($item->barang)->nama_barang,
                        'oleh' => optional($item->user)->username ?? '-',
                        'jumlah' => $item->jumlah . ' Item',
                        'tanggal' => Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y'),
                    ];
                });

            $inventoryData = Barang::with('kategori')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'kode' => $item->kode_barang,
                        'nama' => $item->nama_barang,
                        'kategori' => optional($item->kategori)->nama_kategori ?? '-',
                        'jumlah' => $item->stock,
                        'kondisi' => ucfirst($item->kondisi_barang),
                        'tanggal' => $item->created_at->format('d/m/Y'),
                        'gambar' => $item->gambar_barang,
                    ];
                });

            $recentPengembalian = DetailPengembalian::with('detailPeminjaman.user', 'barang')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'judul' => 'Pengembalian ' . optional(optional($item->detailPeminjaman)->user)->username . ' - ' . optional($item->barang)->nama_barang,
                        'oleh' => optional(optional($item->detailPeminjaman)->user)->username ?? '-',
                        'jumlah' => $item->jumlah . ' Item',
                        'tanggal' => Carbon::parse($item->tanggal_pengembalian)->translatedFormat('d M Y'),
                    ];
                });

            $admin = Auth::user();
            $unreadNotifications = collect();
            $allNotifications = collect();

            if ($admin) {
                $unreadNotifications = $admin->unreadNotifications;
                $allNotifications = $admin->notifications;
            }

            return view('dashboard', compact(
                'summaryData',
                'lineChartData', // Data baru untuk line chart
                'pieChartData',  // Data baru untuk pie chart
                'recentPeminjaman',
                'inventoryData',
                'recentPengembalian',
                'unreadNotifications',
                'allNotifications'
            ));

        } catch (\Exception $e) {
            Log::error('Error in DashboardController@index:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Untuk web, kita bisa redirect back atau tampilkan view error
            return redirect()->back()->with('error', 'Gagal memuat dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Menandai notifikasi spesifik sebagai sudah dibaca (Web).
     * Menggunakan PATCH karena ini adalah update sebagian resource.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markNotificationAsRead($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $notification = $admin->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
                Log::info('Notification marked as read:', ['notification_id' => $id, 'user_id' => $admin->id]);
                return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sebagai sudah dibaca.');
            }
        }
        Log::warning('Failed to mark notification as read:', ['notification_id' => $id, 'user_id' => $admin->id ?? 'guest']);
        return redirect()->back()->with('error', 'Notifikasi tidak ditemukan atau Anda tidak memiliki akses.');
    }

    /**
     * Menandai semua notifikasi yang belum dibaca sebagai sudah dibaca (Web).
     * Menggunakan GET untuk kesederhanaan dari link, tapi PATCH/POST lebih baik jika form.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $admin = Auth::user();
        if ($admin) {
            $admin->unreadNotifications->markAsRead();
            Log::info('All unread notifications marked as read for user:', ['user_id' => $admin->id]);
            return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai sebagai sudah dibaca.');
        }
        Log::warning('Attempt to mark all notifications as read by non-logged in user.');
        return redirect()->back()->with('error', 'Gagal menandai semua notifikasi. Anda harus login.');
    }

    /**
     * Metode API untuk mendapatkan notifikasi (opsional, jika Anda punya admin dashboard API).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiNotifications()
    {
        $admin = Auth::user();
        if ($admin) {
            return response()->json([
                'success' => true,
                'notifications' => $admin->notifications,
                'unread_count' => $admin->unreadNotifications->count()
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
}