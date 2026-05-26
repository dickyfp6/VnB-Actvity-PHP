<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $star_schema_id
 * @property string|null $indicator_key
 * @property string $label
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read StarSchemaIndicatorOption[] $options
 */
class StarSchemaIndicator extends Model
{
    protected $fillable = ['star_schema_id', 'indicator_key', 'label', 'sort_order'];

    public function schema(): BelongsTo
    {
        return $this->belongsTo(StarSchema::class, 'star_schema_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(StarSchemaIndicatorOption::class, 'star_schema_indicator_id');
    }
}