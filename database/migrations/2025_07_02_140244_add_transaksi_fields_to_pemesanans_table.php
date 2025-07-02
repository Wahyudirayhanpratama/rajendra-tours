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
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('kode_booking');
            $table->timestamp('transaction_time')->nullable()->after('transaction_id');
            $table->string('payment_type')->nullable()->after('transaction_time');
            $table->string('va_number')->nullable()->after('payment_type');
            $table->string('gross_amount')->nullable()->after('va_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'transaction_time',
                'payment_type',
                'va_number',
                'gross_amount',
            ]);
        });
    }
};
