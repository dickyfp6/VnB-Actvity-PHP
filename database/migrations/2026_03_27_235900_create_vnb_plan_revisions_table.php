<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vnb_plan_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('vnb_plans')->onDelete('cascade');
            $table->unsignedInteger('version_number');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('decision', ['approve', 'reject'])->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'version_number']);
            $table->index(['plan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_plan_revisions');
    }
};
