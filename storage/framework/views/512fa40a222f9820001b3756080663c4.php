

<?php $__env->startSection('title','Profil Akun - VnB Platform'); ?>
<?php $__env->startSection('page_title','Profil Akun'); ?>
<?php $__env->startSection('page_subtitle','Lihat informasi personal dan detail akun yang sedang digunakan.'); ?>

<?php
    /** @var \App\Models\User $user */
    /** @var \App\Models\Employee|null $employee */
    /** @var \App\Models\Manager|null $manager */
    /** @var string $role */
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="card-glass rounded-xl p-6 md:p-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                <i class="fas fa-user text-white text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm mt-1">Informasi personal dan akun Anda</p>
            </div>
        </div>
    </div>

    <!-- Data Pribadi Card -->
    <div class="card-glass rounded-xl p-6 md:p-8 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center gap-3 mb-6">
            <i class="fas fa-address-card text-green-600 text-xl"></i>
            <h2 class="text-lg font-semibold text-gray-900">Data Pribadi</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
            <?php if($role === 'manager' && $manager): ?>
                <!-- Manager Profile Data -->
                <div>
                    <p class="text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="font-medium text-gray-900"><?php echo e($manager->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Email</p>
                    <p class="font-medium text-gray-900"><?php echo e($manager->email ?? ($user?->email ?? '-')); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">WhatsApp</p>
                    <p class="font-medium text-gray-900"><?php echo e($manager->whatsapp ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Perusahaan</p>
                    <p class="font-medium text-gray-900"><?php echo e($manager->company ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Divisi</p>
                    <p class="font-medium text-gray-900"><?php echo e(data_get($manager, 'division.name', data_get($manager, 'division', '-'))); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Departemen</p>
                    <p class="font-medium text-gray-900"><?php echo e(data_get($manager, 'department.name', data_get($manager, 'department', '-'))); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Posisi</p>
                    <p class="font-medium text-gray-900"><?php echo e(data_get($manager, 'position.name', data_get($manager, 'position', '-'))); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Level</p>
                    <p class="font-medium text-gray-900"><?php echo e($manager->level ?? '-'); ?></p>
                </div>
            <?php elseif($role === 'employee' && $employee): ?>
                <!-- Employee Profile Data -->
                <div>
                    <p class="text-gray-500 mb-1">Nomor Karyawan</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->employee_number ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Email</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->email ?? ($user?->email ?? '-')); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">WhatsApp</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->whatsapp ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Perusahaan</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->company ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Divisi</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->division?->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Departemen</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->department?->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Posisi</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->position?->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Level</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->level ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Placement</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->placement ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Tanggal Bergabung</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->date_joined ? $employee->date_joined->format('d M Y') : '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Tanggal Induction</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->induction_date ? $employee->induction_date->format('d M Y') : '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Status Karyawan</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->employee_status ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Status VnB</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->vnb_status ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Career Stage</p>
                    <p class="font-medium text-gray-900">
                        <?php if($employee->career_stage): ?>
                            <?php
                                $stages = [
                                    'manage_self_staff' => 'Manage Self (Staff)',
                                    'manage_self_non_staff' => 'Manage Self (Non-Staff)',
                                    'manage_others' => 'Manage Others',
                                    'manage_managers' => 'Manage Managers',
                                ];
                                $stageName = $stages[$employee->career_stage] ?? ucfirst(str_replace('_', ' ', $employee->career_stage));
                            ?>
                            <?php echo e($stageName); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Status Employee</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->status ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Manager Functional</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->managerFunctional?->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Manager Operational</p>
                    <p class="font-medium text-gray-900"><?php echo e($employee->managerOperational?->name ?? ($employee->manager_operational_id ? '-' : 'Tidak ada')); ?></p>
                </div>
            <?php elseif($role === 'employee'): ?>
                <div class="md:col-span-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
                    Akun ini belum tersambung ke data employee HRIS. Lengkapi relasi employee untuk menampilkan profil pribadi.
                </div>
            <?php else: ?>
                <p class="text-gray-500">Data profil tidak ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informasi Akun & Password -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun Platform</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-gray-500 mb-1">Email Login</p>
                <p class="font-medium text-gray-900"><?php echo e($user?->email ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Nama Pengguna</p>
                <p class="font-medium text-gray-900"><?php echo e($user?->name ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Nomor Telepon</p>
                <p class="font-medium text-gray-900"><?php echo e($user?->phone ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Status Akun</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #D0EC98; color: #144600;"><?php echo e($user?->status === 'active' ? 'Aktif' : 'Nonaktif'); ?></span>
            </div>
        </div>

        <div class="py-4 border-t">
            <a href="<?php echo e(route('profile.change-password')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition">
                <i class="fas fa-key"></i>
                <span>Ganti Password</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views\account\profile.blade.php ENDPATH**/ ?>