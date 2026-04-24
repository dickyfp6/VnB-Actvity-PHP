<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        if (Schema::hasColumn('employees', 'employment_state') && Schema::hasColumn('employees', 'status')) {
            DB::statement("UPDATE employees
                SET status = CASE
                    WHEN LOWER(TRIM(employment_state)) = 'active' THEN 'Aktif'
                    ELSE 'Inactive'
                END");
        }

        Schema::table('employees', function (Blueprint $table) {
            $columnsToDrop = [];

            foreach (['employment_state', 'status_changed_at', 'status_change_reason', 'status_changed_by'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'employment_state')) {
                $table->string('employment_state', 20)->default('active')->after('vnb_status');
            }

            if (!Schema::hasColumn('employees', 'status_changed_at')) {
                $table->timestamp('status_changed_at')->nullable()->after('employment_state');
            }

            if (!Schema::hasColumn('employees', 'status_change_reason')) {
                $table->text('status_change_reason')->nullable()->after('status_changed_at');
            }

            if (!Schema::hasColumn('employees', 'status_changed_by')) {
                $table->unsignedBigInteger('status_changed_by')->nullable()->after('status_change_reason');
            }
        });

        if (Schema::hasColumn('employees', 'status') && Schema::hasColumn('employees', 'employment_state')) {
            DB::statement("UPDATE employees
                SET employment_state = CASE
                    WHEN LOWER(TRIM(status)) = 'aktif' THEN 'active'
                    ELSE 'terminated'
                END");
        }
    }
};
