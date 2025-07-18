<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pemesanan extends Model
{
    use HasFactory;

    protected $primaryKey = 'pemesanan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pemesanan_id',
        'user_id',
        'jadwal_id',
        'jumlah_penumpang',
        'total_harga',
        'status',
        'kode_booking',
        'transaction_id',   // Ditambahkan dari migrasi
        'transaction_time', // Ditambahkan dari migrasi
        'payment_type',     // Ditambahkan dari migrasi
        'va_number',        // Ditambahkan dari migrasi
        'gross_amount',     // Ditambahkan dari migrasi
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function penumpangs()
    {
        return $this->hasMany(Penumpang::class, 'pemesanan_id', 'pemesanan_id');
    }
    public function penumpang()
    {
        return $this->hasOne(Penumpang::class, 'pemesanan_id');
    }
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'jadwal_id');
    }
    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id'); // asumsi foreign key-nya 'pelanggan_id'
    }
    public function tiket()
    {
        return $this->hasOne(Tiket::class, 'pemesanan_id', 'pemesanan_id');
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pemesanan_id');
    }
}
