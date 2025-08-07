<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => (string) Str::uuid(),
            'nama'    => $this->faker->name(),
            'no_hp'   => $this->faker->phoneNumber(),
            'alamat'  => $this->faker->address(), // sesuai permintaan Anda
            'password'=> Hash::make('password'),
            'role'    => $this->faker->randomElement(['admin', 'pelanggan', 'pemilik']),
        ];
    }
}
