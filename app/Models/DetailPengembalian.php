<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengembalian extends Model
{
    use HasFactory;

    protected $table = 'detail_pengembalian';
    protected $primaryKey = 'id_detail_pengembalian';

    protected $fillable = [
        'id_detail_peminjaman',
        'status',
        'tanggal_kembali',
        'catatan',
    ];

    public function detailPeminjaman()
    {
        return $this->belongsTo(DetailPeminjaman::class, 'id_detail_peminjaman', 'id_detail_peminjaman');
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}