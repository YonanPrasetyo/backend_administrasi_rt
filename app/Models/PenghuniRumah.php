<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenghuniRumah extends Model
{
    protected $table = 'penghuni_rumah';
    protected $primaryKey = 'id_penghuni_rumah';
    protected $guarded = ['id_penghuni_rumah'];
}
