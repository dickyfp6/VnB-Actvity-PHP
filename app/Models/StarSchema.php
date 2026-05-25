<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StarSchema extends Model
{
    protected $fillable = ['name', 'description', 'version', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function indicators(): HasMany
    {
        return $this->hasMany(StarSchemaIndicator::class, 'star_schema_id');
    }
}