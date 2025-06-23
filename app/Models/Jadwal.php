<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jadwal extends Model
{
    use HasFactory;

    protected $primaryKey = 'jadwal_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['jadwal_id', 'mobil_id', 'kota_asal', 'kota_tujuan', 'tanggal', 'jam_berangkat', 'harga'];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id', 'mobil_id');
    }
    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'jadwal_id', 'jadwal_id');
    }
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class, 'jadwal_id', 'jadwal_id');
    }
}
