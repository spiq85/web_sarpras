<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    public function definition()
    {
        return [
            'id_category' => \App\Models\KategoriBarang::factory(), // Relasi otomatis
            'kode_barang' => strtoupper($this->faker->unique()->bothify('BRG###')),
            'nama_barang' => $this->faker->word(),
            'stock' => $this->faker->numberBetween(1, 100),
            'brand' => $this->faker->company(),
            'status' => $this->faker->randomElement(['dipinjam', 'kembali', 'dll']),
            'kondisi_barang' => $this->faker->randomElement(['baik', 'rusak', 'dll']),
        ];
    }
}
