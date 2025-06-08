<?php

namespace App\Http\Controllers;

use App\Models\DetailPengembalian;
use App\Models\DetailPeminjaman; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk Auth::id() jika diperlukan

class DetailPengembalianApiController extends Controller
{

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_detail_peminjaman' => 'required|exists:detail_peminjaman,id_detail_peminjaman',
                'tanggal_pengembalian' => 'required|date',
                'keterangan' => 'nullable|string',
                'kondisi' => 'nullable|string',
                'item_image' => 'nullable|string',
                'status' => 'required|string|in:approve,not approve,pending',
            ]);

            if ($validator->fails()) {
                Log::error('Detail Pengembalian API Store Validation Failed:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // PERBAIKAN DI SINI: Load relasi 'peminjaman' saat mengambil DetailPeminjaman
            $detailPeminjaman = DetailPeminjaman::with('peminjaman')->find($request->id_detail_peminjaman);
            
            if (!$detailPeminjaman) {
                 return response()->json(['success' => false, 'message' => 'Detail Peminjaman tidak ditemukan.'], 404);
            }

            if (is_null($detailPeminjaman->peminjaman)) {
                Log::error('Detail Peminjaman does not have an id_peminjaman:', ['id_detail_peminjaman' => $detailPeminjaman->id_detail_peminjaman, 'peminjaman_relation' => $detailPeminjaman->peminjaman]);
                return response()->json([
                    'success' => false,
                    'message' => 'Detail Peminjaman terkait tidak memiliki ID Peminjaman utama.'
                ], 500); // Atau 400 Bad Request
            }

            $imagePath = null;
            if ($request->item_image) {
                try {
                    $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->item_image));
                    $imageName = uniqid() . '.jpg';
                    Storage::disk('public')->put('pengembalian_images/' . $imageName, $decodedImage);
                    $imagePath = 'pengembalian_images/' . $imageName;
                } catch (\Exception $e) {
                    Log::error('Error decoding or saving image:', ['message' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
                    ], 400);
                }
            }

            $detailPengembalian = DetailPengembalian::create([
                'users_id' => $detailPeminjaman->users_id,
                'id_detail_peminjaman' => $request->id_detail_peminjaman,
                'id_peminjaman' => $detailPeminjaman->peminjaman->id_peminjaman, // Ini seharusnya tidak null sekarang
                'id_barang' => $detailPeminjaman->id_barang,
                'jumlah' => $detailPeminjaman->jumlah,
                'kondisi' => $request->kondisi,
                'status' => $request->status,
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
                'keterangan' => $request->keterangan,
                'item_image' => $imagePath,
            ]);

            // Update status di DetailPeminjaman
            if ($detailPeminjaman->status === 'dipinjam') {
                $detailPeminjaman->status = 'pending';
                $detailPeminjaman->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pengembalian berhasil disimpan.',
                'data' => $detailPengembalian->load(['barang', 'peminjaman', 'detailPeminjaman'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error storing Detail Pengembalian API:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan detail pengembalian: ' . $e->getMessage()
            ], 500);
        }
    }


    // ... (metode approve dan reject - pastikan nama status sesuai dengan ENUM di migrasi Anda)
    public function approve($id)
    {
        try {
            $detail = DetailPengembalian::findOrFail($id);
            $detail->status = 'approve'; // Sesuai ENUM migrasi
            $detail->save();

            // Update status DetailPeminjaman menjadi 'kembali'
            if ($detail->detailPeminjaman) {
                $detailPeminjaman = $detail->detailPeminjaman;
                $detailPeminjaman->status = 'kembali'; // Harus sesuai ENUM DetailPeminjaman
                $detailPeminjaman->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian telah disetujui!',
                'data' => $detail->load(['barang', 'peminjaman', 'detailPeminjaman'])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error approving Detail Pengembalian API:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pengembalian: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            $detail = DetailPengembalian::findOrFail($id);
            $detail->status = 'not approve'; // Sesuai ENUM migrasi
            $detail->save();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian telah ditolak!',
                'data' => $detail->load(['barang', 'peminjaman', 'detailPeminjaman'])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error rejecting Detail Pengembalian API:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pengembalian: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userPengembalian()
    {
        try {
            $userId = Auth::id(); // Mengambil ID user yang sedang login dari token
            $detailPengembalian = DetailPengembalian::with([
                    'barang',
                    'peminjaman',
                    'detailPeminjaman' // Memuat relasi agar data lengkap
                ])
                ->where('users_id', $userId) // Filter berdasarkan user ID
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info("Total pengajuan pengembalian for user $userId found:", ['count' => $detailPengembalian->count()]);

            return response()->json([
                'success' => true,
                'message' => 'Daftar pengajuan pengembalian berhasil diambil.',
                'data' => $detailPengembalian
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in userPengembalian (API):', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar pengajuan pengembalian: ' . $e->getMessage()
            ], 500);
        }
    }
}