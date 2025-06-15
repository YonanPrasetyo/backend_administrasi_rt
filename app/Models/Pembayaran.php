<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $guarded = ['id_pembayaran'];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class, 'id_penghuni');
    }

    public function rumah()
    {
        return $this->belongsTo(Rumah::class, 'id_rumah');
    }
}
