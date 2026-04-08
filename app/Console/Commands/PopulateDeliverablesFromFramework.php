<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateDeliverablesFromFramework extends Command
{
    protected $signature = 'vnb:populate-deliverables';
    protected $description = 'Populate deliverables for plan items from framework integration fields';

    public function handle()
    {
        $this->info('🔄 Populating deliverables for vnb_plan_items...');

        $items = DB::table('vnb_plan_items')
            ->select('id', 'activity_title', 'description', 'integration_1', 'integration_2', 'deliverables')
            ->get();

        $stats = [
            'total' => count($items),
            'already_valid' => 0,
            'fixed_anomalies' => 0,
            'fixed_empty' => 0,
        ];

        foreach ($items as $item) {
            $current = trim($item->deliverables ?? '');
            
            if ($this->isValidDeliverable($current)) {
                $stats['already_valid']++;
                continue;
            }

            $new = $this->generateDeliverable(
                $item->activity_title,
                $item->description,
                $item->integration_1,
                $item->integration_2
            );

            DB::table('vnb_plan_items')
                ->where('id', $item->id)
                ->update(['deliverables' => $new]);

            if (empty($current)) {
                $stats['fixed_empty']++;
            } else {
                $stats['fixed_anomalies']++;
            }
        }

        $this->outputReport($stats);
        return 0;
    }

    private function isValidDeliverable(string $value): bool
    {
        if (empty($value)) return false;
        
        $lower = strtolower(trim($value));
        $invalid = ['-', 'apakah', 'aa', 'hehe', 'huhuu', 'hmm', 'boleh', 'collab', 'n/a', 'na'];
        
        foreach ($invalid as $p) {
            if (strpos($lower, $p) !== false) return false;
        }
        
        return strlen($value) > 5;
    }

    private function generateDeliverable($title, $description, $int1, $int2): string
    {
        if (!empty($int1) && !empty($int2)) {
            return $int1 . "\n---\n" . $int2;
        }
        if (!empty($int1)) return $int1;
        if (!empty($int2)) return $int2;
        
        $parts = array_map('trim', preg_split('/[\|\/]/', $description));
        if (count($parts) > 1) return implode("\n---\n", $parts);
        if (!empty($description)) return $description;
        
        return "Aktivitas: " . $title;
    }

    private function outputReport(array $stats): void
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════╗');
        $this->line('║   VNB Deliverables Population Complete       ║');
        $this->line('╚══════════════════════════════════════════════╝');
        $this->newLine();
        
        $this->line("📊 Total processed      : {$stats['total']}");
        $this->line("   Already valid       : {$stats['already_valid']}");
        $this->line("   Fixed from empty    : {$stats['fixed_empty']}");
        $this->line("   Fixed anomalies     : {$stats['fixed_anomalies']}");
        
        $fixed = $stats['fixed_empty'] + $stats['fixed_anomalies'];
        if ($fixed > 0) {
            $pct = round(($fixed / $stats['total']) * 100, 1);
            $this->info("✅ Deliverables populated for $fixed items ({$pct}%)");
        } else {
            $this->info('✅ All deliverables already valid!');
        }
    }
}
