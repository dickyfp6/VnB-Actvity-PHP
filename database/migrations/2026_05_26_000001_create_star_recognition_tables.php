<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('star_recognition_responses');
        Schema::dropIfExists('star_recognitions');

        // Main recognition submissions (tahap 1: description)
        Schema::create('star_recognitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->uuid('draft_group')->nullable()->index();
            $table->string('activity_name', 200);
            $table->date('activity_date');
            $table->string('organizer', 200);
            $table->string('certificate_path')->nullable();
            $table->string('certificate_original_name')->nullable();
            $table->text('activity_documentation')->nullable();
            $table->enum('status', ['draft', 'submitted', 'pending_approval', 'approved', 'rejected'])
                ->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->decimal('total_points', 8, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['manager_id', 'status']);
            $table->index(['employee_id', 'status']);
        });

        // Recognition responses to schema indicators (tahap 2: filling responses)
        Schema::create('star_recognition_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('star_recognition_id')->constrained('star_recognitions', 'id', 'fk_star_rec_resp_recognition')->cascadeOnDelete();
            $table->foreignId('star_schema_indicator_id')->constrained('star_schema_indicators', 'id', 'fk_star_rec_resp_indicator')->cascadeOnDelete();
            $table->foreignId('star_schema_indicator_option_id')->constrained('star_schema_indicator_options', 'id', 'fk_star_rec_resp_option')->cascadeOnDelete();
            $table->decimal('response_score', 8, 2);
            $table->timestamps();

            $table->unique(['star_recognition_id', 'star_schema_indicator_id'], 
                'star_recognition_response_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('star_recognition_responses');
        Schema::dropIfExists('star_recognitions');
    }
};
