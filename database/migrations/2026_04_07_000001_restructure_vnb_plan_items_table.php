<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restructure vnb_plan_items table dengan proper foreign keys dan approval workflow
     */
    public function up(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            // Tambah Foreign Key ke employees
            $table->after('id', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->constrained()->onDelete('cascade')->comment('Direct reference to employee for easy filtering');
            });

            // Tambah Foreign Key ke vnb_framework_items
            $table->after('employee_id', function (Blueprint $table) {
                $table->foreignId('vnb_framework_id')->nullable()->constrained('vnb_framework_items')->onDelete('set null')->comment('Link to framework template item');
            });

            // Hapus implementation_date karena tidak dipakai
            if (Schema::hasColumn('vnb_plan_items', 'implementation_date')) {
                $table->dropColumn('implementation_date');
            }

            // Update status enum dengan opsi baru
            $table->dropColumn('status');
            $table->after('behavior_metrics', function (Blueprint $table) {
                $table->enum('status', ['draft', 'submitted', 'approved', 'revision', 'completed', 'rejected'])->default('draft')->comment('Workflow status: draft → submitted → approved/revision → completed/rejected');
            });

            // Tambah approval fields
            $table->after('status', function (Blueprint $table) {
                $table->unsignedBigInteger('approved_functional_by')->nullable()->comment('Manager functional yang approve');
                $table->unsignedBigInteger('approved_operational_by')->nullable()->comment('Manager operational yang approve');
                $table->dateTime('approved_functional_at')->nullable();
                $table->dateTime('approved_operational_at')->nullable();
                
                // Foreign key constraints
                $table->foreign('approved_functional_by')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('approved_operational_by')->references('id')->on('employees')->onDelete('set null');
            });

            // Tambah index untuk performa query
            $table->index(['employee_id', 'status']);
            $table->index(['vnb_framework_id']);
            $table->index(['approved_functional_by']);
            $table->index(['approved_operational_by']);
        });
    }

    public function down(): void
    {
        Schema::table('vnb_plan_items', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeignKeyIfExists('vnb_plan_items_employee_id_foreign');
            $table->dropForeignKeyIfExists('vnb_plan_items_vnb_framework_id_foreign');
            $table->dropForeignKeyIfExists('vnb_plan_items_approved_functional_by_foreign');
            $table->dropForeignKeyIfExists('vnb_plan_items_approved_operational_by_foreign');

            // Drop columns
            $table->dropColumn(['employee_id', 'vnb_framework_id', 'approved_functional_by', 'approved_operational_by', 'approved_functional_at', 'approved_operational_at']);

            // Restore implementation_date
            $table->date('implementation_date')->after('description');

            // Restore status enum ke original
            $table->dropColumn('status');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'not_achieved'])->default('not_started')->after('completion_percentage');

            // Drop indexes
            $table->dropIndex('vnb_plan_items_employee_id_status_index');
            $table->dropIndex('vnb_plan_items_vnb_framework_id_index');
            $table->dropIndex('vnb_plan_items_approved_functional_by_index');
            $table->dropIndex('vnb_plan_items_approved_operational_by_index');
        });
    }
};
