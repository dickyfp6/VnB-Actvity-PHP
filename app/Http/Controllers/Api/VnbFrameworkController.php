<?php

namespace App\Http\Controllers\Api;

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

    private function getFrameworkPositions()
    {
        if (Schema::hasTable('employees') && Schema::hasTable('master_positions')) {
            $positions = DB::table('employees as e')
                ->join('master_positions as mp', 'mp.id', '=', 'e.position_id')
                ->select('mp.id', 'mp.name')
                ->distinct()
                ->orderBy('mp.name')
                ->get();

            if ($positions->isNotEmpty()) {
                return $positions;
            }
        }

        if (!Schema::hasTable('master_positions')) {
            return collect();
        }

        return DB::table('master_positions')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    private function getStageConfigs()
    {
        $stageRows = DB::table('vnb_framework_stage_configs')->orderBy('id')->get();

        return $stageRows->map(function ($row) {
            $positions = DB::table('vnb_framework_stage_position_maps as maps')
                ->join('master_positions as mp', 'mp.id', '=', 'maps.position_id')
                ->where('maps.stage_config_id', $row->id)
                ->orderBy('mp.name')
                ->get(['mp.id', 'mp.name']);

            $phases = DB::table('vnb_framework_stage_phases')
                ->where('stage_config_id', $row->id)
                ->orderBy('phase_order')
                ->get(['phase_order', 'duration_months']);

            return [
                'id' => (int) $row->id,
                'career_stage' => (string) $row->career_stage,
                'label' => (string) $row->label,
                'max_integrations' => (int) $row->max_integrations,
                'position_ids' => $positions->pluck('id')->map(fn ($id) => (int) $id)->values(),
                'position_names' => $positions->pluck('name')->values(),
                'phases' => $phases,
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
        $positions = $this->getFrameworkPositions();
        $globalBehaviours = DB::table('vnb_framework_behaviours')->orderBy('name')->pluck('name')->values();

        if ($stageConfigs->isEmpty()) {
            return response()->json([
                'success' => true,
                'setup_required' => true,
                'message' => 'Belum dibuat. Yuk siapkan VnB Framework kamu!',
                'stages' => [],
                'behaviours' => $globalBehaviours,
                'positions' => $positions,
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

        return response()->json([
            'success' => true,
            'setup_required' => false,
            'career_stage' => $selectedStageCode,
            'career_stage_label' => $selectedStageLabel,
            'behaviours' => $behaviours,
            'stages' => $stageConfigs,
            'positions' => $positions,
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
            'stages.*.position_ids' => 'required|array|min:1',
            'stages.*.position_ids.*' => 'required|integer|exists:master_positions,id',
        ]);

        $availablePositionIds = $this->getFrameworkPositions()->pluck('id')->map(fn ($id) => (int) $id)->values();
        $selectedPositionIds = collect($validated['stages'])
            ->flatMap(fn ($row) => $row['position_ids'])
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($selectedPositionIds->count() !== $selectedPositionIds->unique()->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Satu golongan tidak boleh masuk ke beberapa career stage.',
            ], 422);
        }

        $missingPositionIds = $availablePositionIds->diff($selectedPositionIds)->values();
        if ($missingPositionIds->isNotEmpty()) {
            $missingLabels = $this->getFrameworkPositions()
                ->whereIn('id', $missingPositionIds->all())
                ->pluck('name')
                ->values();

            return response()->json([
                'success' => false,
                'message' => 'Semua golongan harus kebagian stage. Golongan yang belum dipilih: ' . $missingLabels->implode(', '),
            ], 422);
        }

        DB::transaction(function () use ($validated): void {
            DB::table('vnb_framework_stage_position_maps')->delete();
            DB::table('vnb_framework_stage_behaviours')->delete();
            DB::table('vnb_framework_stage_phases')->delete();
            DB::table('vnb_framework_stage_configs')->delete();
            DB::table('vnb_framework_behaviours')->delete();
            DB::table('vnb_framework_items')->delete();

            $now = now();
            $behaviourNames = collect($validated['behaviours'])
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique(fn ($value) => mb_strtolower($value))
                ->values();

            $stageCodes = [];
            $stagePayloads = [];
            foreach (array_values($validated['stages']) as $index => $stageInput) {
                $label = trim((string) $stageInput['label']);
                $code = $this->buildStageCode($label, $stageCodes, $index);
                $stageCodes[] = $code;
                $stagePayloads[] = [
                    'label' => $label,
                    'career_stage' => $code,
                    'position_ids' => collect($stageInput['position_ids'])->map(fn ($id) => (int) $id)->values()->all(),
                ];
            }

            $behaviourRows = $behaviourNames->map(fn ($name) => [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();
            DB::table('vnb_framework_behaviours')->insert($behaviourRows);

            $behaviours = DB::table('vnb_framework_behaviours')->get(['id']);

            foreach ($stagePayloads as $mapping) {
                $code = (string) $mapping['career_stage'];
                $stageId = DB::table('vnb_framework_stage_configs')->insertGetId([
                    'career_stage' => $code,
                    'label' => $mapping['label'],
                    'max_integrations' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $stageBehaviourRows = $behaviours->map(fn ($behaviour) => [
                    'stage_config_id' => $stageId,
                    'behaviour_id' => $behaviour->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();
                DB::table('vnb_framework_stage_behaviours')->insert($stageBehaviourRows);

                $positionRows = collect($mapping['position_ids'])->map(fn ($positionId) => [
                    'stage_config_id' => $stageId,
                    'position_id' => (int) $positionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();
                DB::table('vnb_framework_stage_position_maps')->insert($positionRows);
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
            'career_stage' => 'required|string|in:manage_self_non_staff,manage_self_staff,manage_others,manage_managers',
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
            DB::table('vnb_framework_stage_configs')
                ->where('id', $stage->id)
                ->update([
                    'max_integrations' => (int) $validated['max_integrations'],
                    'updated_at' => $now,
                ]);

            DB::table('vnb_framework_stage_phases')->where('stage_config_id', $stage->id)->delete();
            DB::table('vnb_framework_items')->where('career_stage', $validated['career_stage'])->delete();

            $phaseRows = [];
            foreach (array_values($validated['phases']) as $idx => $phase) {
                $phaseRows[] = [
                    'stage_config_id' => $stage->id,
                    'phase_order' => $idx + 1,
                    'duration_months' => (int) $phase['duration_months'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('vnb_framework_stage_phases')->insert($phaseRows);

            $behaviours = DB::table('vnb_framework_behaviours')
                ->join('vnb_framework_stage_behaviours', 'vnb_framework_stage_behaviours.behaviour_id', '=', 'vnb_framework_behaviours.id')
                ->where('vnb_framework_stage_behaviours.stage_config_id', $stage->id)
                ->orderBy('vnb_framework_behaviours.name')
                ->pluck('vnb_framework_behaviours.name')
                ->values();

            $itemsToInsert = [];
            foreach ($behaviours as $behaviour) {
                foreach ($phaseRows as $phaseRow) {
                    $phaseLabel = 'F' . $phaseRow['phase_order'] . ' (' . $phaseRow['duration_months'] . ' bulan)';
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
            'career_stage' => 'required|string|in:manage_self_non_staff,manage_self_staff,manage_others,manage_managers',
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
