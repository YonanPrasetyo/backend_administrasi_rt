<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $table = 'penghuni';
    protected $primaryKey = 'id_penghuni';
    protected $guarded = ['id_penghuni'];

    public function getFotoKtpUrlAttribute()
    {
        if ($this->foto_ktp) {
            return asset('storage/ktp/' . $this->foto_ktp);
        }
        return null;
    }

    public function setFotoKtpAttribute($value)
    {
        if ($this->foto_ktp && $this->foto_ktp !== $value && \Storage::disk('public')->exists('ktp/' . $this->foto_ktp)) {
            \Storage::disk('public')->delete('ktp/' . $this->foto_ktp);
        }

        $this->attributes['foto_ktp'] = $value;
    }
}
