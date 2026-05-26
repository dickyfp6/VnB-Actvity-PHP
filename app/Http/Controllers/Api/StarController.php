<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\StarRecognition;
use App\Models\StarRecognitionResponse;
use App\Models\StarSchema;
use App\Models\StarSchemaIndicator;
use App\Models\StarSchemaIndicatorOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        // Ensure relations are treated as Collections for static analysis
        /** @var \Illuminate\Database\Eloquent\Collection|StarSchemaIndicator[] $indicators */
        $indicators = collect($schema->indicators)->sortBy('sort_order')->values();

        return [
            'id' => $schema->id,
            'name' => $schema->name,
            'description' => $schema->description,
            'version' => $schema->version,
            'is_active' => $schema->is_active,
            'indicators' => $indicators->map(function (StarSchemaIndicator $indicator) {
                /** @var \Illuminate\Database\Eloquent\Collection|StarSchemaIndicatorOption[] $options */
                $options = collect($indicator->options)->sortBy('sort_order')->values();

                return [
                    'id' => $indicator->id,
                    'indicator_key' => $indicator->indicator_key,
                    'label' => $indicator->label,
                    'sort_order' => $indicator->sort_order,
                    'options' => $options->map(function (StarSchemaIndicatorOption $option) {
                        return [
                            'id' => $option->id,
                            'label' => $option->label,
                            'score' => (float) $option->score,
                            'sort_order' => $option->sort_order,
                        ];
                    })->all(),
                ];
            })->all(),
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
        $user = auth()->user();

        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses recognition.');

        $query = StarRecognition::with(['employee', 'manager'])->orderByDesc('submitted_at')->orderByDesc('id');

        if ($user->hasRole('manager')) {
            $query->where('manager_id', $user->id);
        } elseif ($user->hasRole('employee')) {
            $employee = Employee::query()->where('user_id', $user->id)->first();

            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'STAR recognitions',
            'data' => $query->get(),
        ]);
    }

    /**
     * Create a new STAR recognition
     */
    public function createRecognition(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:employees,id',
            'activity_name' => 'required|string|max:200',
            'activity_date' => 'required|date',
            'organizer' => 'required|string|max:200',
            'certificate' => 'nullable|file',
        ]);
        // Only managers can submit recognition for their employees (per spec)
        abort_unless(auth()->user()?->hasRole('manager'), 403, 'Hanya manager yang bisa mengajukan recognition untuk karyawan.');

        $user = auth()->user();

        $recognition = DB::transaction(function () use ($request, $user) {
            $r = new StarRecognition();
            $r->manager_id = $user->id;
            $r->employee_id = $request->input('recipient_id');
            $r->activity_name = trim($request->input('activity_name'));
            $r->activity_date = $request->input('activity_date');
            $r->organizer = trim($request->input('organizer'));

            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $path = $file->store('star_certificates');
                $r->certificate_path = $path;
                $r->certificate_original_name = $file->getClientOriginalName();
            }

            $r->status = 'submitted';
            $r->submitted_at = now();
            $r->save();

            return $r->fresh();
        });
        return response()->json([
            'success' => true,
            'message' => 'STAR recognition created',
            'data' => $recognition,
        ]);
    }

    /**
     * Show specific STAR recognition
     */
    public function showRecognition(int $id): JsonResponse
    {
        $user = auth()->user();
        $recognition = StarRecognition::with(['manager', 'employee', 'responses.indicator', 'responses.option'])->findOrFail($id);

        // Authorization: manager who created it, the recipient employee's user, or admin roles
        if ($user->hasRole('manager') && $recognition->manager_id !== $user->id) {
            abort(403, 'Tidak berwenang melihat recognition ini.');
        }

        if ($user->hasRole('employee')) {
            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee || $recognition->employee_id !== $employee->id) {
                abort(403, 'Tidak berwenang melihat recognition ini.');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'STAR recognition details',
            'data' => $recognition,
        ]);
    }

    /**
     * Save responses for a recognition (tahap 2)
     */
    public function saveRecognitionResponses(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'responses' => 'required|array|min:1',
            'responses.*.star_schema_indicator_id' => 'required|exists:star_schema_indicators,id',
            'responses.*.star_schema_indicator_option_id' => 'required|exists:star_schema_indicator_options,id',
        ]);

        $user = auth()->user();
        $recognition = StarRecognition::findOrFail($id);

        // Only manager who created the recognition can save responses here
        abort_unless($user->hasRole('manager') && $recognition->manager_id === $user->id, 403, 'Hanya manager pemilik pengajuan yang bisa mengisi skema.');

        $totalPoints = 0;

        DB::transaction(function () use ($recognition, $validated, &$totalPoints) {
            // remove existing responses
            $recognition->responses()->delete();

            foreach ($validated['responses'] as $resp) {
                $option = StarSchemaIndicatorOption::find($resp['star_schema_indicator_option_id']);
                if (!$option) continue;

                $score = (float) $option->score;
                StarRecognitionResponse::create([
                    'star_recognition_id' => $recognition->id,
                    'star_schema_indicator_id' => $resp['star_schema_indicator_id'],
                    'star_schema_indicator_option_id' => $resp['star_schema_indicator_option_id'],
                    'response_score' => $score,
                ]);

                $totalPoints += $score;
            }

            $recognition->total_points = $totalPoints;
            $recognition->status = 'pending_approval';
            $recognition->save();
        });

        $recognition->load(['responses.indicator', 'responses.option']);

        return response()->json([
            'success' => true,
            'message' => 'Responses saved and recognition submitted for approval',
            'data' => $recognition,
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
