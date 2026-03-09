<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'project_name',
        'docket_no',
        'location',
        'requested_by',
        'or_no',
        'amount',
        'requested_docs',
        'others_specify',
        'remarks',
        'certified_true_copy',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'requested_docs' => 'array',
        'certified_true_copy' => 'boolean',
    ];
}
