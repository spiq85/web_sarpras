<?php

namespace App\Http\Controllers;

use App\Models\DetailPengembalian;
use Illuminate\Http\Request;

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
        $detail = DetailPengembalian::findOrFail($id);
        $detail->status = 'approve';
        $detail->save();

        return redirect()->back()->with('success', 'Pengembalian telah disetujui!');
    }

    // Reject pengembalian
    public function reject($id)
    {
        $detail = DetailPengembalian::findOrFail($id);
        $detail->status = 'not approve';
        $detail->save();

        return redirect()->back()->with('error', 'Pengembalian telah ditolak!');
    }
}
