<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPengembalianFactory extends Factory
{
    public function definition()
    {
        return [
            'id_pengembalian' => \App\Models\Pengembalian::factory(),
            'id_peminjaman' => \App\Models\Peminjaman::factory(),
            'id_barang' => \App\Models\Barang::factory(),
        ];
    }
}
