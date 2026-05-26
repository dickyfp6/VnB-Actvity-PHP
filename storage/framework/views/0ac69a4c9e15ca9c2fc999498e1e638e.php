

<?php $__env->startSection('title', 'STAR Achievements - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'STAR Achievements'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
	<div class="card-glass rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
		<div class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
			<div>
				<p class="text-xs font-bold uppercase tracking-[0.22em] text-green-700">STAR Framework</p>
				<h2 class="mt-1 text-xl font-bold text-gray-900">Achievements</h2>
				<p class="mt-1 text-sm text-gray-600">Lihat skema STAR untuk memahami acuan penilaian sebelum membuka detail achievements.</p>
			</div>
			<a href="<?php echo e(route('star.schema')); ?>" class="inline-flex items-center gap-2 self-start rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 transition hover:bg-green-100">
				<i class="fas fa-layer-group text-[10px]"></i>
				Lihat Skema STAR
			</a>
		</div>
	</div>

	<div class="card-glass rounded-2xl border border-gray-200 shadow-sm p-5">
		<p class="text-sm text-gray-600">Halaman achievements akan diisi ringkasan capaian di sini.</p>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/star/achievements.blade.php ENDPATH**/ ?>