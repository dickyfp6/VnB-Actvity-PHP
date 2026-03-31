<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100);
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_id')->nullable()->constrained('employees', 'id');
            $table->string('title', 255);
            $table->text('message');
            $table->string('channel', 50)->comment('email, whatsapp, in-app');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['channel', 'status']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('action', 50);
            $table->json('details')->nullable();
            $table->timestamps();
        });

        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imported_by')->constrained('employees', 'id');
            $table->string('file_name', 255);
            $table->integer('total_rows');
            $table->integer('success_rows')->default(0);
            $table->integer('error_rows')->default(0);
            $table->json('summary')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained()->onDelete('cascade');
            $table->integer('row_number');
            $table->json('raw_data');
            $table->enum('status', ['success', 'skipped', 'error', 'duplicate'])->default('success');
            $table->foreignId('employee_id')->nullable()->constrained();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['import_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_rows');
        Schema::dropIfExists('imports');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notifications');
    }
};
