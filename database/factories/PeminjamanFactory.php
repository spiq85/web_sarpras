<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    public function definition()
    {
        return [
            'users_id' => \App\Models\User::factory(),
            'tanggal_pinjam' => $this->faker->dateTimeBetween('-1 months', 'now'),
        ];
    }
}
