<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipalities';
    protected $primaryKey = 'municipality_id';

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function hoas()
    {
        return $this->hasMany(HoaDatabase::class, 'municipality_id', 'municipality_id');
    }
}