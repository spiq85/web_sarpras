<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('peminjaman')->insert([
            [
                'users_id' => 1, // Pastikan user dengan id 1 ada
                'id_detail_peminjaman' => 1,
                'status' => 'pending',
                'keperluan' => 'Praktikum Fisika',
                'soft_delete' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'users_id' => 2,
                'id_detail_peminjaman' => 2,
                'status' => 'dipinjam',
                'keperluan' => 'Presentasi Kelas',
                'soft_delete' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'users_id' => 3,
                'id_detail_peminjaman' => 3,
                'status' => 'rejected',
                'keperluan' => 'Kegiatan Ekstrakurikuler',
                'soft_delete' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
