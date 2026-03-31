<?php

namespace Database\Seeders;

use App\Models\VnbFrameworkItem;
use Illuminate\Database\Seeder;

class VnbFrameworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careerStages = [
            'manage_self_non_staff',
            'manage_self_staff',
            'manage_others',
            'manage_managers',
            'manage_function',
        ];

        $behaviours = [
            'Empathy',
            'Be A Wismilak Ambassador',
            'Effective & Efficient',
            'Speak with Data',
            'Collaborative',
            'Decisive',
            'Open Mind',
        ];

        $phases = ['1-3', '4-6', '6+'];

        // Clear existing data
        VnbFrameworkItem::truncate();

        // Create combination of all career stages, behaviours, and phases
        foreach ($careerStages as $stage) {
            foreach ($behaviours as $behaviour) {
                foreach ($phases as $phase) {
                    VnbFrameworkItem::create([
                        'career_stage'  => $stage,
                        'behaviour'     => $behaviour,
                        'phase'         => $phase,
                        'integration_1' => null,
                        'integration_2' => null,
                    ]);
                }
            }
        }

        echo "V&B Framework seeder completed: 175 items created (5 stages × 7 behaviours × 5 phases)\n";
    }
}
