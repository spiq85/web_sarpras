<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';
    protected $fillable = [
        'users_id',
        'id_detail_peminjaman',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }

    public function detail()
    {
        return $this->belongsTo(DetailPeminjaman::class, 'id_detail_peminjaman', 'id_detail_peminjaman');
    }
}