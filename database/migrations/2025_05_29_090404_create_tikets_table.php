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
        Schema::create('tikets', function (Blueprint $table) {
            $table->uuid('tiket_id')->primary();
            $table->uuid('pemesanan_id');
            $table->string('no_tiket');
            $table->string('nomor_kursi');
            $table->string('nama_penumpang');
            $table->timestamps();

            $table->foreign('pemesanan_id')->references('pemesanan_id')->on('pemesanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
