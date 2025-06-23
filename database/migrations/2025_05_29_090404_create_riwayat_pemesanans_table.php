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
        Schema::create('riwayat_pemesanans', function (Blueprint $table) {
            $table->uuid('riwayatPemesanan_id')->primary();
            $table->uuid('pemesanan_id');
            $table->string('status');
            $table->timestamp('tanggal_riwayat');
            $table->timestamps();

            $table->foreign('pemesanan_id')->references('pemesanan_id')->on('pemesanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pemesanans');
    }
};
