<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restructure vnb_plan_items table dengan proper foreign keys dan approval workflow
     * 
     * Perubahan:
     * 1. Tambah employee_id FK ke employees
     * 2. Tambah vnb_framework_id FK ke vnb_framework_items
     * 3. Hapus implementation_date (deprecated)
     * 4. Update status ENUM dengan workflow baru
     * 5. Tambah approval tracking fields
     */
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            // --- FASE 1: Tambah kolom baru ---
            
            // Foreign Key ke employees (untuk quick filter by employee)
            if (!Schema::hasColumn('vnb_plan_items', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('plan_id')->comment('Direct reference to employee');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            }

            // Foreign Key ke vnb_framework_items (template reference)
            if (!Schema::hasColumn('vnb_plan_items', 'vnb_framework_id')) {
                $table->unsignedBigInteger('vnb_framework_id')->nullable()->after('employee_id')->comment('Link to framework template');
                $table->foreign('vnb_framework_id')->references('id')->on('vnb_framework_items')->onDelete('set null');
            }

            // Approval tracking fields (untuk dual approval: functional & operational)
            if (!Schema::hasColumn('vnb_plan_items', 'approved_functional_by')) {
                $table->unsignedBigInteger('approved_functional_by')->nullable()->after('completion_percentage')->comment('Approved by Manager Functional');
                $table->dateTime('approved_functional_at')->nullable()->after('approved_functional_by');
                $table->foreign('approved_functional_by')->references('id')->on('employees')->onDelete('set null');
            }

            if (!Schema::hasColumn('vnb_plan_items', 'approved_operational_by')) {
                $table->unsignedBigInteger('approved_operational_by')->nullable()->after('approved_functional_at')->comment('Approved by Manager Operational');
                $table->dateTime('approved_operational_at')->nullable()->after('approved_operational_by');
                $table->foreign('approved_operational_by')->references('id')->on('employees')->onDelete('set null');
            }

            // --- FASE 2: Hapus kolom yang deprecated ---
            
            if (Schema::hasColumn('vnb_plan_items', 'implementation_date')) {
                $table->dropColumn('implementation_date');
            }
        });

        // --- FASE 3: Update status ENUM (jika perlu) ---
        // Note: Ini hanya bisa dilakukan di beberapa database. 
        // Kalau di MySQL perlu raw SQL untuk safety
        DB::statement("ALTER TABLE vnb_plan_items MODIFY status ENUM('draft', 'submitted', 'approved', 'revision') DEFAULT 'draft' COMMENT 'Workflow: draft → submitted → approved/revision'");
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            // Drop foreign keys terlebih dahulu
            try {
                $table->dropForeignKeyIfExists('vnb_plan_items_employee_id_foreign');
                $table->dropForeignKeyIfExists('vnb_plan_items_vnb_framework_id_foreign');
                $table->dropForeignKeyIfExists('vnb_plan_items_approved_functional_by_foreign');
                $table->dropForeignKeyIfExists('vnb_plan_items_approved_operational_by_foreign');
            } catch (\Exception $e) {
                // Silent fail jika FK tidak ada
            }

            // Drop columns yang ditambah
            $columns = [
                'employee_id', 
                'vnb_framework_id', 
                'approved_functional_by', 
                'approved_functional_at',
                'approved_operational_by', 
                'approved_operational_at'
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('vnb_plan_items', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Restore implementation_date
            if (!Schema::hasColumn('vnb_plan_items', 'implementation_date')) {
                $table->date('implementation_date')->after('description');
            }
        });

        // Restore status enum ke original
        try {
            DB::statement("ALTER TABLE vnb_plan_items MODIFY status ENUM('not_started', 'in_progress', 'completed', 'not_achieved') DEFAULT 'not_started'");
        } catch (\Exception $e) {
            // Silent fail
        }
    }
};
