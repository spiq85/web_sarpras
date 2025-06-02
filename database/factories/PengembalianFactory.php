<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PengembalianFactory extends Factory
{
    public function definition()
    {
        return [
            'tanggal_pengembalian' => $this->faker->dateTimeBetween('-1 months', 'now'),
            'catatan' => $this->faker->sentence(),
        ];
    }
}
