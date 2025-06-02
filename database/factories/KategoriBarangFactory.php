<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriBarangFactory extends Factory
{
    public function definition()
    {
        return [
            'nama_kategori' => $this->faker->word(),
            'deskripsi' => $this->faker->sentence(),
        ];
    }
}
