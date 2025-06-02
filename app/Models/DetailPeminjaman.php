<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman';
    protected $primaryKey = 'id_detail_peminjaman';
    protected $fillable = [
        'users_id',
        'id_barang',
        'jumlah',
        'keperluan',
        'class',
        'status',
        'tanggal_pinjam',
        'tanggal_kembali',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function peminjaman()
    {
        return $this->hasOne(Peminjaman::class, 'id_detail_peminjaman', 'id_detail_peminjaman');
    }

    public function user(){
        return $this->belongsTo(User::class, 'users_id', 'users_id' );
    }
}