<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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