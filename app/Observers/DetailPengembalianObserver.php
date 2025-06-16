<?php

namespace App\Observers;

use App\Models\DetailPengembalian;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;

class DetailPengembalianObserver
{
    public function updated(DetailPengembalian $detailPengembalian): void
    {
        if ($detailPengembalian->isDirty('status') && $detailPengembalian->status === 'approve') {
            
            $barang = $detailPengembalian->barang;

            if ($barang) {
                try {
                    $jumlahDikembalikan = $detailPengembalian->jumlah;
                    $barang->stock += $jumlahDikembalikan;
                    $barang->stock_dipinjam = max(0, $barang->stock_dipinjam - $jumlahDikembalikan);
                    
                    // --- LOGGING UNTUK INVESTIGASI ---
                    Log::info('Mengecek kondisi barang untuk update status.', [
                        'id_barang' => $barang->id_barang,
                        'stock_dipinjam_sekarang' => $barang->stock_dipinjam,
                        'tipe_data' => gettype($barang->stock_dipinjam) // Untuk memastikan tipenya integer
                    ]);

                    // Gunakan perbandingan ketat (===) untuk memastikan tipe dan nilainya sama
                    if ($barang->stock_dipinjam === 0) {
                        Log::info('Kondisi terpenuhi, mengubah status menjadi "tersedia".');
                        $barang->status = 'tersedia';
                    } else {
                        Log::warning('Kondisi TIDAK terpenuhi, status tidak diubah.', [
                           'stock_dipinjam' => $barang->stock_dipinjam
                        ]);
                    }
                    
                    $barang->save();

                    Log::info("Perubahan pada barang berhasil disimpan.");

                } catch (\Exception $e) {
                    Log::error('Gagal mengembalikan stok barang dari Observer:', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}