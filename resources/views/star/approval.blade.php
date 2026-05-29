@extends('layouts.app')

@section('title', 'STAR Approval - VnB Platform')
@section('page_title', 'STAR Approval')

@section('content')
<div class="px-4 space-y-4">
	<div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
		<div class="rounded-2xl border border-emerald-100 bg-white/90 px-4 py-3 shadow-sm">
			<div class="flex items-center justify-between gap-3">
				<div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Total Achievement</div>
				<div id="star-summary-achievements" class="text-2xl font-bold text-slate-900">-</div>
			</div>
		</div>
		<div class="rounded-2xl border border-emerald-100 bg-white/90 px-4 py-3 shadow-sm">
			<div class="flex items-center justify-between gap-3">
				<div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Total Employee</div>
				<div id="star-summary-employees" class="text-2xl font-bold text-slate-900">-</div>
			</div>
		</div>
		<div class="rounded-2xl border border-emerald-100 bg-white/90 px-4 py-3 shadow-sm">
			<div class="flex items-center justify-between gap-3">
				<div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Total Skor</div>
				<div id="star-summary-score" class="text-2xl font-bold text-slate-900">-</div>
			</div>
		</div>
	</div>
	<div class="overflow-x-auto">
		<table class="table-modern w-max min-w-full" style="table-layout: auto;">
			<thead class="bg-emerald-50">
				<tr>
					<th class="rounded-tl-lg whitespace-nowrap">Nama Kegiatan</th>
					<th class="whitespace-nowrap">Tanggal Kegiatan</th>
					<th class="whitespace-nowrap">Manager</th>
					<th class="whitespace-nowrap">Tanggal Pengajuan</th>
					<th class="whitespace-nowrap">Nama Employee</th>
					<th class="whitespace-nowrap">Score Akhir</th>
					<th class="whitespace-nowrap">Status</th>
					<th class="text-center rounded-tr-lg whitespace-nowrap">Aksi</th>
				</tr>
			</thead>
			<tbody id="star-approvals-body">
				<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
			</tbody>
		</table>
	</div>
</div>

<div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
	<div class="absolute inset-0 bg-black/40"></div>
	<div class="relative flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
		<button id="reject-modal-close" class="absolute top-3 right-3 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700" aria-label="Tutup">×</button>
		<div class="p-6 pr-4">
			<div class="space-y-2">
				<h3 class="text-lg font-bold text-gray-900">Tolak Pengajuan</h3>
				<p class="text-sm text-gray-500">Tambahkan alasan penolakan sebelum melanjutkan.</p>
			</div>
			<div class="mt-4 space-y-3">
				<div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">Penolakan akan diterapkan ke pengajuan yang dipilih.</div>
				<div>
					<label for="reject-reason" class="text-xs font-medium uppercase tracking-wide text-gray-500">Alasan Penolakan</label>
					<textarea id="reject-reason" rows="4" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100" placeholder="Tulis alasan penolakan di sini"></textarea>
				</div>
				<div class="flex items-center justify-end gap-2 pt-2">
					<button id="reject-modal-cancel" type="button" class="inline-flex items-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</button>
					<button id="reject-modal-confirm" type="button" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">Tolak</button>
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
const starApprovalReviewUrl = @json(route('star.star-approval.review'));
let pendingRejectIds = [];

function updateStarSummary(items = []) {
	const achievementsEl = document.getElementById('star-summary-achievements');
	const employeesEl = document.getElementById('star-summary-employees');
	const scoreEl = document.getElementById('star-summary-score');

	const totalAchievements = items.length;
	const uniqueEmployees = new Set();
	let totalScore = 0;

	items.forEach((item) => {
		const employeeIds = Array.isArray(item.employee_ids) ? item.employee_ids : [];
		employeeIds.forEach((employeeId) => {
			if (employeeId !== null && employeeId !== undefined && employeeId !== '') {
				uniqueEmployees.add(String(employeeId));
			}
		});

		const scoreValue = Number(item.total_points);
		if (!Number.isNaN(scoreValue)) {
			totalScore += scoreValue;
		}
	});

	if (achievementsEl) achievementsEl.textContent = totalAchievements.toLocaleString('id-ID');
	if (employeesEl) employeesEl.textContent = uniqueEmployees.size.toLocaleString('id-ID');
	if (scoreEl) scoreEl.textContent = totalScore.toFixed(2);
}

