<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employment_state', 20)->default('active')->after('vnb_status');
            $table->timestamp('status_changed_at')->nullable()->after('employment_state');
            $table->text('status_change_reason')->nullable()->after('status_changed_at');
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('status_change_reason');
        });

        DB::table('employees')
            ->whereNull('employment_state')
            ->update(['employment_state' => 'active']);
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'employment_state',
                'status_changed_at',
                'status_change_reason',
                'status_changed_by',
            ]);
        });
    }
};
