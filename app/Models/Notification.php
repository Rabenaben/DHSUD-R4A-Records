<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'read_at',
        'last_count',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'last_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnreadOverdue($query)
    {
        return $query->where('type', 'overdue_notices')->whereNull('read_at');
    }
}

