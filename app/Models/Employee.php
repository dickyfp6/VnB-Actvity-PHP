<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\EmployeeObserver;

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
 * @property string|null $career_stage
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
 * @property int $manager_functional_id REQUIRED - Every employee MUST have a functional manager
 * @property int|null $manager_operational_id OPTIONAL - Can be null (operational manager is optional)
 * @property string $status Employee active status (Aktif or Inactive)
 */
#[ObservedBy([EmployeeObserver::class])]
class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_number', 'name', 'date_joined', 'induction_date', 'company',
        'division_id', 'department_id', 'position_id', 'placement', 'level', 'career_stage',
        'employee_status', 'email', 'whatsapp', 'manager_functional_id',
        'manager_operational_id', 'vnb_period_start', 'vnb_period_end',
        'vnb_status', 'employment_state', 'status_changed_at', 'status_change_reason', 'status_changed_by', 'notes', 'status'
    ];

    protected $casts = [
        'date_joined' => 'date',
        'induction_date' => 'date',
        'vnb_period_start' => 'date',
        'vnb_period_end' => 'date',
        'status_changed_at' => 'datetime',
    ];

    /**
     * MANAGER ASSIGNMENT RULES (VnB System):
     * 
     * 1. manager_functional_id: REQUIRED (NOT NULL)
     *    - Every employee MUST have exactly one functional manager
     *    - Cannot be null
     *    - Usually the employee's direct line manager
     * 
     * 2. manager_operational_id: OPTIONAL (CAN BE NULL)
     *    - Operational manager can exist but is not required
     *    - Can be null (employee may not have operational manager)
     *    - When both managers exist, they MUST be different people
     * 
     * Usage in VnB:
     * - Functional manager: Handles planning approval, skill assessment
     * - Operational manager: Provides additional oversight/mentoring when applicable
     */

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

    public function masterLevel(): BelongsTo
    {
        return $this->belongsTo(MasterLevel::class, 'level', 'name');
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

    public function histories(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class);
    }

    /**
     * CAREER STAGE MAPPING (VnB System)
     * Automatically determined by position/golongan and stored in database
     * 
     * - Manage Self (Non-Staff) = Non-Staff
     * - Manage Self (Staff) = Staff, Supervisor
     * - Manage Others = Manager, Tim Leader
     * - Manage Managers = General Manager
     * - Manage Function = Kepala Divisi, Direktur, Director, Head of Division
     */
    public function getCareerStage(): ?string
    {
        // Return database value if set (highest priority)
        if ($this->career_stage) {
            return $this->career_stage;
        }

        // Fallback: compute from position for safety
        if (!$this->position) {
            return null;
        }

        return $this->mapPositionToCareerStage($this->position->name);
    }

    /**
     * Get career stage code for framework lookup (underscore format)
     * Used internally for VnbFrameworkItem queries
     */
    public function getCareerStageCode(): string
    {
        $stage = $this->getCareerStage();

        $stageCodeMap = [
            'Manage Self (Non-Staff)' => 'manage_self_non_staff',
            'Manage Self (Staff)' => 'manage_self_staff',
            'Manage Others' => 'manage_others',
            'Manage Managers' => 'manage_managers',
            'Manage Function' => 'manage_function',
        ];

        return $stageCodeMap[$stage] ?? 'manage_self_non_staff';
    }

    /**
     * Determine career stage from position name (for queries/comparisons)
     */
    private function mapPositionToCareerStage(string $position): ?string
    {
        $positionLower = strtolower(trim($position));

        // Non-Staff (Intern, Harian, Contract worker, etc)
        if (str_contains($positionLower, 'non-staff') || 
            str_contains($positionLower, 'non staff') ||
            str_contains($positionLower, 'intern') || 
            str_contains($positionLower, 'harian') || 
            str_contains($positionLower, 'mingguan') ||
            str_contains($positionLower, 'contract')) {
            return 'Manage Self (Non-Staff)';
        }

        // Staff & Supervisor
        if (str_contains($positionLower, 'staff') || str_contains($positionLower, 'supervisor')) {
            return 'Manage Self (Staff)';
        }

        // Manager & Tim Leader (but not "general manager")
        if (!str_contains($positionLower, 'general') && 
            (str_contains($positionLower, 'manager') || 
             str_contains($positionLower, 'tim leader') || 
             str_contains($positionLower, 'lead'))) {
            return 'Manage Others';
        }

        // General Manager (or Managing Director)
        if (str_contains($positionLower, 'general manager') || 
            str_contains($positionLower, 'managing director')) {
            return 'Manage Managers';
        }

        // Kepala Divisi, Director, Head, etc.
        if (str_contains($positionLower, 'kepala divisi') || 
            str_contains($positionLower, 'director') || 
            str_contains($positionLower, 'direktur') || 
            str_contains($positionLower, 'head of') || 
            str_contains($positionLower, 'head division')) {
            return 'Manage Function';
        }

        return null;
    }
}
