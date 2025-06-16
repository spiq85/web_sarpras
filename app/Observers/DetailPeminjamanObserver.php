<?php

namespace App\Observers;

use App\Models\DetailPeminjaman;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;

class DetailPeminjamanObserver
{
    /**
     * Handle the DetailPeminjaman "updated" event.
     * Manages stock when the borrowing detail status changes.
     */
    public function updated(DetailPeminjaman $detailPeminjaman): void
    {
        // Only proceed if the 'status' attribute has changed
        if ($detailPeminjaman->isDirty('status')) {
            $barang = Barang::find($detailPeminjaman->id_barang);

            if (!$barang) {
                Log::error("Barang with ID {$detailPeminjaman->id_barang} not found for DetailPeminjaman ID {$detailPeminjaman->id_detail_peminjaman}.");
                return;
            }

            // Scenario 1: Item is approved and now borrowed
            if ($detailPeminjaman->status === 'dipinjam' && $detailPeminjaman->getOriginal('status') !== 'dipinjam') {
                if ($barang->stock >= $detailPeminjaman->jumlah) {
                    $barang->stock -= $detailPeminjaman->jumlah;
                    $barang->stock_dipinjam += $detailPeminjaman->jumlah;
                    $barang->save();
                    Log::info("Stock updated (borrowed): Barang {$barang->id_barang}, available: {$barang->stock}, borrowed: {$barang->stock_dipinjam}");
                } else {
                    Log::warning("Not enough stock for Barang ID {$barang->id_barang} to borrow {$detailPeminjaman->jumlah}. Current available: {$barang->stock}.");
                    // You might want to revert the status or notify the admin here
                }
            }
            // Scenario 2: Item is returned
            elseif ($detailPeminjaman->status === 'dikembalikan' && $detailPeminjaman->getOriginal('status') !== 'dikembalikan') {
                $barang->stock += $detailPeminjaman->jumlah;
                $barang->stock_dipinjam -= $detailPeminjaman->jumlah;
                $barang->stock_dipinjam = max(0, $barang->stock_dipinjam); // Prevent negative borrowed stock
                $barang->save();
                Log::info("Stock updated (returned): Barang {$barang->id_barang}, available: {$barang->stock}, borrowed: {$barang->stock_dipinjam}");
            }
            // Scenario 3: Item is rejected from 'pending' (no stock adjustment needed if stock wasn't decreased initially)
            elseif ($detailPeminjaman->status === 'ditolak' && $detailPeminjaman->getOriginal('status') === 'pending') {
                Log::info("Peminjaman Detail ID {$detailPeminjaman->id_detail_peminjaman} was rejected. No stock changes needed as it was pending.");
            }
        }
    }
}