async function loadStarApprovals() {
	const body = document.getElementById('star-approvals-body');
	body.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

	const res = await apiGet('/api/star/approvals');
	if (!res || res.success !== true) {
		body.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Gagal memuat approvals</td></tr>';
		return;
	}

	const items = res.data || [];
	updateStarSummary(items);
	if (!items.length) {
		body.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Tidak ada approval item</td></tr>';
		return;
	}

	const approvalStatusLabel = (status) => {
		const normalized = String(status || '').toLowerCase();
		if (normalized === 'approved') return '<span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 border border-green-200">Disetujui</span>';
		if (normalized === 'rejected') return '<span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-200">Ditolak</span>';
		if (normalized === 'submitted' || normalized === 'pending_approval') return '<span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 border border-gray-200">Butuh Persetujuan</span>';
		return `<span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 border border-gray-200">${status || '-'}</span>`;
	};

	const approvalActionLabel = (status) => {
		const normalized = String(status || '').toLowerCase();
		if (normalized === 'approved') return 'Detail';
		if (normalized === 'rejected') return 'Detail';
		return 'Review';
	};

	const approvalActionClass = (status) => {
		const normalized = String(status || '').toLowerCase();
		if (normalized === 'approved') return 'inline-flex items-center px-3 py-1 rounded bg-emerald-600 text-white';
		if (normalized === 'rejected') return 'inline-flex items-center px-3 py-1 rounded bg-gray-600 text-white';
		return 'inline-flex items-center px-3 py-1 rounded bg-blue-600 text-white';
	};

		body.innerHTML = items.map(item => {
		const date = item.activity_date || '-';
		const manager = item.manager_name || '-';
		const status = approvalStatusLabel(item.status);
		const score = item.total_points !== null && item.total_points !== undefined ? Number(item.total_points).toFixed(2) : '-';
		const actionLabel = approvalActionLabel(item.status);
		const actionClass = approvalActionClass(item.status);

		const employeeNamesText = (item.employee_names || []).join(', ');
		const firstEmployeeId = (item.employee_ids && item.employee_ids.length) ? item.employee_ids[0] : '';
		return `
			<tr class="hover:bg-gray-50">
				<td class="px-4 py-3 whitespace-nowrap">${item.activity_name || '-'}</td>
				<td class="px-4 py-3 whitespace-nowrap">${item.activity_date || '-'}</td>
				<td class="px-4 py-3 whitespace-nowrap">${ item.manager_id ? `<a href="/employees/${item.manager_id}" class="text-emerald-800 font-semibold no-underline">${manager}</a>` : manager }</td>
				<td class="px-4 py-3 whitespace-nowrap">${item.submitted_at || '-'}</td>
				<td class="px-4 py-3 whitespace-nowrap">${ firstEmployeeId ? `<a href="/employees/${firstEmployeeId}" class="text-emerald-800 font-semibold no-underline">${employeeNamesText || '-'}</a>` : (employeeNamesText || '-') }</td>
				<td class="px-4 py-3 whitespace-nowrap font-semibold text-green-700">${score}</td>
				<td class="px-4 py-3 whitespace-nowrap">${status}</td>
				<td class="px-4 py-3 whitespace-nowrap flex gap-2 justify-center">
						<a href="${item.draft_group ? (starApprovalReviewUrl + '?group=' + encodeURIComponent(item.draft_group)) : (starApprovalReviewUrl + '?reviewId=' + item.recognition_ids[0]) }" class="${actionClass}">${actionLabel}</a>
				</td>
			</tr>
			`;
	}).join('');

	// attach handlers

	// Detail modal handlers
	document.querySelectorAll('.btn-detail').forEach(btn => {
		btn.addEventListener('click', async (e) => {
			const id = e.currentTarget.getAttribute('data-id');
			showRecognitionDetail(id);
		});
	});

	document.querySelectorAll('.btn-approve').forEach(btn => {
		btn.addEventListener('click', async (e) => {
			const ids = (e.currentTarget.getAttribute('data-ids') || '').split(',').filter(Boolean);
			if (!ids.length) return;
			if (!confirm('Yakin ingin menyetujui semua pengajuan pada grup ini?')) return;
			for (const id of ids) {
				const resp = await apiPost(`/api/star/approvals/${id}/approve`, {});
				if (!resp || resp.success !== true) {
					alert('Gagal approve untuk id ' + id + ': ' + (resp?.message || ''));
					break;
				}
			}
			loadStarApprovals();
		});
	});

	// Modal utilities
	function createModal() {
		if (document.getElementById('star-detail-modal')) return;
		const modal = document.createElement('div');
		modal.id = 'star-detail-modal';
		modal.className = 'fixed inset-0 z-50 flex items-center justify-center hidden';
		modal.innerHTML = `
			<div class="absolute inset-0 bg-black opacity-40"></div>
			<div class="relative bg-white rounded w-11/12 max-w-3xl p-6 shadow-lg">
				<button id="star-detail-close" class="absolute top-3 right-3 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700" aria-label="Tutup">×</button>
				<div id="star-detail-content">Memuat...</div>
			</div>
		`;
		document.body.appendChild(modal);
		document.getElementById('star-detail-close').addEventListener('click', () => {
			hideModal();
		});
		modal.addEventListener('click', (ev) => {
			if (ev.target === modal) hideModal();
		});
	}

	function showModal() {
		createModal();
		const modal = document.getElementById('star-detail-modal');
		modal.classList.remove('hidden');
	}

	function hideModal() {
		const modal = document.getElementById('star-detail-modal');
		if (modal) modal.classList.add('hidden');
	}

	async function showRecognitionDetail(id) {
		showModal();
		const content = document.getElementById('star-detail-content');
		content.innerHTML = 'Memuat detail...';

		const [recResp, schemaResp] = await Promise.all([
			apiGet(`/api/star/recognition/${id}`),
			apiGet('/api/star/schema')
		]);

		if (!recResp || recResp.success !== true) {
			content.innerHTML = '<div class="text-red-500">Gagal memuat detail rekognisi</div>';
			return;
		}

		const rec = recResp.data;
		const schema = (schemaResp && schemaResp.success) ? schemaResp.data : null;

		// Build schema view with selected answers and expected total
		let totalExpected = 0;
		const responsesByIndicator = {};
		(rec.responses || []).forEach(r => {
			responsesByIndicator[r.star_schema_indicator_id] = r;
			totalExpected += parseFloat(r.response_score || 0);
		});
		const finalScore = rec.total_points !== null && rec.total_points !== undefined ? Number(rec.total_points) : totalExpected;
		const scoreLabel = rec.total_points !== null && rec.total_points !== undefined ? 'Jumlah nilai akhir' : 'Jumlah nilai (skema)';

		let html = `
			<h3 class="text-lg font-bold mb-3">Detail Rekognisi</h3>
			<p><strong>Activity:</strong> ${rec.activity_name || '-'}</p>
			<p><strong>Tanggal:</strong> ${rec.activity_date || '-'}</p>
			<p><strong>Employee:</strong> ${rec.employee?rec.employee.name:'-'} </p>
			<p class="mt-3"><strong>Skema & Penilaian</strong></p>
			<div class="mt-2 space-y-2">
		`;

		if (schema && (schema.indicators || []).length) {
			const isApproved = String(rec.status || '').toLowerCase() === 'approved';
			schema.indicators.forEach(ind => {
				const resp = responsesByIndicator[ind.id];
				html += `<div class="p-3 border rounded">
					<div class="font-semibold">${ind.label}</div>
					<div class="text-sm text-gray-600">Pilihan:</div>
					<ul class="mt-1">`;
					ind.options.forEach(opt => {
						const selected = resp && resp.star_schema_indicator_option_id === opt.id;
						const selectedClass = selected ? (isApproved ? ' class="bg-green-50 text-green-800"' : ' class="bg-yellow-50 text-yellow-900"') : '';
						html += `<li${selectedClass}>${opt.label} — <strong>${opt.score}</strong></li>`;
					});
					html += `</ul>
				</div>`;
			});
			html += `</div>
			<div class="mt-4"><strong>${scoreLabel}:</strong> ${Number(finalScore).toFixed(2)}</div>`;
		} else {
			html += '<div class="text-gray-500">Skema STAR tidak tersedia.</div>';
		}

		html += `<div class="mt-4 flex gap-2">
			<button id="modal-approve" class="px-3 py-1 rounded bg-green-700 text-white">Approve</button>
			<button id="modal-reject" class="px-3 py-1 rounded bg-red-600 text-white">Reject</button>
		</div>`;

		content.innerHTML = html;

		document.getElementById('modal-approve').addEventListener('click', async () => {
			if (!confirm('Yakin ingin menyetujui pengajuan ini?')) return;
			const resp = await apiPost(`/api/star/approvals/${id}/approve`, {});
			if (resp && resp.success) {
				alert('Approval berhasil');
				hideModal();
				loadStarApprovals();
			} else {
				alert('Gagal approve: ' + (resp?.message || ''));
			}
		});

		document.getElementById('modal-reject').addEventListener('click', async () => {
			openRejectModal([id]);
		});
	}

	function createRejectModal() {
		const modal = document.getElementById('reject-modal');
		if (!modal || modal.dataset.bound === '1') return;
		const closeBtn = document.getElementById('reject-modal-close');
		const cancelBtn = document.getElementById('reject-modal-cancel');
		const confirmBtn = document.getElementById('reject-modal-confirm');
		if (closeBtn) closeBtn.addEventListener('click', hideRejectModal);
		if (cancelBtn) cancelBtn.addEventListener('click', hideRejectModal);
		if (confirmBtn) confirmBtn.addEventListener('click', submitRejectModal);
		modal.addEventListener('click', (ev) => { if (ev.target === modal) hideRejectModal(); });
		modal.dataset.bound = '1';
	}

	function showRejectModal() {
		const modal = document.getElementById('reject-modal');
		if (modal) modal.classList.remove('hidden');
	}

	function hideRejectModal() {
		const modal = document.getElementById('reject-modal');
		if (modal) modal.classList.add('hidden');
	}

	function openRejectModal(ids) {
		pendingRejectIds = Array.isArray(ids) ? ids.filter(Boolean) : [];
		createRejectModal();
		const reasonField = document.getElementById('reject-reason');
		if (reasonField) reasonField.value = '';
		showRejectModal();
		if (reasonField) reasonField.focus();
	}

	async function submitRejectModal() {
		const reasonField = document.getElementById('reject-reason');
		const reason = String(reasonField?.value || '').trim();
		if (!reason) {
			alert('Masukkan alasan penolakan terlebih dahulu.');
			if (reasonField) reasonField.focus();
			return;
		}
		if (!pendingRejectIds.length) {
			hideRejectModal();
			return;
		}

		for (const id of pendingRejectIds) {
			const resp = await apiPost(`/api/star/approvals/${id}/reject`, { rejection_reason: reason });
			if (!resp || resp.success !== true) {
				alert('Gagal reject untuk id ' + id + ': ' + (resp?.message || ''));
				return;
			}
		}

		alert(pendingRejectIds.length > 1 ? 'Semua pengajuan di grup ditolak' : 'Pengajuan ditolak');
		hideRejectModal();
		loadStarApprovals();
	}

	document.querySelectorAll('.btn-reject').forEach(btn => {
		btn.addEventListener('click', async (e) => {
			const ids = (e.currentTarget.getAttribute('data-ids') || '').split(',').filter(Boolean);
			if (!ids.length) return;
			openRejectModal(ids);
		});
	});
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
	loadStarApprovals();
} else {
	document.addEventListener('DOMContentLoaded', loadStarApprovals);
}
</script>
@endpush

@endsection
