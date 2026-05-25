<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StarSchema;
use App\Models\StarSchemaIndicator;
use App\Models\StarSchemaIndicatorOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StarController extends Controller
{
    private function defaultSchemaTemplate(): array
    {
        return [
            'name' => 'STAR Schema Default',
            'description' => 'Template awal STAR Framework yang bisa diedit bebas.',
            'version' => 1,
            'is_active' => true,
            'indicators' => [
                [
                    'indicator_key' => 'recognition_impact',
                    'label' => 'Tingkat Penghargaan / Rekognisi',
                    'sort_order' => 1,
                    'options' => [
                        ['label' => 'Internal Departemen / Divisi', 'score' => 1.0, 'sort_order' => 1],
                        ['label' => 'Perusahaan / Instansi (Nasional)', 'score' => 2.5, 'sort_order' => 2],
                        ['label' => 'Antar Perusahaan / Global (Internasional)', 'score' => 3.5, 'sort_order' => 3],
                    ],
                ],
                [
                    'indicator_key' => 'result_impact',
                    'label' => 'Skala Dampak Solusi (Result)',
                    'sort_order' => 2,
                    'options' => [
                        ['label' => 'Skala Kecil (Hanya berdampak pada 1 tim/fitur)', 'score' => 1.0, 'sort_order' => 1],
                        ['label' => 'Skala Menengah (Berdampak pada alur kerja satu divisi)', 'score' => 2.0, 'sort_order' => 2],
                        ['label' => 'Skala Besar (Berdampak pada seluruh sistem/efisiensi perusahaan)', 'score' => 3.0, 'sort_order' => 3],
                    ],
                ],
                [
                    'indicator_key' => 'sit_task_complexity',
                    'label' => 'Kompleksitas Masalah (Situation/Task)',
                    'sort_order' => 3,
                    'options' => [
                        ['label' => 'Rendah (Masalah rutin, sudah ada prosedur jelas)', 'score' => 1.0, 'sort_order' => 1],
                        ['label' => 'Sedang (Masalah tidak biasa, butuh analisis tambahan)', 'score' => 2.0, 'sort_order' => 2],
                        ['label' => 'Tinggi (Masalah kritis/baru, butuh riset mendalam)', 'score' => 3.0, 'sort_order' => 3],
                    ],
                ],
                [
                    'indicator_key' => 'action_involvement',
                    'label' => 'Tingkat Keterlibatan Aksi (Action)',
                    'sort_order' => 4,
                    'options' => [
                        ['label' => 'Mandiri / Eksekutor Tunggal', 'score' => 1.5, 'sort_order' => 1],
                        ['label' => 'Kolaborasi Tim / Koordinasi Antar Bagian', 'score' => 2.5, 'sort_order' => 2],
                        ['label' => 'Memimpin Inisiatif (Inisiator / Project Leader)', 'score' => 3.5, 'sort_order' => 3],
                    ],
                ],
            ],
        ];
    }

    private function loadSchemaPayload(?StarSchema $schema = null): array
    {
        $schema ??= StarSchema::query()->where('is_active', true)->latest('id')->first();

        if (!$schema) {
            return $this->defaultSchemaTemplate();
        }

        $schema->load(['indicators.options' => function ($query) {
            $query->orderBy('sort_order')->orderBy('id');
        }]);

        return [
            'id' => $schema->id,
            'name' => $schema->name,
            'description' => $schema->description,
            'version' => $schema->version,
            'is_active' => $schema->is_active,
            'indicators' => $schema->indicators
                ->sortBy('sort_order')
                ->values()
                ->map(function (StarSchemaIndicator $indicator) {
                    return [
                        'id' => $indicator->id,
                        'indicator_key' => $indicator->indicator_key,
                        'label' => $indicator->label,
                        'sort_order' => $indicator->sort_order,
                        'options' => $indicator->options
                            ->sortBy('sort_order')
                            ->values()
                            ->map(function (StarSchemaIndicatorOption $option) {
                                return [
                                    'id' => $option->id,
                                    'label' => $option->label,
                                    'score' => (float) $option->score,
                                    'sort_order' => $option->sort_order,
                                ];
                            })
                            ->all(),
                    ];
                })
                ->all(),
        ];
    }

    /**
     * Get STAR schema (framework for STAR recognitions)
     */
    public function getSchema(): JsonResponse
    {
        $schema = $this->loadSchemaPayload();

        return response()->json([
            'success' => true,
            'message' => 'STAR schema',
            'data' => $schema,
        ]);
    }

    public function saveSchema(Request $request): JsonResponse
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['intercomm', 'pcx_manager']),
            403,
            'Hanya PCX Manager dan Intercomm yang bisa mengubah schema.'
        );

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'indicators' => 'required|array|min:1',
            'indicators.*.label' => 'required|string|max:150',
            'indicators.*.indicator_key' => 'nullable|string|max:80',
            'indicators.*.sort_order' => 'nullable|integer|min:1',
            'indicators.*.options' => 'required|array|min:1',
            'indicators.*.options.*.label' => 'required|string|max:180',
            'indicators.*.options.*.score' => 'required|numeric|min:0',
            'indicators.*.options.*.sort_order' => 'nullable|integer|min:1',
        ]);

        $schema = DB::transaction(function () use ($validated) {
            $current = StarSchema::query()->where('is_active', true)->latest('id')->first() ?? new StarSchema();

            $current->fill([
                'name' => trim($validated['name']),
                'description' => trim((string) ($validated['description'] ?? '')) ?: null,
                'version' => (int) ($current->version ?: 1),
                'is_active' => true,
            ]);
            $current->save();

            $current->indicators()->delete();

            foreach (array_values($validated['indicators']) as $indicatorIndex => $indicatorData) {
                $indicator = $current->indicators()->create([
                    'indicator_key' => trim((string) ($indicatorData['indicator_key'] ?? '')) ?: null,
                    'label' => trim($indicatorData['label']),
                    'sort_order' => (int) ($indicatorData['sort_order'] ?? ($indicatorIndex + 1)),
                ]);

                foreach (array_values($indicatorData['options']) as $optionIndex => $optionData) {
                    $indicator->options()->create([
                        'label' => trim($optionData['label']),
                        'score' => $optionData['score'],
                        'sort_order' => (int) ($optionData['sort_order'] ?? ($optionIndex + 1)),
                    ]);
                }
            }

            return $current->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'STAR schema saved',
            'data' => $this->loadSchemaPayload($schema),
        ]);
    }

    /**
     * List all STAR recognitions visible to current user
     */
    public function listRecognitions(): JsonResponse
    {
        // Authorize: all roles
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses recognitions.');
        
        // TODO: Return list of STAR recognitions
        
        return response()->json([
            'success' => true,
            'message' => 'List of STAR recognitions',
            'data' => [],
        ]);
    }

    /**
     * Create a new STAR recognition
     */
    public function createRecognition(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:employees,id',
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        // Authorize: employees can give recognition
        abort_unless(auth()->user()?->hasRole('employee'), 403, 'Hanya employee yang bisa memberikan recognition.');
        
        // TODO: Create new STAR recognition
        // TODO: Set status as pending approval
        
        return response()->json([
            'success' => true,
            'message' => 'STAR recognition created',
            'data' => [],
        ]);
    }

    /**
     * Show specific STAR recognition
     */
    public function showRecognition(int $id): JsonResponse
    {
        // TODO: Get recognition by ID
        // TODO: Check authorization
        
        return response()->json([
            'success' => true,
            'message' => 'STAR recognition details',
            'data' => [],
        ]);
    }

    /**
     * List employee's submitted achievements
     */
    public function listAchievements(): JsonResponse
    {
        // Authorize: all roles can view achievements
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses achievements.');
        
        // TODO: Get current user's employee
        // TODO: Return list of submitted achievements
        
        return response()->json([
            'success' => true,
            'message' => 'List of achievements',
            'data' => [],
        ]);
    }

    /**
     * Submit a new achievement
     */
    public function submitAchievement(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'evidence' => 'nullable|string',
        ]);

        // Authorize: employees only
        abort_unless(auth()->user()?->hasRole('employee'), 403, 'Hanya employee yang bisa mengajukan achievement.');
        
        // TODO: Create new achievement
        // TODO: Set status as pending approval
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement submitted',
            'data' => [],
        ]);
    }

    /**
     * Show specific achievement
     */
    public function showAchievement(int $id): JsonResponse
    {
        // TODO: Get achievement by ID
        // TODO: Check authorization
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement details',
            'data' => [],
        ]);
    }

    /**
     * List pending approvals for current user
     */
    public function listApprovalsForMe(): JsonResponse
    {
        // TODO: Authorize: PCX, Intercomm, Direktur Utama only
        // TODO: Get current user
        // TODO: Return list of recognitions/achievements awaiting their approval
        
        return response()->json([
            'success' => true,
            'message' => 'Pending approvals for you',
            'data' => [],
        ]);
    }

    /**
     * Assign signature (TTD) to a recognition/achievement
     * Just mark as approved with signature, then calculate points
     */
    public function assignSignature(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Authorize: PCX, Intercomm, Direktur Utama only
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama']), 403, 'Hanya PCX, Intercomm, dan Direktur Utama yang bisa memberikan TTD.');
        
        // TODO: Get recognition/achievement by ID
        // TODO: Mark as approved and assign current user as signer
        // TODO: Set signature timestamp
        // TODO: Calculate points (see calculatePoints)
        
        return response()->json([
            'success' => true,
            'message' => 'Signature assigned and approval completed',
            'data' => [],
        ]);
    }

    /**
     * Approve a recognition/achievement
     * (Same as assignSignature for simple soft-file approval)
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        return $this->assignSignature($request, $id);
    }

    /**
     * Reject a recognition/achievement
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        // Authorize: PCX, Intercomm, Direktur Utama only
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama']), 403, 'Hanya PCX, Intercomm, dan Direktur Utama yang bisa menolak achievement.');
        
        // TODO: Get recognition/achievement by ID
        // TODO: Mark as rejected
        // TODO: Store rejection reason
        // TODO: Notify recipient/submitter
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement rejected',
            'data' => [],
        ]);
    }

    /**
     * Calculate STAR points for a recognition/achievement
     * Based on category/schema (automatic system calculation)
     */
    public function calculatePoints(Request $request, int $id): JsonResponse
    {
        // TODO: Get recognition/achievement by ID
        // TODO: Load STAR schema/category
        // TODO: Calculate points based on category definition
        // TODO: Store points in database
        // TODO: Return calculated points
        
        return response()->json([
            'success' => true,
            'message' => 'STAR points calculated',
            'points' => 0,
            'data' => [],
        ]);
    }
}
