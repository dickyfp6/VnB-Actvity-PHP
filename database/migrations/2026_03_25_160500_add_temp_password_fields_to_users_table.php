<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'temp_password_encrypted')) {
                $table->text('temp_password_encrypted')->nullable()->after('password');
            }

            if (!Schema::hasColumn('users', 'temp_password_generated_at')) {
                $table->timestamp('temp_password_generated_at')->nullable()->after('temp_password_encrypted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'temp_password_generated_at')) {
                $table->dropColumn('temp_password_generated_at');
            }

            if (Schema::hasColumn('users', 'temp_password_encrypted')) {
                $table->dropColumn('temp_password_encrypted');
            }
        });
    }
};
