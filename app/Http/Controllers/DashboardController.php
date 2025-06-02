<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\DetailPengembalian;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAset = Barang::sum('stock');
        $peminjamanAktif = Peminjaman::where('status', 'dipinjam')->count();
        $pengembalianAktif = DetailPengembalian::where('status', 'approve')->count();
        $totalUser = User::count();

        $summaryData = [
            ['title' => 'Total Aset', 'value' => number_format($totalAset)],
            ['title' => 'Total Peminjaman', 'value' => number_format($peminjamanAktif)],
            ['title' => 'Total Pengembalian', 'value' => number_format($pengembalianAktif)],
            ['title' => 'Total User', 'value' => number_format($totalUser)],
        ];

        // Dummy chart data
        $chartData = collect([
            ['name' => 'Jan', 'value' => 45],
            ['name' => 'Feb', 'value' => 63],
            ['name' => 'Mar', 'value' => 58],
            ['name' => 'Apr', 'value' => 75],
            ['name' => 'May', 'value' => 10],
        ]);

        $recentPeminjaman = DetailPeminjaman::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'judul' => 'Peminjaman ' . optional($item->user)->username,
                    'oleh' => optional($item->user)->username ?? '-',
                    'jumlah' => $item->jumlah . ' Item', // Gunakan kolom jumlah langsung dari DetailPeminjaman
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

        $recentPengembalian = DetailPengembalian::with('detailPeminjaman.user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'judul' => 'Pengembalian ' . optional(optional($item->detailPeminjaman)->user)->username,
                    'oleh' => optional(optional($item->detailPeminjaman)->user)->username ?? '-',
                    'jumlah' => $item->detailPeminjaman->jumlah . ' Item', // Gunakan kolom jumlah langsung dari DetailPeminjaman
                    'tanggal' => Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y'),
                ];
            });

        return view('dashboard', compact('summaryData', 'chartData', 'recentPeminjaman', 'inventoryData', 'recentPengembalian'));
    }
}