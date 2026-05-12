<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\VnbFrameworkItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillEmployeeCareerStage extends Command
{
    protected $signature = 'vnb:backfill-career-stage {--force : Skip confirmation}';

    protected $description = 'Backfill career_stage column for employees based on VnB framework configuration';

    public function handle()
    {
        // Check if framework has stage configs
        if (!DB::table('vnb_framework_stage_configs')->exists()) {
            $this->error('❌ VnB framework belum dikonfigurasi. Jalankan setup framework terlebih dahulu.');
            return 1;
        }

        $stageConfigs = DB::table('vnb_framework_stage_configs')
            ->pluck('career_stage')
            ->toArray();

        if (empty($stageConfigs)) {
            $this->error('❌ VnB framework tidak memiliki konfigurasi career stage.');
            return 1;
        }

        $this->info('🔍 Framework stages ditemukan:');
        foreach ($stageConfigs as $stage) {
            $this->line("   • {$stage}");
        }

        $this->newLine();

        // Get all employees and derive their career stage
        $employees = Employee::query()
            ->select('id', 'employee_number', 'name', 'level', 'position_id', 'employee_status', 'company', 'career_stage')
            ->with('position')
            ->get();

        $toUpdate = [];
        $skipped = [];

        foreach ($employees as $emp) {
            // Re-instantiate as Eloquent model to access getCareerStage()
            $empModel = Employee::find($emp->id);
            $derived = $empModel->getCareerStage();

            if (!$derived) {
                $skipped[] = [
                    'id' => $emp->id,
                    'number' => $emp->employee_number,
                    'name' => $emp->name,
                    'level' => $emp->level,
                    'reason' => 'No matching career stage in framework'
                ];
                continue;
            }

            if ($emp->career_stage !== $derived) {
                $toUpdate[] = [
                    'id' => $emp->id,
                    'number' => $emp->employee_number,
                    'name' => $emp->name,
                    'current' => $emp->career_stage ?? '(empty)',
                    'new' => $derived,
                ];
            }
        }

        $this->info("📊 Summary:");
        $this->line("   Updates needed: " . count($toUpdate));
        $this->line("   Skipped (no matching stage): " . count($skipped));

        if (count($toUpdate) === 0 && count($skipped) === 0) {
            $this->info('✅ Semua employee sudah up-to-date.');
            return 0;
        }

        if (!empty($toUpdate)) {
            $this->newLine();
            $this->info('📝 Updates:');
            foreach ($toUpdate as $row) {
                $this->line("   • {$row['number']} ({$row['name']}): {$row['current']} → {$row['new']}");
            }
        }

        if (!empty($skipped)) {
            $this->newLine();
            $this->warn('⚠️  Skipped (no framework config):');
            foreach ($skipped as $row) {
                $this->line("   • {$row['number']} ({$row['name']}) [Level: {$row['level']}]");
            }
        }

        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Lanjutkan backfill?')) {
            $this->info('Dibatalkan.');
            return 0;
        }

        // Execute updates
        $count = 0;
        foreach ($toUpdate as $row) {
            Employee::query()->where('id', $row['id'])->update(['career_stage' => $row['new']]);
            $count++;
        }

        $this->info("✅ Backfill selesai. {$count} employee diperbarui.");

        return 0;
    }
}
