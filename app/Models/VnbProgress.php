<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VnbProgress extends Model
{
    protected $table = 'vnb_progress';

    protected $fillable = [
        'employee_id', 'plan_item_id', 'behavior_progress', 'progress_percentage', 'notes', 'last_updated_at'
    ];

    protected $casts = [
        'behavior_progress' => 'json',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function planItem(): BelongsTo
    {
        return $this->belongsTo(VnbPlanItem::class, 'plan_item_id');
    }
}
