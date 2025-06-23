<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->uuid('pembayaran_id')->primary();
            $table->uuid('pemesanan_id');
            // Midtrans-related fields
            $table->string('order_id')->unique(); // ID yang dikirim ke Midtrans
            $table->string('transaction_id')->nullable(); // ID dari Midtrans
            $table->string('payment_type'); // contoh: bank_transfer, qris
            $table->string('transaction_status'); // contoh: pending, settlement, expire
            $table->string('fraud_status')->nullable(); // untuk kartu kredit
            $table->integer('gross_amount'); // total pembayaran
            $table->json('va_numbers')->nullable(); // jika transfer bank (berisi bank & va_number)

            // Status internal sistem (opsional)
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');

            $table->timestamp('waktu_bayar')->nullable(); // waktu transaksi selesai
            $table->timestamps();

            $table->foreign('pemesanan_id')->references('pemesanan_id')->on('pemesanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
