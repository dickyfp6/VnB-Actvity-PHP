<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * VnbPlanRevision - Tracking revisi planning dari manager ke employee
 * @property int $id
 * @property int $vnb_plan_id
 * @property int $revision_number
 * @property int $requested_by (Manager - deprecated, use revision_type instead)
 * @property string $revision_notes
 * @property string $status (pending, approved, approved_with_revision, rejected)
 * @property string|null $revision_type (manager_revised, approved_as_is) - track if manager edited items
 * @property \Illuminate\Support\Carbon|null $requested_at
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $applied_at
 */
class VnbPlanRevision extends Model
{
    protected $table = 'vnb_plan_revisions';

    protected $fillable = [
        'vnb_plan_id',
        'plan_id',
        'version_number',
        'revision_number',
        'requested_by',
        'submitted_by',
        'revision_notes',
        'status',
        'revision_type',
        'decision',
        'review_notes',
        'requested_at',
        'submitted_at',
        'reviewed_at',
        'applied_at',
        'snapshot',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'submitted_at' => 'datetime',
        'applied_at' => 'datetime',
        'snapshot' => 'array',
    ];

    // Relationships
    public function plan(): BelongsTo
    {
        return $this->belongsTo(VnbPlan::class, 'vnb_plan_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function revisionDetails(): HasMany
    {
        return $this->hasMany(VnbPlanRevisionDetail::class, 'vnb_plan_revision_id');
    }

    // Get latest revision
    public function scopeLatest($query)
    {
        return $query->orderByDesc('revision_number')->first();
    }

    // Check if all plan items have been approved
    public function allItemsApproved(): bool
    {
        return !$this->plan->items()->where('status', '!=', 'approved')->exists();
    }

    // Get status label in Indonesian
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'approved_with_revision' => 'Disetujui dengan Revisi',
            'rejected' => 'Ditolak',
            'in_progress' => 'Sedang Dikerjakan',
            'submitted' => 'Sudah Dikirim',
            'applied' => 'Diterapkan'
        ];
        return $labels[$this->status] ?? $this->status;
    }
}
