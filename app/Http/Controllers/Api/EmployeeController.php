<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\VnbPeriod;
use App\Models\VnbCancellation;
use App\Models\VnbPlanItem;
use App\Models\VnbPlan;
use App\Models\MasterDivision;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use App\Models\MasterCompany;
use App\Models\MasterPlacement;
use App\Models\MasterLevel;
use App\Models\MasterEmployeeStatus;
use App\Models\Manager;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class EmployeeController extends Controller
{
    /**
     * UC-01: View Employee List & Filter
     */
    public function index(Request $request): JsonResponse
    {
        $query = Employee::query();

        $lifecycle = (string) $request->input('lifecycle', 'active');
        if ($lifecycle === 'history') {
            $query->whereIn('employment_state', ['resigned', 'terminated', 'graduated']);
        } elseif (in_array($lifecycle, ['active', 'resigned', 'terminated', 'graduated'], true)) {
            $query->where('employment_state', $lifecycle);
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('vnb_status')) {
            $query->where('vnb_status', $request->vnb_status);
        }

        if ($request->filled('employee_status')) {
            $query->where('employee_status', $request->employee_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query
            ->with([
                'division', 
                'department', 
                'position', 
                'managerFunctional', 
                'managerOperational', 
                'vnbPeriods'
            ])
            ->orderBy('id')
            ->get();

        // Load master data for level and status lookup
        $levels = MasterLevel::pluck('name', 'id')->toArray();
        $statuses = MasterEmployeeStatus::pluck('name', 'name')->toArray();

        // Ensure relationships are loaded, fallback if null
        $employees->each(function ($emp) {
            if (!$emp->department && $emp->department_id) {
                $emp->department = MasterDepartment::find($emp->department_id);
            }
            if (!$emp->position && $emp->position_id) {
                $emp->position = MasterPosition::find($emp->position_id);
            }
        });

        $sameNameCounts = $employees
            ->map(fn (Employee $employee) => Str::lower(trim((string) $employee->name)))
            ->filter()
            ->countBy();

        $progressMap = VnbPlanItem::query()
            ->join('vnb_plans', 'vnb_plans.id', '=', 'vnb_plan_items.plan_id')
            ->whereIn('vnb_plans.employee_id', $employees->pluck('id'))
            ->selectRaw('vnb_plans.employee_id as employee_id, AVG(vnb_plan_items.completion_percentage) as avg_progress')
            ->groupBy('vnb_plans.employee_id')
            ->pluck('avg_progress', 'employee_id');

        $latestPlanMap = VnbPlan::query()
            ->whereIn('employee_id', $employees->pluck('id'))
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['employee_id', 'phase_number', 'status'])
            ->groupBy('employee_id')
            ->map(fn ($plans) => $plans->first());

        $rows = $employees->values()->map(function (Employee $employee, int $index) use ($progressMap, $latestPlanMap, $levels, $statuses) {
            $periodStart = $employee->vnb_period_start ?? $employee->induction_date ?? $employee->date_joined;
            $periodEnd = $employee->vnb_period_end ?? ($periodStart ? Carbon::parse($periodStart)->copy()->addYear()->subDay() : null);
            $phase = $this->deriveEmployeePhaseLabel($employee, $latestPlanMap->get($employee->id));

            $progress = $progressMap->get($employee->id);
            if ($progress === null) {
                $progress = match ($employee->vnb_status) {
                    'completed' => 100,
                    default => 0,
                };
            }

            return [
                'id' => $employee->id,
                'code' => $index + 1,
                'employee_number' => $employee->employee_number,
                'name' => $employee->name,
                'name_display' => (($sameNameCounts[Str::lower(trim((string) $employee->name))] ?? 0) > 1)
                    ? ($employee->name . ' (' . ($employee->division?->name ?? 'Tanpa Divisi') . ')')
                    : $employee->name,
                'date_joined' => optional($employee->date_joined)->toDateString(),
                'induction_date' => optional($employee->induction_date)->toDateString(),
                'email' => $employee->email,
                'whatsapp' => $employee->whatsapp,
                'vnb_period_start' => optional($periodStart)->toDateString(),
                'vnb_period_end' => optional($periodEnd)->toDateString(),
                'career_stage' => $employee->getCareerStage(),
                'phase' => $phase,
                'progress' => round((float) $progress, 1),
                'manager_functional' => $employee->managerFunctional?->name,
                'manager_operational' => $employee->managerOperational?->name,
                'manager_functional_id' => $employee->manager_functional_id,
                'manager_operational_id' => $employee->manager_operational_id,
                'company' => $employee->company,
                'division_id' => $employee->division_id,
                'division' => $employee->division?->name ?? '-',
                'department_id' => $employee->department_id,
                'department' => $employee->department?->name ?? ($employee->department_id ? "Dept #{$employee->department_id}" : '-'),
                'position_id' => $employee->position_id,
                'position' => $employee->position?->name ?? ($employee->position_id ? "Pos #{$employee->position_id}" : '-'),
                'placement' => $employee->placement,
                'level_id' => $employee->level,
                'level' => $levels[$employee->level] ?? ($employee->level ? "Level #{$employee->level}" : '-'),
                'employee_status' => $employee->employee_status,
                'vnb_status' => $employee->vnb_status,
                'employment_state' => $employee->employment_state ?? 'active',
                'status_changed_at' => optional($employee->status_changed_at)->toDateTimeString(),
                'status_change_reason' => $employee->status_change_reason,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * UC-01: Add New Employee & Assign Manager
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateEmployeePayload($request);
        $generatedPassword = null;
        $credentialPayload = null;

        $employee = DB::transaction(function () use ($validated, &$generatedPassword, &$credentialPayload) {
            $periodStart = Carbon::parse($validated['induction_date']);
            $periodEnd = $periodStart->copy()->addYear()->subDay();

            $data = $validated;
            $data['vnb_period_start'] = $periodStart->toDateString();
            $data['vnb_period_end'] = $periodEnd->toDateString();
            $data['vnb_status'] = $this->determineVnbStatus($periodStart, $periodEnd);
            $data['employment_state'] = 'active';
            $data['employee_number'] = trim((string) $validated['employee_number']);

            $employee = $this->createOrRestoreEmployee($data);

            $this->syncVnbPeriods($employee, $periodStart);
            $generatedPassword = $this->provisionEmployeeUserAccount($employee, true);
            if ($generatedPassword !== null) {
                $credentialPayload = [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'username' => $employee->employee_number,
                    'password' => $generatedPassword,
                    'email' => $employee->email,
                ];
            }

            return $employee;
        });

        $emailSent = false;
        if ($generatedPassword) {
            $emailSent = $this->sendEmployeeCredentialEmail($employee, $generatedPassword);
        }

        if ($credentialPayload) {
            $credentialPayload['delivery'] = [
                'email_sent' => $emailSent,
                'popup_available' => true,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Employee berhasil ditambahkan',
            'data' => [
                ...$employee->load(['division', 'department', 'position', 'managerFunctional', 'managerOperational', 'vnbPeriods'])->toArray(),
                'account_credential' => $credentialPayload,
            ],
        ], 201);
    }

    /**
     * UC-02: View Employee Detail & VnB Status
     */
    public function show(Employee $employee): JsonResponse
    {
        $periodStart = $employee->vnb_period_start ?? $employee->induction_date ?? $employee->date_joined;
        $periodEnd = $employee->vnb_period_end ?? ($periodStart ? Carbon::parse($periodStart)->copy()->addYear()->subDay() : null);
        $latestPlan = VnbPlan::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first(['phase_number', 'status']);

        return response()->json([
            'success' => true,
            'data' => [
                ...$employee->load(['division', 'department', 'position', 'managerFunctional', 'managerOperational', 'vnbPeriods.plans', 'user'])->toArray(),
                'career_stage' => $employee->getCareerStage(),
                'phase' => $this->deriveEmployeePhaseLabel($employee, $latestPlan),
                'account_credential_preview' => $this->buildCredentialPreview($employee->fresh(['user'])),
            ],
        ]);
    }

    public function resetCredential(Employee $employee): JsonResponse
    {
        // Check permission
        if (!auth()->user()?->can('reset_password')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk reset password.',
            ], 403);
        }

        $rawPassword = $this->resetEmployeeCredential($employee);

        $emailSent = false;
        if ($rawPassword !== null) {
            $emailSent = $this->sendEmployeeCredentialEmail($employee, $rawPassword);
        }

        $preview = $this->buildCredentialPreview($employee->fresh(['user']));
        $preview['delivery'] = [
            'email_sent' => $emailSent,
            'popup_available' => true,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Password sementara berhasil di-generate ulang.',
            'data' => $preview,
        ]);
    }

    /**
     * UC-02: Edit Employee Profile
     */
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $this->validateEmployeePayload($request, $employee->id, true);

        DB::transaction(function () use ($employee, $validated) {
            $base = $employee->induction_date ? Carbon::parse($employee->induction_date) : Carbon::parse($employee->date_joined);
            if (array_key_exists('induction_date', $validated) && $validated['induction_date']) {
                $base = Carbon::parse($validated['induction_date']);
            }

            $validated['vnb_period_start'] = $base->toDateString();
            $validated['vnb_period_end'] = $base->copy()->addYear()->subDay()->toDateString();
            $validated['vnb_status'] = $this->determineVnbStatus($base, $base->copy()->addYear()->subDay());

            $employee->update($validated);
            $freshEmployee = $employee->fresh();
            $this->syncVnbPeriods($freshEmployee, $base);
            $this->provisionEmployeeUserAccount($freshEmployee, false);
            $this->syncEmployeeUserAccessStatus($freshEmployee);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Employee berhasil diperbarui',
            'data' => $employee->fresh()->load(['division', 'department', 'position', 'managerFunctional', 'managerOperational'])
        ]);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->deactivateEmployeeUserByEmployeeId($employee->id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Employee berhasil dihapus',
        ]);
    }

    public function updateLifecycle(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'employment_state' => 'required|in:active,resigned,terminated,graduated',
            'status_change_reason' => 'nullable|string|max:1000',
        ]);

        $nextState = (string) $validated['employment_state'];
        $payload = [
            'employment_state' => $nextState,
            'status_changed_at' => now(),
            'status_change_reason' => trim((string) ($validated['status_change_reason'] ?? '')) ?: null,
            'status_changed_by' => auth()->id(),
        ];

        if ($nextState === 'graduated') {
            $payload['vnb_status'] = 'completed';
        }

        if ($nextState === 'resigned' || $nextState === 'terminated') {
            $payload['vnb_status'] = 'canceled';
        }

        $employee->update($payload);
        $this->syncEmployeeUserAccessStatus($employee->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Status lifecycle Employee berhasil diperbarui.',
            'data' => $employee->fresh(),
        ]);
    }

    /**
     * Cancel VnB Program
     */
    public function cancelVnb(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|in:budaya_kerja,tidak_cocok_vnb,others',
            'notes' => 'required|string',
        ]);

        VnbCancellation::create([
            'employee_id' => $employee->id,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'canceled_by' => auth()->id(),
            'canceled_at' => now(),
        ]);

        $employee->update(['vnb_status' => 'canceled']);

        return response()->json([
            'success' => true,
            'message' => 'VnB program canceled successfully',
        ]);
    }

    /**
     * Bulk delete employees
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:employees,id',
        ])['ids'];

        Employee::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' employees deleted successfully',
        ]);
    }

    public function managerOptions(): JsonResponse
    {
        $rows = Manager::query()
            ->where('status', 'active')
            ->get(['id', 'name', 'email'])
            ->map(function (Manager $manager) {
                return [
                    'id' => $manager->id,
                    'name' => $manager->name,
                    'email' => $manager->email,
                    'label' => $manager->name . ' (' . $manager->email . ')',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function importFromPaste(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rows' => 'required|array|min:1',
            'rows.*.employee_number' => 'nullable',
            'rows.*.date_joined' => 'nullable',
            'rows.*.induction_date' => 'nullable',
            'rows.*.name' => 'nullable',
            'rows.*.email' => 'nullable',
            'rows.*.whatsapp' => 'nullable',
            'rows.*.manager_functional_input' => 'nullable',
            'rows.*.manager_operational_input' => 'nullable',
            'rows.*.company' => 'nullable',
            'rows.*.division' => 'nullable',
            'rows.*.department' => 'nullable',
            'rows.*.position' => 'nullable',
            'rows.*.placement' => 'nullable',
            'rows.*.level' => 'nullable',
            'rows.*.employee_status' => 'nullable',
            'rows.*.manager_functional_id' => 'nullable',
            'rows.*.manager_operational_id' => 'nullable',
        ]);

        $preview = $this->buildImportPreview($validated['rows']);

        return response()->json([
            'success' => true,
            'message' => 'Preview import berhasil dibuat. Data belum disimpan.',
            'data' => $preview,
        ]);
    }

    public function importFromFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        $file = $request->file('file');
        $rows = $this->parseImportFile($file->getRealPath(), $file->getClientOriginalExtension());

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak berisi data yang valid.',
            ], 422);
        }

        $preview = $this->buildImportPreview($rows);

        return response()->json([
            'success' => true,
            'message' => 'Preview import file berhasil dibuat. Data belum disimpan.',
            'data' => $preview,
        ]);
    }

    public function confirmImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rows' => 'required|array|min:1',
            'rows.*.employee_number' => 'nullable',
            'rows.*.date_joined' => 'nullable',
            'rows.*.induction_date' => 'nullable',
            'rows.*.name' => 'nullable',
            'rows.*.email' => 'nullable',
            'rows.*.whatsapp' => 'nullable',
            'rows.*.manager_functional_input' => 'nullable',
            'rows.*.manager_operational_input' => 'nullable',
            'rows.*.company' => 'nullable',
            'rows.*.division' => 'nullable',
            'rows.*.department' => 'nullable',
            'rows.*.position' => 'nullable',
            'rows.*.placement' => 'nullable',
            'rows.*.level' => 'nullable',
            'rows.*.employee_status' => 'nullable',
            'add_missing_master' => 'nullable|array',
            'add_missing_master.companies' => 'nullable|boolean',
            'add_missing_master.divisions' => 'nullable|boolean',
            'add_missing_master.departments' => 'nullable|boolean',
            'add_missing_master.positions' => 'nullable|boolean',
            'add_missing_master.placements' => 'nullable|boolean',
            'add_missing_master.levels' => 'nullable|boolean',
            'add_missing_master.employee_statuses' => 'nullable|boolean',
        ]);

        $summary = $this->confirmImportRows(
            $validated['rows'],
            $validated['add_missing_master'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi import selesai.',
            'data' => $summary,
        ], 201);
    }

    public function downloadImportTemplate(): StreamedResponse
    {
        $headers = [
            'NIP',
            'Tanggal Masuk',
            'Tanggal Induction',
            'Nama Employee',
            'Email',
            'Whatsapp',
            'Manager Fungsional',
            'Manager Operasional',
            'Perusahaan',
            'Divisi',
            'Departemen',
            'Jabatan',
            'Penempatan',
            'Golongan',
            'Status Pegawai',
        ];

        $exampleRow = [
            'NH-00001',
            now()->format('Y-m-d'),
            now()->format('Y-m-d'),
            'Contoh Nama',
            'contoh@email.com',
            '081234567890',
            'Nama Manager / email manager',
            'Nama Manager / email manager',
            'PT Contoh',
            'Sales',
            'Trade Marketing',
            'Supervisor',
            'Jakarta',
            'Staff',
            'PKWTT',
        ];

        $xlsxBinary = $this->generateSimpleXlsxBinary([$headers, $exampleRow]);

        return response()->streamDownload(function () use ($xlsxBinary) {
            echo $xlsxBinary;
        }, 'template_import_employee.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function validateEmployeePayload(Request $request, ?int $employeeId = null, bool $isUpdate = false): array
    {
        $prefix = $isUpdate ? 'sometimes|' : '';

        if ($request->has('employee_status')) {
            $request->merge([
                'employee_status' => $this->normalizeEmployeeStatusValue($request->input('employee_status')),
            ]);
        }

        $emailUniqueRule = Rule::unique('employees', 'email')
            ->ignore($employeeId)
            ->whereNull('deleted_at');

        $employeeNumberUniqueRule = Rule::unique('employees', 'employee_number')
            ->ignore($employeeId)
            ->whereNull('deleted_at');

        $validated = $request->validate([
            'name' => $prefix . 'required|string|max:255',
            'date_joined' => $prefix . 'required|date',
            'induction_date' => $prefix . 'required|date',
            'email' => [$prefix . 'required', 'email', $emailUniqueRule],
            'whatsapp' => 'nullable|string|max:20',
                'employee_number' => [$prefix . 'required', 'string', 'max:50', $employeeNumberUniqueRule],
            'manager_functional_id' => 'nullable|exists:managers,id',
            'manager_operational_id' => 'nullable|exists:managers,id|different:manager_functional_id',
            'company' => [$prefix . 'required', 'string', 'max:100', Rule::exists('master_companies', 'name')],
            'division_id' => $prefix . 'required|exists:master_divisions,id',
            'department_id' => $prefix . 'required|exists:master_departments,id',
            'position_id' => $prefix . 'required|exists:master_positions,id',
            'placement' => [$prefix . 'required', 'string', 'max:100', Rule::exists('master_placements', 'name')],
            'level' => [$prefix . 'required', 'string', 'max:50', Rule::exists('master_levels', 'name')],
            'employee_status' => [$prefix . 'required', 'string', 'max:50', Rule::exists('master_employee_statuses', 'name')],
        ]);

        $this->assertEmployeeDuplicateRules($validated, $employeeId);

        return $validated;
    }

    private function assertEmployeeDuplicateRules(array $validated, ?int $employeeId = null): void
    {
        $duplicateQuery = Employee::query();
        if ($employeeId) {
            $duplicateQuery->where('id', '!=', $employeeId);
        }

        $email = trim((string) ($validated['email'] ?? ''));
        if ($email !== '') {
            $exists = (clone $duplicateQuery)
                ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages(['email' => ['Email sudah terdaftar di Employee lain.']]);
            }
        }

        $whatsapp = trim((string) ($validated['whatsapp'] ?? ''));
        if ($whatsapp !== '') {
            $targetWhatsapp = $this->normalizeWhatsappKey($whatsapp);
            $existsWhatsapp = (clone $duplicateQuery)
                ->whereNotNull('whatsapp')
                ->get(['whatsapp'])
                ->contains(fn ($item) => $this->normalizeWhatsappKey((string) $item->whatsapp) === $targetWhatsapp);

            if ($existsWhatsapp) {
                throw ValidationException::withMessages(['whatsapp' => ['Nomor Whatsapp sudah terdaftar di Employee lain.']]);
            }
        }

        $employeeNumber = trim((string) ($validated['employee_number'] ?? ''));
        if ($employeeNumber !== '') {
            $existsEmployeeNumber = (clone $duplicateQuery)
                ->whereRaw('LOWER(employee_number) = ?', [Str::lower($employeeNumber)])
                ->exists();
            if ($existsEmployeeNumber) {
                throw ValidationException::withMessages(['employee_number' => ['NIP sudah terdaftar di Employee lain.']]);
            }
        }

        $currentEmployee = $employeeId ? Employee::find($employeeId) : null;
        $name = trim((string) ($validated['name'] ?? ($currentEmployee?->name ?? '')));
        $divisionId = $validated['division_id'] ?? ($currentEmployee?->division_id ?? null);

        if ($name !== '' && $divisionId) {
            $existsSameNameDivision = (clone $duplicateQuery)
                ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
                ->where('division_id', $divisionId)
                ->exists();

            if ($existsSameNameDivision) {
                $divisionName = MasterDivision::where('id', $divisionId)->value('name') ?? 'Tanpa Divisi';
                throw ValidationException::withMessages([
                    'name' => ["Nama sama persis sudah ada pada Divisi {$divisionName}."],
                ]);
            }
        }
    }

    private function normalizeWhatsappKey(string $value): string
    {
        return preg_replace('/\D+/', '', trim($value)) ?? '';
    }

    private function syncVnbPeriods(Employee $employee, Carbon $periodStart): void
    {
        $employee->vnbPeriods()->delete();

        for ($phase = 1; $phase <= 3; $phase++) {
            $start = $periodStart->copy()->addMonths(($phase - 1) * 4);
            $end = $phase === 3 ? $periodStart->copy()->addYear()->subDay() : $start->copy()->addMonths(4)->subDay();

            VnbPeriod::create([
                'employee_id' => $employee->id,
                'phase_number' => $phase,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'cutoff_date' => $end->copy()->setDay(min(25, $end->daysInMonth))->toDateString(),
                'status' => now()->lt($start) ? 'not_started' : (now()->lte($end) ? 'in_progress' : 'completed'),
            ]);
        }
    }

    private function determineVnbStatus(Carbon $start, Carbon $end): string
    {
        $today = now();
        if ($today->lt($start)) {
            return 'not_started';
        }
        if ($today->gt($end)) {
            return 'completed';
        }
        return 'active';
    }

    private function createOrRestoreEmployee(array $payload): Employee
    {
        $email = trim((string) ($payload['email'] ?? ''));
        $employeeNumber = trim((string) ($payload['employee_number'] ?? ''));

        $existing = null;

        if ($email !== '') {
            $existing = Employee::withTrashed()
                ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
                ->first();
        }

        if (!$existing && $employeeNumber !== '') {
            $existing = Employee::withTrashed()
                ->whereRaw('LOWER(employee_number) = ?', [Str::lower($employeeNumber)])
                ->first();
        }

        if ($existing && $existing->trashed()) {
            $existing->restore();
            $existing->update($payload);
            return $existing->fresh();
        }

        if ($existing) {
            throw ValidationException::withMessages([
                'email' => ['Email atau NIP sudah terdaftar di Employee lain.'],
            ]);
        }

        return Employee::create($payload);
    }

    private function determinePhase(?Carbon $start, ?Carbon $end): string
    {
        if (!$start || !$end) {
            return '-';
        }

        $today = now();
        if ($today->lt($start)) {
            return 'Fase 1';
        }
        if ($today->gt($end)) {
            return 'Selesai';
        }

        $months = $start->diffInMonths($today);
        if ($months < 4) {
            return 'Fase 1';
        }
        if ($months < 8) {
            return 'Fase 2';
        }
        return 'Fase 3';
    }

    private function deriveEmployeePhaseLabel(Employee $employee, mixed $latestPlan): string
    {
        if ($employee->vnb_status === 'completed') {
            return 'Selesai';
        }

        if (!$latestPlan) {
            return 'Planning';
        }

        $planStatus = (string) ($latestPlan->status ?? '');
        if (in_array($planStatus, ['draft', 'waiting_manager_approval', 'rejected'], true)) {
            return 'Planning';
        }

        $phaseNumber = (int) ($latestPlan->phase_number ?? 1);
        if ($phaseNumber < 1 || $phaseNumber > 3) {
            $phaseNumber = 1;
        }

        return 'Fase ' . $phaseNumber;
    }

    private function deriveCareerStage(?string $level): string
    {
        if (!$level) {
            return '-';
        }

        $normalized = Str::lower(trim($level));
        $normalizedCompact = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if (Str::contains($normalized, ['non staff', 'non-staff', 'harian', 'mingguan'])) {
            return 'Manage Self (Non-Staff)';
        }

        if (Str::contains($normalized, ['staff', 'supervisor'])) {
            return 'Manage Self (Staff)';
        }

        if (in_array($normalizedCompact, ['manager'], true)) {
            return 'Manage Others';
        }

        if (Str::contains($normalized, ['senior manager', 'general manager', 'manager managers'])) {
            return 'Manage Managers';
        }

        if (Str::contains($normalized, ['head', 'gm', 'director', 'function'])) {
            return 'Manage Function';
        }

        return Str::title($level);
    }

    private function generateEmployeeNumber(int $id): string
    {
        return 'NH-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }

    private function buildDefaultPasswordFromEmployee(Employee $employee): string
    {
        $name = trim((string) $employee->name);
        $firstName = preg_split('/\s+/', $name)[0] ?? '';
        $firstName = Str::of($firstName)->lower()->replaceMatches('/[^a-z0-9]/', '')->value();

        if ($firstName === '') {
            $firstName = 'user';
        }

        // Get last 2 digits from NIP
        $nipDigits = preg_replace('/\D+/', '', (string) ($employee->employee_number ?? '')) ?? '';
        $suffix = $nipDigits === '' ? '00' : str_pad(substr($nipDigits, -2), 2, '0', STR_PAD_LEFT);

        return $firstName . $suffix;
    }

    private function provisionEmployeeUserAccount(Employee $employee, bool $allowCreate): ?string
    {
        $email = trim((string) $employee->email);
        if ($email === '') {
            return null;
        }

        $existingByEmployee = User::query()->where('employee_id', $employee->id)->first();
        $existingByEmail = User::query()->whereRaw('LOWER(email) = ?', [Str::lower($email)])->first();

        if ($existingByEmployee && $existingByEmail && $existingByEmployee->id !== $existingByEmail->id) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah digunakan akun lain.'],
            ]);
        }

        if ($existingByEmail && (int) ($existingByEmail->employee_id ?? 0) !== (int) $employee->id && $existingByEmail->employee_id !== null) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah digunakan akun user yang terhubung ke karyawan lain.'],
            ]);
        }

        $user = $existingByEmployee ?: $existingByEmail;

        if (!$user && !$allowCreate) {
            return null;
        }

        if ($user) {
            $user->update([
                'name' => $employee->name,
                'email' => $email,
                'phone' => $employee->whatsapp,
                'status' => 'active',
                'employee_id' => $employee->id,
            ]);

            $employeeRole = $this->resolveEmployeeRoleName();
            if ($employeeRole !== null && !$user->getRoleNames()->contains($employeeRole)) {
                $user->syncRoles([$employeeRole]);
            }

            return null;
        }

        $rawPassword = $this->buildDefaultPasswordFromEmployee($employee);

        $user = User::create([
            'name' => $employee->name,
            'email' => $email,
            'password' => Hash::make($rawPassword),
            'temp_password_encrypted' => Crypt::encryptString($rawPassword),
            'temp_password_generated_at' => now(),
            'phone' => $employee->whatsapp,
            'status' => 'active',
            'employee_id' => $employee->id,
            'email_verified_at' => now(),
        ]);

        $employeeRole = $this->resolveEmployeeRoleName();
        if ($employeeRole !== null) {
            $user->assignRole($employeeRole);
        }

        return $rawPassword;
    }

    private function resetEmployeeCredential(Employee $employee): ?string
    {
        $email = trim((string) $employee->email);
        if ($email === '') {
            return null;
        }

        $user = User::query()->where('employee_id', $employee->id)->first();

        if (!$user) {
            $this->provisionEmployeeUserAccount($employee, true);
            $user = User::query()->where('employee_id', $employee->id)->first();
        }

        if (!$user) {
            return null;
        }

        $rawPassword = $this->buildDefaultPasswordFromEmployee($employee);

        $user->update([
            'name' => $employee->name,
            'email' => $email,
            'phone' => $employee->whatsapp,
            'status' => 'active',
            'employee_id' => $employee->id,
            'password' => Hash::make($rawPassword),
            'temp_password_encrypted' => Crypt::encryptString($rawPassword),
            'temp_password_generated_at' => now(),
        ]);

        $employeeRole = $this->resolveEmployeeRoleName();
        if ($employeeRole !== null && !$user->getRoleNames()->contains($employeeRole)) {
            $user->syncRoles([$employeeRole]);
        }

        return $rawPassword;
    }

    private function buildCredentialPreview(Employee $employee): array
    {
        $user = $employee->user;
        $employeeRole = $this->resolveEmployeeRoleName() ?? 'employee';
        $tempPassword = null;

        if ($user && !empty($user->temp_password_encrypted)) {
            try {
                $tempPassword = Crypt::decryptString((string) $user->temp_password_encrypted);
            } catch (\Throwable $e) {
                $tempPassword = null;
            }
        }

        return [
            'has_account' => (bool) $user,
            'username' => $employee->employee_number,
            'email' => $employee->email,
            'role' => $employeeRole,
            'temporary_password' => $tempPassword,
            'temporary_password_generated_at' => optional($user?->temp_password_generated_at)->toDateTimeString(),
        ];
    }

    private function resolveEmployeeRoleName(): ?string
    {
        $employeeRole = Role::query()->firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'web',
        ]);

        return $employeeRole->name;
    }

    private function sendEmployeeCredentialEmail(Employee $employee, string $rawPassword): bool
    {
        $email = trim((string) $employee->email);
        if ($email === '') {
            return false;
        }

        try {
            Mail::raw(
                "Halo {$employee->name},\n\nAkun VnB Anda telah dibuat.\nNIP: {$employee->employee_number}\nEmail: {$employee->email}\nPassword: {$rawPassword}\n\nSilakan login lalu ubah password di menu akun Anda.",
                function ($message) use ($email) {
                    $message->to($email)->subject('Akun VnB Employee Anda');
                }
            );
            return true;
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email akun Employee', [
                'employee_id' => $employee->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function syncEmployeeUserAccessStatus(Employee $employee): void
    {
        $user = User::query()->where('employee_id', $employee->id)->first();
        if (!$user) {
            return;
        }

        $employeeStatus = Str::lower(trim((string) ($employee->employee_status ?? '')));
        $statusChangeReason = Str::lower(trim((string) ($employee->status_change_reason ?? '')));
        $isMutated = Str::contains($employeeStatus, 'mutasi') || Str::contains($statusChangeReason, 'mutasi');
        $employmentState = Str::lower(trim((string) ($employee->employment_state ?? 'active')));
        $isInactiveState = in_array($employmentState, ['resigned', 'terminated'], true);

        $graduatedExpired = false;
        if ($employmentState === 'graduated') {
            $referenceTime = $employee->status_changed_at
                ?? $employee->vnb_period_end
                ?? $employee->updated_at;

            $graduatedExpired = $referenceTime && now()->greaterThan($referenceTime->copy()->addDays(30));
        }

        $shouldDeactivate = $isMutated || $isInactiveState || $graduatedExpired;

        if ($shouldDeactivate) {
            if ($user->status !== 'inactive') {
                $user->update(['status' => 'inactive']);
            }
            $user->tokens()->delete();
            return;
        }

        if ($user->status !== 'active') {
            $user->update(['status' => 'active']);
        }
    }

    private function deactivateEmployeeUserByEmployeeId(int $employeeId): void
    {
        $user = User::query()->where('employee_id', $employeeId)->first();
        if (!$user) {
            return;
        }

        if ($user->status !== 'inactive') {
            $user->update(['status' => 'inactive']);
        }

        $user->tokens()->delete();
    }

    private function importRows(array $rows): array
    {
        $inserted = 0;
        $failed = 0;
        $errors = [];
        $credentials = [];

        foreach ($rows as $index => $row) {
            try {
                $managerFunctionalId = $row['manager_functional_id'] ?? $this->resolveManagerId((string) ($row['manager_functional_input'] ?? ''));
                $managerOperationalId = $row['manager_operational_id'] ?? $this->resolveManagerId((string) ($row['manager_operational_input'] ?? ''));
                $divisionId = MasterDivision::where('name', trim((string) ($row['division'] ?? '')))->value('id');
                $departmentId = MasterDepartment::where('name', trim((string) ($row['department'] ?? '')))->value('id');
                $positionId = MasterPosition::where('name', trim((string) ($row['position'] ?? '')))->value('id');

                if (trim((string) ($row['employee_number'] ?? '')) === '') {
                    throw new \RuntimeException('NIP wajib diisi');
                }

                if (trim((string) ($row['whatsapp'] ?? '')) === '') {
                    throw new \RuntimeException('Whatsapp wajib diisi');
                }

                if (!$managerFunctionalId) {
                    throw new \RuntimeException('Manager Fungsional wajib diisi dan harus valid');
                }

                if (trim((string) ($row['employee_status'] ?? '')) === '') {
                    throw new \RuntimeException('Status Pegawai wajib diisi');
                }

                $payload = [
                    'employee_number' => trim((string) ($row['employee_number'] ?? '')),
                    'name' => trim((string) ($row['name'] ?? '')),
                    'date_joined' => $this->normalizeImportDateValue($row['date_joined'] ?? null),
                    'induction_date' => $this->normalizeImportDateValue($row['induction_date'] ?? null),
                    'email' => trim((string) ($row['email'] ?? '')),
                    'whatsapp' => trim((string) ($row['whatsapp'] ?? '')),
                    'manager_functional_id' => $managerFunctionalId,
                    'manager_operational_id' => $managerOperationalId,
                    'company' => trim((string) ($row['company'] ?? '')),
                    'division_id' => $divisionId,
                    'department_id' => $departmentId,
                    'position_id' => $positionId,
                    'placement' => trim((string) ($row['placement'] ?? '')),
                    'level' => trim((string) ($row['level'] ?? '')),
                    'employee_status' => $this->normalizeEmployeeStatusValue($row['employee_status'] ?? ''),
                ];

                $fakeRequest = new Request($payload);
                $validated = $this->validateEmployeePayload($fakeRequest);

                $credentialPayload = null;

                DB::transaction(function () use ($validated, &$credentialPayload) {
                    $periodStart = Carbon::parse($validated['induction_date']);
                    $periodEnd = $periodStart->copy()->addYear()->subDay();

                    $validated['vnb_period_start'] = $periodStart->toDateString();
                    $validated['vnb_period_end'] = $periodEnd->toDateString();
                    $validated['vnb_status'] = $this->determineVnbStatus($periodStart, $periodEnd);
                    $validated['employment_state'] = 'active';

                    $employee = $this->createOrRestoreEmployee($validated);
                    $this->syncVnbPeriods($employee, $periodStart);

                    $generatedPassword = $this->provisionEmployeeUserAccount($employee, true);
                    if ($generatedPassword) {
                        $emailSent = $this->sendEmployeeCredentialEmail($employee, $generatedPassword);
                        $credentialPayload = [
                            'employee_id' => $employee->id,
                            'name' => $employee->name,
                            'username' => $employee->employee_number,
                            'password' => $generatedPassword,
                            'email' => $employee->email,
                            'delivery' => [
                                'email_sent' => $emailSent,
                                'popup_available' => true,
                            ],
                        ];
                    }
                });

                $inserted++;
                if ($credentialPayload) {
                    $credentials[] = $credentialPayload;
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('Import Employee row gagal', [
                    'row' => $index + 1,
                    'employee_number' => $row['employee_number'] ?? null,
                    'email' => $row['email'] ?? null,
                    'error' => $e->getMessage(),
                ]);
                $errors[] = [
                    'row' => $index + 1,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'inserted' => $inserted,
            'failed' => $failed,
            'errors' => $errors,
            'credentials' => $credentials,
        ];
    }

    private function normalizeImportDateValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }

        if (is_numeric($raw)) {
            $serial = (float) $raw;
            if ($serial > 0) {
                $excelBase = Carbon::create(1899, 12, 30, 0, 0, 0);
                return $excelBase->copy()->addDays((int) floor($serial))->toDateString();
            }
        }

        $raw = str_replace('.', '/', $raw);
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y', 'Y/m/d'];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $raw);
                if ($parsed !== false) {
                    return $parsed->toDateString();
                }
            } catch (\Throwable $e) {
            }
        }

        $timestamp = strtotime($raw);
        if ($timestamp !== false) {
            return Carbon::createFromTimestamp($timestamp)->toDateString();
        }

        return trim((string) $value);
    }

    private function normalizeEmployeeStatusValue(mixed $value): string
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return '';
        }

        $key = preg_replace('/[^a-z]/', '', Str::lower($normalized)) ?? '';
        $map = [
            'PKWTT' => ['pkwtt', 'tetap', 'permanent', 'active', 'aktif'],
            'PKWT' => ['pkwt', 'kontrak', 'contract', 'inactive', 'nonactive', 'nonaktif', 'resigned', 'resign', 'terminated', 'terminate', 'phk', 'leave', 'cuti'],
            'OS' => ['os', 'outsourcing', 'outsource'],
        ];

        foreach ($map as $target => $aliases) {
            foreach ($aliases as $alias) {
                $aliasKey = preg_replace('/[^a-z]/', '', $alias) ?? '';
                if ($aliasKey !== '' && $aliasKey === $key) {
                    return $target;
                }
            }
        }

        return mb_strtoupper($normalized);
    }

    private function buildImportPreview(array $rows): array
    {
        $previewRows = [];
        $missingMaster = [
            'companies' => [],
            'divisions' => [],
            'departments' => [],
            'positions' => [],
            'placements' => [],
            'levels' => [],
            'employee_statuses' => [],
        ];

        $emailsInFile = [];
        $whatsappsInFile = [];
        $employeeNumbersInFile = [];
        $nameDivisionInFile = [];

        $existingEmployees = Employee::query()
            ->with('division:id,name')
            ->get(['id', 'email', 'whatsapp', 'employee_number', 'name', 'division_id']);

        $existingEmails = $existingEmployees
            ->pluck('email')
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->filter()
            ->flip();

        $existingWhatsapps = $existingEmployees
            ->pluck('whatsapp')
            ->map(fn ($whatsapp) => $this->normalizeWhatsappKey((string) $whatsapp))
            ->filter()
            ->flip();

        $existingEmployeeNumbers = $existingEmployees
            ->pluck('employee_number')
            ->map(fn ($employeeNumber) => Str::lower(trim((string) $employeeNumber)))
            ->filter()
            ->flip();

        $existingNameDivisionPairs = [];
        foreach ($existingEmployees as $existingEmployee) {
            $existingName = trim((string) $existingEmployee->name);
            $existingDivision = trim((string) ($existingEmployee->division?->name ?? ''));
            if ($existingName === '' || $existingDivision === '') {
                continue;
            }

            $key = Str::lower($existingName) . '|' . Str::lower($existingDivision);
            $existingNameDivisionPairs[$key] = true;
        }

        foreach ($rows as $index => $row) {
            $normalized = $this->normalizeImportRow($row);
            $errors = [];

            $this->appendRequiredFieldErrors($normalized, $errors);
            $this->appendDateEmailErrors($normalized, $existingEmails, $emailsInFile, $existingWhatsapps, $whatsappsInFile, $existingEmployeeNumbers, $employeeNumbersInFile, $errors);

            $this->appendManagerErrors($normalized, $errors);
            $this->appendMissingMasterErrors($normalized, $errors, $missingMaster);
            $this->appendNameDivisionDuplicateErrors($normalized, $existingNameDivisionPairs, $nameDivisionInFile, $errors);

            $previewRows[] = [
                'row_number' => $index + 1,
                'data' => $normalized,
                'errors' => $errors,
                'valid' => empty($errors),
            ];
        }

        foreach ($missingMaster as $category => $values) {
            $missingMaster[$category] = collect($values)
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $validRows = collect($previewRows)->where('valid', true)->count();

        return [
            'rows' => $previewRows,
            'summary' => [
                'total' => count($previewRows),
                'valid' => $validRows,
                'invalid' => count($previewRows) - $validRows,
            ],
            'missing_master' => $missingMaster,
        ];
    }

    private function importRequiredFieldMessages(): array
    {
        return [
                'employee_number' => 'NIP wajib diisi',
            'date_joined' => 'Tanggal Masuk wajib diisi',
            'induction_date' => 'Tanggal Induction wajib diisi',
            'name' => 'Nama Employee wajib diisi',
            'email' => 'Email wajib diisi',
            'whatsapp' => 'Whatsapp wajib diisi',
            'manager_functional_input' => 'Manager Fungsional wajib diisi',
            'company' => 'Perusahaan wajib diisi',
            'division' => 'Divisi wajib diisi',
            'department' => 'Departemen wajib diisi',
            'position' => 'Jabatan wajib diisi',
            'placement' => 'Penempatan wajib diisi',
            'level' => 'Golongan wajib diisi',
            'employee_status' => 'Status Pegawai wajib diisi',
        ];
    }

    private function appendRequiredFieldErrors(array $normalized, array &$errors): void
    {
        foreach ($this->importRequiredFieldMessages() as $field => $message) {
            if (($normalized[$field] ?? null) === '' || ($normalized[$field] ?? null) === null) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                    'type' => 'validation',
                ];
            }
        }
    }

    private function appendDateEmailErrors(
        array $normalized,
        \Illuminate\Support\Collection $existingEmails,
        array &$emailsInFile,
        \Illuminate\Support\Collection $existingWhatsapps,
        array &$whatsappsInFile,
        \Illuminate\Support\Collection $existingEmployeeNumbers,
        array &$employeeNumbersInFile,
        array &$errors
    ): void
    {
        if (($normalized['date_joined'] ?? '') !== '' && !strtotime((string) $normalized['date_joined'])) {
            $errors[] = ['field' => 'date_joined', 'message' => 'Format Tanggal Masuk tidak valid', 'type' => 'validation'];
        }

        if (($normalized['induction_date'] ?? '') !== '' && !strtotime((string) $normalized['induction_date'])) {
            $errors[] = ['field' => 'induction_date', 'message' => 'Format Tanggal Induction tidak valid', 'type' => 'validation'];
        }

        if (($normalized['email'] ?? '') !== '' && !filter_var($normalized['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['field' => 'email', 'message' => 'Format email tidak valid', 'type' => 'validation'];
        }

        $emailKey = Str::lower((string) ($normalized['email'] ?? ''));
        if ($emailKey !== '') {
            if (isset($existingEmails[$emailKey])) {
                $errors[] = ['field' => 'email', 'message' => 'Email sudah terdaftar di sistem', 'type' => 'validation'];
            }
            if (isset($emailsInFile[$emailKey])) {
                $errors[] = ['field' => 'email', 'message' => 'Email duplikat pada file import', 'type' => 'validation'];
            }

            $emailsInFile[$emailKey] = true;
        }

        $whatsappKey = $this->normalizeWhatsappKey((string) ($normalized['whatsapp'] ?? ''));
        if ($whatsappKey !== '') {
            if (isset($existingWhatsapps[$whatsappKey])) {
                $errors[] = ['field' => 'whatsapp', 'message' => 'Nomor Whatsapp sudah terdaftar di sistem', 'type' => 'validation'];
            }
            if (isset($whatsappsInFile[$whatsappKey])) {
                $errors[] = ['field' => 'whatsapp', 'message' => 'Nomor Whatsapp duplikat pada file import', 'type' => 'validation'];
            }
            $whatsappsInFile[$whatsappKey] = true;
        }

        $employeeNumberKey = Str::lower(trim((string) ($normalized['employee_number'] ?? '')));
        if ($employeeNumberKey !== '') {
            if (isset($existingEmployeeNumbers[$employeeNumberKey])) {
                $errors[] = ['field' => 'employee_number', 'message' => 'NIP sudah terdaftar di sistem', 'type' => 'validation'];
            }
            if (isset($employeeNumbersInFile[$employeeNumberKey])) {
                $errors[] = ['field' => 'employee_number', 'message' => 'NIP duplikat pada file import', 'type' => 'validation'];
            }
            $employeeNumbersInFile[$employeeNumberKey] = true;
        }
    }

    private function appendNameDivisionDuplicateErrors(array $normalized, array $existingNameDivisionPairs, array &$nameDivisionInFile, array &$errors): void
    {
        $name = trim((string) ($normalized['name'] ?? ''));
        $division = trim((string) ($normalized['division'] ?? ''));

        if ($name === '' || $division === '') {
            return;
        }

        $key = Str::lower($name) . '|' . Str::lower($division);

        if (isset($existingNameDivisionPairs[$key])) {
            $errors[] = [
                'field' => 'name',
                'message' => "Nama sama persis sudah terdaftar pada Divisi {$division}",
                'type' => 'validation',
            ];
        }

        if (isset($nameDivisionInFile[$key])) {
            $errors[] = [
                'field' => 'name',
                'message' => "Nama duplikat dalam file import pada Divisi {$division}",
                'type' => 'validation',
            ];
        }

        $nameDivisionInFile[$key] = true;
    }

    private function appendManagerErrors(array &$normalized, array &$errors): void
    {
        $managerFunctionalId = $this->resolveManagerId((string) ($normalized['manager_functional_input'] ?? ''));
        $managerOperationalId = $this->resolveManagerId((string) ($normalized['manager_operational_input'] ?? ''));

        if (($normalized['manager_functional_input'] ?? '') !== '' && !$managerFunctionalId) {
            $errors[] = ['field' => 'manager_functional_input', 'message' => 'Manager Fungsional tidak ditemukan', 'type' => 'validation'];
        }

        if (($normalized['manager_operational_input'] ?? '') !== '' && !$managerOperationalId) {
            $errors[] = ['field' => 'manager_operational_input', 'message' => 'Manager Operasional tidak ditemukan', 'type' => 'validation'];
        }

        $normalized['manager_functional_id'] = $managerFunctionalId;
        $normalized['manager_operational_id'] = $managerOperationalId;
    }

    private function appendMissingMasterErrors(array $normalized, array &$errors, array &$missingMaster): void
    {
        $masterChecks = [
            'company' => ['category' => 'companies', 'exists' => MasterCompany::where('name', (string) ($normalized['company'] ?? ''))->exists()],
            'division' => ['category' => 'divisions', 'exists' => MasterDivision::where('name', (string) ($normalized['division'] ?? ''))->exists()],
            'department' => ['category' => 'departments', 'exists' => MasterDepartment::where('name', (string) ($normalized['department'] ?? ''))->exists()],
            'position' => ['category' => 'positions', 'exists' => MasterPosition::where('name', (string) ($normalized['position'] ?? ''))->exists()],
            'placement' => ['category' => 'placements', 'exists' => MasterPlacement::where('name', (string) ($normalized['placement'] ?? ''))->exists()],
            'level' => ['category' => 'levels', 'exists' => MasterLevel::where('name', (string) ($normalized['level'] ?? ''))->exists()],
            'employee_status' => ['category' => 'employee_statuses', 'exists' => MasterEmployeeStatus::whereRaw('LOWER(name) = ?', [Str::lower((string) ($normalized['employee_status'] ?? ''))])->exists()],
        ];

        foreach ($masterChecks as $field => $config) {
            $value = (string) ($normalized[$field] ?? '');
            if ($value === '') {
                continue;
            }

            if (!$config['exists']) {
                $errors[] = [
                    'field' => $field,
                    'message' => 'Data master belum tersedia',
                    'type' => 'master_missing',
                    'category' => $config['category'],
                    'value' => $value,
                ];
                $missingMaster[$config['category']][] = $value;
            }
        }
    }

    private function confirmImportRows(array $rows, array $addMissingMaster): array
    {
        if (!empty($addMissingMaster['companies'])) {
            $values = collect($rows)->pluck('company')->map(fn ($v) => trim((string) $v))->filter()->unique();
            foreach ($values as $value) {
                MasterCompany::firstOrCreate(['name' => $value]);
            }
        }

        if (!empty($addMissingMaster['divisions'])) {
            $values = collect($rows)->pluck('division')->map(fn ($v) => trim((string) $v))->filter()->unique();
            foreach ($values as $value) {
                MasterDivision::firstOrCreate(['name' => $value]);
            }
        }

        if (!empty($addMissingMaster['departments'])) {
            $values = collect($rows)->pluck('department')->map(fn ($v) => trim((string) $v))->filter()->unique();
            foreach ($values as $value) {
                MasterDepartment::firstOrCreate(['name' => $value]);
            }
        }

        if (!empty($addMissingMaster['positions'])) {
            $values = collect($rows)->pluck('position')->map(fn ($v) => trim((string) $v))->filter()->unique();
            foreach ($values as $value) {
                MasterPosition::firstOrCreate(['name' => $value]);
            }
        }

        if (!empty($addMissingMaster['placements'])) {
            $values = collect($rows)->pluck('placement')->map(fn ($v) => trim((string) $v))->filter()->unique();
            foreach ($values as $value) {
                MasterPlacement::firstOrCreate(['name' => $value]);
            }
        }

        if (!empty($addMissingMaster['levels'])) {
            $values = collect($rows)->pluck('level')->map(fn ($v) => trim((string) $v))->filter()->unique();
            foreach ($values as $value) {
                MasterLevel::firstOrCreate(['name' => $value]);
            }
        }

        if (!empty($addMissingMaster['employee_statuses'])) {
            $values = collect($rows)->pluck('employee_status')->map(fn ($v) => $this->normalizeEmployeeStatusValue($v))->filter()->unique();
            foreach ($values as $value) {
                MasterEmployeeStatus::firstOrCreate(['name' => $value]);
            }
        }

        return $this->importRows($rows);
    }

    private function normalizeImportRow(array $row): array
    {
        return [
            'employee_number' => trim((string) ($row['employee_number'] ?? $row['nip'] ?? '')),
            'date_joined' => $this->normalizeImportDateValue($row['date_joined'] ?? ''),
            'induction_date' => $this->normalizeImportDateValue($row['induction_date'] ?? ''),
            'name' => trim((string) ($row['name'] ?? '')),
            'email' => trim((string) ($row['email'] ?? '')),
            'whatsapp' => trim((string) ($row['whatsapp'] ?? '')),
            'manager_functional_input' => trim((string) ($row['manager_functional_input'] ?? $row['manager_functional_id'] ?? '')),
            'manager_operational_input' => trim((string) ($row['manager_operational_input'] ?? $row['manager_operational_id'] ?? '')),
            'company' => trim((string) ($row['company'] ?? '')),
            'division' => trim((string) ($row['division'] ?? '')),
            'department' => trim((string) ($row['department'] ?? '')),
            'position' => trim((string) ($row['position'] ?? '')),
            'placement' => trim((string) ($row['placement'] ?? '')),
            'level' => trim((string) ($row['level'] ?? '')),
            'employee_status' => $this->normalizeEmployeeStatusValue($row['employee_status'] ?? ''),
        ];
    }

    private function resolveManagerId(string $value): ?int
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        if (ctype_digit($normalized)) {
            $manager = Manager::find((int) $normalized);
            if ($manager) {
                return (int) $manager->id;
            }
        }

        $managerByEmail = Manager::whereRaw('LOWER(email) = ?', [Str::lower($normalized)])->first();
        if ($managerByEmail) {
            return (int) $managerByEmail->id;
        }

        $managerByName = Manager::whereRaw('LOWER(name) = ?', [Str::lower($normalized)])->first();
        if ($managerByName) {
            return (int) $managerByName->id;
        }

        return null;
    }

    private function generateSimpleXlsxBinary(array $rows): string
    {
        $escape = function (string $value): string {
            return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
        };

        $sheetRowsXml = '';
        foreach ($rows as $rowIndex => $cells) {
            $r = $rowIndex + 1;
            $sheetRowsXml .= '<row r="' . $r . '">';
            foreach (array_values($cells) as $cellIndex => $value) {
                $col = '';
                $index = $cellIndex;
                do {
                    $col = chr(($index % 26) + 65) . $col;
                    $index = intdiv($index, 26) - 1;
                } while ($index >= 0);

                $sheetRowsXml .= '<c r="' . $col . $r . '" t="inlineStr"><is><t>' . $escape((string) $value) . '</t></is></c>';
            }
            $sheetRowsXml .= '</row>';
        }

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetRowsXml . '</sheetData>'
            . '</worksheet>';

        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Template" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf/></cellStyleXfs>'
            . '<cellXfs count="1"><xf/></cellXfs>'
            . '</styleSheet>';

        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new ZipArchive();
        $zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $contentTypesXml);
        $zip->addFromString('_rels/.rels', $relsXml);
        $zip->addFromString('xl/workbook.xml', $workbookXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->addFromString('xl/styles.xml', $stylesXml);
        $zip->close();

        $binary = file_get_contents($tmpFile) ?: '';
        @unlink($tmpFile);

        return $binary;
    }

    private function parseImportFile(string $path, string $ext): array
    {
        $ext = Str::lower($ext);

        if (in_array($ext, ['csv', 'txt'], true)) {
            $rows = [];
            if (($handle = fopen($path, 'r')) !== false) {
                $headers = fgetcsv($handle) ?: [];
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $this->mapImportRow(array_combine($headers, $data));
                }
                fclose($handle);
            }
            return $rows;
        }

        if ($ext === 'xlsx') {
            return $this->parseXlsxRows($path);
        }

        return [];
    }

    private function parseXlsxRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml) {
            $xml = simplexml_load_string($sharedXml);
            foreach ($xml->si as $si) {
                $sharedStrings[] = (string) ($si->t ?? '');
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXml) {
            $zip->close();
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        $rows = [];
        $headers = [];

        foreach ($sheet->sheetData->row as $rowIndex => $row) {
            $cellsByIndex = [];
            foreach ($row->c as $cell) {
                $raw = (string) ($cell->v ?? '');
                if ((string) $cell['t'] === 's') {
                    $raw = $sharedStrings[(int) $raw] ?? '';
                }

                $cellRef = (string) ($cell['r'] ?? '');
                $columnRef = preg_replace('/\d+/', '', $cellRef) ?: 'A';
                $columnIndex = $this->excelColumnIndex($columnRef);

                $cellsByIndex[$columnIndex] = trim($raw);
            }

            if (empty($cellsByIndex)) {
                continue;
            }

            ksort($cellsByIndex);
            $maxIndex = max(array_keys($cellsByIndex));
            $cells = array_fill(0, $maxIndex + 1, '');
            foreach ($cellsByIndex as $index => $value) {
                $cells[$index] = $value;
            }

            if ((int) $row['r'] === 1) {
                $headers = $cells;
                continue;
            }

            if (empty(array_filter($cells))) {
                continue;
            }

            $rows[] = $this->mapImportRow(array_combine($headers, array_pad($cells, count($headers), '')) ?: []);
        }

        $zip->close();
        return $rows;
    }

    private function excelColumnIndex(string $column): int
    {
        $column = Str::upper(trim($column));
        if ($column === '') {
            return 0;
        }

        $index = 0;
        foreach (str_split($column) as $char) {
            if ($char < 'A' || $char > 'Z') {
                continue;
            }

            $index = ($index * 26) + (ord($char) - 64);
        }

        return max(0, $index - 1);
    }

    private function mapImportRow(array $row): array
    {
        $get = function (array $keys) use ($row) {
            foreach ($keys as $key) {
                foreach ($row as $column => $value) {
                    if (Str::lower(trim((string) $column)) === Str::lower($key)) {
                        return $value;
                    }
                }
            }
            return null;
        };

        return [
            'employee_number' => $get(['nip', 'employee_number', 'nomor pegawai']),
            'date_joined' => $get(['tanggal masuk', 'date_joined']),
            'induction_date' => $get(['tanggal induction', 'induction_date']),
            'name' => $get(['nama employee', 'name']),
            'email' => $get(['email']),
            'whatsapp' => $get(['whatsapp', 'nomor whatsapp']),
            'manager_functional_input' => $get(['manager fungsional', 'manager_functional', 'manager_functional_id']),
            'manager_operational_input' => $get(['manager operasional', 'manager_operational', 'manager_operational_id']),
            'company' => $get(['perusahaan', 'company']),
            'division' => $get(['divisi', 'division']),
            'department' => $get(['departemen', 'department']),
            'position' => $get(['jabatan', 'position']),
            'placement' => $get(['penempatan', 'placement']),
            'level' => $get(['golongan', 'level']),
            'employee_status' => $this->normalizeEmployeeStatusValue($get(['status pegawai', 'employee_status']) ?? ''),
        ];
    }
}
