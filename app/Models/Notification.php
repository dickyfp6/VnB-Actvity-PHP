<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'type', 'employee_id', 'recipient_id', 'title', 'message', 'channel',
        'status', 'error_message', 'metadata', 'sent_at', 'delivered_at'
    ];

    protected $casts = [
        'metadata' => 'json',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recipient_id');
    }
}
