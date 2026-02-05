<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemDatabase extends Model
{
    protected $table = 'rem'; // your table name
    protected $primaryKey = 'id';      // primary key

    protected $fillable = [
        'docket_no',
        'project_name',
        'province',
        'municipality',
        'status',
        'quantity',
        'remarks',
        'files'
    ];
}
