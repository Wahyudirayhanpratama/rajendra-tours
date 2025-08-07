<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MobilFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mobil_id'      => Str::uuid(),
            'nama_mobil'    => $this->faker->company . ' ' . $this->faker->word,
            'nomor_polisi'  => strtoupper($this->faker->bothify('B #### ??')), // Contoh: B 1234 CD
            'kapasitas'     => $this->faker->numberBetween(1, 5),
            'gambar'        => 'mobil_default.jpg', // atau bisa pakai $this->faker->imageUrl()
        ];
    }
}
