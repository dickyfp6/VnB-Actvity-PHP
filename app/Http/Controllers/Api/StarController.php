<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\StarRecognition;
use App\Models\StarRecognitionResponse;
use App\Models\StarSchema;
use App\Models\StarSchemaIndicator;
use App\Models\StarSchemaIndicatorOption;
use App\Support\ActiveRoleContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $employee = $user->employee;

            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $recognitions = $query->get();

        $grouped = $recognitions->groupBy(function (StarRecognition $recognition) {
            if ($recognition->draft_group) {
                return 'draft-group:' . $recognition->draft_group;
            }

            $submittedAtKey = $recognition->submitted_at?->format('Y-m-d H:i:s')
                ?? $recognition->created_at?->format('Y-m-d H:i:s')
                ?? 'unknown';

            return implode('|', [
                (string) $recognition->manager_id,
                $submittedAtKey,
                (string) $recognition->activity_name,
                $recognition->activity_date?->format('Y-m-d') ?? (string) $recognition->activity_date,
                (string) $recognition->organizer,
                (string) ($recognition->certificate_path ?? ''),
                (string) ($recognition->certificate_original_name ?? ''),
            ]);
        });

        $data = $grouped->map(function ($items) {
            /** @var \Illuminate\Support\Collection<int, StarRecognition> $items */
            $first = $items->first();
            $employeeNames = $items->map(function (StarRecognition $recognition) {
                return $recognition->employee?->name
                    ?? $recognition->employee?->name_display
                    ?? $recognition->employee?->full_name
                    ?? $recognition->employee?->display_name
                    ?? $recognition->employee?->employee_number
                    ?? 'Employee #' . $recognition->employee_id;
            })->values()->all();

            $status = 'draft';
            if ($items->every(fn (StarRecognition $recognition) => in_array($recognition->status, ['approved', 'disetujui'], true))) {
                $status = 'approved';
            } elseif ($items->contains(fn (StarRecognition $recognition) => in_array($recognition->status, ['rejected', 'ditolak'], true))) {
                $status = 'rejected';
            } elseif ($items->contains(fn (StarRecognition $recognition) => in_array($recognition->status, ['submitted', 'pending_approval'], true))) {
                $status = 'submitted';
            } elseif ($items->contains(fn (StarRecognition $recognition) => in_array($recognition->status, ['approved', 'disetujui'], true))) {
                $status = 'approved';
            }

            return [
                'id' => $first->id,
                'draft_group' => $first->draft_group,
                'recognition_ids' => $items->pluck('id')->values()->all(),
                'manager_id' => $first->manager_id,
                'employee_id' => $first->employee_id,
                'employee' => $first->employee,
                'employee_names' => $employeeNames,
                'employee_names_text' => implode(', ', $employeeNames),
                'activity_name' => $first->activity_name,
                'activity_date' => $first->activity_date,
                'organizer' => $first->organizer,
                'certificate_path' => $first->certificate_path,
                'certificate_original_name' => $first->certificate_original_name,
                'activity_documentation_path' => $first->activity_documentation_path,
                'activity_documentation_original_name' => $first->activity_documentation_original_name,
                'activity_documentation' => $first->activity_documentation,
                'status' => $status,
                'total_points' => $first->total_points,
                'submitted_at' => $first->submitted_at,
                'created_at' => $first->created_at,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'STAR recognitions',
            'data' => $data,
        ]);
    }

    /**
     * Create a new STAR recognition
     */
    public function createRecognition(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_ids' => 'sometimes|array|min:1',
            'recipient_ids.*' => 'integer|exists:employees,id',
            'recipient_id' => 'nullable|integer|exists:employees,id',
            'activity_name' => 'required|string|max:200',
            'activity_date' => 'required|date',
            'organizer' => 'required|string|max:200',
            'certificate' => 'nullable|file',
        ]);
        // Only managers can submit recognition for their employees (per spec)
        abort_unless(auth()->user()?->hasRole('manager'), 403, 'Hanya manager yang bisa mengajukan recognition untuk karyawan.');

        $user = auth()->user();
        $manager = $user?->manager;

        $recipientIds = collect($validated['recipient_ids'] ?? [])
            ->merge($validated['recipient_id'] ?? null ? [(int) $validated['recipient_id']] : [])
            ->filter()
            ->map(fn ($recipientId) => (int) $recipientId)
            ->unique()
            ->values();

        abort_if($recipientIds->isEmpty(), 422, 'Pilih minimal satu employee.');

        $allowedRecipientIds = Employee::query()
            ->whereIn('id', $recipientIds)
            ->where(function ($query) use ($manager) {
                $query->where('manager_functional_id', $manager?->id)
                    ->orWhere('manager_operational_id', $manager?->id);
            })
            ->pluck('id')
            ->all();

        abort_if(count($allowedRecipientIds) !== $recipientIds->count(), 403, 'Ada employee yang bukan bawahan Anda.');

        $created = [];
        $draftGroup = (string) Str::uuid();

        DB::transaction(function () use ($request, $user, $recipientIds, $draftGroup, &$created) {
            $certificatePath = null;
            $certificateOriginalName = null;

            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $certificatePath = $file->store('star_certificates', 'public');
                $certificateOriginalName = $file->getClientOriginalName();
            }

            foreach ($recipientIds as $recipientId) {
                $recognition = new StarRecognition();
                $recognition->manager_id = $user->id;
                $recognition->employee_id = $recipientId;
                $recognition->draft_group = $draftGroup;
                $recognition->activity_name = trim($request->input('activity_name'));
                $recognition->activity_date = $request->input('activity_date');
                $recognition->organizer = trim($request->input('organizer'));
                $recognition->certificate_path = $certificatePath;
                $recognition->certificate_original_name = $certificateOriginalName;
                $recognition->activity_documentation = trim((string) $request->input('activity_documentation', '')) ?: null;
                $recognition->status = 'draft';
                $recognition->submitted_at = null;
                $recognition->save();

                $created[] = $recognition->fresh();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'STAR recognition created',
            'data' => $created,
            'draft_group' => $draftGroup,
        ]);
    }

    public function showDraftGroup(string $draftGroup): JsonResponse
    {
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses draft recognition.');

        $items = StarRecognition::with(['employee', 'responses.indicator', 'responses.option'])
            ->where('draft_group', $draftGroup)
            ->orderBy('id')
            ->get();

        abort_if($items->isEmpty(), 404, 'Draft recognition tidak ditemukan.');

        $first = $items->first();
        $user = auth()->user();
        abort_unless($user->id === $first->manager_id || $user->hasAnyRole(['intercomm', 'pcx_manager', 'direktur_utama']), 403, 'Tidak berwenang melihat draft ini.');

        $schema = $this->loadSchemaPayload();

        return response()->json([
            'success' => true,
            'message' => 'Draft recognition',
            'data' => [
                'draft_group' => $draftGroup,
                'recognition_ids' => $items->pluck('id')->values()->all(),
                'employee_ids' => $items->pluck('employee_id')->values()->all(),
                'employee_names' => $items->map(fn (StarRecognition $recognition) => $recognition->employee?->name ?? $recognition->employee?->name_display ?? $recognition->employee?->full_name ?? $recognition->employee?->display_name ?? $recognition->employee?->employee_number ?? 'Employee #' . $recognition->employee_id)->values()->all(),
                'employee_names_text' => $items->map(fn (StarRecognition $recognition) => $recognition->employee?->name ?? $recognition->employee?->name_display ?? $recognition->employee?->full_name ?? $recognition->employee?->display_name ?? $recognition->employee?->employee_number ?? 'Employee #' . $recognition->employee_id)->values()->implode(', '),
                'activity_name' => $first->activity_name,
                'activity_date' => optional($first->activity_date)->format('Y-m-d'),
                'manager_id' => $first->manager_id,
                'manager_name' => $first->manager?->name ?? null,
                'submitted_at' => optional($first->submitted_at)->toDateTimeString(),
                'organizer' => $first->organizer,
                'certificate_path' => $first->certificate_path,
                'certificate_original_name' => $first->certificate_original_name,
                'activity_documentation_path' => $first->activity_documentation_path ?? null,
                'activity_documentation_original_name' => $first->activity_documentation_original_name ?? null,
                'responses' => $first->responses->map(fn (StarRecognitionResponse $response) => [
                    'star_schema_indicator_id' => $response->star_schema_indicator_id,
                    'star_schema_indicator_option_id' => $response->star_schema_indicator_option_id,
                    'response_score' => (float) ($response->response_score ?? 0),
                ])->values()->all(),
                'schema' => $schema,
                'status' => $first->status,
                'approval_notes' => $first->approval_notes,
                'notes' => $first->approval_notes,
                'total_points' => $first->total_points !== null
                    ? (float) $first->total_points
                    : $first->responses->sum(fn (StarRecognitionResponse $response) => (float) ($response->response_score ?? 0)),
            ],
        ]);
    }

    public function saveDraftGroup(Request $request, string $draftGroup): JsonResponse
    {
        $finalize = $request->boolean('finalize');

        $validated = $request->validate([
            'certificate' => 'nullable|file|max:10240',
            'activity_documentation_file' => 'nullable|file|max:10240',
            'activity_name' => $finalize ? 'required|string|max:200' : 'nullable|string|max:200',
            'activity_date' => $finalize ? 'required|date' : 'nullable|date',
            'organizer' => $finalize ? 'required|string|max:200' : 'nullable|string|max:200',
            'responses' => $finalize ? 'required|array|min:1' : 'nullable|array',
            'responses.*.star_schema_indicator_id' => 'required_with:responses|exists:star_schema_indicators,id',
            'responses.*.star_schema_indicator_option_id' => 'required_with:responses|exists:star_schema_indicator_options,id',
        ]);

        abort_unless(auth()->check(), 403, 'Anda harus login untuk menyimpan draft recognition.');

        $user = auth()->user();
        /** @var \Illuminate\Database\Eloquent\Collection<int, StarRecognition> $items */
        $items = StarRecognition::query()->where('draft_group', $draftGroup)->get();
        abort_if($items->isEmpty(), 404, 'Draft recognition tidak ditemukan.');

        $first = $items->first();
        abort_unless($user->id === $first->manager_id, 403, 'Anda tidak berwenang menyimpan draft ini.');

        if ($finalize) {
            $schema = $this->loadSchemaPayload();
            $schemaIndicatorCount = count($schema['indicators'] ?? []);
            abort_if($schemaIndicatorCount === 0, 422, 'Schema STAR belum tersedia.');
            abort_if(count($validated['responses'] ?? []) !== $schemaIndicatorCount, 422, 'Lengkapi semua pertanyaan skema sebelum mengajukan.');
            abort_if(!$request->hasFile('certificate') && !$first->certificate_path, 422, 'Dokumen pendukung wajib diisi sebelum mengajukan.');
            abort_if(!$request->hasFile('activity_documentation_file') && !($first->activity_documentation_path ?? null), 422, 'Dokumentasi saat kegiatan wajib diisi sebelum mengajukan.');
        }

        DB::transaction(function () use ($request, $items, $validated, $finalize) {
            $certificatePath = $items->first()->certificate_path;
            $certificateOriginalName = $items->first()->certificate_original_name;
            $activityDocumentationPath = $items->first()->activity_documentation_path ?? null;
            $activityDocumentationOriginalName = $items->first()->activity_documentation_original_name ?? null;

            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $certificatePath = $file->store('star_certificates', 'public');
                $certificateOriginalName = $file->getClientOriginalName();
            }

            if ($request->hasFile('activity_documentation_file')) {
                $file = $request->file('activity_documentation_file');
                $activityDocumentationPath = $file->store('star_documentations', 'public');
                $activityDocumentationOriginalName = $file->getClientOriginalName();
            }

            foreach ($items as $recognition) {
                /** @var StarRecognition $recognition */
                if ($request->filled('activity_name')) {
                    $recognition->activity_name = trim((string) $request->input('activity_name'));
                }
                if ($request->filled('activity_date')) {
                    $recognition->activity_date = $request->input('activity_date');
                }
                if ($request->filled('organizer')) {
                    $recognition->organizer = trim((string) $request->input('organizer'));
                }
                $recognition->certificate_path = $certificatePath;
                $recognition->certificate_original_name = $certificateOriginalName;
                $recognition->activity_documentation_path = $activityDocumentationPath;
                $recognition->activity_documentation_original_name = $activityDocumentationOriginalName;
                $recognition->status = $finalize ? 'submitted' : 'draft';
                $recognition->submitted_at = $finalize ? now() : null;
                $recognition->save();

                if (array_key_exists('responses', $validated) && is_array($validated['responses'])) {
                    $recognition->responses()->delete();

                    foreach ($validated['responses'] as $response) {
                        $option = StarSchemaIndicatorOption::find($response['star_schema_indicator_option_id']);
                        if (!$option) {
                            continue;
                        }

                        StarRecognitionResponse::create([
                            'star_recognition_id' => $recognition->id,
                            'star_schema_indicator_id' => $response['star_schema_indicator_id'],
                            'star_schema_indicator_option_id' => $response['star_schema_indicator_option_id'],
                            'response_score' => (float) $option->score,
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => $finalize ? 'Draft recognition submitted' : 'Draft recognition saved',
            'data' => $this->showDraftGroup($draftGroup)->getData(true)['data'],
        ]);
    }

    /**
     * Show specific STAR recognition
     */
    public function showRecognition(int $id): JsonResponse
    {
        $user = auth()->user();
        $recognition = StarRecognition::with(['manager', 'employee', 'responses.indicator', 'responses.option'])->findOrFail($id);

        // Authorization: allow if
        // - user is manager and is the owner (manager_id matches), OR
        // - user is employee and is the recipient, OR
        // - user has elevated roles (intercomm, pcx_manager, direktur_utama)
        // Note: a user may have multiple roles; elevated roles should bypass manager-only restriction.

        if ($user->hasRole('manager') && !$user->hasAnyRole(['intercomm', 'pcx_manager', 'direktur_utama'])) {
            // user is exclusively a manager (or at least not elevated); enforce ownership
            if ($recognition->manager_id !== $user->id) {
                abort(403, 'Tidak berwenang melihat recognition ini.');
            }
        }

        if ($user->hasRole('employee') && !$user->hasAnyRole(['intercomm', 'pcx_manager', 'direktur_utama'])) {
            $employee = $user->employee;
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
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses achievements.');

        // Only employees may access achievements
        abort_unless(auth()->user()?->hasRole('employee'), 403, 'Hanya employee yang dapat mengakses achievements.');

        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'success' => true,
                'message' => 'List of achievements',
                'data' => [],
            ]);
        }

        $items = StarRecognition::with(['manager'])
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->orderByDesc('approved_at')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        $data = $items->map(function (StarRecognition $recognition) {
            return [
                'id' => $recognition->id,
                'manager_id' => $recognition->manager_id,
                'manager_name' => $recognition->manager?->name ?? null,
                'activity_name' => $recognition->activity_name,
                'activity_date' => optional($recognition->activity_date)->format('Y-m-d'),
                'organizer' => $recognition->organizer,
                'status' => $recognition->status,
                'status_label' => 'Disetujui',
                'total_points' => $recognition->total_points !== null ? (float) $recognition->total_points : null,
                'submitted_at' => optional($recognition->submitted_at)->toDateTimeString(),
                'approved_at' => optional($recognition->approved_at)->toDateTimeString(),
                'certificate_original_name' => $recognition->certificate_original_name,
                'activity_documentation_original_name' => $recognition->activity_documentation_original_name,
            ];
        })->values();
        
        return response()->json([
            'success' => true,
            'message' => 'List of achievements',
            'data' => $data,
        ]);
    }

    public function dashboardOverview(Request $request): JsonResponse
    {
        $user = auth()->user();
        abort_unless($user !== null, 401, 'Anda harus login untuk mengakses dashboard STAR.');

        $activeRole = ActiveRoleContext::current($request, $user);

        $query = StarRecognition::query()
            ->with(['employee.department'])
            ->where('status', 'approved');

        if ($activeRole === 'employee') {
            $employeeId = $user->employee?->id;
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($activeRole === 'manager') {
            $manager = $user->manager;
            if ($manager) {
                $employeeIds = Employee::query()
                    ->where(function ($q) use ($manager) {
                        $q->where('manager_functional_id', $manager->id)
                            ->orWhere('manager_operational_id', $manager->id);
                    })
                    ->pluck('id');

                $query->whereIn('employee_id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $recognitions = $query
            ->orderByDesc('approved_at')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        $topEmployees = $recognitions
            ->groupBy('employee_id')
            ->map(function ($items) {
                $first = $items->first();
                $employee = $first?->employee;

                return [
                    'label' => $employee?->name
                        ?? $employee?->name_display
                        ?? $employee?->full_name
                        ?? $employee?->display_name
                        ?? $employee?->employee_number
                        ?? 'Employee #' . ($first?->employee_id ?? '-'),
                    'points' => round($items->sum(fn (StarRecognition $recognition) => (float) ($recognition->total_points ?? 0)), 1),
                ];
            })
            ->sortByDesc('points')
            ->take(5)
            ->values();

        $topDepartments = $recognitions
            ->groupBy(function (StarRecognition $recognition) {
                return $recognition->employee?->department_id ?? 'none';
            })
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'label' => $first?->employee?->department?->name ?? 'Tanpa Departemen',
                    'points' => round($items->sum(fn (StarRecognition $recognition) => (float) ($recognition->total_points ?? 0)), 1),
                ];
            })
            ->sortByDesc('points')
            ->take(5)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'STAR dashboard overview',
            'data' => [
                'has_data' => $topEmployees->isNotEmpty() || $topDepartments->isNotEmpty(),
                'active_role' => $activeRole,
                'summary' => [
                    'approved_count' => $recognitions->count(),
                    'total_points' => round($recognitions->sum(fn (StarRecognition $recognition) => (float) ($recognition->total_points ?? 0)), 1),
                ],
                'top_employees' => $topEmployees->all(),
                'top_departments' => $topDepartments->all(),
            ],
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
        abort_unless(auth()->check(), 403, 'Anda harus login untuk mengakses achievements.');

        $user = auth()->user();
        $recognition = StarRecognition::with(['manager', 'employee', 'responses.indicator', 'responses.option'])->findOrFail($id);

        if ($user->hasRole('employee') && !$user->hasAnyRole(['intercomm', 'pcx_manager', 'direktur_utama'])) {
            $employee = $user->employee;
            abort_unless($employee && $recognition->employee_id === $employee->id, 403, 'Tidak berwenang melihat achievement ini.');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Achievement details',
            'data' => $recognition,
        ]);
    }

    /**
     * List pending approvals for current user
     */
    public function listApprovalsForMe(): JsonResponse
    {
        $user = auth()->user();
        abort_unless($user !== null, 401, 'Anda harus login untuk mengakses approvals.');

        // If user is PCX/Intercomm/Direktur, show all recognitions they are allowed to see
        if ($user->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama'])) {
            $items = StarRecognition::with(['employee', 'manager'])
                ->orderByDesc('submitted_at')
                ->get();
        } else {
            // For regular managers, show pending recognitions for their direct reports
            // Use manager relation on user to find manager id
            $manager = $user->manager ?? null;
            if (!$manager) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pending approvals for you',
                    'data' => [],
                ]);
            }

                        $employeeIds = Employee::query()
                ->where(function ($q) use ($manager) {
                    $q->where('manager_functional_id', $manager->id)
                      ->orWhere('manager_operational_id', $manager->id);
                })->pluck('id')->all();

            $items = StarRecognition::with(['employee', 'manager'])
                ->whereIn('employee_id', $employeeIds)
                ->orderByDesc('submitted_at')
                ->get();
        }

        // Group recognitions server-side so frontend can render one row per group
        $grouped = $items->groupBy(function (StarRecognition $r) {
            return implode('|', [
                (string) $r->manager_id,
                $r->activity_name ?? '',
                optional($r->activity_date)->format('Y-m-d') ?? '',
                $r->organizer ?? '',
                $r->certificate_path ?? '',
            ]);
        });

        $data = $grouped->map(function ($group) {
            /** @var \Illuminate\Support\Collection|StarRecognition[] $group */
            $first = $group->first();

            $employeeNames = $group->map(function (StarRecognition $r) {
                return $r->employee?->name
                    ?? $r->employee?->name_display
                    ?? $r->employee?->full_name
                    ?? $r->employee?->display_name
                    ?? $r->employee?->employee_number
                    ?? 'Employee #' . $r->employee_id;
            })->values()->all();

            $statuses = $group->pluck('status')->map(fn ($status) => strtolower((string) $status));
            $status = 'draft';
            if ($statuses->every(fn ($status) => $status === 'approved')) {
                $status = 'approved';
            } elseif ($statuses->every(fn ($status) => in_array($status, ['rejected', 'ditolak'], true))) {
                $status = 'rejected';
            } elseif ($statuses->contains(fn ($status) => in_array($status, ['submitted', 'pending_approval'], true))) {
                $status = 'submitted';
            } elseif ($statuses->contains('approved')) {
                $status = 'approved';
            } elseif ($statuses->contains(fn ($status) => in_array($status, ['rejected', 'ditolak'], true))) {
                $status = 'rejected';
            }

            return [
                'recognition_ids' => $group->pluck('id')->values()->all(),
                'draft_group' => $first->draft_group ?? null,
                'manager_id' => $first->manager_id,
                'manager_name' => $first->manager?->name ?? null,
                'activity_name' => $first->activity_name,
                'activity_date' => optional($first->activity_date)->format('Y-m-d'),
                'organizer' => $first->organizer,
                'certificate_path' => $first->certificate_path,
                'status' => $status,
                'total_points' => $first->total_points !== null ? (float) $first->total_points : null,
                'submitted_at' => optional($first->submitted_at)->toDateTimeString(),
                'employee_ids' => $group->pluck('employee_id')->unique()->values()->all(),
                'employee_names' => $employeeNames,
                'employee_names_text' => implode(', ', $employeeNames),
                'employee_number' => $group->pluck('employee_number')->first() ?? null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Pending approvals for you',
            'data' => $data,
        ]);
    }

    /**
     * Assign signature (TTD) to a recognition/achievement
     * Just mark as approved with signature, then calculate points
     */
    public function assignSignature(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'overrides' => 'nullable|array',
            'overrides.*.indicator_id' => 'required_with:overrides|exists:star_schema_indicators,id',
            'overrides.*.option_id' => 'required_with:overrides|exists:star_schema_indicator_options,id',
            'adjustment' => 'nullable|numeric',
        ]);

        // Authorize: PCX, Intercomm, Direktur Utama only
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama']), 403, 'Hanya PCX, Intercomm, dan Direktur Utama yang bisa memberikan TTD.');

        $recognition = StarRecognition::with('responses')->findOrFail($id);

        // If overrides provided, replace responses accordingly
        $overrides = $validated['overrides'] ?? null;
        if (is_array($overrides) && count($overrides)) {
            // delete existing responses for this recognition and recreate from overrides
            $recognition->responses()->delete();
            $totalPoints = 0;
            foreach ($overrides as $ov) {
                $option = StarSchemaIndicatorOption::find($ov['option_id']);
                if (!$option) continue;
                StarRecognitionResponse::create([
                    'star_recognition_id' => $recognition->id,
                    'star_schema_indicator_id' => $ov['indicator_id'],
                    'star_schema_indicator_option_id' => $ov['option_id'],
                    'response_score' => (float) $option->score,
                ]);
                $totalPoints += (float) $option->score;
            }
        } else {
            // Sum existing responses (ensure we treat as collection)
            $totalPoints = collect($recognition->responses)->sum(function ($r) { return (float) ($r->response_score ?? 0); });
        }

        // Apply custom adjustment if any
        $adjustment = (float) ($validated['adjustment'] ?? 0);
        $totalPoints = $totalPoints + $adjustment;
        $approvalNotes = trim((string) ($validated['notes'] ?? ''));

        // Mark as approved and save total points
        $recognition->total_points = $totalPoints;
        $recognition->status = 'approved';
        $recognition->approved_at = now();
        if (Schema::hasColumn('star_recognitions', 'approval_notes')) {
            $recognition->approval_notes = $approvalNotes !== '' ? $approvalNotes : null;
        }
        $recognition->save();

        // Reload responses
        $recognition->load('responses.indicator', 'responses.option');

        return response()->json([
            'success' => true,
            'message' => 'Signature assigned and approval completed',
            'data' => $recognition,
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
        // Authorize: PCX, Intercomm, Direktur Utama only
        abort_unless(auth()->user()?->hasAnyRole(['pcx_manager', 'intercomm', 'direktur_utama']), 403, 'Hanya PCX, Intercomm, dan Direktur Utama yang bisa menolak achievement.');

        $recognition = StarRecognition::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $rejectionReason = trim((string) ($validated['rejection_reason'] ?? ''));
        $recognition->status = 'rejected';
        $recognition->rejection_reason = $rejectionReason;
        $recognition->approval_notes = $rejectionReason;
        $recognition->approved_at = null;
        $recognition->save();

        return response()->json([
            'success' => true,
            'message' => 'Achievement rejected',
            'data' => $recognition,
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
