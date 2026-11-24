<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'penghuni_id',
        'bulan_tagihan',
        'nominal',
        'status',
        'metode_bayar',
        'nama_bank',
        'bukti_bayar',
        'tanggal_bayar',
    ];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }
}