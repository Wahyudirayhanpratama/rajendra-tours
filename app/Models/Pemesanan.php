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
        'kode_booking'
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
}
