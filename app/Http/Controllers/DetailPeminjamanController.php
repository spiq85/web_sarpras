<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPeminjaman;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DetailPeminjamanController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('Received request to store detail peminjaman:', $request->all());

        $validator = Validator::make($request->all(), [
            'users_id' => 'required|integer',
            'id_barang' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'class' => 'required|string',
            'status' => 'required|in:pending,dipinjam,kembali,rejected',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $detailPeminjaman = DetailPeminjaman::create([
            'users_id' => $request->users_id,
            'id_barang' => $request->id_barang,
            'jumlah' => $request->jumlah,
            'keperluan' => $request->keperluan,
            'class' => $request->class,
            'status' => $request->status,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
        ]);

        \Log::info('Detail peminjaman created:', $detailPeminjaman->toArray());

        return response()->json(['message' => 'Detail peminjaman berhasil ditambahkan', 'data' => $detailPeminjaman], 201);
    }

    public function show($id)
    {
        $detail = DetailPeminjaman::with(['barang', 'peminjaman'])->findOrFail($id);
        return response()->json($detail);
    }
}