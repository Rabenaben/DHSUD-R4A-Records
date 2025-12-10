<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    use HasFactory;

    protected $table = 'borrowers';

    protected $fillable = [
        'borrower_name',
        'remarks',
        'status_id',
        'docket_number',
        'file_name',
        'file_location',
        'date_borrowed',
        'date_returned',
    ];

    public function borrowerStatus()
    {
        return $this->belongsTo(BorrowerStatus::class, 'status_id');
    }
}
