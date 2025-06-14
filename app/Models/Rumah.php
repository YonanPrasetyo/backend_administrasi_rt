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
}
