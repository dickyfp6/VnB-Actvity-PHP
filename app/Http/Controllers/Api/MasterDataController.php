<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterCompany;
use App\Models\MasterLevel;
use App\Models\MasterPlacement;
use App\Models\MasterEmployeeStatus;
use App\Models\MasterDivision;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MasterDataController extends Controller
{
    private array $categoryMap = [
        'companies'   => MasterCompany::class,
        'levels'      => MasterLevel::class,
        'placements'  => MasterPlacement::class,
        'employee_statuses' => MasterEmployeeStatus::class,
        'divisions'   => MasterDivision::class,
        'departments' => MasterDepartment::class,
        'positions'   => MasterPosition::class,
    ];

    public function index(Request $request, string $category): JsonResponse
    {
        $model = $this->resolveModel($category);
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $query = $model::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $rows = $query->orderBy('id')->get()->values()->map(function ($item, int $index) {
            $item->setAttribute('code', $index + 1);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function store(Request $request, string $category): JsonResponse
    {
        $model = $this->resolveModel($category);
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = trim($validated['name']);
        $this->ensureUniqueName($model, $name);

        $item = $model::create([
            'name' => $name,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan', 'data' => $item], 201);
    }

    public function bulkStore(Request $request, string $category): JsonResponse
    {
        $model = $this->resolveModel($category);
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'names' => 'required|array|min:1',
            'names.*' => 'nullable|string|max:255',
        ]);

        $normalizedNames = collect($validated['names'])
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->values();

        if ($normalizedNames->isEmpty()) {
            throw ValidationException::withMessages([
                'names' => ['Tidak ada data valid untuk ditambahkan.'],
            ]);
        }

        $uniqueNames = [];
        $seen = [];
        foreach ($normalizedNames as $name) {
            $key = mb_strtolower($name);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueNames[] = $name;
            }
        }

        $existingMap = $model::query()
            ->pluck('name')
            ->mapWithKeys(function ($name) {
                $clean = trim((string) $name);
                return [mb_strtolower($clean) => $clean];
            })
            ->all();

        $toInsert = [];
        $duplicates = [];
        $now = now();

        foreach ($uniqueNames as $name) {
            $key = mb_strtolower($name);
            if (isset($existingMap[$key])) {
                $duplicates[] = $name;
                continue;
            }

            $toInsert[] = [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $existingMap[$key] = $name;
        }

        if (!empty($toInsert)) {
            $model::query()->insert($toInsert);
        }

        $duplicateCount = $normalizedNames->count() - count($toInsert);

        return response()->json([
            'success' => true,
            'message' => count($toInsert) . ' data ditambahkan' . ($duplicateCount > 0 ? ', ' . $duplicateCount . ' duplikat dilewati' : ''),
            'data' => [
                'inserted' => count($toInsert),
                'duplicates' => $duplicates,
            ],
        ], 201);
    }

    public function update(Request $request, string $category, int $id): JsonResponse
    {
        $model = $this->resolveModel($category);
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $item = $model::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        if (array_key_exists('name', $validated)) {
            $name = trim($validated['name']);
            $this->ensureUniqueName($model, $name, $id);
            $validated['name'] = $name;
        }

        $item->update($validated);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui', 'data' => $item]);
    }

    public function destroy(string $category, int $id): JsonResponse
    {
        $model = $this->resolveModel($category);
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $item = $model::findOrFail($id);
        $item->forceDelete();

        if ($model::count() === 0) {
            if (method_exists($model, 'onlyTrashed')) {
                $model::onlyTrashed()->forceDelete();
            }
            $this->resetAutoIncrement($item->getTable());
        }

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    private function resolveModel(string $category): ?string
    {
        return $this->categoryMap[$category] ?? null;
    }

    private function resetAutoIncrement(string $table): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("DELETE FROM sqlite_sequence WHERE name = ?", [$table]);
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER SEQUENCE {$table}_id_seq RESTART WITH 1");
        }
    }

    private function ensureUniqueName(string $model, string $name, ?int $ignoreId = null): void
    {
        $query = $model::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => ['Nama sudah terdaftar di kategori ini.'],
            ]);
        }
    }
}
