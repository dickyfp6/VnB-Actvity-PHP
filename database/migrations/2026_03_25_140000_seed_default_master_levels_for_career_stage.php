<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('master_levels')) {
            return;
        }

        $defaults = [
            'Staff/Supervisor',
            'Non-Staff',
            'Manager',
            'Harian',
            'Mingguan',
        ];

        $now = now();

        foreach ($defaults as $name) {
            $exists = DB::table('master_levels')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->exists();

            if (!$exists) {
                DB::table('master_levels')->insert([
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('master_levels')) {
            return;
        }

        DB::table('master_levels')
            ->whereIn('name', ['Staff/Supervisor', 'Non-Staff', 'Manager', 'Harian', 'Mingguan'])
            ->delete();
    }
};
