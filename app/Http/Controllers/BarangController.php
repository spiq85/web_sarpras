<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\KategoriBarang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Pastikan ini diimpor untuk operasi file

class BarangController extends Controller
{
    /**
     * Tampilkan daftar barang untuk API (digunakan oleh Flutter).
     * Mendukung pencarian berdasarkan nama_barang dan filter berdasarkan id_category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        try {
            $barang = Barang::with('kategori'); // Selalu eager load kategori

            // Filter berdasarkan nama_barang jika parameter 'search' ada
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $barang->where('nama_barang', 'like', '%' . $search . '%');
                Log::info("API Search by nama_barang: '$search'");
            }

            // Filter berdasarkan kategori jika parameter 'id_category' ada
            if ($request->has('id_category') && !empty($request->id_category)) {
                $categoryId = $request->id_category;
                $barang->where('id_category', $categoryId);
                Log::info("API Filter by id_category: $categoryId");
            }

            $barang = $barang->get(); // Ambil data yang sudah difilter

            return response()->json([
                'success' => true,
                'message' => 'Daftar barang berhasil diambil.',
                'data' => $barang
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in BarangController@apiIndex:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan daftar barang untuk tampilan web admin.
     * Mendukung pencarian dan filter, serta passing data kategori.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $barang = Barang::with('kategori'); // Selalu eager load kategori

        // Filter berdasarkan nama_barang jika parameter 'search' ada
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $barang->where('nama_barang', 'like', '%' . $search . '%');
            Log::info("Web Search by nama_barang: '$search'");
        }

        // Filter berdasarkan kategori jika parameter 'id_category' ada
        if ($request->has('id_category') && !empty($request->id_category)) {
            $categoryId = $request->id_category;
            $barang->where('id_category', $categoryId);
            Log::info("Web Filter by id_category: $categoryId");
        }

        $barang = $barang->get(); // Ambil data yang sudah difilter
        $kategori = KategoriBarang::all(); // Ambil semua kategori untuk dropdown filter

        // Pass parameter pencarian dan filter yang sedang aktif ke view
        $currentSearch = $request->input('search', '');
        $currentCategory = $request->input('id_category', '');

        return view('barang.index', compact('barang', 'kategori', 'currentSearch', 'currentCategory'));
    }

    /**
     * Tampilkan form untuk membuat barang baru (Web).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $kategori = KategoriBarang::all();
        return view('barang.create', compact('kategori'));
    }

    /**
     * Simpan barang baru ke database (Web).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_category' => 'required|exists:kategori_barang,id_category',
            'nama_barang' => 'required|string',
            'stock' => 'required|integer|min:1',
            'brand' => 'nullable|string',
            'status' => 'nullable|in:tersedia,dipinjam',
            'kondisi_barang' => 'nullable|in:baik,rusak,dll', // Pastikan ENUM ini ada di migrasi
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
        ]);

        // Ambil Kategori buat di Prefix jadi kode barang   
        $kategori = KategoriBarang::findOrFail($request->id_category);
        $prefix = $kategori->prefix_kode;

        // Validasi Prefix kode barang
        if(empty($prefix)) {
            return redirect()->back()->withInput()->with('error', 'Prefix kode kategori tidak boleh kosong!');
        }

        // Buat nomor urut terakhir untuk kategori
        $lastBarang = Barang::where('id_category', $request->id_category)
        ->where('kode_barang', 'like', $prefix . '%')
        ->orderBy('kode_barang', 'desc')
        ->first();


        $newNumber = 1;
        if($lastBarang) {
            // Ekstrak angka dari kode barang terakhir
            $lastNumber = (int) substr($lastBarang->kode_barang, strlen($prefix));
            $newNumber = $lastNumber + 1; 
        }

        // Format nomor urut 3 digit
        $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        $kode_barang = $prefix . $formattedNumber;



        $data = $request->only([
            'id_category', 'nama_barang', 'stock',
            'brand', 'status', 'kondisi_barang'
        ]);

        // menambah kode barang yang di generate
        $data['kode_barang'] = $kode_barang;

        // Proses upload gambar jika ada
        if ($request->hasFile('gambar_barang')) {
            $file = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/gambar_barang', $filename); // Disimpan di storage/app/public/gambar_barang
            $data['gambar_barang'] = $filename; // Simpan hanya nama file di database
        }

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail barang berdasarkan ID (API).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $barang = Barang::with('kategori')->findOrFail($id);
            return response()->json($barang); // Mengembalikan JSON langsung, bukan wrapper success/data
            // Jika Anda ingin wrapper, gunakan:
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Detail barang berhasil diambil.',
            //     'data' => $barang
            // ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error in BarangController@show (API):', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal mengambil detail barang.'], 500);
        }
    }

    /**
     * Tampilkan form edit barang (Web).
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategori = KategoriBarang::all();
        return view('barang.edit', compact('barang', 'kategori'));
    }

    /**
     * Perbarui data barang di database (Web).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'id_category' => 'required|exists:kategori_barang,id_category',
            // 'kode_barang' tidak perlu divalidasi unique jika kita tidak mengizinkan perubahan secara manual
            // Jika mau diizinkan manual, biarkan validasi unique dengan ignore id
            'kode_barang' => 'required|string|unique:barang,kode_barang,' . $id . ',id_barang',
            'nama_barang' => 'required|string',
            'stock' => 'required|integer|min:1',
            'brand' => 'nullable|string',
            'status' => 'nullable|in:tersedia,dipinjam',
            'kondisi_barang' => 'nullable|in:baik,rusak,dll',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Cek apakah id_category berubah
        if ($request->id_category != $barang->id_category) {
            // Jika kategori berubah, generate kode_barang baru
            $kategori = KategoriBarang::findOrFail($request->id_category);
            $prefix = $kategori->prefix_kode;

            if (empty($prefix)) {
                return redirect()->back()->withInput()->with('error', 'Prefix kode belum diatur untuk kategori yang dipilih. Mohon atur di pengaturan kategori.');
            }

            $lastBarang = Barang::where('id_category', $request->id_category)
                                ->where('kode_barang', 'like', $prefix . '%')
                                ->orderBy('kode_barang', 'desc')
                                ->first();

            $newNumber = 1;
            if ($lastBarang) {
                $lastNumber = (int) substr($lastBarang->kode_barang, strlen($prefix));
                $newNumber = $lastNumber + 1;
            }
            $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            $barang->kode_barang = $prefix . $formattedNumber;
        } else {
            // Jika kategori tidak berubah, pertahankan kode_barang yang sudah ada
            $barang->kode_barang = $request->kode_barang; // Biarkan user update jika diperlukan, atau hapus baris ini jika tidak boleh diubah manual
        }


        $barang->id_category = $request->id_category;
        $barang->nama_barang = $request->nama_barang;
        $barang->stock = $request->stock;
        $barang->brand = $request->brand;
        $barang->status = $request->status;
        $barang->kondisi_barang = $request->kondisi_barang;

        // Ganti gambar jika ada file baru
        if ($request->hasFile('gambar_barang')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar_barang && Storage::exists('public/gambar_barang/' . $barang->gambar_barang)) {
                Storage::delete('public/gambar_barang/' . $barang->gambar_barang);
            }

            $file = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/gambar_barang', $filename);
            $barang->gambar_barang = $filename;
        }

        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Hapus barang dari database (Web).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $barang = Barang::findOrFail($id);

            // Hapus gambar dari storage jika ada
            if ($barang->gambar_barang && Storage::exists('public/gambar_barang/' . $barang->gambar_barang)) {
                Storage::delete('public/gambar_barang/' . $barang->gambar_barang);
            }

            $barang->delete();

            return redirect()->route('barang.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting barang:', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus barang!');
        }
    }
}