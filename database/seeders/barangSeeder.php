<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    public function run()
    {
        Barang::create([
            'id_category' => 1, // Elektronik
            'kode_barang' => 'ELK001',
            'nama_barang' => 'Laptop Dell',
            'stock' => 10,
            'brand' => 'Dell',
            'status' => 'tersedia',
            'kondisi_barang' => 'baik',
            'gambar_barang' => 'contoh laptop.jpg',
        ]);

        Barang::create([
            'id_category' => 2, // Alat Tulis
            'kode_barang' => 'ATK001',
            'nama_barang' => 'Buku Tulis',
            'stock' => 100,
            'brand' => 'Sinar Dunia',
            'status' => 'tersedia',
            'kondisi_barang' => 'baik',
        ]);
        Barang::create([
            'id_category' => 3, // Peralatan Olahraga
            'kode_barang' => 'OLR001',
            'nama_barang' => 'Bola Basket',
            'stock' => 20,
            'brand' => 'Molten',
            'status' => 'tersedia',
            'kondisi_barang' => 'baik',
        ]);
    }
}
