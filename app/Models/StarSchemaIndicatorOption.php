<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $star_schema_indicator_id
 * @property string $label
 * @property float $score
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read StarSchemaIndicator $indicator
 */
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