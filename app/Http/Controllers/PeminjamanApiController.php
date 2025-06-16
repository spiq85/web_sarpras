<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanApiController extends Controller
{
    /**
     * Mengambil riwayat peminjaman untuk user yang sedang login.
     */
    public function userLoans()
    {
        try {
            $userId = Auth::id();

            $peminjaman = Peminjaman::with(['detail', 'detail.barang'])
                ->where('users_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data peminjaman berhasil diambil.',
                'data' => $peminjaman
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data peminjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    // Anda bisa menambahkan method lain terkait API peminjaman di sini nanti
}