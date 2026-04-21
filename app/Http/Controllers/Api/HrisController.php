<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        $sourceRow = $this->getHrisSourceRows()->firstWhere('id', $id);
        if (!$sourceRow) {
            return response()->json([
                'success' => false,
                'message' => 'Data HRIS tidak ditemukan.',
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
        $sourceRows = $this->getHrisSourceRows();
        $employeeByNumber = Employee::query()->get()->keyBy('employee_number');

        $rows = $sourceRows->map(function (array $sourceRow) use ($employeeByNumber) {
            $existing = $employeeByNumber->get($sourceRow['employee_number']);
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
            'source' => $rows,
            'pending' => $pending,
            'summary' => [
                'total_source' => $rows->count(),
                'pending_total' => $pending->count(),
                'new_total' => $pending->where('sync_type', 'new')->count(),
                'updated_total' => $pending->where('sync_type', 'updated')->count(),
            ],
        ];
    }

    /**
     * Temporary source adapter for external HRIS data.
     * In production this should fetch from API or dedicated mirror table.
     */
    private function getHrisSourceRows(): Collection
    {
        $rows = [
            [
                'id' => 1,
                'employee_number' => 'EMP0001',
                'name' => 'Dina Prameswari',
                'date_joined' => '2022-01-17',
                'email' => 'dina.prameswari@wismilak.co.id',
                'whatsapp' => '081234500001',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Human Capital',
                'department' => 'C&B and HRIS',
                'position' => 'HRIS Specialist',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
            [
                'id' => 2,
                'employee_number' => 'EMP0002',
                'name' => 'Rifki Mahendra',
                'date_joined' => '2021-08-03',
                'email' => 'rifki.mahendra@wismilak.co.id',
                'whatsapp' => '081234500002',
                'company' => 'PT Gelora Djaja',
                'division' => 'Sales',
                'department' => 'Area East',
                'position' => 'Area Supervisor',
                'placement' => 'Malang',
                'level' => 'Supervisor',
                'employee_status' => 'PKWTT',
            ],
            [
                'id' => 3,
                'employee_number' => 'EMP0003',
                'name' => 'Silfia Nur Aini',
                'date_joined' => '2024-03-12',
                'email' => 'silfia.nuraini@wismilak.co.id',
                'whatsapp' => '081234500003',
                'company' => 'PT Wismilak Inti Makmur',
                'division' => 'Technology',
                'department' => 'Product Engineering',
                'position' => 'Backend Engineer',
                'placement' => 'Surabaya',
                'level' => 'Staff',
                'employee_status' => 'PKWT',
            ],
            [
                'id' => 4,
                'employee_number' => 'EMP0099',
                'name' => 'Bagas Pratama',
                'date_joined' => '2026-04-01',
                'email' => 'bagas.pratama@wismilak.co.id',
                'whatsapp' => '081234509999',
                'company' => 'PT Gelora Djaja',
                'division' => 'Technology',
                'department' => 'Digital Platform',
                'position' => 'Data Analyst',
                'placement' => 'Gresik',
                'level' => 'Staff',
                'employee_status' => 'PKWTT',
            ],
        ];

        return collect($rows);
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
            'division' => 'division_id',
            'department' => 'department_id',
            'position' => 'position_id',
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

        try {
            DB::transaction(function () use ($employee, $payload): void {
                if ($employee) {
                    $employee->update($payload);
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
            'message' => 'Data HRIS berhasil disinkronkan ke Employees.',
            'data' => [
                'employee_number' => $payload['employee_number'],
                'name' => $payload['name'],
            ],
        ];
    }

    private function mapSourceToEmployeePayload(array $sourceRow, ?Employee $existing): array
    {
        $divisionId = $this->resolveMasterId('master_divisions', (string) ($sourceRow['division'] ?? ''));
        $departmentId = $this->resolveMasterId('master_departments', (string) ($sourceRow['department'] ?? ''));
        $positionId = $this->resolveMasterId('master_positions', (string) ($sourceRow['position'] ?? ''));
        $managerFunctionalId = $existing?->manager_functional_id ?? DB::table('managers')->value('id');

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
        ];
    }

    private function resolveMasterId(string $table, string $name): ?int
    {
        $needle = trim($name);
        if ($needle === '') {
            return null;
        }

        return DB::table($table)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($needle)])
            ->value('id');
    }
}
