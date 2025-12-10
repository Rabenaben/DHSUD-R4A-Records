<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerStatus extends Model
{
    use HasFactory;

    protected $table = 'borrower_status';

    protected $fillable = [
        'status_name',
    ];

    public function borrowers()
    {
        return $this->hasMany(Borrower::class, 'status_id');
    }
}
