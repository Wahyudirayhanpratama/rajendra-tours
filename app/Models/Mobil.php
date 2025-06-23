<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mobil extends Model
{
    use HasFactory;

    protected $primaryKey = 'mobil_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['mobil_id','nama_mobil', 'nomor_polisi', 'kapasitas'];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'mobil_id');
    }
}
