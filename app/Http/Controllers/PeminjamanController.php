<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman; 
use App\Models\Barang; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 
use App\Models\User; 
use App\Notifications\NewPeminjamanNotification; 

class PeminjamanController extends Controller
{
    /**
     * Tampilkan daftar peminjaman untuk tampilan web admin.
     * Mendukung pencarian berdasarkan nama_peminjam atau username.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'detail.barang']); // Selalu eager load relasi

            // Filter berdasarkan nama_peminjam atau username jika parameter 'search' ada
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $peminjaman->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', '%' . $search . '%')
                          ->orWhere('name', 'like', '%' . $search . '%'); // Cari juga berdasarkan kolom 'name'
                });
                Log::info("Web Search Peminjaman by user (username/name): '$search'");
            }

            $peminjaman = $peminjaman->orderBy('created_at', 'desc')->get(); // Ambil data yang sudah difilter

            // Pass parameter pencarian yang sedang aktif ke view
            $currentSearch = $request->input('search', '');

            return view('peminjaman.index', compact('peminjaman', 'currentSearch'));

        } catch (\Exception $e) {
            Log::error('Error in peminjaman index (Web):', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Untuk web, kita bisa redirect back atau tampilkan view error
            return redirect()->back()->with('error', 'Gagal memuat daftar peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail peminjaman berdasarkan ID (Untuk API, misalnya dipanggil oleh Flutter).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
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
            Log::error('Error in peminjaman show (API):', [
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

    /**
     * Simpan peminjaman baru ke database (Untuk API, dipanggil dari Flutter).
     * Juga mengirim notifikasi ke admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'users_id' => 'required|exists:users,users_id',
                'id_barang' => 'required|exists:barang,id_barang',
                'jumlah' => 'required|integer|min:1',
                'keperluan' => 'required|string',
                'class' => 'required|string', // Pastikan ini sesuai dengan kolom di DetailPeminjaman
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            ]);

            if ($validator->fails()) {
                Log::error('Peminjaman Store Validation Failed:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // --- Validasi Ketersediaan Stok Sebelum Membuat Peminjaman ---
            $barang = Barang::find($request->id_barang);
            if (!$barang) {
                return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan.'], 404);
            }
            if ($barang->stock < $request->jumlah) {
                return response()->json(['success' => false, 'message' => 'Stok barang tidak mencukupi. Stok tersedia: ' . $barang->stock], 400);
            }
            // --- Akhir Validasi Stok ---

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

            // === KIRIM NOTIFIKASI KE ADMIN ===
            $admins = User::where('role', 'admin')->get(); // Ambil semua user dengan role 'admin'
            foreach ($admins as $admin) {
                // Load relasi user dan detail.barang untuk notifikasi
                $admin->notify(new NewPeminjamanNotification($peminjaman->load(['user', 'detail.barang'])));
            }
            Log::info('New Peminjaman Notification sent to admins.');
            // ===============================

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan',
                'data' => $peminjaman->load(['user', 'detail.barang']) // Load relasi untuk respons Flutter
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating peminjaman:', [
                'message' => $e->getMessage(),
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan peminjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyetujui peminjaman (Untuk API atau Web, tergantung caller).
     * Mengubah status peminjaman utama dan detail peminjaman terkait.
     * Stok akan diatur oleh Observer.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function approve($id, Request $request)
    {
        try {
            Log::info("Attempting to approve peminjaman with ID: $id");

            $peminjaman = Peminjaman::with('detail.barang')->findOrFail($id);

            // Validasi status peminjaman utama
            if ($peminjaman->status !== 'pending') {
                $message = 'Peminjaman ini tidak dalam status "pending" dan tidak bisa disetujui.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                } else {
                    return redirect()->back()->with('error', $message);
                }
            }

            // Validasi ketersediaan stok sebelum menyetujui
            if (!$peminjaman->detail || !$peminjaman->detail->barang) {
                $message = 'Detail peminjaman atau barang terkait tidak ditemukan.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 404);
                } else {
                    return redirect()->back()->with('error', $message);
                }
            }

            $barang = $peminjaman->detail->barang;
            $jumlahDipinjam = $peminjaman->detail->jumlah;

            if ($barang->stock < $jumlahDipinjam) {
                $message = 'Stok barang tidak mencukupi untuk peminjaman ini. Stok tersedia: ' . $barang->stock;
                Log::warning("Stok tidak cukup saat menyetujui: Barang {$barang->id_barang}, dibutuhkan: {$jumlahDipinjam}, tersedia: {$barang->stock}");
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                } else {
                    return redirect()->back()->with('error', $message);
                }
            }

            // Ubah status Peminjaman utama
            $peminjaman->status = 'dipinjam';
            $peminjaman->save();

            // Ubah status DetailPeminjaman terkait. Ini akan memicu Observer.
            $detailPeminjaman = $peminjaman->detail;
            $detailPeminjaman->status = 'dipinjam';
            $detailPeminjaman->save(); // Observer DetailPeminjaman akan mengurangi 'stock' dan menambah 'stock_dipinjam' di sini

            Log::info("Peminjaman approved successfully:", [
                'id' => $id,
                'new_status_peminjaman' => $peminjaman->status,
                'new_status_detail_peminjaman' => $peminjaman->detail?->status
            ]);

            // Opsional: Perbarui status 'tersedia'/'dipinjam' di Barang jika semua stok dipinjam
            // Ini bisa juga dilakukan di observer jika logikanya lebih kompleks
            $barang->refresh(); // Ambil data barang terbaru setelah observer berjalan
            if ($barang->stock === 0 && $barang->stock_dipinjam > 0 && $barang->status !== 'dipinjam') {
                $barang->status = 'dipinjam';
                $barang->save();
                Log::info("Barang ID {$barang->id_barang} status changed to 'dipinjam' (all stock borrowed).");
            }


            if ($request->expectsJson()) { // Jika dipanggil dari API (Flutter)
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman berhasil disetujui!',
                    'data' => $peminjaman->load(['user', 'detail.barang']) // Kembalikan data lengkap
                ], 200);
            } else { // Jika dipanggil dari web admin
                return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil disetujui!');
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Peminjaman not found for approval:", ['id' => $id]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Peminjaman tidak ditemukan.'], 404);
            } else {
                return redirect()->back()->with('error', 'Peminjaman tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error("Error approving peminjaman:", [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()], 500);
            } else {
                return redirect()->back()->with('error', 'Gagal menyetujui peminjaman!');
            }
        }
    }

    /**
     * Menolak peminjaman (Untuk API atau Web, tergantung caller).
     * Mengubah status peminjaman utama dan detail peminjaman terkait.
     * Stok tidak terpengaruh jika sebelumnya dalam status 'pending'.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function reject($id, Request $request)
    {
        try {
            Log::info("Attempting to reject peminjaman with ID: $id");

            $peminjaman = Peminjaman::with('detail.barang')->findOrFail($id);

            // Hanya izinkan penolakan jika statusnya 'pending' atau 'dipinjam' (jika ingin bisa menolak yang sudah dipinjam)
            // Saya asumsikan penolakan hanya dari status 'pending' atau 'dipinjam' (kalau ada masalah di tengah jalan)
            if ($peminjaman->status === 'ditolak' || $peminjaman->status === 'dikembalikan') {
                $message = 'Peminjaman ini sudah dalam status "ditolak" atau "dikembalikan" dan tidak bisa ditolak lagi.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                } else {
                    return redirect()->back()->with('error', $message);
                }
            }

            $originalPeminjamanStatus = $peminjaman->status; // Simpan status asli untuk kondisi stok
            
            $peminjaman->status = 'ditolak'; // Ubah status Peminjaman utama
            $peminjaman->save(); // Simpan perubahan Peminjaman utama

            // Juga update status di DetailPeminjaman terkait. Ini akan memicu Observer.
            if ($peminjaman->detail) {
                $detailPeminjaman = $peminjaman->detail;
                $originalDetailStatus = $detailPeminjaman->status; // Simpan status asli detail

                $detailPeminjaman->status = 'ditolak'; // Ubah status DetailPeminjaman
                $detailPeminjaman->save(); // Observer DetailPeminjaman akan terpicu

                Log::info("Detail Peminjaman rejected successfully:", [
                    'detail_id' => $detailPeminjaman->id_detail_peminjaman,
                    'new_detail_status' => $detailPeminjaman->status
                ]);

                // Logika opsional: Jika peminjaman ditolak dari status 'dipinjam', kembalikan stok
                // Observer sudah menangani ini ketika status DetailPeminjaman berubah dari 'dipinjam' ke 'dikembalikan'
                // atau 'ditolak'. Pastikan Observer Anda menangani transisi ini dengan benar.
                // Untuk kasus 'ditolak' dari 'pending', tidak ada perubahan stok.
                if ($originalDetailStatus === 'dipinjam' && $detailPeminjaman->status === 'ditolak') {
                    $barang = $detailPeminjaman->barang;
                    if ($barang) {
                        $barang->stock += $detailPeminjaman->jumlah;
                        $barang->stock_dipinjam -= $detailPeminjaman->jumlah;
                        $barang->stock_dipinjam = max(0, $barang->stock_dipinjam);
                        $barang->save();
                        Log::info("Stok dikembalikan karena penolakan dari status 'dipinjam': Barang {$barang->id_barang}");

                        // Perbarui status barang keseluruhan jika sudah tidak ada yang dipinjam
                        $barang->refresh(); // Ambil data barang terbaru
                        if ($barang->stock_dipinjam === 0 && $barang->status !== 'tersedia') {
                            $barang->status = 'tersedia';
                            $barang->save();
                            Log::info("Barang ID {$barang->id_barang} status changed to 'tersedia' (no more items borrowed after rejection).");
                        }
                    }
                }
            } else {
                Log::warning("No detail found for peminjaman ID: $id during reject.");
            }

            Log::info("Peminjaman rejected successfully:", [
                'id' => $id,
                'new_status_peminjaman' => $peminjaman->status,
                'new_status_detail_peminjaman' => $peminjaman->detail?->status
            ]);

            if ($request->expectsJson()) { // Jika dipanggil dari API (Flutter)
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman berhasil ditolak!',
                    'data' => $peminjaan->load(['user', 'detail.barang']) // Kembalikan data lengkap
                ], 200);
            } else { // Jika dipanggil dari web admin
                return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditolak!');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Peminjaman not found for rejection:", ['id' => $id]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Peminjaman tidak ditemukan.'], 404);
            } else {
                return redirect()->back()->with('error', 'Peminjaman tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error("Error rejecting peminjaman:", [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menolak peminjaman: ' . $e->getMessage()], 500);
            } else {
                return redirect()->back()->with('error', 'Gagal menolak peminjaman!');
            }
        }
    }

    /**
     * Menandai peminjaman sebagai dikembalikan (Untuk API atau Web).
     * Mengubah status peminjaman utama dan detail peminjaman terkait.
     * Stok akan diatur oleh Observer.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function returnItem($id, Request $request)
    {
        try {
            Log::info("Attempting to return peminjaman with ID: $id");

            $peminjaman = Peminjaman::with('detail.barang')->findOrFail($id);

            // Hanya izinkan pengembalian jika statusnya 'dipinjam'
            if ($peminjaman->status !== 'dipinjam') {
                $message = 'Peminjaman ini tidak dalam status "dipinjam" dan tidak bisa dikembalikan.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                } else {
                    return redirect()->back()->with('error', $message);
                }
            }

            $peminjaman->status = 'dikembalikan'; // Ubah status Peminjaman utama
            $peminjaman->save(); // Simpan perubahan Peminjaman utama

            if ($peminjaman->detail) {
                $detailPeminjaman = $peminjaman->detail;
                $detailPeminjaman->status = 'dikembalikan'; // Ubah status DetailPeminjaman
                $detailPeminjaman->save(); // Observer DetailPeminjaman akan menambah 'stock' dan mengurangi 'stock_dipinjam' di sini

                Log::info("Detail Peminjaman updated to 'dikembalikan':", [
                    'detail_id' => $detailPeminjaman->id_detail_peminjaman,
                    'new_detail_status' => $detailPeminjaman->status
                ]);

                // Opsional: Perbarui status 'tersedia'/'dipinjam' di Barang jika sudah tidak ada yang dipinjam
                // Ini bisa juga dilakukan di observer jika logikanya lebih kompleks
                $barang = $peminjaman->detail->barang;
                if ($barang) {
                    $barang->refresh(); // Ambil data barang terbaru setelah observer berjalan
                    if ($barang->stock_dipinjam === 0 && $barang->status !== 'tersedia') {
                        $barang->status = 'tersedia';
                        $barang->save();
                        Log::info("Barang ID {$barang->id_barang} status changed to 'tersedia' (no more items borrowed).");
                    }
                }
            } else {
                Log::warning("No detail found for peminjaman ID: $id during return.");
            }

            Log::info("Peminjaman returned successfully:", [
                'id' => $id,
                'new_status_peminjaman' => $peminjaman->status,
                'new_status_detail_peminjaman' => $peminjaman->detail?->status
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman berhasil dikembalikan!',
                    'data' => $peminjaman->load(['user', 'detail.barang'])
                ], 200);
            } else {
                return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dikembalikan!');
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Peminjaman not found for return:", ['id' => $id]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Peminjaman tidak ditemukan.'], 404);
            } else {
                return redirect()->back()->with('error', 'Peminjaman tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error("Error returning peminjaman:", [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengembalikan peminjaman: ' . $e->getMessage()], 500);
            } else {
                return redirect()->back()->with('error', 'Gagal mengembalikan peminjaman!');
            }
        }
    }

    /**
     * Tampilkan daftar peminjaman pengguna tertentu (Untuk API, dipanggil dari Flutter).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userPeminjaman()
    {
        try {
            $userId = Auth::id(); // Mengambil ID user yang sedang login
            $peminjaman = Peminjaman::with(['user', 'detail.barang'])
                ->where('users_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Total peminjaman for user $userId found:', ['count' => $peminjaman->count()]);
            
            // Perhatikan: endpoint ini mengembalikan JSON langsung (tanpa 'success'/'data' wrapper)
            // Ini konsisten dengan implementasi awal fetchUserLoans di Flutter.
            return response()->json($peminjaman, 200); 

        } catch (\Exception $e) {
            Log::error('Error in user peminjaman (API):', [
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