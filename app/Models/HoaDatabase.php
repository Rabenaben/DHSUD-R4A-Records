<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDatabase extends Model
{
    protected $table = 'hoa_database'; // your table name
    protected $primaryKey = 'id';      // primary key

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }
}
