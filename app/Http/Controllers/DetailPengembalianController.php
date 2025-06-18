<?php

namespace App\Http\Controllers;

use App\Models\DetailPengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\Barang;


class DetailPengembalianController extends Controller
{
    // Tampilkan semua data detail pengembalian
    public function index()
    {
        $data = DetailPengembalian::with('barang', 'peminjaman', 'detailPeminjaman')->where('soft_delete', 0)->get();
        return view('detail-pengembalian.index', compact('data'));
    }

    // Simpan data detail pengembalian baru
    public function store(Request $request)
    {
        $request->validate([
            'users_id' => 'required|exists:users,users_id',
            'id_detail_peminjaman' => 'required|exists:detail_peminjaman,id_detail_peminjaman',
            'id_peminjaman' => 'required|exists:peminjaman,id_peminjaman',
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pengembalian' => 'required|date',
            'kondisi' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'item_image' => 'nullable|string',
        ]);

        $detail = DetailPengembalian::create($request->all());

        return response()->json([
            'message' => 'Data detail pengembalian berhasil disimpan.',
            'data' => $detail
        ], 201);
    }

    // Tampilkan detail pengembalian berdasarkan ID
    public function show($id)
    {
        $detail = DetailPengembalian::with('barang', 'peminjaman', 'detailPeminjaman')->findOrFail($id);
        return response()->json($detail);
    }

    // Update data detail pengembalian
    public function update(Request $request, $id)
    {
        $detail = DetailPengembalian::findOrFail($id);

        $request->validate([
            'jumlah' => 'nullable|integer|min:1',
            'kondisi' => 'nullable|string',
            'tanggal_pengembalian' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'item_image' => 'nullable|string',
        ]);

        $detail->update($request->all());

        return response()->json([
            'message' => 'Data detail pengembalian berhasil diperbarui.',
            'data' => $detail
        ]);
    }

    // Soft delete
    public function destroy($id)
    {
        $detail = DetailPengembalian::findOrFail($id);
        $detail->soft_delete = 1;
        $detail->save();

        return response()->json([
            'message' => 'Data berhasil dihapus (soft delete).'
        ]);
    }

    // Approve pengembalian
   public function approve($id)
{
    DB::beginTransaction();
    try {
        $detailPengembalian = DetailPengembalian::with('detailPeminjaman.barang', 'peminjaman')->findOrFail($id);

        // ... (validasi relasi)

        $detailPengembalian->status = 'approve';
        $detailPengembalian->save();

        // Ini akan memicu DetailPeminjamanObserver untuk mengelola stok
        $detailPeminjaman = $detailPengembalian->detailPeminjaman;
        $detailPeminjaman->status = 'kembali';
        $detailPeminjaman->save();

        // === PASTIKAN BAGIAN INI BENAR-BENAR BERJALAN DAN MENGUBAH STATUS DI DB ===
        $peminjamanUtama = $detailPengembalian->peminjaman; // Mendapatkan model Peminjaman dari relasi
        if ($peminjamanUtama) { // Pastikan model Peminjaman induk ditemukan
             if ($peminjamanUtama->status !== 'kembali') { // Hanya update jika belum 'kembali'
                 $peminjamanUtama->status = 'kembali';
                 $peminjamanUtama->save(); // <--- INI KRITIS!
                 Log::info("Peminjaman utama ID {$peminjamanUtama->id_peminjaman} status diubah menjadi 'kembali' setelah pengembalian disetujui.");
             }
        } else {
             Log::warning("Model Peminjaman induk tidak ditemukan untuk DetailPengembalian ID {$id}. Tidak dapat memperbarui status Peminjaman utama.");
        }
        // =========================================================================

        // ... (logika stok barang yang sudah dipindahkan ke Observer)

        DB::commit();
        return redirect()->back()->with('success', 'Pengembalian telah disetujui dan status diperbarui!');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error approving return via web:', ['id' => $id, 'error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        return redirect()->back()->with('error', 'Gagal menyetujui pengembalian: ' . $e->getMessage());
    }
}

    // Reject pengembalian
    public function reject($id)
    {
        DB::beginTransaction();
        try {
            $detailPengembalian = DetailPengembalian::with('detailPeminjaman.barang', 'peminjaman')->findOrFail($id);

            if (!$detailPengembalian->detailPeminjaman || !$detailPengembalian->peminjaman) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Detail Peminjaman atau Peminjaman utama terkait tidak ditemukan.');
            }

            $detailPengembalian->status = 'not approve'; // Status pengembalian ditolak
            $detailPengembalian->save();

            // Jika pengembalian ditolak, kembalikan status DetailPeminjaman ke 'dipinjam'
            // Ini asumsinya jika pengajuan pengembalian ditolak, barang masih dianggap 'dipinjam'.
            $detailPeminjaman = $detailPengembalian->detailPeminjaman;
            if ($detailPeminjaman->status !== 'dipinjam' && $detailPeminjaman->status !== 'pending') { // Hanya jika tidak dalam status 'dipinjam' atau 'pending'
                $detailPeminjaman->status = 'dipinjam'; // Kembali ke 'dipinjam'
                $detailPeminjaman->save();
                Log::info("Detail Peminjaman ID {$detailPeminjaman->id_detail_peminjaman} status diubah kembali ke 'dipinjam' karena pengajuan pengembalian ditolak.");
            }

            // Status Peminjaman utama TIDAK diubah menjadi 'ditolak' di sini.
            // Peminjaman utama tetap 'dipinjam' jika pengembaliannya ditolak,
            // karena barangnya belum berhasil kembali.
            // Jika Anda ingin Peminjaman utama berubah menjadi 'rejected' saat DetailPeminjaman ditolak,
            // itu harus terjadi di PeminjamanController@reject, bukan di sini.

            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan pengembalian telah ditolak. Barang masih dianggap dipinjam.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting return via web:', ['id' => $id, 'error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->with('error', 'Gagal menolak pengajuan pengembalian: ' . $e->getMessage());
        }
    }
}