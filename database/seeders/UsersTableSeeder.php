<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'user_id' => uniqid(),
            'nama' => 'admin',
            'no_hp' => '081234567890',
            'alamat' => 'Alamat Admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'user_id' => uniqid(),
            'nama' => 'pemilik',
            'no_hp' => '081234567891',
            'alamat' => 'Alamat Pemilik',
            'password' => Hash::make('pemilik123'),
            'role' => 'pemilik'
        ]);
    }
}
