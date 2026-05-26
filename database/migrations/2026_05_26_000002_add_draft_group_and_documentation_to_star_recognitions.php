<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('star_recognitions', function (Blueprint $table) {
            if (!Schema::hasColumn('star_recognitions', 'draft_group')) {
                $table->uuid('draft_group')->nullable()->index()->after('employee_id');
            }

            if (!Schema::hasColumn('star_recognitions', 'activity_documentation')) {
                $table->text('activity_documentation')->nullable()->after('certificate_original_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('star_recognitions', function (Blueprint $table) {
            if (Schema::hasColumn('star_recognitions', 'activity_documentation')) {
                $table->dropColumn('activity_documentation');
            }

            if (Schema::hasColumn('star_recognitions', 'draft_group')) {
                $table->dropIndex(['draft_group']);
                $table->dropColumn('draft_group');
            }
        });
    }
};
