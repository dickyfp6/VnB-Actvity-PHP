<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Manager;
use Carbon\Carbon;

class SyncEmployeesFromSource extends Command
{
    protected $signature = 'employees:sync-from-source {--force : Force sync without confirmation}';

    protected $description = 'Synchronize employees from sync_source_employees table. Updates manager references from sync data.';

    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will re-sync all employees from source data. Continue?')) {
                $this->info('Sync cancelled.');
                return 1;
            }
        }

        $this->info('🔄 Starting employee synchronization from source data...');

        try {
            $sourceEmployees = DB::table('sync_source_employees')->orderBy('employee_number')->get();

            if ($sourceEmployees->isEmpty()) {
                $this->warn('No sync source data found.');
                return 0;
            }

            // Seed manager records from source first so FK lookups can resolve.
            $managerIdMap = $this->buildManagerIdMap($sourceEmployees);
            $this->info('📌 Manager mapping created with ' . count($managerIdMap) . ' managers');

            // Sync employees from source, creating missing rows when needed.
            [$created, $updated] = $this->syncEmployeesWithManagers($sourceEmployees, $managerIdMap);
            
            $this->info('✅ Synchronization completed successfully!');
            $this->info('   - Employees created: ' . $created);
            $this->info('   - Employees updated: ' . $updated);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function buildManagerIdMap($sourceEmployees): array
    {
        $map = [];
        $sourceByName = [];

        foreach ($sourceEmployees as $sourceEmp) {
            $name = trim((string) ($sourceEmp->name ?? ''));
            if ($name !== '') {
                $sourceByName[mb_strtolower($name)] = $sourceEmp;
            }
        }

        foreach ($sourceEmployees as $sourceEmp) {
            $name = trim((string) ($sourceEmp->manager_functional ?? ''));
            if ($name !== '' && $name !== '-') {
                $this->upsertManagerFromSourceName($name, $sourceByName, $map);
            }

            $name = trim((string) ($sourceEmp->manager_operational ?? ''));
            if ($name !== '' && $name !== '-') {
                $this->upsertManagerFromSourceName($name, $sourceByName, $map);
            }

            $selfName = trim((string) ($sourceEmp->name ?? ''));
            if ($selfName !== '' && $this->isManagerLevel((string) ($sourceEmp->level ?? ''))) {
                $manager = $this->upsertManagerFromSourceRow($sourceEmp);
                $map[$selfName] = $manager->id;
            }
        }

        return $map;
    }

    private function upsertManagerFromSourceName(string $managerName, array $sourceByName, array &$map): void
    {
        if (isset($map[$managerName])) {
            return;
        }

        $sourceEmp = $sourceByName[mb_strtolower(trim($managerName))] ?? null;
        if (!$sourceEmp) {
            return;
        }

        $manager = $this->upsertManagerFromSourceRow($sourceEmp);
        $map[$managerName] = $manager->id;
    }

    private function upsertManagerFromSourceRow(object $sourceEmp): Manager
    {
        $divisionId = $this->getDivisionId((string) ($sourceEmp->division ?? ''));
        $departmentId = $this->getDepartmentId($divisionId, (string) ($sourceEmp->department ?? ''));

        return Manager::updateOrCreate(
            ['email' => (string) ($sourceEmp->email ?? '')],
            [
                'name' => (string) ($sourceEmp->name ?? ''),
                'employee_number' => (string) ($sourceEmp->employee_number ?? ''),
                'company' => (string) ($sourceEmp->company ?? ''),
                'division' => (string) ($sourceEmp->division ?? ''),
                'division_id' => $divisionId,
                'department_id' => $departmentId,
                'status' => 'active',
                'user_id' => null,
            ]
        );
    }

    private function syncEmployeesWithManagers($sourceEmployees, array $managerIdMap): array
    {
        $created = 0;
        $updated = 0;

        foreach ($sourceEmployees as $sourceEmp) {
            $payload = $this->buildEmployeePayload($sourceEmp, $managerIdMap);
            $employee = Employee::where('employee_number', $payload['employee_number'])->first();

            if ($employee) {
                $employee->update($payload);
                $updated++;
                continue;
            }

            Employee::create($payload);
            $created++;
        }

        return [$created, $updated];
    }

    private function buildEmployeePayload(object $sourceEmp, array $managerIdMap): array
    {
        $divisionId = $this->getDivisionId((string) ($sourceEmp->division ?? ''));
        $departmentId = $this->getDepartmentId($divisionId, (string) ($sourceEmp->department ?? ''));
        $positionId = $this->getPositionId((string) ($sourceEmp->position ?? ''));

        $managerFunctionalName = trim((string) ($sourceEmp->manager_functional ?? ''));
        $managerOperationalName = trim((string) ($sourceEmp->manager_operational ?? ''));

        return [
            'employee_number' => (string) ($sourceEmp->employee_number ?? ''),
            'name' => (string) ($sourceEmp->name ?? ''),
            'date_joined' => (string) ($sourceEmp->date_joined ?? ''),
            'email' => (string) ($sourceEmp->email ?? ''),
            'whatsapp' => (string) ($sourceEmp->whatsapp ?? ''),
            'company' => (string) ($sourceEmp->company ?? ''),
            'division_id' => $divisionId,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'placement' => (string) ($sourceEmp->placement ?? ''),
            'level' => (string) ($sourceEmp->level ?? ''),
            'employee_status' => (string) ($sourceEmp->employee_status ?? 'PKWTT'),
            'status' => $this->normalizeStatus((string) ($sourceEmp->status ?? 'Aktif')),
            'manager_functional' => $managerFunctionalName !== '-' ? $managerFunctionalName : null,
            'manager_operational' => $managerOperationalName !== '-' ? $managerOperationalName : null,
            'manager_functional_id' => ($managerFunctionalName && $managerFunctionalName !== '-') ? ($managerIdMap[$managerFunctionalName] ?? null) : null,
            'manager_operational_id' => ($managerOperationalName && $managerOperationalName !== '-') ? ($managerIdMap[$managerOperationalName] ?? null) : null,
            'career_stage' => null,
            'vnb_status' => 'not_started',
            'vnb_period_start' => null,
            'vnb_period_end' => null,
            'notes' => null,
        ];
    }

    private function normalizeStatus(string $status): string
    {
        return $this->isInactiveEmployeeStatus($status) ? 'Inactive' : 'Aktif';
    }

    private function isInactiveEmployeeStatus(string $status): bool
    {
        $normalized = mb_strtolower(trim($status));

        return in_array($normalized, ['inactive', 'inaktif', 'tidak aktif', 'nonaktif', 'non-active', 'non active'], true);
    }

    private function getDivisionId(?string $divisionName): ?int
    {
        if (!$divisionName) {
            return null;
        }

        return match ($divisionName) {
            'General' => 1,
            'Human Resource' => 2,
            'Information Technology' => 3,
            'FAT' => 9,
            default => null,
        };
    }

    private function getDepartmentId(?int $divisionId, ?string $departmentName): ?int
    {
        if (!$divisionId || !$departmentName) {
            return null;
        }

        return match ($departmentName) {
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
            default => null,
        };
    }

    private function getPositionId(?string $positionName): ?int
    {
        if (!$positionName) {
            return null;
        }

        return match ($positionName) {
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
            default => null,
        };
    }

    private function isManagerLevel(string $level): bool
    {
        return in_array(mb_strtolower(trim($level)), ['direktur', 'manager'], true);
    }
}
