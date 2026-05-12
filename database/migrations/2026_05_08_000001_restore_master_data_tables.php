<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (!Schema::hasTable('master_companies')) {
            Schema::create('master_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_companies') && DB::table('master_companies')->doesntExist()) {
            DB::table('master_companies')->insert([
                ['name' => 'PT Wismilak Inti Makmur', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'PT Gelora Djaja', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'PT Gawih Djaja', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'PT Outsourcing (Khusus OS)', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
            ]);
        }

        if (!Schema::hasTable('master_divisions')) {
            Schema::create('master_divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_divisions') && DB::table('master_divisions')->doesntExist()) {
            DB::table('master_divisions')->insert([
                ['name' => 'General', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Human Resource', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Information Technology', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Operations', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Supply Chain Management', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Commercial', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Finance & Business Support', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Strategic Research & Development', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
            ]);
        }

        if (!Schema::hasTable('master_departments')) {
            Schema::create('master_departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->constrained('master_divisions')->onDelete('cascade');
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_departments') && DB::table('master_departments')->doesntExist()) {
            $divisionIds = DB::table('master_divisions')->pluck('id', 'name');

            $departments = [
                ['division' => 'General', 'name' => 'General'],
                ['division' => 'Human Resource', 'name' => 'General'],
                ['division' => 'Human Resource', 'name' => 'People & Culture Excellence (PCX)'],
                ['division' => 'Human Resource', 'name' => 'Recruitment'],
                ['division' => 'Human Resource', 'name' => 'C&B and HRIS'],
                ['division' => 'Information Technology', 'name' => 'General'],
                ['division' => 'Information Technology', 'name' => 'Technical Support'],
                ['division' => 'Information Technology', 'name' => 'Webapp Dev'],
                ['division' => 'Information Technology', 'name' => 'SAP'],
                ['division' => 'Operations', 'name' => 'Primary Process'],
                ['division' => 'Operations', 'name' => 'Secondary Process'],
                ['division' => 'Operations', 'name' => 'Engineering & Maintenance'],
                ['division' => 'Operations', 'name' => 'Quality Assurance'],
                ['division' => 'Supply Chain Management', 'name' => 'PPIC'],
                ['division' => 'Supply Chain Management', 'name' => 'Procurement'],
                ['division' => 'Supply Chain Management', 'name' => 'Warehouse'],
                ['division' => 'Supply Chain Management', 'name' => 'Logistics'],
                ['division' => 'Commercial', 'name' => 'Brand Management'],
                ['division' => 'Commercial', 'name' => 'Sales Retail'],
                ['division' => 'Commercial', 'name' => 'Area Sales'],
                ['division' => 'Finance & Business Support', 'name' => 'Financial Planning & Analysis (FP&A)'],
                ['division' => 'Finance & Business Support', 'name' => 'Accounting & Tax'],
                ['division' => 'Finance & Business Support', 'name' => 'Treasury & Cash Management'],
                ['division' => 'Finance & Business Support', 'name' => 'Internal Audit & Risk Management'],
                ['division' => 'Finance & Business Support', 'name' => 'IT Infrastructure'],
                ['division' => 'Finance & Business Support', 'name' => 'IT Digital Transformation / Software Dev'],
                ['division' => 'Finance & Business Support', 'name' => 'General Affairs & Asset Management'],
                ['division' => 'Finance & Business Support', 'name' => 'Legal & Compliance'],
                ['division' => 'Strategic Research & Development', 'name' => 'Product Development'],
                ['division' => 'Strategic Research & Development', 'name' => 'Packaging Development'],
                ['division' => 'Strategic Research & Development', 'name' => 'Regulatory Affairs'],
            ];

            $departmentRows = [];

            foreach ($departments as $department) {
                $departmentRows[] = [
                    'division_id' => $divisionIds[$department['division']] ?? null,
                    'name' => $department['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ];
            }

            DB::table('master_departments')->insert($departmentRows);
        }

        if (!Schema::hasTable('master_positions')) {
            Schema::create('master_positions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_positions') && DB::table('master_positions')->doesntExist()) {
            DB::table('master_positions')->insert([
                ['name' => 'Harian', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Mingguan', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Borongan', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Staf/Supervisor', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Manager', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
            ]);
        }

        if (!Schema::hasTable('master_levels')) {
            Schema::create('master_levels', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_levels') && DB::table('master_levels')->doesntExist()) {
            DB::table('master_levels')->insert([
                ['name' => 'Non-Staff', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Staff', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Supervisor', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Manager', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Kepala Tim', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'General Manager', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Kepala Divisi', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Direktur', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
            ]);
        }

        if (!Schema::hasTable('master_employee_statuses')) {
            Schema::create('master_employee_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_employee_statuses') && DB::table('master_employee_statuses')->doesntExist()) {
            DB::table('master_employee_statuses')->insert([
                ['name' => 'PKWTT', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'PKWT', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'OS', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
            ]);
        }

        if (!Schema::hasTable('master_placements')) {
            Schema::create('master_placements', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('master_placements') && DB::table('master_placements')->doesntExist()) {
            DB::table('master_placements')->insert([
                ['name' => 'Bandung', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Banjarmasin', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Bengkulu', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Bogor', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Buntaran', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Cirebon', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Jakarta', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Jember', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Jombang', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Kediri', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Malang', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Medan', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Padangsidimpuan', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Pamekasan', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Pati', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Pematangsiantar', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Purwokerto', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Semarang', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Solo', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Jambi', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Surabaya', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Tangerang', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Tegal', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
                ['name' => 'Yogyakarta', 'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null],
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'master_placements',
            'master_employee_statuses',
            'master_levels',
            'master_positions',
            'master_departments',
            'master_divisions',
            'master_companies',
        ] as $tableName) {
            Schema::dropIfExists($tableName);
        }

        Schema::enableForeignKeyConstraints();
    }
};