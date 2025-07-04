<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $primaryKey = 'pembayaran_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pembayaran_id',
        'pemesanan_id',
        'order_id', // ✅ TAMBAHKAN INI
        'transaction_id',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'gross_amount',
        'va_numbers',
        'status',
        'waktu_bayar'
    ];
}
