<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriBarang;

class KategoriBarangSeeder extends Seeder
{
    public function run()
    {
        KategoriBarang::create([
            'nama_kategori' => 'Elektronik',
            'deskripsi' => 'Barang-barang elektronik seperti laptop, proyektor, dll.'
        ]);

        KategoriBarang::create([
            'nama_kategori' => 'Alat Tulis',
            'deskripsi' => 'Barang ATK seperti pena, buku, dll.'
        ]);
        KategoriBarang::create([
            'nama_kategori' => 'Peralatan Olahraga',
            'deskripsi' => 'Barang-barang olahraga seperti bola, raket, dll.'
        ]);
    }
}
    