<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VnbFrameworkItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VnbFrameworkController extends Controller
{
    private const DEFAULT_STAGES = [
        'manage_self_non_staff' => 'Manage Self (Non-Staff)',
        'manage_self_staff' => 'Manage Self (Staff dan Supervisor)',
        'manage_others' => 'Manage Other (Manager)',
        'manage_managers' => 'Manage Manager (Direktur)',
    ];

    private function getFrameworkLevels()
    {
        // 1. Get unique level strings from employees table
        $employeeLevels = DB::table('employees')
            ->select('level')
            ->whereNotNull('level')
            ->where('level', '!=', '')
            ->distinct()
            ->orderBy('level')
            ->pluck('level')
            ->map(fn($v) => trim((string)$v))
            ->filter()
            ->values();

        if ($employeeLevels->isEmpty()) {
            return collect();
        }

        $now = now();
        
        // 2. Ensure each level string exists in master_levels (auto-sync)
        foreach ($employeeLevels as $name) {
            $exists = DB::table('master_levels')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->exists();

            if (!$exists) {
                DB::table('master_levels')->insert([
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 3. Return the mapped objects from master_levels
        // Include both current employee levels AND levels already mapped to stages
        $mappedLevelIds = DB::table('vnb_framework_stage_level_maps')->pluck('level_id');
        $activeNames = collect($employeeLevels)->map(fn($n) => mb_strtolower($n))->toArray();

        return DB::table('master_levels')
            ->whereIn('name', $employeeLevels)
            ->orWhereIn('id', $mappedLevelIds)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($row) use ($activeNames) {
                return [
                    'id' => (int) $row->id,
                    'name' => (string) $row->name,
                    'is_active' => in_array(mb_strtolower($row->name), $activeNames, true),
                ];
            })
            ->values();
    }

    public function saveBehaviours(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'behaviours' => 'required|array|min:1',
            'behaviours.*' => 'required|string|max:120|distinct',
        ]);

        $now = now();
        $behaviourNames = collect($validated['behaviours'])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => mb_strtolower($value))
            ->values();

        DB::transaction(function () use ($behaviourNames, $now): void {
            // Clear existing behaviours and related data
            DB::table('vnb_framework_stage_behaviours')->delete();
            DB::table('vnb_framework_behaviours')->delete();

            // Insert new behaviours with sort order
            $behaviourRows = $behaviourNames->map(fn ($name, $index) => [
                'name' => $name,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();
            DB::table('vnb_framework_behaviours')->insert($behaviourRows);
        });

        return response()->json([
            'success' => true,
            'message' => 'Behaviour berhasil disimpan. Lanjut ke step 2 untuk setup career stage.',
        ]);
    }

    private function getStageConfigs()
    {
        $stageRows = DB::table('vnb_framework_stage_configs')->orderBy('sort_order')->orderBy('id')->get();

        return $stageRows->map(function ($row) {
            $phases = DB::table('vnb_framework_stage_phases')
                ->where('stage_config_id', $row->id)
                ->orderBy('phase_order')
                ->get(['phase_order', 'duration_months']);

            $levelIds = DB::table('vnb_framework_stage_level_maps')
                ->where('stage_config_id', $row->id)
                ->pluck('level_id')
                ->map(fn($id) => (int)$id)
                ->values();

            return [
                'id' => (int) $row->id,
                'career_stage' => (string) $row->career_stage,
                'label' => (string) $row->label,
                'max_integrations' => (int) $row->max_integrations,
                'phases' => $phases,
                'level_ids' => $levelIds,
            ];
        })->values();
    }

    private function buildStageCode(string $label, array $existingCodes, int $fallbackIndex): string
    {
        $base = Str::slug(trim($label), '_');
        if ($base === '') {
            $base = 'stage_' . ($fallbackIndex + 1);
        }

        $code = $base;
        $suffix = 2;

        while (in_array($code, $existingCodes, true)) {
            $code = $base . '_' . $suffix;
            $suffix++;
        }

        return $code;
    }

    private function buildIntegrationTemplates(string $behaviour, string $phaseLabel, int $maxIntegrations): array
    {
        $safeMax = max(1, $maxIntegrations);
        $templates = [];

        for ($i = 1; $i <= $safeMax; $i++) {
            $templates[] = $behaviour . ' | ' . $phaseLabel . ' | Integrasi ' . $i;
        }

        return $templates;
    }

    public function index(Request $request): JsonResponse
    {
        $careerStage = (string) $request->get('career_stage', 'manage_self_non_staff');
        $stageConfigs = $this->getStageConfigs();
        $levels = $this->getFrameworkLevels();
        $globalBehaviours = DB::table('vnb_framework_behaviours')
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 999999 ELSE sort_order END')
            ->orderBy('id')
            ->pluck('name')
            ->values();

        if ($stageConfigs->isEmpty()) {
            // Check if behaviours exist (Step 1 complete, waiting for Step 2)
            $hasBehaviours = $globalBehaviours->isNotEmpty();
            
            return response()->json([
                'success' => true,
                'setup_required' => true,
                'setup_step' => $hasBehaviours ? 'step_2_pending' : 'step_1_pending',
                'message' => $hasBehaviours ? 'Lanjut setup career stage' : 'Belum dibuat. Yuk siapkan VnB Framework kamu!',
                'stages' => [],
                'behaviours' => $globalBehaviours,
                'levels' => $levels,
            ]);
        }

        $selectedStage = $stageConfigs->firstWhere('career_stage', $careerStage) ?? $stageConfigs->first();
        $selectedStageCode = (string) ($selectedStage['career_stage'] ?? 'manage_self_non_staff');
        $selectedStageLabel = (string) ($selectedStage['label'] ?? $selectedStageCode);

        $behaviours = $globalBehaviours;

        $items = VnbFrameworkItem::where('career_stage', $selectedStageCode)
            ->orderBy('behaviour')
            ->orderBy('phase')
            ->get();

        // Check if framework is incomplete (new levels added to company but not assigned to a stage)
        $employeeLevels = DB::table('employees')
            ->whereNotNull('level')
            ->where('level', '!=', '')
            ->distinct()
            ->pluck('level');

        $activeLevelIds = DB::table('master_levels')
            ->whereIn('name', $employeeLevels)
            ->pluck('id');

        $assignedLevelsCount = DB::table('vnb_framework_stage_level_maps')
            ->whereIn('level_id', $activeLevelIds)
            ->distinct('level_id')
            ->count();

        $frameworkIncomplete = ($activeLevelIds->count() > $assignedLevelsCount);

        return response()->json([
            'success' => true,
            'setup_required' => false,
            'setup_step' => 'complete',
            'framework_incomplete' => $frameworkIncomplete,
            'career_stage' => $selectedStageCode,
            'career_stage_label' => $selectedStageLabel,
            'behaviours' => $behaviours,
            'stages' => $stageConfigs,
            'levels' => $levels,
            'data' => $items,
        ]);
    }

    public function setupInitialize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'behaviours' => 'required|array|min:1',
            'behaviours.*' => 'required|string|max:120',
            'stages' => 'required|array|min:1',
            'stages.*.label' => 'required|string|max:120',
            'stages.*.level_ids' => 'required|array|min:1',
            'stages.*.level_ids.*' => 'required|integer|exists:master_levels,id',
        ]);

        $now = now();
        $existingStageConfigs = DB::table('vnb_framework_stage_configs')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'career_stage', 'label', 'sort_order']);

        $submittedBehaviours = collect($validated['behaviours'])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => mb_strtolower($value))
            ->values();

        $existingBehaviours = DB::table('vnb_framework_behaviours')
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 999999 ELSE sort_order END')
            ->orderBy('id')
            ->pluck('name')
            ->map(fn ($value) => trim((string) $value))
            ->values();

        $canPreserveExistingFramework = $existingStageConfigs->isNotEmpty()
            && $existingStageConfigs->count() === count($validated['stages'])
            && $existingBehaviours->count() === $submittedBehaviours->count()
            && $existingBehaviours->map(fn ($value) => mb_strtolower($value))->values()->all() === $submittedBehaviours->map(fn ($value) => mb_strtolower($value))->values()->all();

        if ($canPreserveExistingFramework) {
            DB::transaction(function () use ($validated, $existingStageConfigs, $now): void {
                // Keep existing stage codes/items intact; only refresh labels and level mappings.
                DB::table('vnb_framework_stage_level_maps')->delete();

                foreach (array_values($validated['stages']) as $index => $stageInput) {
                    $stageRow = $existingStageConfigs[$index] ?? null;
                    if (!$stageRow) {
                        continue;
                    }

                    $label = trim((string) $stageInput['label']);
                    DB::table('vnb_framework_stage_configs')
                        ->where('id', $stageRow->id)
                        ->update([
                            'label' => $label,
                            'sort_order' => $index + 1,
                            'updated_at' => $now,
                        ]);

                    if (!empty($stageInput['level_ids'])) {
                        $levelMapRows = collect($stageInput['level_ids'])->map(fn ($lvlId) => [
                            'stage_config_id' => $stageRow->id,
                            'level_id' => (int) $lvlId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->all();
                        DB::table('vnb_framework_stage_level_maps')->insert($levelMapRows);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Kerangka stage berhasil diperbarui tanpa mengubah integrasi yang sudah ada.',
            ]);
        }

        DB::transaction(function () use ($validated, $now): void {
            // Rebuild behaviour ordering from submitted payload so the UI keeps the entered sequence.
            DB::table('vnb_framework_stage_behaviours')->delete();
            DB::table('vnb_framework_behaviours')->delete();

            $behaviourRows = collect($validated['behaviours'])
                ->map(fn ($value, $index) => trim((string) $value))
                ->filter()
                ->unique(fn ($value) => mb_strtolower($value))
                ->values()
                ->map(fn ($name, $index) => [
                    'name' => $name,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all();

            if (!empty($behaviourRows)) {
                DB::table('vnb_framework_behaviours')->insert($behaviourRows);
            }

            // Only reset stage-related configs
            DB::table('vnb_framework_stage_level_maps')->delete();
            DB::table('vnb_framework_stage_phases')->delete();
            DB::table('vnb_framework_stage_configs')->delete();
            DB::table('vnb_framework_items')->delete();
            
            $stageCodes = [];
            $stagePayloads = [];
            foreach (array_values($validated['stages']) as $index => $stageInput) {
                $label = trim((string) $stageInput['label']);
                $code = $this->buildStageCode($label, $stageCodes, $index);
                $stageCodes[] = $code;
                $stagePayloads[] = [
                    'label' => $label,
                    'career_stage' => $code,
                ];
            }

            // Use behaviours in the same order the user submitted.
            $behaviours = DB::table('vnb_framework_behaviours')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id']);

            foreach ($stagePayloads as $index => $mapping) {
                $code = (string) $mapping['career_stage'];
                $stageId = DB::table('vnb_framework_stage_configs')->insertGetId([
                    'career_stage' => $code,
                    'label' => $mapping['label'],
                    'sort_order' => $index + 1,
                    'max_integrations' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Save level mappings
                $inputStage = $validated['stages'][$index] ?? null;
                if ($inputStage && !empty($inputStage['level_ids'])) {
                    $levelMapRows = collect($inputStage['level_ids'])->map(fn($lvlId) => [
                        'stage_config_id' => $stageId,
                        'level_id' => (int)$lvlId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();
                    DB::table('vnb_framework_stage_level_maps')->insert($levelMapRows);
                }

                $stageBehaviourRows = $behaviours->map(fn ($behaviour) => [
                    'stage_config_id' => $stageId,
                    'behaviour_id' => $behaviour->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();
                DB::table('vnb_framework_stage_behaviours')->insert($stageBehaviourRows);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Kerangka awal framework berhasil dibuat. Lanjut atur fase dan batas integrasi per stage.',
        ]);
    }

    public function saveStageDetails(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'career_stage' => 'required|string|exists:vnb_framework_stage_configs,career_stage',
            'max_integrations' => 'required|integer|min:1|max:20',
            'phases' => 'required|array|min:1',
            'phases.*.duration_months' => 'required|integer|min:1|max:60',
        ]);

        $stage = DB::table('vnb_framework_stage_configs')
            ->where('career_stage', $validated['career_stage'])
            ->first();

        if (!$stage) {
            return response()->json([
                'success' => false,
                'message' => 'Career stage belum diinisialisasi.',
            ], 422);
        }

        DB::transaction(function () use ($validated, $stage): void {
            $now = now();
            $existingPhases = DB::table('vnb_framework_stage_phases')
                ->where('stage_config_id', $stage->id)
                ->orderBy('phase_order')
                ->get(['id', 'phase_order', 'duration_months']);

            $existingPhaseLabels = [];
            foreach ($existingPhases as $phaseRow) {
                $existingPhaseLabels[(int) $phaseRow->phase_order] = sprintf(
                    'Fase %d (%d Bulan)',
                    (int) $phaseRow->phase_order,
                    (int) $phaseRow->duration_months
                );
            }

            $newPhaseRows = [];
            $newPhaseLabels = [];
            foreach (array_values($validated['phases']) as $idx => $phase) {
                $phaseOrder = $idx + 1;
                $duration = (int) ($phase['duration_months'] ?? 1);
                $newPhaseRows[] = [
                    'stage_config_id' => $stage->id,
                    'phase_order' => $phaseOrder,
                    'duration_months' => $duration,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $newPhaseLabels[$phaseOrder] = sprintf('Fase %d (%d Bulan)', $phaseOrder, $duration);
            }

            DB::table('vnb_framework_stage_configs')
                ->where('id', $stage->id)
                ->update([
                    'max_integrations' => (int) $validated['max_integrations'],
                    'updated_at' => $now,
                ]);

            $oldPhaseOrders = $existingPhases->pluck('phase_order')->map(fn ($value) => (int) $value)->all();
            $newPhaseOrders = array_keys($newPhaseLabels);

            $removedOrders = array_values(array_diff($oldPhaseOrders, $newPhaseOrders));
            $keptOrders = array_values(array_intersect($oldPhaseOrders, $newPhaseOrders));

            if (!empty($removedOrders)) {
                foreach ($removedOrders as $removedOrder) {
                    $oldLabel = $existingPhaseLabels[$removedOrder] ?? null;
                    if ($oldLabel) {
                        DB::table('vnb_framework_items')
                            ->where('career_stage', $validated['career_stage'])
                            ->where('phase', $oldLabel)
                            ->delete();
                    }
                }
            }

            // Update kept phase labels so existing integration values stay attached.
            foreach ($keptOrders as $order) {
                $oldLabel = $existingPhaseLabels[$order] ?? null;
                $newLabel = $newPhaseLabels[$order] ?? null;
                if ($oldLabel && $newLabel && $oldLabel !== $newLabel) {
                    DB::table('vnb_framework_items')
                        ->where('career_stage', $validated['career_stage'])
                        ->where('phase', $oldLabel)
                        ->update([
                            'phase' => $newLabel,
                            'updated_at' => $now,
                        ]);
                }
            }

            DB::table('vnb_framework_stage_phases')->where('stage_config_id', $stage->id)->delete();
            DB::table('vnb_framework_stage_phases')->insert($newPhaseRows);

            $behaviours = DB::table('vnb_framework_behaviours')
                ->join('vnb_framework_stage_behaviours', 'vnb_framework_stage_behaviours.behaviour_id', '=', 'vnb_framework_behaviours.id')
                ->where('vnb_framework_stage_behaviours.stage_config_id', $stage->id)
                ->orderBy('vnb_framework_behaviours.name')
                ->pluck('vnb_framework_behaviours.name')
                ->values();

            $existingItems = DB::table('vnb_framework_items')
                ->where('career_stage', $validated['career_stage'])
                ->get()
                ->groupBy(fn ($item) => (string) $item->phase);

            $itemsToInsert = [];
            foreach ($behaviours as $behaviour) {
                foreach ($newPhaseRows as $idx => $phaseRow) {
                    // Use human-friendly phase label: "Fase {n} ({duration} Bulan)"
                    $phaseOrder = ($phaseRow['phase_order'] ?? ($idx + 1));
                    $duration = (int) ($phaseRow['duration_months'] ?? 1);
                    $phaseLabel = sprintf('Fase %d (%d Bulan)', $phaseOrder, $duration);

                    $existingItem = ($existingItems[$phaseLabel] ?? collect())->firstWhere('behaviour', $behaviour);
                    if ($existingItem) {
                        continue;
                    }

                    $integrationTemplates = $this->buildIntegrationTemplates(
                        (string) $behaviour,
                        $phaseLabel,
                        (int) $validated['max_integrations']
                    );

                    $itemsToInsert[] = [
                        'career_stage' => $validated['career_stage'],
                        'behaviour' => $behaviour,
                        'phase' => $phaseLabel,
                        'integration_1' => $integrationTemplates[0] ?? null,
                        'integration_2' => $integrationTemplates[1] ?? null,
                        'integrations' => json_encode($integrationTemplates),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($itemsToInsert)) {
                DB::table('vnb_framework_items')->insert($itemsToInsert);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Detail stage berhasil disimpan dan kerangka aktivitas otomatis dibuat.',
        ]);
    }

    public function resetStageTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'career_stage' => 'required|string|exists:vnb_framework_stage_configs,career_stage',
        ]);

        $stage = DB::table('vnb_framework_stage_configs')
            ->where('career_stage', $validated['career_stage'])
            ->first();

        if (!$stage) {
            return response()->json([
                'success' => false,
                'message' => 'Career stage belum diinisialisasi.',
            ], 422);
        }

        $maxIntegrations = max(1, (int) $stage->max_integrations);

        DB::transaction(function () use ($validated, $maxIntegrations): void {
            $items = VnbFrameworkItem::where('career_stage', $validated['career_stage'])->get();

            foreach ($items as $item) {
                $templates = $this->buildIntegrationTemplates(
                    (string) $item->behaviour,
                    (string) $item->phase,
                    $maxIntegrations
                );

                VnbFrameworkItem::where('id', (int) $item->id)->update([
                    'integrations' => $templates,
                    'integration_1' => $templates[0] ?? null,
                    'integration_2' => $templates[1] ?? null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Integrasi stage berhasil di-reset ke template default.',
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'career_stage'  => 'required|string|max:50',
            'behaviour'     => 'required|string|max:100',
            'phase'         => 'required|string',
            'integration_1' => 'nullable|string',
            'integration_2' => 'nullable|string',
        ]);

        $item = VnbFrameworkItem::updateOrCreate(
            [
                'career_stage' => $validated['career_stage'],
                'behaviour'    => $validated['behaviour'],
                'phase'        => $validated['phase'],
            ],
            [
                'integration_1' => $validated['integration_1'] ?? null,
                'integration_2' => $validated['integration_2'] ?? null,
                'integrations' => null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Integrasi Pengukuran berhasil diperbarui', 'data' => $item]);
    }

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

        VnbFrameworkItem::where('career_stage', $target)->delete();

        foreach ($sourceItems as $item) {
            VnbFrameworkItem::create([
                'career_stage'  => $target,
                'behaviour'     => $item->behaviour,
                'phase'         => $item->phase,
                'integration_1' => $item->integration_1,
                'integration_2' => $item->integration_2,
                'integrations'  => $item->integrations,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Framework berhasil di-clone dari {$source} ke {$target}",
            'cloned_count' => $sourceItems->count(),
        ]);
    }

    public function saveIntegrations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:vnb_framework_items,id',
            'items.*.integrations' => 'required|array|min:1',
            'items.*.integrations.*' => 'nullable|string',
        ]);
        // Additional server-side business validation:
        // - integration_1 (index 0) must be non-empty for every item
        // - integration_2 (index 1) is optional per item, but each career_stage must have at least one non-empty integration_2
        $stageGroups = [];
        foreach ($validated['items'] as $row) {
            $item = VnbFrameworkItem::find($row['id']);
            if (!$item) continue;
            $stage = $item->career_stage;
            $stageGroups[$stage][] = [
                'item' => $item,
                'integrations' => is_array($row['integrations']) ? $row['integrations'] : [],
            ];
        }

        foreach ($stageGroups as $stage => $rows) {
            $hasIntegrasi2 = false;
            foreach ($rows as $r) {
                $normalized = array_map(fn($v) => trim((string)$v), $r['integrations']);
                // ensure integration_1 exists
                if (!isset($normalized[0]) || $normalized[0] === '') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Integrasi 1 wajib diisi untuk semua baris sebelum menyimpan.',
                    ], 422);
                }
                if (isset($normalized[1]) && $normalized[1] !== '') {
                    $hasIntegrasi2 = true;
                }
            }

            if (!$hasIntegrasi2) {
                // If the stage supports at least 2 integrations (max_integrations >= 2), require one filled integration_2
                $maxIntegrations = (int) DB::table('vnb_framework_stage_configs')->where('career_stage', $stage)->value('max_integrations');
                if ($maxIntegrations >= 2) {
                    return response()->json([
                        'success' => false,
                        'message' => "Setidaknya satu Integrasi 2 harus diisi untuk stage {$stage}.",
                    ], 422);
                }
            }
        }

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $row) {
                $item = VnbFrameworkItem::find($row['id']);
                if (!$item) {
                    continue;
                }

                $maxIntegrations = (int) DB::table('vnb_framework_stage_configs')
                    ->where('career_stage', $item->career_stage)
                    ->value('max_integrations');

                if ($maxIntegrations <= 0) {
                    $maxIntegrations = 2;
                }

                $normalized = collect($row['integrations'])
                    ->map(fn ($value) => trim((string) $value))
                    ->take($maxIntegrations)
                    ->values()
                    ->all();

                while (count($normalized) < $maxIntegrations) {
                    $normalized[] = '';
                }

                $item->update([
                    'integrations' => $normalized,
                    'integration_1' => $normalized[0] ?? null,
                    'integration_2' => $normalized[1] ?? null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Integrasi aktivitas berhasil disimpan.',
        ]);
    }
}
