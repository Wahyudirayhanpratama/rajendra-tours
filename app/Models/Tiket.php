<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tiket extends Model
{
    use HasFactory;

    protected $primaryKey = 'tiket_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tiket_id','pemesanan_id', 'no_tiket','nomor_kursi', 'nama_penumpang'];

    // Model User
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class, 'user_id', 'user_id');
    }

    // Model Pemesanan
    public function penumpangs()
    {
        return $this->hasMany(Penumpang::class, 'pemesanan_id', 'pemesanan_id');
    }
    // public function jadwal()
    // {
    //     return $this->belongsTo(Jadwal::class, 'jadwal_id', 'jadwal_id');
    // }
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id', 'pemesanan_id');
    }
    public function jadwal()
    {
        return $this->hasOneThrough(
            Jadwal::class,
            Pemesanan::class,
            'pemesanan_id',  // foreign key di Pemesanan
            'jadwal_id',     // foreign key di Jadwal
            'pemesanan_id',  // local key di Tiket
            'jadwal_id'      // local key di Pemesanan
        );
    }
}
