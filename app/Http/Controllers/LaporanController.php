<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPeminjaman;
use App\Models\DetailPengembalian;
use App\Models\KategoriBarang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use PDF;
use Excel;
use App\Exports\BarangExport;
use App\Exports\PeminjamanExport;
use App\Exports\PengembalianExport;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $barangTersedia = Barang::where('status', 'Tersedia')->count();
        $totalPeminjaman = DetailPeminjaman::count();
        $peminjamanAktif = DetailPeminjaman::where('status', 'pending')->count();
        $totalPengembalian = DetailPengembalian::where('soft_delete', 0)->count();
        $terlambat = DetailPengembalian::where('soft_delete', 0)->where('status', 'approve')->count();
        $kategoriList = KategoriBarang::all();

        $recentPeminjaman = DetailPeminjaman::with(['user', 'barang'])->latest()->take(5)->get()->map(function ($item) {
            return [
                'judul' => 'Peminjaman ' . optional($item->user)->name,
                'oleh' => optional($item->user)->username ?? '-',
                'jumlah' => $item->jumlah . ' Item',
                'tanggal' => Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y'),
            ];
        });

        $recentPengembalian = DetailPengembalian::with(['peminjaman.user', 'barang'])->latest()->take(5)->get()->map(function ($item) {
            return [
                'judul' => 'Pengembalian ' . optional(optional($item->peminjaman)->user)->name,
                'oleh' => optional(optional($item->peminjaman)->user)->username ?? '-',
                'jumlah' => $item->jumlah . ' Item',
                'tanggal' => Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y'),
            ];
        });

        $inventoryData = Barang::with('kategori')->latest()->take(10)->get()->map(function ($item) {
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

        $chartData = collect([
            ['name' => 'Jan', 'value' => 45],
            ['name' => 'Feb', 'value' => 63],
            ['name' => 'Mar', 'value' => 58],
            ['name' => 'Apr', 'value' => 75],
            ['name' => 'May', 'value' => 10],
        ]);

        // Create empty peminjaman collection as default
        $peminjaman = collect();
        
        // Set default values for filter parameters
        $startDate = null;
        $endDate = null;
        $kategori = null;

        return view('laporan.index', compact(
            'totalBarang', 'barangTersedia',
            'totalPeminjaman', 'peminjamanAktif',
            'totalPengembalian', 'terlambat',
            'kategoriList', 'recentPeminjaman',
            'recentPengembalian', 'inventoryData',
            'chartData', 'peminjaman', 'startDate', 'endDate', 'kategori'
        ));
    }

    public function filter(Request $request)
    {
        // Get filter parameters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $kategori = $request->input('kategori');

        // Base stats that are always needed
        $totalBarang = Barang::count();
        $barangTersedia = Barang::where('status', 'Tersedia')->count();
        $totalPeminjaman = DetailPeminjaman::count();
        $peminjamanAktif = DetailPeminjaman::where('status', 'pending')->count();
        $totalPengembalian = DetailPengembalian::where('soft_delete', 0)->count();
        $terlambat = DetailPengembalian::where('soft_delete', 0)->where('status', 'approve')->count();
        $kategoriList = KategoriBarang::all();

        $recentPeminjaman = DetailPeminjaman::with(['user', 'barang'])->latest()->take(5)->get()->map(function ($item) {
            return [
                'judul' => 'Peminjaman ' . optional($item->user)->name,
                'oleh' => optional($item->user)->username ?? '-',
                'jumlah' => $item->jumlah . ' Item',
                'tanggal' => Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y'),
            ];
        });

        $recentPengembalian = DetailPengembalian::with(['peminjaman.user', 'barang'])->latest()->take(5)->get()->map(function ($item) {
            return [
                'judul' => 'Pengembalian ' . optional(optional($item->peminjaman)->user)->name,
                'oleh' => optional(optional($item->peminjaman)->user)->username ?? '-',
                'jumlah' => $item->jumlah . ' Item',
                'tanggal' => Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y'),
            ];
        });

        // Default inventory data (always needed)
        $inventoryData = Barang::with('kategori')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->latest()->take(10)->get()->map(function ($item) {
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

        $chartData = collect([
            ['name' => 'Jan', 'value' => 45],
            ['name' => 'Feb', 'value' => 63],
            ['name' => 'Mar', 'value' => 58],
            ['name' => 'Apr', 'value' => 75],
            ['name' => 'May', 'value' => 10],
        ]);

        // Initialize empty collection
        $peminjaman = collect();

        // Filter data based on selected category
        if ($kategori === 'peminjaman') {
            $peminjaman = Peminjaman::with(['user', 'detail.barang'])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereHas('detail', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
                    });
                })
                ->get();
        } elseif ($kategori === 'barang') {
            $peminjaman = Barang::with('kategori')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get();
        } elseif ($kategori === 'pengembalian') {
            $peminjaman = DetailPengembalian::with(['barang', 'peminjaman.user'])
                ->where('soft_delete', 0)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('tanggal_pengembalian', [$startDate, $endDate]);
                })
                ->get();
        } elseif ($startDate && $endDate && empty($kategori)) {
            // Handle "Semua Kategori" with date filtering
            // Collect data from all three categories
            $barangData = Barang::with('kategori')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function ($item) {
                    $item->data_type = 'barang';
                    return $item;
                });
                
            $peminjamanData = Peminjaman::with(['user', 'detail.barang'])
                ->whereHas('detail', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
                })
                ->get()
                ->map(function ($item) {
                    $item->data_type = 'peminjaman';
                    return $item;
                });
                
            $pengembalianData = DetailPengembalian::with(['barang', 'peminjaman.user'])
                ->where('soft_delete', 0)
                ->whereBetween('tanggal_pengembalian', [$startDate, $endDate])
                ->get()
                ->map(function ($item) {
                    $item->data_type = 'pengembalian';
                    return $item;
                });
                
            // Combine all data
            $peminjaman = collect([
                'barang' => $barangData,
                'peminjaman' => $peminjamanData,
                'pengembalian' => $pengembalianData
            ]);
            
            // Set "all" flag to indicate we're showing all categories
            $kategori = 'all';
        }

        return view('laporan.index', compact(
            'peminjaman', 'totalBarang', 'barangTersedia',
            'totalPeminjaman', 'peminjamanAktif',
            'totalPengembalian', 'terlambat',
            'kategoriList', 'recentPeminjaman',
            'recentPengembalian', 'inventoryData',
            'chartData', 'startDate', 'endDate', 'kategori'
        ));
    }

    // The rest of your methods remain unchanged...
    // ---------------- BARANG ----------------
    public function barang(Request $request)
    {
        $query = Barang::with('kategori');
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $query->get();
        return view('laporan.pdf.barang', compact('data'));
    }

    public function barangPdf(Request $request)
    {
        $query = Barang::with('kategori');
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $query->get();
        $pdf = PDF::loadView('laporan.pdf.barang', compact('data'));
        return $pdf->download('laporan_barang.pdf');
    }

    public function barangExcel(Request $request)
    {
        return Excel::download(new BarangExport($request->start_date, $request->end_date), 'laporan_barang.xlsx');
    }

    // ---------------- PEMINJAMAN ----------------
    public function peminjaman(Request $request)
    {
        $query = DetailPeminjaman::with(['barang', 'user']);
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_pinjam', [$request->start_date, $request->end_date]);
        }
        $data = $query->get();
        return view('laporan.pdf.peminjaman', compact('data'));
    }

    public function peminjamanPdf(Request $request)
    {
        $query = Peminjaman::with(['user', 'detail.barang']);
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereHas('detail', function ($q) use ($request) {
                $q->whereBetween('tanggal_pinjam', [$request->start_date, $request->end_date]);
            });
        }
        $data = $query->get();
        $pdf = PDF::loadView('laporan.pdf.peminjaman', compact('data'));
        return $pdf->download('laporan_peminjaman.pdf');
    }
    
    public function peminjamanExcel(Request $request)
    {
        return Excel::download(new PeminjamanExport($request->start_date, $request->end_date), 'laporan_peminjaman.xlsx');
    }

    // ---------------- PENGEMBALIAN ----------------
    public function pengembalian(Request $request)
    {
        $query = DetailPengembalian::with(['barang', 'peminjaman.user'])->where('soft_delete', 0);
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_kembali', [$request->start_date, $request->end_date]);
        }
        $data = $query->get();
        return view('laporan.pdf.pengembalian', compact('data'));
    }

    public function pengembalianPdf(Request $request)
    {
        $query = DetailPengembalian::with(['barang', 'peminjaman.user'])->where('soft_delete', 0);
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_kembali', [$request->start_date, $request->end_date]);
        }
        $data = $query->get();
        $pdf = PDF::loadView('laporan.pdf.pengembalian', compact('data'));
        return $pdf->download('laporan_pengembalian.pdf');
    }

    public function pengembalianExcel(Request $request)
    {
        return Excel::download(new PengembalianExport($request->start_date, $request->end_date), 'laporan_pengembalian.xlsx');
    }
}