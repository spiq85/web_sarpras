<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailPengembalian;

class DetailPengembalianSeeder extends Seeder
{
    public function run()
    {
        DetailPengembalian::create([
            'users_id' => 1, 
            'id_detail_pengembalian' => 1,
            'id_detail_peminjaman' => 1,
            'id_peminjaman' => 1,
            'id_barang' => 1,
            'jumlah' => 1,
            'tanggal_pengembalian' => now(),
            'kondisi' => 'Baik',
            'keterangan' => 'Tidak ada kerusakan',
        ]);

        DetailPengembalian::create([
            'users_id' => 2,
            'id_detail_pengembalian' => 2,
            'id_detail_peminjaman' => 2,
            'id_peminjaman' => 2,
            'id_barang' => 2,
            'jumlah' => 1,
            'tanggal_pengembalian' => now(),
            'kondisi' => 'Baik',
            'keterangan' => 'Tidak ada kerusakan',
        ]);

        DetailPengembalian::create([
            'users_id' => 3,
            'id_detail_pengembalian' => 3,
            'id_detail_peminjaman' => 3,
            'id_peminjaman' => 3,
            'id_barang' => 3,
            'jumlah' => 1,
            'tanggal_pengembalian' => now(),
            'kondisi' => 'Baik',
            'keterangan' => 'Tidak ada kerusakan',
        ]);
    }
}
