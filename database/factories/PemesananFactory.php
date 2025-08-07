<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Jadwal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PemesananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pemesanan_id'     => Str::uuid(),
            'user_id'          => User::factory(),
            'jadwal_id'        => Jadwal::factory(),
            'jumlah_penumpang' => $this->faker->numberBetween(1, 5),
            'total_harga'      => $this->faker->numberBetween(100000, 500000),
            'status'           => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'kode_booking'     => strtoupper(Str::random(8)),
            'transaction_id'   => 'TRX-' . strtoupper(Str::random(10)),
            'transaction_time' => now(),
            'payment_type'     => $this->faker->randomElement(['bank_transfer', 'gopay', 'qris']),
            'va_number'        => $this->faker->numerify('1234567890####'),
            'gross_amount'     => $this->faker->numberBetween(100000, 500000),
        ];
    }
}
