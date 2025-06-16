<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_category', 'kode_barang', 'nama_barang', 'stock','stock_dipinjam', 'brand', 'status', 'kondisi_barang', 'gambar_barang'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'id_category');
    }
}
