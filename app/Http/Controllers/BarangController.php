<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\KategoriBarang;

class BarangController extends Controller
{
    public function apiIndex()
    {
        $barang = Barang::all();
        return response()->json($barang);
    }

    public function index()
    {
        $barang = Barang::with('kategori')->get();
        $kategori = KategoriBarang::all();
        return view('barang.index', compact('barang', 'kategori'));
    }

    public function create()
    {
        $kategori = KategoriBarang::all();
        return view('barang.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_category' => 'required|exists:kategori_barang,id_category',
            'kode_barang' => 'required|string|unique:barang,kode_barang',
            'nama_barang' => 'required|string',
            'stock' => 'required|integer',
            'brand' => 'nullable|string',
            'status' => 'nullable|in:tersedia,dipinjam',
            'kondisi_barang' => 'nullable|in:baik,rusak,dll',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:204',
        ]);

        $data = $request->only([
            'id_category', 'kode_barang', 'nama_barang', 'stock',
            'brand', 'status', 'kondisi_barang'
        ]);

        // Proses upload gambar jika ada
        if ($request->hasFile('gambar_barang')) {
            $file = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/gambar_barang', $filename);
            $data['gambar_barang'] = $filename;
        }

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function show($id)
    {
        return response()->json(Barang::with('kategori')->findOrFail($id));
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategori = KategoriBarang::all();
        return view('barang.edit', compact('barang', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_category' => 'required|exists:kategori_barang,id_category',
            'kode_barang' => 'required|string|unique:barang,kode_barang,' . $id . ',id_barang',
            'nama_barang' => 'required|string',
            'stock' => 'required|integer',
            'brand' => 'nullable|string',
            'status' => 'nullable|in:tersedia,dipinjam',
            'kondisi_barang' => 'nullable|in:baik,rusak,dll',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $barang = Barang::findOrFail($id);

        $barang->id_category = $request->id_category;
        $barang->kode_barang = $request->kode_barang;
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

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        // Hapus gambar dari storage jika ada
        if ($barang->gambar_barang && Storage::exists('public/gambar_barang/' . $barang->gambar_barang)) {
            Storage::delete('public/gambar_barang/' . $barang->gambar_barang);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data berhasil dihapus!');
    }
}