<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // V&B Framework items: Career Stage × Behaviour × Phase × Integration
        Schema::create('vnb_framework_items', function (Blueprint $table) {
            $table->id();
            $table->string('career_stage', 50)
                  ->comment('manage_self_non_staff | manage_self_staff | manage_others | manage_manager | manage_function');
            $table->string('behaviour', 100)
                  ->comment('Empathy | Be A Wismilak Ambassador | Effective & Efficient | Speak with Data | Collaborative | Decisive | Open Mind');
            $table->string('phase', 20)
                  ->comment('1-3 | 4-6 | 6+');
            $table->text('integration_1')->nullable();
            $table->text('integration_2')->nullable();
            $table->timestamps();

            $table->unique(['career_stage', 'behaviour', 'phase']);
            $table->index('career_stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnb_framework_items');
    }
};
