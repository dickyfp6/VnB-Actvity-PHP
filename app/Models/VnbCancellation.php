<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VnbCancellation extends Model
{
    protected $fillable = ['employee_id', 'reason', 'notes', 'canceled_by', 'approved_by', 'approval_status', 'approval_notes', 'canceled_at'];

    protected $casts = [
        'canceled_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
