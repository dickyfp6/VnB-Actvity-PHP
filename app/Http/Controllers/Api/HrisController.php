<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\EmployeeHistory;
use App\Http\Controllers\Controller;
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
                return [
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
                ];
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
            $currentValue = $this->resolveEmployeeComparableValue($existing, $employeeField);

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

    private function resolveEmployeeComparableValue(Employee $employee, string $field): string
    {
        if ($field === 'division_id') {
            return (string) optional(DB::table('master_divisions')->where('id', $employee->division_id)->first())->name;
        }

        if ($field === 'department_id') {
            return (string) optional(DB::table('master_departments')->where('id', $employee->department_id)->first())->name;
        }

        if ($field === 'position_id') {
            return (string) optional(DB::table('master_positions')->where('id', $employee->position_id)->first())->name;
        }

        return (string) ($employee->{$field} ?? '');
    }

    private function syncRowToEmployee(array $sourceRow): array
    {
        $employee = Employee::query()->where('employee_number', $sourceRow['employee_number'])->first();
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
        $divisionId = $this->resolveMasterId('master_divisions', (string) ($sourceRow['division'] ?? ''));
        $departmentId = $this->resolveMasterId('master_departments', (string) ($sourceRow['department'] ?? ''));
        $positionId = $this->resolveMasterId('master_positions', (string) ($sourceRow['position'] ?? ''));
        $managerFunctionalId = $this->resolveFunctionalManagerId($existing);
        $status = $this->normalizeEmployeeStatusLabel((string) ($sourceRow['status'] ?? 'Aktif'));
        $isInactive = $this->isInactiveEmployeeStatus($status);
        $existingStatus = $existing ? $this->normalizeEmployeeStatusLabel((string) ($existing->status ?? 'Aktif')) : null;
        $wasInactive = $existingStatus !== null ? $this->isInactiveEmployeeStatus($existingStatus) : false;
        $statusChangedAt = $existing?->status_changed_at;
        $statusChangeReason = $existing?->status_change_reason;
        $statusChangedBy = $existing?->status_changed_by;

        if (($existing && !$wasInactive && $isInactive) || (!$existing && $isInactive)) {
            $statusChangedAt = now();
            $statusChangeReason = 'Sinkronisasi HRIS: status menjadi Inactive';
            $statusChangedBy = auth()->id();
        } elseif ($existing && $wasInactive && !$isInactive) {
            $statusChangedAt = null;
            $statusChangeReason = null;
            $statusChangedBy = null;
        }

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
            'manager_operational_id' => $existing?->manager_operational_id,
            'status' => $status,
            'employment_state' => $isInactive ? 'terminated' : 'active',
            'status_changed_at' => $statusChangedAt,
            'status_change_reason' => $statusChangeReason,
            'status_changed_by' => $statusChangedBy,
        ];
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

    private function resolveFunctionalManagerId(?Employee $existing): ?int
    {
        if ($existing?->manager_functional_id) {
            return (int) $existing->manager_functional_id;
        }

        if (!Schema::hasTable('managers')) {
            return null;
        }

        $existingManagerId = DB::table('managers')
            ->where('status', 'active')
            ->orderBy('id')
            ->value('id');

        if ($existingManagerId) {
            return (int) $existingManagerId;
        }

        $now = now();
        $fallbackEmail = 'sync.default.manager@vnb.local';

        DB::table('managers')->updateOrInsert(
            ['email' => $fallbackEmail],
            [
                'name' => 'Default Sync Manager',
                'company' => 'VnB Platform',
                'division' => 'System',
                'status' => 'active',
                'user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int) DB::table('managers')->where('email', $fallbackEmail)->value('id');
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
            'employment_state' => $employee->employment_state,
            'status_changed_at' => $employee->status_changed_at,
            'status_change_reason' => $employee->status_change_reason,
            'status_changed_by' => $employee->status_changed_by,
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
