<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VnbPlanRevisionDetail - Version Control detail untuk setiap activity yang direvisi
 * Menyimpan old_values dan new_values untuk setiap perubahan
 * 
 * @property int $id
 * @property int $vnb_plan_revision_id
 * @property int $vnb_plan_item_id (Activity)
 * @property array $old_values (title, description, implementation_date, deliverables, behavior_metrics)
 * @property array $new_values
 * @property int $changed_by (Employee yang melakukan perubahan)
 * @property string|null $created_at
 */
class VnbPlanRevisionDetail extends Model
{
    protected $table = 'vnb_plan_revision_details';

    protected $fillable = [
        'vnb_plan_revision_id',
        'vnb_plan_item_id',
        'old_values',
        'new_values',
        'changed_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function revision(): BelongsTo
    {
        return $this->belongsTo(VnbPlanRevision::class, 'vnb_plan_revision_id');
    }

    public function planItem(): BelongsTo
    {
        return $this->belongsTo(VnbPlanItem::class, 'vnb_plan_item_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by');
    }

    // Compare what changed
    public function getChangedFields(): array
    {
        $changed = [];
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changed[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }
        return $changed;
    }
}
