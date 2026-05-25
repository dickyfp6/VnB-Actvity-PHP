<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StarSchemaIndicatorOption extends Model
{
    protected $fillable = ['star_schema_indicator_id', 'label', 'score', 'sort_order'];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(StarSchemaIndicator::class, 'star_schema_indicator_id');
    }
}