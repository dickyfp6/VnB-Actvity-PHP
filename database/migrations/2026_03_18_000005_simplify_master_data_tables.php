<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->rebuildAsSimpleMaster('master_companies');
        $this->rebuildAsSimpleMaster('master_divisions');
        $this->rebuildAsSimpleMaster('master_departments');
        $this->rebuildAsSimpleMaster('master_positions');
        $this->rebuildAsSimpleMaster('master_placements');
        $this->rebuildAsSimpleMaster('master_levels');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No-op rollback for simplified master data structure.
    }

    private function rebuildAsSimpleMaster(string $table): void
    {
        $backupTable = $table . '_backup_simplify_' . time();

        Schema::rename($table, $backupTable);

        Schema::create($table, function (Blueprint $newTable) {
            $newTable->id();
            $newTable->string('name', 255);
            $newTable->timestamps();
            $newTable->softDeletes();
        });

        DB::table($backupTable)
            ->select('name', 'created_at', 'updated_at', 'deleted_at')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($table) {
                $payload = $rows->map(function ($row) {
                    return [
                        'name' => $row->name,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                        'deleted_at' => $row->deleted_at,
                    ];
                })->toArray();

                if (!empty($payload)) {
                    DB::table($table)->insert($payload);
                }
            });

        Schema::drop($backupTable);
    }
};

