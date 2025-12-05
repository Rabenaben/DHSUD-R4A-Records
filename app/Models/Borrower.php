<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    use HasFactory;

    protected $table = 'borrowers';

    protected $fillable = [
        'id',
        'borrower_name',
        'remarks',
        'status_id',
    ];

    public function recordStatus()
    {
        return $this->belongsTo(RecordStatus::class, 'status_id');
    }
}
