<?php

namespace App\Console\Commands;

use App\Models\VnbPlanItem;
use App\Models\VnbFrameworkItem;
use Illuminate\Console\Command;

class LinkPlanItemsToFramework extends Command
{
    protected $signature = 'vnb:link-framework';
    protected $description = 'Link existing plan items to VnB framework items based on behavior';

    public function handle()
    {
        $this->info('Starting to link plan items to framework items...');

        $planItems = VnbPlanItem::whereNull('framework_item_id')->get();

        if ($planItems->isEmpty()) {
            $this->info('✅ All plan items are already linked to framework!');
            return 0;
        }

        $linked = 0;
        $notFound = 0;

        foreach ($planItems as $planItem) {
            // Extract behavior name dari activity_title
            // Format: "Behaviour Name - Phase X"
            $titleParts = explode(' - ', $planItem->activity_title);
            if (empty($titleParts)) {
                $notFound++;
                continue;
            }

            $behaviourName = trim($titleParts[0]);

            // Cari framework item yang match dengan behaviour
            // Assumption: semua plan items ada di framework
            $frameworkItem = VnbFrameworkItem::where('behaviour', $behaviourName)
                ->first();

            if ($frameworkItem) {
                $planItem->update(['framework_item_id' => $frameworkItem->id]);
                $linked++;
                $this->line("✓ Linked: {$planItem->activity_title}");
            } else {
                $notFound++;
                $this->line("✗ Not found: {$behaviourName}", 'error');
            }
        }

        $this->info("\n📊 Linking completed!");
        $this->info("✅ Linked: $linked");
        $this->info("❌ Not found: $notFound");

        return 0;
    }
}
