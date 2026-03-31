<?php

namespace App\Http\Controllers\Api;

use App\Models\VnbFrameworkItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VnbFrameworkController extends Controller
{
    /**
     * UC002: Return framework grid for a given career_stage
     */
    public function index(Request $request): JsonResponse
    {
        $careerStage = $request->get('career_stage', 'manage_self_non_staff');

        $items = VnbFrameworkItem::where('career_stage', $careerStage)
            ->orderByRaw("CASE behaviour
                WHEN 'Empathy' THEN 1
                WHEN 'Be A Wismilak Ambassador' THEN 2
                WHEN 'Effective & Efficient' THEN 3
                WHEN 'Speak with Data' THEN 4
                WHEN 'Collaborative' THEN 5
                WHEN 'Decisive' THEN 6
                WHEN 'Open Mind' THEN 7
                ELSE 8 END")
            ->orderByRaw("CASE phase WHEN '1-3' THEN 1 WHEN '4-6' THEN 2 WHEN '6+' THEN 3 ELSE 4 END")
            ->get();

        return response()->json([
            'success' => true,
            'career_stage' => $careerStage,
            'career_stage_label' => VnbFrameworkItem::$careerStages[$careerStage] ?? $careerStage,
            'behaviours' => VnbFrameworkItem::$behaviours,
            'phases' => VnbFrameworkItem::$phases,
            'data' => $items,
        ]);
    }

    /**
     * UC002: Update a single cell (integration_1 or integration_2)
     */
    public function upsert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'career_stage'  => 'required|string|max:50',
            'behaviour'     => 'required|string|max:100',
            'phase'         => 'required|string',
            'integration_1' => 'nullable|string',
            'integration_2' => 'nullable|string',
        ]);

        // Normalize phase display format to storage format
        $phaseMap = [
            '1-3 Bulan' => '1-3',
            '4-6 Bulan' => '4-6',
            '>6 Bulan' => '6+',
            '1-3' => '1-3',
            '4-6' => '4-6',
            '6+' => '6+',
        ];
        
        $storagePhase = $phaseMap[$validated['phase']] ?? $validated['phase'];

        $item = VnbFrameworkItem::updateOrCreate(
            [
                'career_stage' => $validated['career_stage'],
                'behaviour'    => $validated['behaviour'],
                'phase'        => $storagePhase,
            ],
            [
                'integration_1' => $validated['integration_1'] ?? null,
                'integration_2' => $validated['integration_2'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Integrasi Pengukuran berhasil diperbarui', 'data' => $item]);
    }

    /**
     * UC002 Scenario B: Clone all framework items from source to target career stage
     */
    public function clone(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_career_stage' => 'required|string|max:50',
            'target_career_stage' => 'required|string|max:50|different:source_career_stage',
        ]);

        $source = $validated['source_career_stage'];
        $target = $validated['target_career_stage'];

        $sourceItems = VnbFrameworkItem::where('career_stage', $source)->get();

        if ($sourceItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Career stage sumber tidak memiliki data'], 422);
        }

        // Delete existing target data, then clone
        VnbFrameworkItem::where('career_stage', $target)->delete();

        foreach ($sourceItems as $item) {
            VnbFrameworkItem::create([
                'career_stage'  => $target,
                'behaviour'     => $item->behaviour,
                'phase'         => $item->phase,
                'integration_1' => $item->integration_1,
                'integration_2' => $item->integration_2,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Framework berhasil di-clone dari {$source} ke {$target}",
            'cloned_count' => $sourceItems->count(),
        ]);
    }
}
