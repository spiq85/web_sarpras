<?php

namespace App\Http\Controllers;

use App\Models\DetailPengembalian;
use App\Models\DetailPeminjaman; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Auth; 
use App\Models\User; 
use App\Notifications\NewPengembalianNotification; 
use Illuminate\Support\Facades\DB; // Import DB Facade for transactions

class DetailPengembalianApiController extends Controller
{

    public function store(Request $request)
    {
        // Mulai transaksi database untuk memastikan operasi atomik
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'id_detail_peminjaman' => 'required|exists:detail_peminjaman,id_detail_peminjaman',
                'tanggal_pengembalian' => 'required|date',
                'keterangan' => 'nullable|string',
                'kondisi' => 'nullable|string',
                'item_image' => 'nullable|string', // Base64 encoded string
                'status' => 'required|string|in:approve,not approve,pending', // Status awal dari user biasanya 'pending'
            ]);

            if ($validator->fails()) {
                Log::error('Detail Pengembalian API Store Validation Failed:', $validator->errors()->toArray());
                DB::rollBack(); // Rollback transaksi jika validasi gagal
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Load DetailPeminjaman beserta relasi Peminjaman-nya
            $detailPeminjaman = DetailPeminjaman::with('peminjaman')->find($request->id_detail_peminjaman);
            
            if (!$detailPeminjaman) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Detail Peminjaman tidak ditemukan.'], 404);
            }

            // Memastikan id_peminjaman di detailPeminjaman tidak null
            if (is_null($detailPeminjaman->peminjaman)) {
                Log::error('Detail Peminjaman does not have an associated Peminjaman record (relasi peminjaman null):', [
                    'id_detail_peminjaman' => $detailPeminjaman->id_detail_peminjaman
                ]);
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Detail Peminjaman terkait tidak memiliki ID Peminjaman utama. Pastikan data peminjaman sudah lengkap.'
                ], 400); 
            }

            $imagePath = null;
            if ($request->item_image) {
                try {
                    // Hapus prefix data:image/jpeg;base64,
                    $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->item_image));
                    $imageName = uniqid() . '.jpg'; // Anda bisa membuat nama file lebih unik jika perlu
                    // Simpan ke storage/app/public/pengembalian_images
                    Storage::disk('public')->put('pengembalian_images/' . $imageName, $decodedImage);
                    $imagePath = 'pengembalian_images/' . $imageName;
                    Log::info('Image uploaded for return request.', ['image_path' => $imagePath]);
                } catch (\Exception $e) {
                    Log::error('Error decoding or saving image:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
                    ], 400);
                }
            }

            // Buat entri DetailPengembalian baru
            $detailPengembalian = DetailPengembalian::create([
                'users_id' => $detailPeminjaman->peminjaman->users_id, // Mengambil user ID dari relasi Peminjaman
                'id_detail_peminjaman' => $request->id_detail_peminjaman,
                'id_peminjaman' => $detailPeminjaman->peminjaman->id_peminjaman, 
                'id_barang' => $detailPeminjaman->id_barang,
                'jumlah' => $detailPeminjaman->jumlah,
                'kondisi' => $request->kondisi,
                'status' => $request->status, // Dari request, yang diharapkan 'pending'
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
                'keterangan' => $request->keterangan,
                'item_image' => $imagePath,
            ]);

            // Update status di DetailPeminjaman menjadi 'pending_pengembalian'
            // Hanya jika status detail peminjaman sebelumnya adalah 'dipinjam'
            if ($detailPeminjaman->status === 'dipinjam') {
                // Gunakan status yang lebih deskriptif untuk pengembalian yang sedang menunggu persetujuan
                $detailPeminjaman->status = 'pending_pengembalian'; 
                $detailPeminjaman->save();
                Log::info('Detail Peminjaman status updated to pending_pengembalian:', [
                    'id' => $detailPeminjaman->id_detail_peminjaman,
                    'status' => 'pending_pengembalian'
                ]);
            } else {
                Log::warning('Detail Peminjaman status was not "dipinjam" when attempting return update to "pending_pengembalian". Current status:', [
                    'id' => $detailPeminjaman->id_detail_peminjaman,
                    'current_status' => $detailPeminjaman->status
                ]);
                // Jika status bukan 'dipinjam', mungkin ada masalah data atau alur yang tidak terduga.
                // Anda bisa memilih untuk membatalkan transaksi atau mengabaikan update ini.
                // Untuk saat ini, kita akan lanjutkan tapi log warning.
            }

            // === KIRIM NOTIFIKASI KE ADMIN ===
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewPengembalianNotification($detailPengembalian));
            }
            Log::info('New Pengembalian Notification sent to admins.');
            // ===============================

            DB::commit(); // Commit transaksi jika semua operasi berhasil

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pengembalian berhasil disimpan.',
                'data' => $detailPengembalian->load(['barang', 'peminjaman', 'detailPeminjaman'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi kesalahan
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

    public function approve($id)
    {
        // Mulai transaksi database
        DB::beginTransaction();
        try {
            $detailPengembalian = DetailPengembalian::with('detailPeminjaman.barang')->findOrFail($id); // Load relasi yang dibutuhkan

            // Pastikan DetailPeminjaman terkait ada
            if (!$detailPengembalian->detailPeminjaman) {
                Log::warning("No associated DetailPeminjaman found for DetailPengembalian ID $id during approval. Cannot update related records.");
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Detail Peminjaman terkait tidak ditemukan untuk pengembalian ini.'
                ], 404);
            }

            // Update status di DetailPengembalian itu sendiri
            $detailPengembalian->status = 'approve'; 
            $detailPengembalian->save();

            // Update status di DetailPeminjaman terkait menjadi 'kembali'
            $detailPeminjaman = $detailPengembalian->detailPeminjaman;
            $detailPeminjaman->status = 'kembali'; // Pastikan 'kembali' ada di ENUM migrasi DetailPeminjaman
            $detailPeminjaman->save();
            Log::info("DetailPeminjaman status updated to 'kembali' after return approval.", ['id_detail_peminjaman' => $detailPeminjaman->id_detail_peminjaman]);

            // === Mengembalikan Stok Barang ===
            // Pastikan relasi barang ada
            if ($detailPeminjaman->barang) {
                $barang = $detailPeminjaman->barang;
                $jumlahDikembalikan = $detailPeminjaman->jumlah;

                // Kurangi stock_dipinjam dan tambahkan kembali ke stock
                $barang->stock_dipinjam = max(0, $barang->stock_dipinjam - $jumlahDikembalikan); // Pastikan tidak negatif
                $barang->stock += $jumlahDikembalikan;
                
                // Opsional: Perbarui status barang jika semua sudah kembali dan tidak ada yang dipinjam
                if ($barang->stock_dipinjam == 0) {
                    $barang->status = 'tersedia'; // Asumsi 'tersedia' jika tidak ada yang dipinjam
                }
                $barang->save();
                Log::info("Stock and stock_dipinjam updated for barang ID {$barang->id_barang}.", [
                    'stock_after_return' => $barang->stock,
                    'stock_dipinjam_after_return' => $barang->stock_dipinjam
                ]);
            } else {
                Log::warning("Barang related to DetailPeminjaman ID {$detailPeminjaman->id_detail_peminjaman} not found. Stock not updated.");
            }
            // ===============================

            DB::commit(); // Commit transaksi jika semua berhasil

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian telah disetujui!',
                'data' => $detailPengembalian->load(['barang', 'peminjaman', 'detailPeminjaman']) // Load ulang data untuk respons
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi kesalahan
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
        // Mulai transaksi database
        DB::beginTransaction();
        try {
            $detailPengembalian = DetailPengembalian::with('detailPeminjaman')->findOrFail($id);

            // Update status di DetailPengembalian
            $detailPengembalian->status = 'not approve'; 
            $detailPengembalian->save();

            // Jika ditolak, kembalikan status DetailPeminjaman ke 'dipinjam'
            if ($detailPengembalian->detailPeminjaman) {
                $detailPeminjaman = $detailPengembalian->detailPeminjaman;
                // Hanya revert jika statusnya memang 'pending_pengembalian'
                if ($detailPeminjaman->status === 'pending_pengembalian') {
                    $detailPeminjaman->status = 'dipinjam';
                    $detailPeminjaman->save();
                    Log::info("DetailPeminjaman status reverted to 'dipinjam' after return rejection.", ['id_detail_peminjaman' => $detailPeminjaman->id_detail_peminjaman]);
                } else {
                    Log::warning("DetailPeminjaman status was not 'pending_pengembalian' when attempting to revert after rejection. Current status:", [
                        'id_detail_peminjaman' => $detailPeminjaman->id_detail_peminjaman,
                        'current_status' => $detailPeminjaman->status
                    ]);
                }
            } else {
                 Log::warning("No associated DetailPeminjaman found for DetailPengembalian ID $id during rejection.");
            }

            DB::commit(); // Commit transaksi
            
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pengembalian telah ditolak!',
                'data' => $detailPengembalian->load(['barang', 'peminjaman', 'detailPeminjaman'])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi
            Log::error('Error rejecting Detail Pengembalian API:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pengajuan pengembalian: ' . $e->getMessage()
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
                'detailPeminjaman' 
            ])
                ->where('users_id', $userId) 
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