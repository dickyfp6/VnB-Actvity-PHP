

<?php $__env->startSection('title', 'STAR Approval - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'STAR Approval'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
	<div class="overflow-x-auto">
		<table class="table-modern" style="width:100%; table-layout: auto;">
			<thead>
				<tr>
					<th>Employee</th>
					<th>NIP</th>
					<th>Activity</th>
					<th>Tanggal</th>
					<th>Manager</th>
					<th>Status</th>
					<th class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody id="star-approvals-body">
				<tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
			</tbody>
		</table>
	</div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function loadStarApprovals() {
	const body = document.getElementById('star-approvals-body');
	body.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

	const res = await apiGet('/api/star/approvals');
	if (!res || res.success !== true) {
		body.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500">Gagal memuat approvals</td></tr>';
		return;
	}

	const items = res.data || [];
	if (!items.length) {
		body.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Tidak ada approval item</td></tr>';
		return;
	}

	body.innerHTML = items.map(item => {
		const date = item.activity_date || '-';
		const manager = item.manager_name || '-';
		const status = (item.status || '-');

		return `
			<tr class="hover:bg-gray-50">
				<td class="px-4 py-3"><a href="/employees/${item.employee_id}" class="font-bold" style="color:#144600">${item.employee_name || '-'}</a></td>
				<td class="px-4 py-3">${item.employee_number || '-'}</td>
				<td class="px-4 py-3">${item.activity_name || '-'}</td>
				<td class="px-4 py-3">${date}</td>
				<td class="px-4 py-3">${manager}</td>
				<td class="px-4 py-3">${status}</td>
				<td class="px-4 py-3 flex gap-2 justify-center">
					<button class="btn-approve inline-flex items-center px-3 py-1 rounded bg-green-700 text-white" data-id="${item.id}">Approve</button>
					<button class="btn-reject inline-flex items-center px-3 py-1 rounded bg-red-600 text-white" data-id="${item.id}">Reject</button>
				</td>
			</tr>
		`;
	}).join('');

	// attach handlers
	document.querySelectorAll('.btn-approve').forEach(btn => {
		btn.addEventListener('click', async (e) => {
			const id = e.currentTarget.getAttribute('data-id');
			if (!confirm('Yakin ingin menyetujui pengajuan ini?')) return;
			const resp = await apiPost(`/api/star/approvals/${id}/approve`, {});
			if (resp && resp.success) {
				alert('Approval berhasil');
				loadStarApprovals();
			} else {
				alert('Gagal approve: ' + (resp?.message || ''));
			}
		});
	});

	document.querySelectorAll('.btn-reject').forEach(btn => {
		btn.addEventListener('click', async (e) => {
			const id = e.currentTarget.getAttribute('data-id');
			const reason = prompt('Masukkan alasan penolakan:');
			if (!reason) return;
			const resp = await apiPost(`/api/star/approvals/${id}/reject`, { rejection_reason: reason });
			if (resp && resp.success) {
				alert('Pengajuan ditolak');
				loadStarApprovals();
			} else {
				alert('Gagal reject: ' + (resp?.message || ''));
			}
		});
	});
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
	loadStarApprovals();
} else {
	document.addEventListener('DOMContentLoaded', loadStarApprovals);
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/star/approval.blade.php ENDPATH**/ ?>