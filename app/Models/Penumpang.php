<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penumpang extends Model
{
    use HasFactory;

    protected $primaryKey = 'penumpang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'penumpang_id',
        'pemesanan_id',
        'nama',
        'no_hp',
        'jenis_kelamin',
        'nomor_kursi',
        'alamat_jemput',
        'alamat_antar'
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id', 'pemesanan_id');
    }
    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id', 'mobil_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
