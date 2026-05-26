<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $star_recognition_id
 * @property int $star_schema_indicator_id
 * @property int $star_schema_indicator_option_id
 * @property float $response_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read StarRecognition $recognition
 * @property-read StarSchemaIndicator $indicator
 * @property-read StarSchemaIndicatorOption $option
 */
class StarRecognitionResponse extends Model
{
    protected $fillable = [
        'star_recognition_id',
        'star_schema_indicator_id',
        'star_schema_indicator_option_id',
        'response_score',
    ];

    protected $casts = [
        'response_score' => 'decimal:2',
    ];

    public function recognition(): BelongsTo
    {
        return $this->belongsTo(StarRecognition::class, 'star_recognition_id');
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(StarSchemaIndicator::class, 'star_schema_indicator_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(StarSchemaIndicatorOption::class, 'star_schema_indicator_option_id');
    }
}
