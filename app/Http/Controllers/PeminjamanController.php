<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman; // Penting: Pastikan ini diimpor jika Anda mengubah status detail
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    // Ini adalah endpoint untuk halaman admin/dashboard, yang biasanya mengembalikan view, bukan JSON
    // Saya akan tambahkan respons JSON jika diakses sebagai API (misal: /api/peminjaman)
    public function index()
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'detail.barang'])
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Total peminjaman found:', ['count' => $peminjaman->count()]);
            
            foreach ($peminjaman as $index => $pinjam) {
                Log::info("Peminjaman #{$index}:", [
                    'id' => $pinjam->id_peminjaman,
                    'users_id' => $pinjam->users_id,
                    'id_detail_peminjaman' => $pinjam->id_detail_peminjaman ?? 'null',
                    'user_exists' => $pinjam->user ? true : false,
                    'user_username' => $pinjam->user?->username ?? 'null',
                    'detail_exists' => $pinjam->detail ? true : false,
                    'status' => $pinjam->status,
                ]);

                if ($pinjam->detail) {
                    Log::info("Detail info:", [
                        'id_detail' => $pinjam->detail->id_detail_peminjaman ?? 'null',
                        'barang_exists' => $pinjam->detail->barang ? true : false,
                        'nama_barang' => $pinjam->detail->barang?->nama_barang ?? 'null',
                        'keperluan' => $pinjam->detail->keperluan ?? 'null',
                        'jumlah' => $pinjam->detail->jumlah ?? 'null',
                    ]);
                }
            }

            return view('peminjaman.index', compact('peminjaman'));

        } catch (\Exception $e) {
            Log::error('Error in peminjaman index:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([], 500);
        }
    }

    public function show($id)
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'detail.barang'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail peminjaman berhasil diambil.',
                'data' => $peminjaman
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in peminjaman show:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail peminjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'users_id' => 'required|exists:users,users_id',
                'id_barang' => 'required|exists:barang,id_barang',
                'jumlah' => 'required|integer|min:1',
                'keperluan' => 'required|string',
                'class' => 'required|string',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Buat Detail Peminjaman terlebih dahulu
            $detailPeminjaman = DetailPeminjaman::create([
                'users_id' => $request->users_id,
                'id_barang' => $request->id_barang,
                'jumlah' => $request->jumlah,
                'keperluan' => $request->keperluan,
                'class' => $request->class,
                'status' => 'pending', // Status awal detail
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
            ]);

            Log::info('Detail peminjaman created:', $detailPeminjaman->toArray());

            // Buat Peminjaman utama
            $peminjaman = Peminjaman::create([
                'users_id' => $request->users_id,
                'id_detail_peminjaman' => $detailPeminjaman->id_detail_peminjaman,
                'status' => 'pending', // Status awal peminjaman utama
            ]);

            Log::info('Peminjaman created successfully:', [
                'id' => $peminjaman->id_peminjaman,
                'users_id' => $peminjaman->users_id,
                'detail_id' => $peminjaman->id_detail_peminjaman,
                'status' => $peminjaman->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan',
                'data' => $peminjaman->load(['user', 'detail.barang']) // Load relasi untuk respons
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating peminjaman:', [
                'message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan peminjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            Log::info("Attempting to approve peminjaman with ID: $id");
            
            $peminjaman = Peminjaman::findOrFail($id);
            $peminjaman->status = 'dipinjam'; // Ubah status Peminjaman utama
            $peminjaman->save(); // Simpan perubahan Peminjaman utama

            // Juga update status di DetailPeminjaman terkait
            if ($peminjaman->detail) { // Cek apakah ada detail terkait
                $detailPeminjaman = $peminjaman->detail; // Ambil detail
                $detailPeminjaman->status = 'dipinjam'; // Ubah status DetailPeminjaman
                $detailPeminjaman->save(); // Simpan perubahan DetailPeminjaman

                Log::info("Detail Peminjaman updated successfully:", [
                    'detail_id' => $detailPeminjaman->id_detail_peminjaman,
                    'new_detail_status' => $detailPeminjaman->status
                ]);
            } else {
                Log::warning("No detail found for peminjaman ID: $id during approve.");
            }

            Log::info("Peminjaman approved successfully:", [
                'id' => $id,
                'new_status_peminjaman' => $peminjaman->status,
                'new_status_detail_peminjaman' => $peminjaman->detail?->status // Tampilkan status detail juga
            ]);

            // Mengembalikan respons JSON
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disetujui!',
                'data' => $peminjaman->load(['user', 'detail.barang']) // Kembalikan data lengkap yang sudah diperbarui
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Peminjaman not found for approval:", ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error approving peminjaman:", [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            Log::info("Attempting to reject peminjaman with ID: $id");

            $peminjaman = Peminjaman::findOrFail($id);
            $peminjaman->status = 'ditolak'; // Ubah status Peminjaman utama
            $peminjaman->save(); // Simpan perubahan Peminjaman utama

            // Juga update status di DetailPeminjaman terkait
            if ($peminjaman->detail) {
                $detailPeminjaman = $peminjaman->detail;
                $detailPeminjaman->status = 'ditolak'; // Ubah status DetailPeminjaman
                $detailPeminjaman->save(); // Simpan perubahan DetailPeminjaman

                Log::info("Detail Peminjaman rejected successfully:", [
                    'detail_id' => $detailPeminjaman->id_detail_peminjaman,
                    'new_detail_status' => $detailPeminjaman->status
                ]);
            } else {
                Log::warning("No detail found for peminjaman ID: $id during reject.");
            }

            Log::info("Peminjaman rejected successfully:", [
                'id' => $id,
                'new_status_peminjaman' => $peminjaman->status,
                'new_status_detail_peminjaman' => $peminjaman->detail?->status
            ]);

            // Mengembalikan respons JSON
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil ditolak!',
                'data' => $peminjaman->load(['user', 'detail.barang']) // Kembalikan data lengkap yang sudah diperbarui
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Peminjaman not found for rejection:", ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error rejecting peminjaman:", [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak peminjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userPeminjaman()
    {
        try {
            $userId = Auth::id(); // Mengambil ID user yang sedang login
            $peminjaman = Peminjaman::with(['user', 'detail.barang'])
                ->where('users_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Total peminjaman for user $userId found:', ['count' => $peminjaman->count()]);
            
            return response()->json($peminjaman, 200); // Mengembalikan JSON langsung

        } catch (\Exception $e) {
            Log::error('Error in user peminjaman:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil peminjaman pengguna: ' . $e->getMessage()
            ], 500);
        }
    }
}