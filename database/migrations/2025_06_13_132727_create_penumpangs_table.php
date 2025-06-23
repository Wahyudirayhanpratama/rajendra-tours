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
        Schema::create('penumpangs', function (Blueprint $table) {
            $table->uuid('penumpang_id')->primary();
            $table->uuid('pemesanan_id');
            $table->string('nama');
            $table->string('no_hp');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nomor_kursi');
            $table->text('alamat_jemput')->nullable();
            $table->text('alamat_antar')->nullable();
            $table->timestamps();
            $table->foreign('pemesanan_id')->references('pemesanan_id')->on('pemesanans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penumpangs');
    }
};
