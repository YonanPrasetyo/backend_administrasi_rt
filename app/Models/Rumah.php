<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rumah extends Model
{
    protected $table = 'rumah';
    protected $primaryKey = 'id_rumah';
    protected $fillable = [
        'nomor_rumah',
        'status_rumah'
    ];

    public function penghuni_rumah()
    {
        return $this->hasMany(PenghuniRumah::class, 'id_rumah', 'id_rumah');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_rumah');
    }
}
