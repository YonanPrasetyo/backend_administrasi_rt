<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenghuniRumah extends Model
{
    protected $table = 'penghuni_rumah';
    protected $primaryKey = 'id_penghuni_rumah';
    protected $guarded = ['id_penghuni_rumah'];

    public function rumah()
    {
        return $this->belongsTo(Rumah::class, 'id_rumah', 'id_rumah');
    }

    public function penghuni()
    {
        return $this->hasOne(Penghuni::class, 'id_penghuni', 'id_penghuni');
    }
}
