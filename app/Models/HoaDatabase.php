<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDatabase extends Model
{
    protected $table = 'hoa_database'; // your table name
    protected $primaryKey = 'id';      // primary key

    protected $fillable = ['docket_no', 'hoa_name', 'location', 'province_id', 'municipality_id', 'status', 'quantity', 'remarks', 'files'];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }
}
