<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PenumpangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'penumpang_id'   => Str::uuid(),
            'pemesanan_id'   => Pemesanan::factory(),
            'nama'           => $this->faker->name(),
            'no_hp'          => $this->faker->phoneNumber(),
            'jenis_kelamin'  => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'nomor_kursi'    => $this->faker->numberBetween(1, 30),
            'alamat_jemput'  => $this->faker->address(),
            'alamat_antar'   => $this->faker->address(),
        ];
    }
}
