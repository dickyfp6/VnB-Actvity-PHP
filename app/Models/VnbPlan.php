<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $employee_id
 * @property int $period_id
 * @property int $phase_number
 * @property string $title
 * @property string $status
 * @property string $description
 * @property string $planning_mode
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int $revision_count
 * @property string|null $revision_notes
 */
class VnbPlan extends Model
{
    protected $table = 'vnb_plans';

    protected $fillable = [
        'employee_id', 'period_id', 'phase_number', 'title', 'description',
        'planning_mode', 'status', 'submitted_at', 'approved_at', 'approved_by',
        'rejection_reason', 'discussion_notes', 'revision_count', 'revision_notes'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(VnbPeriod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VnbPlanItem::class, 'plan_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(VnbPlanRevision::class, 'vnb_plan_id');
    }

    // Get latest/current revision
    public function latestRevision(): ?VnbPlanRevision
    {
        return $this->revisions()->orderByDesc('revision_number')->first();
    }

    // Check if has pending revision
    public function hasPendingRevision(): bool
    {
        return (bool) $this->revisions()
            ->whereIn('status', ['pending', 'in_progress'])
            ->exists();
    }
}
