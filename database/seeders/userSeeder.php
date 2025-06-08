<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'username' => 'admin',
            'name' => 'admin',
            'password' => Hash::make('password'), // password = "password"
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            
        ]);

        User::create([
            'username' => 'user',
            'name' => 'user',
            'password' => Hash::make('password'), // password = "password"
            'role' => 'user',
            'email' => 'user@gmail.com',
        ]);

        User::create([
            'username' => 'petugas',
            'password' => Hash::make('password'), // password = "password"
            'role' => 'user',
            'email' => 'petugas@gmail.com',
            ]);
    }
}
