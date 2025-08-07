<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PembayaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pembayaran_id'       => Str::uuid(),
            'pemesanan_id'        => Pemesanan::factory(),
            'order_id'            => 'ORDER-' . strtoupper(Str::random(10)),
            'transaction_id'      => 'TRX-' . strtoupper(Str::random(10)),
            'payment_type'        => $this->faker->randomElement(['bank_transfer', 'gopay', 'qris']),
            'transaction_status'  => $this->faker->randomElement(['pending', 'settlement', 'cancel']),
            'fraud_status'        => $this->faker->randomElement(['accept', 'challenge', 'deny']),
            'gross_amount'        => $this->faker->numberBetween(100000, 500000),
            'va_numbers'          => $this->faker->numerify('1234567890####'),
            'status'              => $this->faker->randomElement(['berhasil', 'gagal', 'pending']),
            'waktu_bayar'         => now()->subMinutes(rand(1, 120)),
        ];
    }
}
