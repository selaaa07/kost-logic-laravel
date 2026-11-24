<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kamar',
        'fasilitas',
        'harga',
        'status',
        'foto',
    ];

    public function penghuni()
    {
        return $this->hasOne(Penghuni::class);
    }

    public function komplains()
    {
        return $this->hasMany(Komplain::class);
    }
}