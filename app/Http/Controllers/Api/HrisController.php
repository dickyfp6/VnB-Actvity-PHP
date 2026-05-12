<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeHistory;
use App\Models\Manager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HrisController extends Controller
{
    /**
     * List all HRIS data (full external API data)
     */
    public function index(): JsonResponse
    {
        // Authorize: PCX, Intercomm only
        $this->authorizeHrisAccess('Hanya PCX dan Intercomm yang bisa mengakses HRIS.');

        $comparison = $this->buildComparisonDataset();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar data HRIS berhasil dimuat',
            'data' => $comparison,
        ]);
    }

    /**
     * Get pending HRIS updates (changes not yet synced to employees table)
     */
    public function getPendingUpdates(): JsonResponse
    {
        // Authorize: PCX, Intercomm only
        $this->authorizeHrisAccess('Hanya PCX dan Intercomm yang bisa mengakses pending updates HRIS.');

        $comparison = $this->buildComparisonDataset();
        
        return response()->json([
            'success' => true,
            'message' => 'Pending HRIS updates',
            'data' => [
                'pending' => $comparison['pending'],
                'summary' => $comparison['summary'],
            ],
        ]);
    }

    /**
     * Sync specific HRIS record to employees table
     */
    public function syncToEmployee(Request $request, int $id): JsonResponse
    {
        // Authorize: PCX, Intercomm only
        $this->authorizeHrisAccess('Hanya PCX dan Intercomm yang bisa melakukan sinkronisasi HRIS.');

        $sourceRow = $this->getCombinedSourceRows()->firstWhere('id', $id);
        if (!$sourceRow) {
            return response()->json([
                'success' => false,
                'message' => 'Data sumber tidak ditemukan.',
            ], 404);
        }

        $result = $this->syncRowToEmployee($sourceRow);
        $statusCode = $result['success'] ? 200 : 422;
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $statusCode);
    }

    /**
     * Batch sync multiple HRIS records
     */
    public function syncBatch(Request $request): JsonResponse
    {
        // Authorize: PCX, Intercomm only
        $this->authorizeHrisAccess('Hanya PCX dan Intercomm yang bisa melakukan batch sync HRIS.');
        
        $request->validate([
            'ids' => 'nullable|array',
            'ids.*' => 'integer',
            'sync_all' => 'nullable|boolean',
        ]);

        $comparison = $this->buildComparisonDataset();
        $pendingRows = collect($comparison['pending']);

        $shouldSyncAll = (bool) $request->boolean('sync_all');
        $requestedIds = collect($request->input('ids', []))->map(fn ($id) => (int) $id)->unique()->values();

        $rowsToSync = $shouldSyncAll
            ? $pendingRows
            : $pendingRows->whereIn('id', $requestedIds->all())->values();

        if ($rowsToSync->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data pending yang dipilih untuk sinkronisasi.',
                'synced_count' => 0,
                'failed_count' => 0,
                'errors' => [],
            ], 422);
        }

        $synced = 0;
        $failed = 0;
        $errors = [];

        foreach ($rowsToSync as $row) {
            $result = $this->syncRowToEmployee($row);
            if ($result['success']) {
                $synced++;
                continue;
            }

            $failed++;
            $errors[] = [
                'id' => $row['id'],
                'employee_number' => $row['employee_number'] ?? null,
                'message' => $result['message'],
            ];
        }

        $message = $failed > 0
            ? "Batch sinkron selesai: {$synced} berhasil, {$failed} gagal."
            : "Batch sinkron selesai: {$synced} data berhasil disinkronkan.";
        
        return response()->json([
            'success' => $failed === 0,
            'message' => $message,
            'synced_count' => $synced,
            'failed_count' => $failed,
            'errors' => $errors,
        ]);
    }

    /**
     * Get HRIS sync history
     */
    public function getSyncHistory(): JsonResponse
    {
        // Authorize: PCX, Intercomm only
        $this->authorizeHrisAccess('Hanya PCX dan Intercomm yang bisa melihat history sinkronisasi HRIS.');
        
        return response()->json([
            'success' => true,
            'message' => 'HRIS sync history',
            'data' => [],
        ]);
    }

    private function authorizeHrisAccess(string $message): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm']), 403, $message);
    }

    private function buildComparisonDataset(): array
    {
        $hrisSourceRows = $this->getSourceRowsBySystem('HRIS');
        $hrmsSourceRows = $this->getSourceRowsBySystem('HRMS');
        $sourceRows = $hrisSourceRows->concat($hrmsSourceRows)->values();

        $employeeByNumber = Employee::query()
            ->get()
            ->keyBy(fn (Employee $employee) => mb_strtolower(trim((string) $employee->employee_number)));

        $rows = $sourceRows->map(function (array $sourceRow) use ($employeeByNumber) {
            $existing = $employeeByNumber->get(mb_strtolower(trim((string) ($sourceRow['employee_number'] ?? ''))));
            $changes = $this->detectChanges($existing, $sourceRow);

            if (!$existing) {
                $syncType = 'new';
            } elseif (!empty($changes)) {
                $syncType = 'updated';
            } else {
                $syncType = 'up_to_date';
            }

            return [
                ...$sourceRow,
                'sync_type' => $syncType,
                'changes' => $changes,
                'is_pending' => in_array($syncType, ['new', 'updated'], true),
            ];
        })->values();

        $pending = $rows->where('is_pending', true)->values();

        return [
            'source' => $hrisSourceRows,
            'hrms_source' => $hrmsSourceRows,
            'employees' => Employee::query()->get(),
            'pending' => $pending,
            'summary' => [
                'total_source' => $sourceRows->count(),
                'pending_total' => $pending->count(),
                'new_total' => $pending->where('sync_type', 'new')->count(),
                'updated_total' => $pending->where('sync_type', 'updated')->count(),
            ],
        ];
    }

    private function getCombinedSourceRows(): Collection
    {
        return $this->getSourceRowsBySystem('HRIS')
            ->concat($this->getSourceRowsBySystem('HRMS'))
            ->values();
    }

    private function getSourceRowsBySystem(string $sourceSystem): Collection
    {
        if (!Schema::hasTable('sync_source_employees')) {
            return collect();
        }

        return DB::table('sync_source_employees')
            ->where('source_system', strtoupper($sourceSystem))
            ->orderBy('employee_number')
            ->get()
            ->map(function ($row): array {
                $mappedRow = [
                    'id' => (int) $row->id,
                    'source_system' => (string) $row->source_system,
                    'employee_number' => (string) ($row->employee_number ?? ''),
                    'name' => (string) ($row->name ?? ''),
                    'date_joined' => (string) ($row->date_joined ?? ''),
                    'email' => (string) ($row->email ?? ''),
                    'whatsapp' => (string) ($row->whatsapp ?? ''),
                    'company' => (string) ($row->company ?? ''),
                    'division' => (string) ($row->division ?? ''),
                    'department' => (string) ($row->department ?? ''),
                    'position' => (string) ($row->position ?? ''),
                    'placement' => (string) ($row->placement ?? ''),
                    'level' => (string) ($row->level ?? ''),
                    'employee_status' => (string) ($row->employee_status ?? ''),
                    'status' => (string) ($row->status ?? 'Aktif'),
                    'manager_functional' => (string) ($row->manager_functional ?? ''),
                    'manager_operational' => (string) ($row->manager_operational ?? ''),
                ];

                $divisionId = $this->getDivisionId($mappedRow['division']);
                $departmentId = $this->getDepartmentId($divisionId, $mappedRow['department']);

                if ($mappedRow['manager_functional'] === '') {
                    $mappedRow['manager_functional'] = $this->resolveManagerNameById(
                        $this->resolveFunctionalManagerId($divisionId)
                    ) ?? '';
                }

                if ($mappedRow['manager_operational'] === '') {
                    $mappedRow['manager_operational'] = $this->resolveManagerNameById(
                        $this->resolveOperationalManagerId($divisionId, $departmentId, $mappedRow['department'])
                    ) ?? $mappedRow['manager_functional'];
                }

                return $this->applyGeneralManagerNormalization($mappedRow);
            })
            ->values();
    }

    private function detectChanges(?Employee $existing, array $sourceRow): array
    {
        if (!$existing) {
            return [];
        }

        $fieldMap = [
            'name' => 'name',
            'date_joined' => 'date_joined',
            'email' => 'email',
            'whatsapp' => 'whatsapp',
            'company' => 'company',
            'placement' => 'placement',
            'level' => 'level',
            'employee_status' => 'employee_status',
            'status' => 'status',
            'division' => 'division_id',
            'department' => 'department_id',
            'position' => 'position_id',
        ];
        $fieldLabels = [
            'name' => 'Nama',
            'date_joined' => 'Tanggal Masuk',
            'email' => 'Email',
            'whatsapp' => 'Whatsapp',
            'company' => 'Perusahaan',
            'placement' => 'Penempatan',
            'level' => 'Golongan',
            'employee_status' => 'Status Pegawai',
            'status' => 'Status Aktif',
            'division' => 'Divisi',
            'department' => 'Departemen',
            'position' => 'Jabatan',
        ];

        $changes = [];
        foreach ($fieldMap as $sourceKey => $employeeField) {
            $sourceValue = (string) ($sourceRow[$sourceKey] ?? '');
            $currentValue = $this->resolveEmployeeComparableValue(
                $existing,
                $employeeField,
                (string) ($sourceRow['source_system'] ?? '')
            );

            if ($sourceKey === 'division' && trim($sourceValue) === '-') {
                $sourceValue = '';
            }

            if ($sourceKey === 'date_joined') {
                $sourceValue = substr($sourceValue, 0, 10);
                $currentValue = substr((string) $currentValue, 0, 10);
            }

            if (mb_strtolower(trim($sourceValue)) !== mb_strtolower(trim((string) $currentValue))) {
                $changes[] = [
                    'field' => $sourceKey,
                    'label' => $fieldLabels[$sourceKey] ?? $sourceKey,
                    'from' => $currentValue,
                    'to' => $sourceValue,
                ];
            }
        }

        return $changes;
    }

    private function resolveEmployeeComparableValue(Employee $employee, string $field, string $sourceSystem = ''): string
    {
        if ($field === 'division_id') {
            return (string) $this->getDivisionNameById($employee->division_id);
        }

        if ($field === 'department_id') {
            return (string) $this->getDepartmentNameById($employee->department_id, $sourceSystem);
        }

        if ($field === 'position_id') {
            return (string) $this->getPositionNameById($employee->position_id);
        }

        return (string) ($employee->{$field} ?? '');
    }

    private function syncRowToEmployee(array $sourceRow): array
    {
        // Try to find existing employee by employee_number first, then by email
        $employee = Employee::query()
            ->where('employee_number', $sourceRow['employee_number'])
            ->first();
        
        // If not found by employee_number, try email (for cases where employee was created via seeding)
        if (!$employee) {
            $employee = Employee::query()
                ->where('email', (string) $sourceRow['email'])
                ->first();
        }
        
        $payload = $this->mapSourceToEmployeePayload($sourceRow, $employee);
        $statusChangedToInactive = false;

        try {
            DB::transaction(function () use ($employee, $payload, $sourceRow, &$statusChangedToInactive): void {
                // Check if status is changing to Inactive
                if ($employee
                    && !$this->isInactiveEmployeeStatus((string) ($employee->status ?? 'Aktif'))
                    && $this->isInactiveEmployeeStatus((string) ($payload['status'] ?? 'Aktif'))
                ) {
                    $statusChangedToInactive = true;
                }

                if ($employee) {
                    $employee->update($payload);

                    // If status changed to Inactive, move to history
                    if ($statusChangedToInactive) {
                        $this->moveEmployeeToHistory($employee);
                    }
                    return;
                }

                Employee::query()->create($payload);
            });
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Sinkron gagal: ' . $e->getMessage(),
            ];
        }

        return [
            'success' => true,
            'message' => $statusChangedToInactive
                ? 'Data HRIS berhasil disinkronkan dan dipindahkan ke History.'
                : 'Data HRIS berhasil disinkronkan ke Employees.',
            'data' => [
                'employee_number' => $payload['employee_number'],
                'name' => $payload['name'],
                'status_changed_to_inactive' => $statusChangedToInactive,
            ],
        ];
    }

    private function mapSourceToEmployeePayload(array $sourceRow, ?Employee $existing): array
    {
        $divisionName = trim((string) ($sourceRow['division'] ?? ''));
        $divisionId = $divisionName === '-' ? null : $this->getDivisionId($divisionName);
        $departmentId = $this->getDepartmentId($divisionId, (string) ($sourceRow['department'] ?? ''));
        $positionId = $this->getPositionId((string) ($sourceRow['position'] ?? ''));
        $managerFunctionalId = $this->resolveFunctionalManagerId($divisionId);
        $managerOperationalId = $this->resolveOperationalManagerId($divisionId, $departmentId, (string) ($sourceRow['department'] ?? ''));
        $status = $this->isInactiveEmployeeStatus((string) ($sourceRow['status'] ?? 'Aktif')) ? 'Inactive' : 'Aktif';

        return [
            'employee_number' => (string) $sourceRow['employee_number'],
            'name' => (string) $sourceRow['name'],
            'date_joined' => (string) $sourceRow['date_joined'],
            'company' => (string) $sourceRow['company'],
            'division_id' => $divisionId,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'placement' => (string) ($sourceRow['placement'] ?? ''),
            'level' => (string) ($sourceRow['level'] ?? ''),
            'employee_status' => (string) ($sourceRow['employee_status'] ?? 'PKWTT'),
            'email' => (string) $sourceRow['email'],
            'whatsapp' => (string) ($sourceRow['whatsapp'] ?? ''),
            'manager_functional_id' => $managerFunctionalId,
            'manager_functional' => $this->resolveManagerNameById($managerFunctionalId),
            'manager_operational_id' => $managerOperationalId,
            'manager_operational' => $this->resolveManagerNameById($managerOperationalId),
            // Every HRIS sync starts as VnB inactive until explicitly assigned.
            'vnb_status' => 'not_started',
            'vnb_period_start' => null,
            'vnb_period_end' => null,
            'status' => $status,
        ];
    }

    private function applyGeneralManagerNormalization(array $row): array
    {
        $level = mb_strtolower(trim((string) ($row['level'] ?? '')));
        if ($level !== 'general manager') {
            return $row;
        }

        $department = trim((string) ($row['department'] ?? ''));

        // Keep the real division (IT/HR/etc.), but normalize the role naming.
        if ($department === '') {
            $row['department'] = 'General';
        }

        $row['position'] = 'General Manager';
        $row['level'] = 'Manager';

        return $row;
    }

    private function normalizeEmployeeStatusLabel(string $status): string
    {
        return $this->isInactiveEmployeeStatus($status) ? 'Inactive' : 'Aktif';
    }

    private function isInactiveEmployeeStatus(string $status): bool
    {
        $normalized = mb_strtolower(trim($status));

        return in_array($normalized, ['inactive', 'inaktif', 'tidak aktif', 'nonaktif', 'non-active', 'non active'], true);
    }

    private function resolveFunctionalManagerId(?int $divisionId): ?int
    {
        // Functional manager is always the GM of the division
        return $this->findGeneralManagerIdOfDivision($divisionId);
    }

    private function resolveOperationalManagerId(?int $divisionId, ?int $departmentId, string $departmentName): ?int
    {
        if (!$divisionId) return null;

        $deptNameLower = strtolower(trim($departmentName));
        $gmId = $this->findGeneralManagerIdOfDivision($divisionId);

        // If employee is in General department, MO = GM
        if ($deptNameLower === 'general') {
            return $gmId;
        }

        // Find Dept Manager (Manager in same department)
        $managerId = Manager::query()
            ->where('division_id', $divisionId)
            ->where('department_id', $departmentId)
            ->where('status', 'active')
            ->value('id');

        // If no Dept Manager found, MO = GM (Functional Manager)
        return $managerId ? (int) $managerId : $gmId;
    }

    private function resolveManagerNameById(?int $id): ?string
    {
        if (!$id) return null;
        return Manager::where('id', $id)->value('name');
    }

    /**
     * Find General Manager (direktur) ID of a division.
     * GM = Manager with department "General" in the given division.
     */
    private function findGeneralManagerIdOfDivision(?int $divisionId): ?int
    {
        if (!$divisionId) {
            return null;
        }
        $generalDeptId = $this->getDepartmentId(null, 'General');
        if (!$generalDeptId) {
            return null;
        }

        $managerId = Manager::query()
            ->where('division_id', $divisionId)
            ->where('department_id', $generalDeptId)
            ->where('status', 'active')
            ->value('id');

        return $managerId ? (int) $managerId : null;
    }

    // Mapping helpers copied from SyncEmployeesSeeder to avoid master_* table lookups
    private function getDivisionId(?string $divisionName): ?int
    {
        if (!$divisionName) {
            return null;
        }
        $mapping = [
            'General' => 1,
            'Human Resource' => 2,
            'Information Technology' => 3,
            'FAT' => 9,
        ];
        return $mapping[$divisionName] ?? null;
    }

    private function getDivisionNameById(?int $id): string
    {
        if (!$id) return '';
        $mapping = [
            1 => 'General',
            2 => 'Human Resource',
            3 => 'Information Technology',
            9 => 'FAT',
        ];
        return $mapping[$id] ?? '';
    }

    private function getDepartmentId(?int $divisionId, ?string $departmentName): ?int
    {
        if (!$divisionId || !$departmentName) {
            return null;
        }
        $mapping = [
            'General' => 1,
            'People, Culture and Experiences' => 32,
            'C&B and HRIS' => 33,
            'Recruitment' => 34,
            'Webapp Dev' => 35,
            'Technical Support' => 36,
            'IT - SAP' => 37,
            'Factory Production' => 34,
            'Accounting Purchase' => 35,
            'Tax Record' => 36,
        ];
        return $mapping[$departmentName] ?? null;
    }

    private function getDepartmentNameById(?int $id, string $sourceSystem = ''): string
    {
        if (!$id) return '';

        if (strtoupper($sourceSystem) === 'HRMS') {
            $hrmsMapping = [
                34 => 'Factory Production',
                35 => 'Accounting Purchase',
                36 => 'Tax Record',
            ];

            if (isset($hrmsMapping[$id])) {
                return $hrmsMapping[$id];
            }
        }

        $mapping = [
            1 => 'General',
            32 => 'People, Culture and Experiences',
            33 => 'C&B and HRIS',
            34 => 'Recruitment',
            35 => 'Webapp Dev',
            36 => 'Technical Support',
            37 => 'IT - SAP',
        ];
        return $mapping[$id] ?? '';
    }

    private function getPositionId(?string $positionName): ?int
    {
        if (!$positionName) {
            return null;
        }
        $mapping = [
            'Direktur Utama' => 1,
            'Manager' => 2,
            'Internal Communication Specialist' => 3,
            'General Manager' => 4,
            'Web Junior Developer' => 5,
            'Webapp Developer Manager' => 6,
            'HR Bussiness Partner Staf' => 7,
            'C&B and HRIS Manager' => 8,
            'Employee Assurance Supervisor' => 9,
            'HRIS Maintenance Staff' => 10,
            'Recruitment Manager' => 11,
            'Regional Supervisor' => 12,
            'Recruitment Staff' => 13,
            'TS Manager' => 14,
            'Technical Support' => 15,
            'SAP Manager' => 16,
            'SAP Production' => 17,
            'HRGA Specialist' => 18,
            'Sekretaris HR' => 19,
            'Network and Hardware Mainteance' => 20,
            'IT Quality Audit' => 21,
            'Production Staff' => 22,
            'Cashflow Management Assisten' => 23,
            'Tax Management Staff' => 24,
        ];
        return $mapping[$positionName] ?? null;
    }

    private function getPositionNameById(?int $id): string
    {
        if (!$id) return '';
        $mapping = [
            1 => 'Direktur Utama',
            2 => 'Manager',
            3 => 'Internal Communication Specialist',
            4 => 'General Manager',
            5 => 'Web Junior Developer',
            6 => 'Webapp Developer Manager',
            7 => 'HR Bussiness Partner Staf',
            8 => 'C&B and HRIS Manager',
            9 => 'Employee Assurance Supervisor',
            10 => 'HRIS Maintenance Staff',
            11 => 'Recruitment Manager',
            12 => 'Regional Supervisor',
            13 => 'Recruitment Staff',
            14 => 'TS Manager',
            15 => 'Technical Support',
            16 => 'SAP Manager',
            17 => 'SAP Production',
            18 => 'HRGA Specialist',
            19 => 'Sekretaris HR',
            20 => 'Network and Hardware Mainteance',
            21 => 'IT Quality Audit',
            22 => 'Production Staff',
            23 => 'Cashflow Management Assisten',
            24 => 'Tax Management Staff',
        ];
        return $mapping[$id] ?? '';
    }

    /**
     * Determine career stage during HRIS sync for manager routing.
     *
     * Note: Do not guess career_stage here. Career stage MUST be defined
     * by the VnB Framework setup (vnb_framework_stage_level_maps).
     */
    private function determineCareeStageForSync(string $level, string $position, string $employeeStatus, string $company): ?string
    {
        // Intentionally return null so sync does not hardcode career_stage.
        return null;
    }

    private function moveEmployeeToHistory(Employee $employee): void
    {
        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'name' => $employee->name,
            'date_joined' => $employee->date_joined,
            'induction_date' => $employee->induction_date,
            'company' => $employee->company,
            'division_id' => $employee->division_id,
            'department_id' => $employee->department_id,
            'position_id' => $employee->position_id,
            'placement' => $employee->placement,
            'level' => $employee->level,
            'employee_status' => $employee->employee_status,
            'email' => $employee->email,
            'whatsapp' => $employee->whatsapp,
            'manager_functional_id' => $employee->manager_functional_id,
            'manager_operational_id' => $employee->manager_operational_id,
            'career_stage' => $employee->career_stage,
            'notes' => $employee->notes,
            'status' => $employee->status,
            'moved_to_history_at' => now(),
        ]);
    }

    private function resolveMasterId(string $table, string $name): ?int
    {
        $needle = trim($name);
        if ($needle === '') {
            return null;
        }

        if (!Schema::hasTable($table)) {
            return null;
        }

        $existing = DB::table($table)
            ->select('id', 'deleted_at')
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($needle)])
            ->first();

        if ($existing) {
            if (!empty($existing->deleted_at)) {
                DB::table($table)
                    ->where('id', $existing->id)
                    ->update([
                        'deleted_at' => null,
                        'updated_at' => now(),
                    ]);
            }

            return (int) $existing->id;
        }

        return (int) DB::table($table)->insertGetId([
            'name' => $needle,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
