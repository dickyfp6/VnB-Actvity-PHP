<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $employee_id
 * @property int $phase_number
 * @property string $status
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property \Illuminate\Support\Carbon $cutoff_date
 */
class VnbPeriod extends Model
{
    protected $table = 'vnb_periods';

    protected $fillable = [
        'employee_id', 'phase_number', 'start_date', 'end_date',
        'cutoff_date', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'cutoff_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(VnbPlan::class, 'period_id');
    }
}
