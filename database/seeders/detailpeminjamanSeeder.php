<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DetailPeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('detail_peminjaman')->insert([
            [
                'users_id' => 1, // Pastikan user dengan id 1 ada
                'id_barang' => 1, // Pastikan barang dengan id 1 ada
                'jumlah' => 2,
                'keperluan' => 'Praktikum Fisika',
                'class' => 'XII ANIMASI 1',
                'status' => 'dipinjam',
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_kembali' => Carbon::now()->addDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'users_id' => 2, // Pastikan user dengan id 2 ada
                'id_barang' => 2,
                'jumlah' => 1,
                'keperluan' => 'Presentasi Kelas',
                'class' => 'XI RPL 3',
                'status' => 'kembali',
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_kembali' => Carbon::now()->addDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'users_id' => 3, // Pastikan user dengan id 3 ada
                'id_barang' => 3,
                'jumlah' => 1,
                'keperluan' => 'Kegiatan Ekstrakurikuler',
                'class' => 'XII TKJ 2',
                'status' => 'rejected',
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_kembali' => Carbon::now()->addDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
