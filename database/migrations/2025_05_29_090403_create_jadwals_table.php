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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->uuid('jadwal_id')->primary();
            $table->uuid('mobil_id');
            $table->string('kota_asal');
            $table->string('kota_tujuan');
            $table->date('tanggal');
            $table->time('jam_berangkat');
            $table->decimal('harga', 10, 2);
            $table->timestamps();

            $table->foreign('mobil_id')->references('mobil_id')->on('mobils')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
