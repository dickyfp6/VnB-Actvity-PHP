<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VnbEvidence extends Model
{
    protected $table = 'vnb_evidences';

    protected $fillable = [
        'plan_item_id', 'uploaded_by', 'file_name', 'file_path', 'file_type',
        'file_size', 's3_url', 'description', 'status', 'verification_notes'
    ];

    public function planItem(): BelongsTo
    {
        return $this->belongsTo(VnbPlanItem::class, 'plan_item_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }
}
