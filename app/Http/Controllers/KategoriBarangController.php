<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class KategoriBarangController extends Controller
{
    public function index()
    {
        return view('kategori.index', [
            'kategori' => KategoriBarang::all()
        ]);
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = KategoriBarang::create($request->all());
        return view('kategori.index', [
            'kategori' => KategoriBarang::all()
        ])->with('success', 'Data berhasil ditambahkan!');
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
        $kategori->update($request->all());
        return redirect()->route('kategori.index')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {
        KategoriBarang::destroy($id);
        return redirect()->route('kategori.index')->with('success', 'Data berhasil dihapus!');
    }
}
