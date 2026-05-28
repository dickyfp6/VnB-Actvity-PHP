@extends('layouts.app')

@section('title', 'STAR Approval - VnB Platform')
@section('page_title', 'STAR Approval')

@section('content')
<div class="px-4 space-y-4">
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

@push('scripts')
<script>
const starApprovalReviewUrl = @json(route('star.star-approval.review'));

async function loadStarApprovals() {
	const body = document.getElementById('star-approvals-body');
	body.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

	const res = await apiGet('/api/star/approvals');
	if (!res || res.success !== true) {
		body.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Gagal memuat approvals</td></tr>';
		return;
	}

	const items = res.data || [];
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

		body.innerHTML = items.map(item => {
		const date = item.activity_date || '-';
		const manager = item.manager_name || '-';
		const status = approvalStatusLabel(item.status);
		const score = item.total_points !== null && item.total_points !== undefined ? Number(item.total_points).toFixed(2) : '-';

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
						<a href="${item.draft_group ? (starApprovalReviewUrl + '?group=' + encodeURIComponent(item.draft_group)) : (starApprovalReviewUrl + '?reviewId=' + item.recognition_ids[0]) }" class="inline-flex items-center px-3 py-1 rounded bg-blue-600 text-white">Review</a>
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
			schema.indicators.forEach(ind => {
				const resp = responsesByIndicator[ind.id];
				html += `<div class="p-3 border rounded">
					<div class="font-semibold">${ind.label}</div>
					<div class="text-sm text-gray-600">Pilihan:</div>
					<ul class="mt-1">`;
					ind.options.forEach(opt => {
						const selected = resp && resp.star_schema_indicator_option_id === opt.id;
						html += `<li${selected? ' class="bg-green-50"':''}>${opt.label} — <strong>${opt.score}</strong>${selected? ' <span class="text-xs text-green-700">(dipilih)</span>':''}</li>`;
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
			const reason = prompt('Masukkan alasan penolakan:');
			if (!reason) return;
			const resp = await apiPost(`/api/star/approvals/${id}/reject`, { rejection_reason: reason });
			if (resp && resp.success) {
				alert('Pengajuan ditolak');
				hideModal();
				loadStarApprovals();
			} else {
				alert('Gagal reject: ' + (resp?.message || ''));
			}
		});
	}

	document.querySelectorAll('.btn-reject').forEach(btn => {
		btn.addEventListener('click', async (e) => {
			const ids = (e.currentTarget.getAttribute('data-ids') || '').split(',').filter(Boolean);
			if (!ids.length) return;
			const reason = prompt('Masukkan alasan penolakan:');
			if (!reason) return;
			for (const id of ids) {
				const resp = await apiPost(`/api/star/approvals/${id}/reject`, { rejection_reason: reason });
				if (!resp || resp.success !== true) {
					alert('Gagal reject untuk id ' + id + ': ' + (resp?.message || ''));
					break;
				}
			}
			loadStarApprovals();
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
