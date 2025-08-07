<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RiwayatPemesananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'riwayatPemesanan_id' => Str::uuid(),
            'pemesanan_id' => Pemesanan::factory(), // relasi ke tabel pemesanan
            'status' => $this->faker->randomElement(['menunggu', 'dibayar', 'dibatalkan', 'selesai']),
            'tanggal_riwayat' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
