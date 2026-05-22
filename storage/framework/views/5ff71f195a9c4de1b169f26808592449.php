

<?php $__env->startSection('title', 'VnB Activity - Belum Ditugaskan'); ?>
<?php $__env->startSection('page_title', 'VnB Activity'); ?>
<?php $__env->startSection('page_subtitle', 'Modul aktivitas belum tersedia untuk akun Anda saat ini.'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-8">
    <div class="card-glass w-full max-w-2xl rounded-3xl border border-amber-200/80 bg-gradient-to-br from-amber-50/95 via-white to-orange-50/80 p-8 md:p-12 text-center shadow-[0_18px_60px_rgba(180,83,9,0.12)]">
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-700 shadow-inner">
            <i class="fas fa-user-slash text-2xl"></i>
        </div>
        <p class="text-[11px] font-black uppercase tracking-[0.35em] text-amber-600">Belum Ditugaskan</p>
        <h2 class="mt-3 text-3xl md:text-4xl font-black tracking-tight text-gray-900">Anda belum di-assign untuk VnB Activity</h2>
        <p class="mt-4 text-base md:text-lg leading-relaxed text-amber-900/80 max-w-xl mx-auto">
            Mohon maaf, akun Anda belum masuk sebagai participant VnB Activity.
            Untuk informasi lebih lanjut, silakan hubungi manager Anda.
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-activity/not-assigned.blade.php ENDPATH**/ ?>