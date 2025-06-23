<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPemesanan extends Model
{
    use HasFactory;

    protected $primaryKey = 'riwayatPemesanan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['pemesanan_id', 'status', 'tanggal_riwayat'];
}
