

<?php $__env->startSection('title', 'STAR Recognition - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'STAR Recognition'); ?>
<?php $__env->startSection('page_subtitle', 'Kelola daftar ajuan recognition dan buka skema STAR saat dibutuhkan.'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-6xl mx-auto px-4">
	<div class="flex items-center justify-between gap-4">
		<button type="button" onclick="openStarSchemaPreview()" class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-white px-4 py-2 text-sm font-semibold text-green-800 transition hover:bg-green-50">
			<i class="fas fa-layer-group text-xs"></i>
			Skema STAR
		</button>
		<a href="<?php echo e(route('star.recognition.create')); ?>" class="inline-flex items-center gap-2 rounded-full bg-[#144600] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
			<i class="fas fa-plus text-xs"></i>
			Tambah Ajuan
		</a>
	</div>

	<div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
		<div class="overflow-x-auto">
			<table id="recognition-table" class="w-full min-w-[1180px] table-fixed divide-y divide-gray-200">
				<colgroup>
					<col style="width: 70px;">
					<col style="width: 180px;">
					<col style="width: 220px;">
					<col style="width: 150px;">
					<col style="width: 230px;">
					<col style="width: 230px;">
					<col style="width: 110px;">
					<col style="width: 90px;">
				</colgroup>
				<thead class="bg-gray-50">
					<tr>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Kegiatan</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Employee</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Dokumen Pendukung</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Dokumentasi</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
					</tr>
				</thead>
				<tbody id="recognition-table-body" class="divide-y divide-gray-100 bg-white">
					<tr>
						<td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">Memuat daftar ajuan...</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo $__env->make('star.partials.schema-preview-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let recognitionRows = [];

function formatDate(value) {
	if (!value) return '-';
	const parsed = new Date(value);
	if (Number.isNaN(parsed.getTime())) return value;
	return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(parsed);
}

function getFileName(pathValue, originalName) {
	if (originalName) {
		return originalName;
	}
	if (pathValue) {
		const parts = String(pathValue).split('/');
		return parts[parts.length - 1] || pathValue;
	}
	return '-';
}

function getStorageUrl(pathValue) {
	if (!pathValue) return '#';
	return `/storage/${String(pathValue).replace(/^\/+/, '')}`;
}

function mapRecognitionStatus(status) {
	const raw = String(status || '').toLowerCase();
	if (['rejected', 'ditolak'].includes(raw)) {
		return { label: 'Ditolak', className: 'bg-red-50 text-red-700 border-red-100' };
	}
	if (['draft'].includes(raw)) {
		return { label: 'Draft', className: 'bg-slate-50 text-slate-700 border-slate-200' };
	}
	return { label: 'Diajukan', className: 'bg-amber-50 text-amber-700 border-amber-100' };
}

function renderRecognitionTable(items) {
	const tbody = document.getElementById('recognition-table-body');
	const table = document.getElementById('recognition-table');
	if (!tbody) return;

	if (!items.length) {
		if (table) {
			table.className = 'min-w-full w-full table-auto divide-y divide-gray-200';
		}
		tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">Belum ada ajuan recognition.</td></tr>';
		return;
	}

	if (table) {
		table.className = 'w-full min-w-[1180px] table-fixed divide-y divide-gray-200';
	}

	tbody.innerHTML = items.map((item, index) => {
		const employeeName = Array.isArray(item.employee_names)
			? item.employee_names.join(', ')
			: (item.employee_names_text || item.employee?.name || item.employee?.name_display || item.employee?.employee_number || `Employee #${item.employee_id ?? '-'}`);
		const statusMeta = mapRecognitionStatus(item.status);
		const supportName = getFileName(item.certificate_path, item.certificate_original_name);
		const supportUrl = item.certificate_path ? getStorageUrl(item.certificate_path) : '#';
		const documentationName = getFileName(item.activity_documentation_path, item.activity_documentation_original_name);
		const documentationUrl = item.activity_documentation_path ? getStorageUrl(item.activity_documentation_path) : '#';
		const actionHref = item.status === 'draft' && item.draft_group
			? `/star/recognition/create?group=${encodeURIComponent(item.draft_group)}`
			: '#';
		return `
			<tr class="hover:bg-gray-50/80">
				<td class="whitespace-nowrap px-5 py-4 text-sm text-gray-900">${index + 1}</td>
				<td class="truncate px-5 py-4 text-sm text-gray-700" title="${item.activity_name || '-'}">${item.activity_name || '-'}</td>
				<td class="truncate px-5 py-4 text-sm text-gray-700" title="${employeeName}">${employeeName}</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">${formatDate(item.activity_date)}</td>
				<td class="px-5 py-4 text-sm text-gray-700">
					<div class="flex flex-col gap-1">
						<a href="${supportUrl}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center gap-2 truncate rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 transition hover:bg-green-100 ${item.certificate_path ? '' : 'pointer-events-none opacity-50'}" title="${supportName}">
							<i class="fas fa-paperclip text-[10px]"></i>
							<span class="truncate">${supportName}</span>
						</a>
					</div>
				</td>
				<td class="px-5 py-4 text-sm text-gray-700">
					<div class="flex flex-col gap-1">
						<a href="${documentationUrl}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center gap-2 truncate rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800 transition hover:bg-sky-100 ${item.activity_documentation_path ? '' : 'pointer-events-none opacity-50'}" title="${documentationName}">
							<i class="fas fa-image text-[10px]"></i>
							<span class="truncate">${documentationName}</span>
						</a>
					</div>
				</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm"><span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ${statusMeta.className}">${statusMeta.label}</span></td>
				<td class="whitespace-nowrap px-5 py-4 text-sm"><a href="${item.draft_group ? '/star/recognition/create?group=' + encodeURIComponent(item.draft_group) : '#'}" class="inline-flex rounded-full border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">Lihat</a></td>
			</tr>
		`;
	}).join('');
}

async function loadRecognitions() {
	const tbody = document.getElementById('recognition-table-body');
	try {
		const res = await fetch('/api/star/recognition', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
		const payload = await res.json();
		if (!res.ok || !payload.success) throw new Error(payload.message || 'failed');
		recognitionRows = Array.isArray(payload.data) ? payload.data : [];
		renderRecognitionTable(recognitionRows);
	} catch (error) {
		if (tbody) {
			tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-10 text-center text-sm text-red-600">Gagal memuat daftar ajuan.</td></tr>';
		}
	}
}

document.addEventListener('DOMContentLoaded', () => {
	loadRecognitions();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/star/recognition.blade.php ENDPATH**/ ?>