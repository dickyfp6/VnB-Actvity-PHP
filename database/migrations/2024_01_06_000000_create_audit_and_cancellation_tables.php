<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('employees', 'id');
            $table->string('action_type', 100);
            $table->string('target_type', 100);
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['actor_id', 'created_at']);
            $table->index(['target_type', 'target_id']);
            $table->index(['action_type', 'created_at']);
        });

        Schema::create('vnb_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('reason', ['budaya_kerja', 'tidak_cocok_vnb', 'others'])->comment('Culture, Not Suitable, Others');
            $table->text('notes');
            $table->foreignId('canceled_by')->constrained('employees', 'id');
            $table->foreignId('approved_by')->nullable()->constrained('employees', 'id');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('approval_notes')->nullable();
            $table->dateTime('canceled_at');
            $table->timestamps();

            $table->index(['employee_id', 'approval_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_cancellations');
        Schema::dropIfExists('activity_logs');
    }
};
