<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $employee_number
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $date_joined
 * @property \Illuminate\Support\Carbon|null $induction_date
 * @property string|null $company
 * @property string $email
 * @property string|null $whatsapp
 * @property string|null $placement
 * @property string|null $level
 * @property string $employee_status
 * @property \Illuminate\Support\Carbon|null $vnb_period_start
 * @property \Illuminate\Support\Carbon|null $vnb_period_end
 * @property string $vnb_status
 * @property string|null $notes
 * @property int|null $division_id
 * @property int|null $department_id
 * @property string $employment_state
 * @property \Illuminate\Support\Carbon|null $status_changed_at
 * @property string|null $status_change_reason
 * @property int|null $status_changed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $position_id
 * @property int|null $manager_functional_id
 * @property int|null $manager_operational_id
 */
class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_number', 'name', 'date_joined', 'induction_date', 'company',
        'division_id', 'department_id', 'position_id', 'placement', 'level',
        'employee_status', 'email', 'whatsapp', 'manager_functional_id',
        'manager_operational_id', 'vnb_period_start', 'vnb_period_end',
        'vnb_status', 'employment_state', 'status_changed_at', 'status_change_reason', 'status_changed_by', 'notes'
    ];

    protected $casts = [
        'date_joined' => 'date',
        'induction_date' => 'date',
        'vnb_period_start' => 'date',
        'vnb_period_end' => 'date',
        'status_changed_at' => 'datetime',
    ];

    // Relationships
    public function division(): BelongsTo
    {
        return $this->belongsTo(MasterDivision::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(MasterPosition::class);
    }

    public function managerFunctional(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'manager_functional_id');
    }

    public function managerOperational(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'manager_operational_id');
    }

    public function vnbPeriods(): HasMany
    {
        return $this->hasMany(VnbPeriod::class);
    }

    public function vnbPlans(): HasMany
    {
        return $this->hasMany(VnbPlan::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
