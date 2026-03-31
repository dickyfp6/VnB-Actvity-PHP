<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vnb_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_item_id')->constrained('vnb_plan_items')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('employees', 'id');
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->string('file_type', 50);
            $table->integer('file_size');
            $table->string('s3_url')->nullable()->comment('Supabase Storage URL');
            $table->text('description')->nullable();
            $table->enum('status', ['pending_verification', 'verified', 'rejected'])->default('pending_verification');
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->index(['plan_item_id', 'status']);
        });

        Schema::create('vnb_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_item_id')->constrained('vnb_plan_items')->onDelete('cascade');
            $table->json('behavior_progress')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->dateTime('last_updated_at');
            $table->timestamps();

            $table->unique(['employee_id', 'plan_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_progress');
        Schema::dropIfExists('vnb_evidences');
    }
};
