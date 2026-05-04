<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Observers\EmployeeObserver;
use App\Models\Manager;

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
        'employee_status', 'email', 'whatsapp', 'manager_functional_id', 'manager_functional',
        'manager_operational_id', 'manager_operational', 'vnb_period_start', 'vnb_period_end',
        'vnb_status', 'notes', 'status'
    ];

    protected $casts = [
        'date_joined' => 'date',
        'induction_date' => 'date',
        'vnb_period_start' => 'date',
        'vnb_period_end' => 'date',
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
     * Career stage is now determined directly from the employee's level (from HRIS)
     * 
     * Level -> Career Stage Mapping:
     * - Non-Staff, Contract, Intern → Manage Self (Non-Staff)
     * - Staff, Supervisor → Manage Self (Staff dan Supervisor)
     * - Manager, Tim Leader → Manage Other (Manager)
     * - General Manager → Manage Manager (Direktur)
     * - Kepala Divisi, Director, Head of Division → Manage Function
     */
    public function getCareerStage(): ?string
    {
        // Return database value if set (highest priority)
        if ($this->career_stage) {
            return $this->career_stage;
        }

        // Get career stage from HRIS level (new approach - integrated with HRIS)
        if ($this->level) {
            $careerStage = $this->mapLevelToCareerStage(
                $this->level,
                $this->employee_status,
                $this->company,
                $this->position?->name
            );
            if ($careerStage) {
                return $careerStage;
            }
        }

        // Fallback: compute from position for safety when level is not available
        if ($this->position) {
            return $this->mapPositionToCareerStage(
                $this->position->name,
                $this->employee_status,
                $this->company,
                $this->level
            );
        }

        return null;
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
            'Manage Self (Staff dan Supervisor)' => 'manage_self_staff',
            'Manage Other (Manager)' => 'manage_others',
            'Manage Manager (Direktur)' => 'manage_managers',
            'Manage Function' => 'manage_function',
        ];

        return $stageCodeMap[$stage] ?? 'manage_self_non_staff';
    }

    /**
     * Map HRIS level directly to career stage (no master_levels dependency)
     * This is the primary mapping source when integrated with HRIS
     */
    private function mapLevelToCareerStage(
        string $level,
        ?string $employeeStatus = null,
        ?string $company = null,
        ?string $position = null
    ): ?string
    {
        if (!$level) {
            return null;
        }

        $levelLower = strtolower(trim($level));
        $isOutsource = $this->isOutsourceContext($employeeStatus, $company, $position, $level);

        // Non-Staff levels (Contract, Intern, etc)
        if (
            str_contains($levelLower, 'non-staff') ||
            str_contains($levelLower, 'non staff') ||
            str_contains($levelLower, 'contract') ||
            str_contains($levelLower, 'intern') ||
            (
                $isOutsource && (
                    str_contains($levelLower, 'harian') ||
                    str_contains($levelLower, 'mingguan') ||
                    str_contains($levelLower, 'borongan')
                )
            )
        ) {
            return 'Manage Self (Non-Staff)';
        }

        // Staff & Supervisor levels
        if (
            str_contains($levelLower, 'staff') ||
            str_contains($levelLower, 'supervisor')
        ) {
            return 'Manage Self (Staff dan Supervisor)';
        }

        // Manager levels
        if (
            str_contains($levelLower, 'manager') ||
            str_contains($levelLower, 'tim leader') ||
            str_contains($levelLower, 'lead')
        ) {
            return 'Manage Other (Manager)';
        }

        // General Manager / Direktur level (but not Kepala Divisi)
        if (
            (str_contains($levelLower, 'general manager') ||
             str_contains($levelLower, 'direktur')) &&
            !str_contains($levelLower, 'kepala')
        ) {
            return 'Manage Manager (Direktur)';
        }

        // Function/Division head levels
        if (
            str_contains($levelLower, 'kepala divisi') ||
            str_contains($levelLower, 'kepala') ||
            str_contains($levelLower, 'director') ||
            str_contains($levelLower, 'head of division')
        ) {
            return 'Manage Function';
        }

        return null;
    }

    /**
     * Determine career stage from position name (fallback for safety)
     */
    private function mapPositionToCareerStage(
        string $position,
        ?string $employeeStatus = null,
        ?string $company = null,
        ?string $level = null
    ): ?string
    {
        $positionLower = strtolower(trim($position));
        $isOutsource = $this->isOutsourceContext($employeeStatus, $company, $position, $level);

        // Non-Staff (Harian, Mingguan, Borongan) only for OS/outsource workers
        if (
            (
                $isOutsource && (
                    str_contains($positionLower, 'harian') ||
                    str_contains($positionLower, 'mingguan') ||
                    str_contains($positionLower, 'borongan')
                )
            ) ||
            str_contains($positionLower, 'non-staff') ||
            str_contains($positionLower, 'non staff') ||
            str_contains($positionLower, 'contract') ||
            str_contains($positionLower, 'intern')
        ) {
            return 'Manage Self (Non-Staff)';
        }

        // Staff & Supervisor
        if (str_contains($positionLower, 'staf') || str_contains($positionLower, 'staff') || str_contains($positionLower, 'supervisor')) {
            return 'Manage Self (Staff dan Supervisor)';
        }

        // Manager
        if (str_contains($positionLower, 'manager') || 
            str_contains($positionLower, 'tim leader') || 
            str_contains($positionLower, 'lead')) {
            return 'Manage Other (Manager)';
        }

        return null;
    }

    /**
     * Find appropriate functional manager based on employee hierarchy.
     * 
     * Hierarchy Rules:
     * - OS/Outsource employees → null (no manager assignment)
     * - Staff (department-specific) → Manager of same department
     * - Staff General → Manager of General department in same division (GM)
     * - Manager (department) → Manager of General department in same division (GM)
     * - General Manager/Director → null (top level, or same division GM)
     * 
     * @return Manager|null
     */
    public function findFunctionalManager(): ?Manager
    {
        // Skip if Outsource/OS
        if ($this->isOutsourceContext($this->employee_status, $this->company, $this->position?->name, $this->level)) {
            return null;
        }

        // Need division to proceed
        if (!$this->division_id) {
            return null;
        }

        $careerStage = $this->getCareerStage();

        // If employee is at top level (Manage Function, or Manage Managers)
        if (in_array($careerStage, ['Manage Function', 'Manage Manager (Direktur)'], true)) {
            // For Direktur/GM: return null or GM of same division (if needed for signing authority chain)
            // Currently: return null (top level)
            return null;
        }

        // If employee is Staff (Manage Self - Staff atau Non-Staff)
        if (in_array($careerStage, ['Manage Self (Staff dan Supervisor)', 'Manage Self (Non-Staff)'], true)) {
            // If has a department → find manager of same department
            if ($this->department_id) {
                $manager = Manager::query()
                    ->where('division_id', $this->division_id)
                    ->where('department_id', $this->department_id)
                    ->where('status', 'active')
                    ->first();

                if ($manager) {
                    return $manager;
                }
            }

            // If no department or no manager found → find GM (General department manager) of same division
            // General department manager is the next level up
            return $this->findGeneralManagerOfDivision();
        }

        // If employee is Manager (Manage Other - Manager)
        if ($careerStage === 'Manage Other (Manager)') {
            // Manager reports to GM (General department manager) of same division
            return $this->findGeneralManagerOfDivision();
        }

        return null;
    }

    /**
     * Find General Manager (direktur) of employee's division.
     * GM = Manager with department "General" in employee's division.
     */
    private function findGeneralManagerOfDivision(): ?Manager
    {
        if (!$this->division_id) {
            return null;
        }

        // Find the "General" department ID
        $generalDept = DB::table('master_departments')
            ->where('name', 'General')
            ->first();

        if (!$generalDept) {
            return null;
        }

        return Manager::query()
            ->where('division_id', $this->division_id)
            ->where('department_id', $generalDept->id)
            ->where('status', 'active')
            ->first();
    }

    private function isOutsourceContext(
        ?string $employeeStatus = null,
        ?string $company = null,
        ?string $position = null,
        ?string $level = null
    ): bool {
        $status = strtolower(trim((string) $employeeStatus));
        if (in_array($status, ['os', 'outsource', 'outsourcing'], true)) {
            return true;
        }

        $context = strtolower(trim(implode(' ', array_filter([
            (string) $employeeStatus,
            (string) $company,
            (string) $position,
            (string) $level,
        ]))));

        if ($context === '') {
            return false;
        }

        return str_contains($context, 'outsource')
            || str_contains($context, 'outsourcing')
            || preg_match('/\bos\b/i', $context) === 1;
    }
}

