<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    protected $primaryKey = 'province_id';

    public function municipalities()
    {
        return $this->hasMany(Municipality::class, 'province_id', 'province_id');
    }

    public function hoas()
    {
        return $this->hasMany(HoaDatabase::class, 'province_id', 'province_id');
    }
}
