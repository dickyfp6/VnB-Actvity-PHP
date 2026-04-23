<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHistory extends Model
{
    protected $table = 'employee_histories';

    protected $fillable = [
        'employee_id',
        'employee_number',
        'name',
        'date_joined',
        'induction_date',
        'company',
        'division_id',
        'department_id',
        'position_id',
        'placement',
        'level',
        'employee_status',
        'email',
        'whatsapp',
        'manager_functional_id',
        'manager_operational_id',
        'career_stage',
        'employment_state',
        'status_changed_at',
        'status_change_reason',
        'status_changed_by',
        'notes',
        'status',
        'moved_to_history_at',
    ];

    protected $casts = [
        'date_joined' => 'date',
        'induction_date' => 'date',
        'status_changed_at' => 'datetime',
        'moved_to_history_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

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
}
