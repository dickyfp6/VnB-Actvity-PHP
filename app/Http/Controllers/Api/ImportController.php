<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\Import;
use App\Models\ImportRow;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    /**
     * UC-01: Import Employees from Excel
     */
    public function importEmployees(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');

        // Create import record
        $import = Import::create([
            'imported_by' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'total_rows' => 0,
            'status' => 'processing',
        ]);

        // TODO: Queue job to process import asynchronously
        // ProcessImportJob::dispatch($import, $file);

        return response()->json([
            'success' => true,
            'message' => 'Import started',
            'data' => [
                'import_id' => $import->id,
                'file_name' => $import->file_name,
            ]
        ], 202);
    }

    /**
     * Get Import Status & Results
     */
    public function getImportStatus(Import $import): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $import->id,
                'file_name' => $import->file_name,
                'status' => $import->status,
                'total_rows' => $import->total_rows,
                'success_rows' => $import->success_rows,
                'error_rows' => $import->error_rows,
                'summary' => $import->summary,
                'created_at' => $import->created_at,
            ]
        ]);
    }

    /**
     * Get Import Error Details
     */
    public function getImportErrors(Import $import): JsonResponse
    {
        $errors = ImportRow::where('import_id', $import->id)
            ->whereIn('status', ['error', 'duplicate'])
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => [
                'errors' => $errors->items(),
                'pagination' => [
                    'total' => $errors->total(),
                    'per_page' => $errors->perPage(),
                    'current_page' => $errors->currentPage(),
                ]
            ]
        ]);
    }

    /**
     * Get Import History
     */
    public function getImportHistory(Request $request): JsonResponse
    {
        $imports = Import::where('imported_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $imports->items(),
            'pagination' => [
                'total' => $imports->total(),
                'per_page' => $imports->perPage(),
                'current_page' => $imports->currentPage(),
            ]
        ]);
    }
}
