<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriBarangController extends Controller
{
    /**
     * Tampilkan daftar kategori barang untuk tampilan web admin.
     * Mendukung pencarian berdasarkan nama_kategori.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        try {
            $kategori = KategoriBarang::query(); // Mulai query

            // Filter berdasarkan nama_kategori jika parameter 'search' ada
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $kategori->where('nama_kategori', 'like', '%' . $search . '%');
                Log::info("Web Search Kategori by nama_kategori: '$search'");
            }

            $kategori = $kategori->get(); // Ambil data yang sudah difilter

            // Pass parameter pencarian yang sedang aktif ke view
            $currentSearch = $request->input('search', '');

            return view('kategori.index', compact('kategori', 'currentSearch'));

        } catch (\Exception $e) {
            Log::error('Error in KategoriBarangController@index (Web):', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Untuk web, kita bisa redirect back atau tampilkan view error
            return redirect()->back()->with('error', 'Gagal memuat daftar kategori: ' . $e->getMessage());
        }
    }

    // ... (metode create, store, show, edit, update, destroy lainnya tetap)
    // Pastikan metode store juga mengembalikan redirect()->route() seperti update/destroy
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string',
            'prefix_kode' => 'nullable|string|max:10|unique:kategori_barang,prefix_kode',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = KategoriBarang::create($request->all());
        return redirect()->route('kategori.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function show($id)
    {
        return view('kategori.show', [
            'kategori' => KategoriBarang::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        return view('kategori.edit', [
            'kategori' => KategoriBarang::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriBarang::findOrFail($id);

        // Abaaikan prefix kalo kategori masih sama
        $request->validate([
            'nama_kategori' => 'required|string',
            'prefix_kode' => 'nullable|string|max:10|unique:kategori_barang,prefix_kode,' . $kategori->id_category,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());
        return redirect()->route('kategori.index')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {
        KategoriBarang::destroy($id);
        return redirect()->route('kategori.index')->with('success', 'Data berhasil dihapus!');
    }
}
