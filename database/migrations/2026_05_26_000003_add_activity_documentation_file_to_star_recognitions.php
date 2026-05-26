<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('star_recognitions', function (Blueprint $table) {
            if (!Schema::hasColumn('star_recognitions', 'activity_documentation_path')) {
                $table->string('activity_documentation_path')->nullable()->after('certificate_original_name');
            }

            if (!Schema::hasColumn('star_recognitions', 'activity_documentation_original_name')) {
                $table->string('activity_documentation_original_name')->nullable()->after('activity_documentation_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('star_recognitions', function (Blueprint $table) {
            if (Schema::hasColumn('star_recognitions', 'activity_documentation_original_name')) {
                $table->dropColumn('activity_documentation_original_name');
            }

            if (Schema::hasColumn('star_recognitions', 'activity_documentation_path')) {
                $table->dropColumn('activity_documentation_path');
            }
        });
    }
};
