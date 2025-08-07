<?php

namespace Database\Factories;

use App\Models\Mobil;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JadwalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jadwal_id'     => (string) Str::uuid(),
            'mobil_id'      => Mobil::factory(),
            'kota_asal'     => $this->faker->city(),
            'kota_tujuan'   => $this->faker->city(),
            'tanggal'       => $this->faker->dateTimeBetween('+1 days', '+1 month')->format('Y-m-d'),
            'jam_berangkat' => $this->faker->time('H:i'),
            'harga'         => $this->faker->numberBetween(100000, 500000),
        ];
    }
}
