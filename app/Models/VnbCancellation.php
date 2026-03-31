<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VnbCancellation extends Model
{
    protected $fillable = ['employee_id', 'reason', 'cancelled_at'];
    protected $casts = ['cancelled_at' => 'datetime'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
