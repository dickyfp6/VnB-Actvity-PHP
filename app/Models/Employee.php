<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\DB;
use App\Models\VnbFrameworkItem;
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
        // Guard: if master_positions table doesn't exist, return empty relation
        // This prevents queries during HRIS sync after master tables are dropped
        if (!Schema::hasTable('master_positions')) {
            // Return a relation that won't execute (no actual DB fetch happens)
            // The relation will return null when accessed
            return $this->belongsTo(MasterPosition::class)
                ->whereRaw('false'); // Impossible condition, prevents any query execution
        }

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
            // Only return stored career_stage if framework knows about it
            if ($this->frameworkHasStage($this->career_stage)) {
                // Convert code to label from database
                $label = $this->getCareerStageLabelFromDatabase($this->career_stage);
                return $label ?? $this->career_stage;
            }
            return null;
        }

        // Get career stage code from HRIS level
        if ($this->level) {
            $careerStageCode = $this->mapLevelToCareerStage(
                $this->level,
                $this->employee_status,
                $this->company,
                $this->getPositionNameSafe()
            );
            if ($careerStageCode) {
                // only use derived career stage when framework contains config
                if ($this->frameworkHasStage($careerStageCode)) {
                    // Convert code to label from database for display
                    $label = $this->getCareerStageLabelFromDatabase($careerStageCode);
                    return $label ?? $careerStageCode;
                }
            }
        }

        // Fallback: compute from position for safety when level is not available
        if ($this->position) {
            $posStageCode = $this->mapPositionToCareerStage(
                $this->position->name,
                $this->employee_status,
                $this->company,
                $this->level
            );
            if ($posStageCode && $this->frameworkHasStage($posStageCode)) {
                // Convert code to label from database for display
                $label = $this->getCareerStageLabelFromDatabase($posStageCode);
                return $label ?? $posStageCode;
            }
        }

        return null;
    }

    /**
     * Check if VnB framework has configuration for given career stage.
     */
    private function frameworkHasStage(?string $stage): bool
    {
        if (!$stage) return false;
        $normalized = trim((string) $stage);
        $normalizedCode = $this->normalizeCareerStageToCode($normalized);

        // Check stage configs table first
        if (Schema::hasTable('vnb_framework_stage_configs')) {
            $exists = DB::table('vnb_framework_stage_configs')
                ->where(function ($query) use ($normalized, $normalizedCode) {
                    $query->where('career_stage', $normalized)
                        ->orWhere('label', $normalized);

                    if ($normalizedCode && $normalizedCode !== $normalized) {
                        $query->orWhere('career_stage', $normalizedCode);
                    }
                })
                ->exists();
            if ($exists) return true;
        }

        // Fallback: check items table for matching career_stage
        if (Schema::hasTable('vnb_framework_items')) {
            $exists = VnbFrameworkItem::query()
                ->where(function ($query) use ($normalized, $normalizedCode) {
                    $query->where('career_stage', $normalized);
                    if ($normalizedCode && $normalizedCode !== $normalized) {
                        $query->orWhere('career_stage', $normalizedCode);
                    }
                })
                ->exists();
            if ($exists) return true;
        }

        return false;
    }

    /**
     * Get career stage code for framework lookup (underscore format)
     * Used internally for VnbFrameworkItem queries
     * Now resolves code directly from level/position mapping
     */
    public function getCareerStageCode(): string
    {
        // Try to get code from database column first (if stored as code)
        if ($this->career_stage) {
            $code = $this->normalizeCareerStageToCode($this->career_stage);
            if ($code) {
                return $code;
            }
        }

        // Get code from HRIS level mapping
        if ($this->level) {
            $code = $this->mapLevelToCareerStage(
                $this->level,
                $this->employee_status,
                $this->company,
                $this->getPositionNameSafe()
            );
            if ($code) {
                return $code;
            }
        }

        // Fallback to position mapping
        if ($this->position) {
            $code = $this->mapPositionToCareerStage(
                $this->position->name,
                $this->employee_status,
                $this->company,
                $this->level
            );
            if ($code) {
                return $code;
            }
        }

        return '';
    }

    private function normalizeCareerStageToCode(?string $stage): ?string
    {
        if (!$stage) {
            return null;
        }

        $normalized = trim($stage);

        $stageCodeMap = [
            'manage_self_non_staff' => 'manage_self_non_staff',
            'manage_self_staff' => 'manage_self_staff',
            'manage_others' => 'manage_others',
            'manage_manager' => 'manage_manager',
            'manage_managers' => 'manage_manager',
            'manage_function' => 'manage_function',
            'Manage Self (Non-Staff)' => 'manage_self_non_staff',
            'Manage Self (Staff dan Supervisor)' => 'manage_self_staff',
            'Manage Self (Staff)' => 'manage_self_staff',
            'Manage Other (Manager)' => 'manage_others',
            'Manage Others' => 'manage_others',
            'Manage Manager (Direktur)' => 'manage_manager',
            'Manage Manager' => 'manage_manager',
            'Manage Function' => 'manage_function',
        ];

        return $stageCodeMap[$normalized] ?? null;
    }

    /**
     * Get career stage label from database based on code.
     * This ensures labels stay in sync when user edits them in framework UI.
     */
    private function getCareerStageLabelFromDatabase(?string $code): ?string
    {
        if (!$code) {
            return null;
        }

        if (!Schema::hasTable('vnb_framework_stage_configs')) {
            return null;
        }

        return DB::table('vnb_framework_stage_configs')
            ->where('career_stage', $code)
            ->value('label');
    }

    /**
     * Map HRIS level directly to career stage code (underscore format)
     * Returns the career_stage CODE (not label) for lookup in framework configs
     * This ensures labels stay in sync with database when user edits them in UI
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

        // Look up master_levels by exact (case-insensitive) name
        $levelName = trim((string) $level);
        $levelRow = DB::table('master_levels')
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($levelName)])
            ->first();

        if (!$levelRow) {
            return null;
        }

        // Find stage mapping for this level
        if (!Schema::hasTable('vnb_framework_stage_level_maps')) {
            return null;
        }

        $stageMap = DB::table('vnb_framework_stage_level_maps')
            ->where('level_id', $levelRow->id)
            ->first();

        if (!$stageMap) {
            return null;
        }

        // Resolve career_stage code from stage_config
        $stageConfig = DB::table('vnb_framework_stage_configs')
            ->where('id', $stageMap->stage_config_id)
            ->first();

        return $stageConfig->career_stage ?? null;
    }

    /**
     * Determine career stage code from position name (fallback for safety)
     * Returns career_stage CODE for lookup, not label
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
            return 'manage_self_non_staff';
        }

        // Staff & Supervisor
        if (str_contains($positionLower, 'staf') || str_contains($positionLower, 'staff') || str_contains($positionLower, 'supervisor')) {
            return 'manage_self_staff';
        }

        // Manager
        if (str_contains($positionLower, 'manager') || 
            str_contains($positionLower, 'tim leader') || 
            str_contains($positionLower, 'lead')) {
            return 'manage_others';
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
     * Note: getCareerStage() now returns code (manage_self_staff) not label
     * 
     * @return Manager|null
     */
    public function findFunctionalManager(): ?Manager
    {
        // Skip if Outsource/OS
        if ($this->isOutsourceContext($this->employee_status, $this->company, $this->getPositionNameSafe(), $this->level)) {
            return null;
        }

        // Need division to proceed
        if (!$this->division_id) {
            return null;
        }

        $careerStageCode = $this->normalizeCareerStageToCode($this->getCareerStage());

        // If employee is at top level (Manage Function, or Manage Managers)
        if (in_array($careerStageCode, ['manage_function', 'manage_manager'], true)) {
            // For Direktur/GM: return null (top level)
            return null;
        }

        // If employee is Staff (Manage Self - Staff atau Non-Staff)
        if (in_array($careerStageCode, ['manage_self_staff', 'manage_self_non_staff'], true)) {
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

        // If employee is Manager (Manage Others)
        if ($careerStageCode === 'manage_others') {
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
        if (!Schema::hasTable('master_departments')) {
            return null;
        }

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

    /**
     * Safely fetch position name without triggering Eloquent relation when
     * master_positions table has been removed.
     */
    private function getPositionNameSafe(): ?string
    {
        if (!$this->position_id) {
            return null;
        }

        if (!Schema::hasTable('master_positions')) {
            return null;
        }

        return DB::table('master_positions')
            ->where('id', $this->position_id)
            ->whereNull('deleted_at')
            ->value('name');
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

