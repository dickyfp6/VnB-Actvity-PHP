<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthWebController;

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');

// Data Salvage Route - Migrate data from SQLite backup to MariaDB
Route::get('/salvage-data', function () {
    try {
        // 1. MATIKAN pengecekan relasi sementara agar tidak error "Foreign Key"
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        config(['database.connections.sqlite_backup' => [
            'driver' => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix' => '',
        ]]);

        $tables = ['users', 'employees', 'vnb_plans', 'vnb_plan_items', 'vnb_plan_revisions']; 
        $report = [];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $report[] = "<span style='color:orange;'>⚠️ Tabel $table tidak ada di MariaDB. Jalankan migrate dulu!</span>";
                continue;
            }

            $oldData = DB::connection('sqlite_backup')->table($table)->get();
            $count = 0;
            $errors = [];

            foreach ($oldData as $item) {
                try {
                    // Pakai insertOrIgnore supaya kalau ID sudah ada, dia nggak bikin error
                    DB::table($table)->insertOrIgnore((array)$item);
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            $msg = "✅ Tabel <strong>$table</strong>: $count data berhasil.";
            if (count($errors) > 0) {
                $msg .= " <span style='color:red;'>(Error pertama: " . $errors[0] . ")</span>";
            }
            $report[] = $msg;
        }

        // 2. NYALAKAN KEMBALI pengecekan relasi
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return '<div style="font-family:sans-serif; padding:20px; line-height:1.6;">'
             . '<h2>Hasil Operasi Penyelamatan Data</h2>'
             . implode('<br>', $report)
             . '<br><br><a href="/">Kembali ke Dashboard</a></div>';

    } catch (\Exception $e) {
        return "Gagal total: " . $e->getMessage();
    }
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [PageController::class, 'employees'])->name('employees');
    Route::get('/vnb-plans', [PageController::class, 'vnbPlans'])->name('vnb-plans');
    Route::get('/evidence', [PageController::class, 'evidence'])->name('evidence');

    // UC001 - Manage Intercomm
    Route::get('/intercomm', [PageController::class, 'intercomm'])->name('intercomm');

    // UC002 - Manage V&B Framework
    Route::get('/vnb-framework', [PageController::class, 'vnbFramework'])->name('vnb-framework');

    // UC003 - Manage Manager
    Route::get('/managers', [PageController::class, 'managers'])->name('managers');

    // UC006 - VnB Activity
    Route::get('/vnb-activity', [PageController::class, 'vnbActivity'])->name('vnb-activity');

    // UC007 - Review & Approve Activity
    Route::get('/review-activity', [PageController::class, 'reviewActivity'])->name('review-activity');

    // Manager Portal
    Route::get('/manager/new-hires', [PageController::class, 'managerNewHires'])->name('manager.new-hires');
    Route::get('/manager/new-hires/{employeeId}', [PageController::class, 'managerNewHireDetail'])->name('manager.new-hire.detail');
    Route::get('/manager/new-hires/{employeeId}/planning-history', [PageController::class, 'managerPlanningHistory'])->name('manager.new-hire.planning-history');
    Route::get('/manager/approval-requests', [PageController::class, 'managerApprovalRequests'])->name('manager.approval-requests');
    Route::get('/manager/approval/{planId}', [PageController::class, 'managerApprovalDetail'])->name('manager.approval.detail');

    // UC009 - Master Database
    Route::get('/master-data', [PageController::class, 'masterData'])->name('master-data');

    // Account - Profile (dengan profile info + password change dalam satu halaman)
    Route::get('/my-account/profile', [PageController::class, 'profile'])->name('my-account.profile');
});
