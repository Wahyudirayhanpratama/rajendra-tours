<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TiketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tiket_id'       => Str::uuid(),
            'pemesanan_id'   => Pemesanan::factory(),
            'no_tiket'       => 'TK-' . strtoupper(Str::random(8)),
            'nomor_kursi'    => $this->faker->numberBetween(1, 5),
            'nama_penumpang' => $this->faker->name(),
        ];
    }
}
