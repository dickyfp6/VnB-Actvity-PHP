<?php

namespace App\Console\Commands;

use App\Models\VnbPlanItem;
use App\Models\VnbPlan;
use Illuminate\Console\Command;

class DebugPlanItems extends Command
{
    protected $signature = 'debug:plan-items {plan_id?}';
    protected $description = 'Debug VnB plan items data';

    public function handle()
    {
        $planId = $this->argument('plan_id') ?? 1;
        
        $plan = VnbPlan::find($planId);
        if (!$plan) {
            $this->error("Plan ID {$planId} not found");
            return 1;
        }

        $this->info("=== PLAN ITEMS DEBUG ===\n");
        $this->line("Plan: {$plan->title} (ID: {$plan->id})");
        $this->line("Total Items: {$plan->items()->count()}\n");

        foreach ($plan->items as $item) {
            $this->info("Item ID: {$item->id}");
            $this->line("  Activity Title: {$item->activity_title}");
            $this->line("  Description (raw): '{$item->description}'");
            $this->line("  Deliverables: '{$item->deliverables}'");
            
            // Parse integrations
            $integrations = !$item->description 
                ? ['-'] 
                : array_filter(array_map('trim', explode('|', $item->description)));
            
            $this->line("  Integrations parsed: " . count($integrations));
            foreach ($integrations as $idx => $int) {
                $this->line("    [{$idx}]: '{$int}'");
            }
            $this->line("");
        }
    }
}
