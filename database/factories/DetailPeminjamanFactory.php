<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPeminjamanFactory extends Factory
{
    public function definition()
    {
        return [
            'id_barang' => \App\Models\Barang::factory(),
            'id_peminjaman' => \App\Models\Peminjaman::factory(),
            'jumlah' => $this->faker->numberBetween(1, 5),
        ];
    }
}
