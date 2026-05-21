<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $plan_id
 * @property string $activity_title
 * @property string|null $description
 * @property string|null $integration_1
 * @property string|null $integration_2
 * @property \Illuminate\Support\Carbon|null $implementation_date
 * @property string|null $deliverables
 * @property array|null $behavior_metrics
 * @property string $status
 * @property int $completion_percentage
 * @property string|null $activity_description
 * @property \Illuminate\Support\Carbon|null $activity_date
 * @property array|null $activity_rows
 * @property string $submission_status
 * @property array|null $manager_review_snapshot
 * @property string|null $revision_notes
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property int|null $approved_functional_by
 * @property \Illuminate\Support\Carbon|null $approved_functional_at
 * @property int|null $approved_operational_by
 * @property \Illuminate\Support\Carbon|null $approved_operational_at
 */
class VnbPlanItem extends Model
{
    protected $table = 'vnb_plan_items';

    protected $fillable = [
        'plan_id', 'framework_item_id', 'activity_title', 'description', 'integration_1', 'integration_2', 'implementation_date',
        'deliverables', 'behavior_metrics', 'status', 'completion_percentage',
        'activity_description', 'activity_date', 'activity_rows', 'submission_status', 'revision_notes',
        'manager_review_snapshot',
        'submitted_at', 'due_date', 'approved_functional_by', 'approved_functional_at',
        'approved_operational_by', 'approved_operational_at'
    ];

    protected $casts = [
        'implementation_date' => 'date',
        'behavior_metrics' => 'json',
        'submitted_at' => 'datetime',
        'due_date' => 'date',
        'activity_rows' => 'array',
        'manager_review_snapshot' => 'array',
        'approved_functional_at' => 'datetime',
        'approved_operational_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(VnbPlan::class);
    }

    public function frameworkItem(): BelongsTo
    {
        return $this->belongsTo(VnbFrameworkItem::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(VnbEvidence::class, 'plan_item_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(VnbProgress::class, 'plan_item_id');
    }
}
