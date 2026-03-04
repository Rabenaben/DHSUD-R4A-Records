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
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the requested docs as an array.
     */
    public function getRequestedDocsArrayAttribute(): array
    {
        return $this->requested_docs ? json_decode($this->requested_docs, true) : [];
    }
}
