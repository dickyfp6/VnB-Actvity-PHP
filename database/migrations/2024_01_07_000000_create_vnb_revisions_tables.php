<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update vnb_plans table - tambah revision tracking fields
        Schema::table('vnb_plans', function (Blueprint $table) {
            $table->after('status', function ($table) {
                $table->integer('revision_count')->default(0)->comment('Total revisions requested');
                $table->text('revision_notes')->nullable()->comment('Current revision notes from manager');
            });
        });

        // Table untuk menyimpan history revisi
        Schema::create('vnb_plan_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vnb_plan_id')->constrained('vnb_plans')->onDelete('cascade');
            $table->integer('revision_number')->comment('Revision attempt number');
            $table->foreignId('requested_by')->constrained('employees', 'id')->comment('Manager who requested revision');
            $table->text('revision_notes')->comment('Catatan revisi dari manager');
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'applied'])->default('pending')->comment('pending=draft, in_progress=being worked on, submitted=nhire kirim, applied=manager approve');
            $table->dateTime('requested_at');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('applied_at')->nullable();
            $table->timestamps();

            $table->index(['vnb_plan_id', 'revision_number']);
            $table->unique(['vnb_plan_id', 'revision_number']);
        });

        // Table untuk version control detail revisi (khusus Activity Planning)
        Schema::create('vnb_plan_revision_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vnb_plan_revision_id')->constrained('vnb_plan_revisions')->onDelete('cascade');
            $table->foreignId('vnb_plan_item_id')->constrained('vnb_plan_items')->onDelete('cascade')->comment('Activity yang direvisi');
            $table->json('old_values')->nullable()->comment('Values sebelum revisi: title, desc, dates, deliverables, metrics');
            $table->json('new_values')->nullable()->comment('Values sesudah revisi');
            $table->foreignId('changed_by')->constrained('employees', 'id')->comment('New hire yang melakukan perubahan');
            $table->timestamps();

            $table->index(['vnb_plan_revision_id']);
            $table->index(['vnb_plan_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_plan_revision_details');
        Schema::dropIfExists('vnb_plan_revisions');

        Schema::table('vnb_plans', function (Blueprint $table) {
            $table->dropColumn(['revision_count', 'revision_notes']);
        });
    }
};
