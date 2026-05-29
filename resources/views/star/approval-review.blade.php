@extends('layouts.app')

@section('title', 'STAR Approval Review - VnB Platform')
@section('page_title', 'STAR Approval Review')
@section('page_subtitle', 'Review pengajuan STAR dalam satu halaman sederhana.')

@section('content')
<div class="max-w-4xl mx-auto px-4 space-y-6">
	<div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
		<!-- Scoped override: hide small amber spacing/blocks inside review card -->
		<style>
			#review-content [class*="bg-amber"] {
				background: transparent !important;
				border-color: transparent !important;
				box-shadow: none !important;
				padding: 0 !important;
				margin: 0 !important;
			}

			#approve-modal-content {
				scrollbar-width: none;
				-ms-overflow-style: none;
			}

			#approve-modal-content::-webkit-scrollbar {
				width: 0;
				height: 0;
			}

			#approve-modal-content:hover {
				scrollbar-width: thin;
				scrollbar-color: #cbd5e1 transparent;
			}

			#approve-modal-content:hover::-webkit-scrollbar {
				width: 10px;
				height: 10px;
			}

			#approve-modal-content:hover::-webkit-scrollbar-thumb {
				background: #cbd5e1;
				border-radius: 9999px;
			}

			/* Scoped fixes for review layout to avoid global CSS regressions */
			#review-content .space-y-1 {
				display: flex;
				flex-direction: column;
				gap: 0.25rem;
			}

			#review-content .space-y-1 .text-xs { margin-bottom: 0.25rem; }

			/* Remove global small-text padding that breaks alignment inside review */
			#review-content .text-xs {
				padding: 0 !important;
				background: transparent !important;
				border-radius: 0 !important;
				display: block;
			}
			#review-content .text-base {
				margin: 0;
				padding: 0;
				background: transparent !important;
				box-shadow: none !important;
			}

			#review-content a.inline-flex.items-center {
				display: inline-flex !important;
				width: auto !important;
				align-self: flex-start !important;
				padding: 0.25rem 0.6rem !important;
				border-radius: 0.45rem !important;
				font-size: 0.9rem !important;
				box-shadow: none !important;
			}

			/* Ensure approval placeholder does not grow and stays tight to the right */
			#review-content > .flex.items-center.justify-end > #approval-action-buttons-placeholder {
				width: 100% !important;
				margin-left: auto !important;
			}

			/* Keep the placeholder as a normal block container */
			#approval-action-buttons-placeholder {
				display: block !important;
				width: 100% !important;
			}

			/* Restore nicer pill/button styles for badges and file links inside review */
			#review-content .review-status-badge {
				display: inline-flex;
				border-radius: 9999px;
				padding: 0.25rem 0.6rem;
				font-size: 0.75rem;
				font-weight: 600;
				line-height: 1;
			}

			#review-content .review-file-link {
				display: inline-flex;
				align-items: center;
				gap: 0.5rem;
				padding: 0.35rem 0.7rem;
				border-radius: 0.5rem;
				font-size: 0.95rem;
				font-weight: 600;
				text-decoration: none;
				box-shadow: none !important;
			}
		</style>
		<div class="flex items-start justify-between gap-4">
			<div>
				<h2 class="text-xl font-bold text-gray-900">Detail Review</h2>
				<p class="text-sm text-gray-500">Menampilkan informasi ajuan, employee, dan pertanyaan skema.</p>
			</div>
			<a href="{{ route('star.star-approval') }}" class="rounded-full border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
		</div>

		<div id="review-alert" class="hidden mt-4 rounded-xl p-3 text-sm"></div>
		<div id="review-content" class="mt-6 space-y-5">
			<div class="text-sm text-gray-500">Memuat detail review...</div>
		</div>

			<!-- Approve modal (hidden) -->
			<div id="approve-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
				<div class="absolute inset-0 bg-black/40"></div>
				<div class="relative flex w-full max-w-4xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
					<button id="approve-modal-close" class="absolute top-3 right-3 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700" aria-label="Tutup">×</button>
					<div id="approve-modal-content" class="max-h-[90vh] overflow-y-auto p-6 pr-4">Memuat...</div>
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
							<div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">Penolakan akan diterapkan ke pengajuan yang sedang dibuka.</div>
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
	</div>
</div>
@endsection

@push('scripts')
<script>
const approvalListUrl = @json(route('star.star-approval'));
let pendingRejectIds = [];

// ensure escapeHtml is available (some pages define it globally)
if (typeof escapeHtml !== 'function') {
	function escapeHtml(value) {
		return (value || '').toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	}
}

function reviewFormatDate(value) {
	if (!value) return '-';
	const parsed = new Date(value);
	if (Number.isNaN(parsed.getTime())) return value;
	return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(parsed);
}

function reviewShowAlert(message, type = 'error') {
	const alertBox = document.getElementById('review-alert');
	if (!alertBox) return;
	alertBox.className = 'mt-4 rounded-xl p-3 text-sm ' + (type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
	alertBox.textContent = message;
	alertBox.classList.remove('hidden');
}

function reviewTextBlock(label, value) {
	return `
		<div class="space-y-1">
			<div class="text-xs font-medium uppercase tracking-wide text-gray-500">${label}</div>
			<div class="text-base font-semibold text-gray-900 leading-7">${value || '-'}</div>
		</div>
	`;
}

function reviewFileLink(label, path, fileName) {
	const hasPath = Boolean(path);
	const hasFileName = Boolean(fileName);
	if (!hasPath && !hasFileName) {
		return `
			<div class="space-y-1">
				<div class="text-xs font-medium uppercase tracking-wide text-gray-500">${label}</div>
				<div class="text-base font-semibold text-gray-900 leading-7">-</div>
			</div>
		`;
	}

	const url = hasPath ? `/storage/${path}` : '#';
	const text = fileName || (String(path).split('/').pop() || '-');
	return `
		<div class="space-y-1">
			<div class="text-xs font-medium uppercase tracking-wide text-gray-500">${label}</div>
			<a href="${url}" target="_blank" rel="noopener" class="review-file-link inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 py-1.5 text-sm font-semibold text-green-800 transition hover:border-green-300 hover:bg-green-100">
				<i class="fas fa-file-arrow-down text-[11px]"></i>
				<span>${text}</span>
			</a>
		</div>
	`;
}

function reviewStatusBadge(status) {
	const normalized = String(status || '').toLowerCase();
	if (normalized === 'approved') {
		return '<span class="review-status-badge inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 border border-green-200">Disetujui</span>';
	}
	if (normalized === 'rejected') {
		return '<span class="review-status-badge inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-200">Ditolak</span>';
	}
	return '<span class="review-status-badge inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 border border-gray-200">Butuh Persetujuan</span>';
}

function reviewSchemaHtml(schema, responsesByIndicator) {
	if (!schema || !Array.isArray(schema.indicators) || !schema.indicators.length) {
		return '<div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500">Skema STAR tidak tersedia.</div>';
	}

	return schema.indicators.map(ind => {
		const resp = responsesByIndicator[ind.id];
		const options = (ind.options || []).map(opt => {
			const selected = resp && resp.star_schema_indicator_option_id === opt.id;
			return `
				<div class="flex items-center justify-between gap-4 rounded-xl px-3 py-2 ${selected ? 'bg-yellow-50 text-yellow-900' : 'text-gray-700'}">
					<div class="text-sm ${selected ? 'font-semibold' : 'text-gray-800'}">${opt.label}</div>
					<div class="text-sm font-bold text-green-600 tabular-nums">${opt.score}</div>
				</div>
			`;
		}).join('');

			return `
				<div class="py-1">
					<div class="text-sm font-semibold text-gray-900">${ind.label}</div>
					<div class="mt-2 space-y-1">${options}</div>
				</div>
			`;
	}).join('');
}

async function loadReviewPage() {
	const content = document.getElementById('review-content');
	const params = new URLSearchParams(window.location.search);
	const group = params.get('group');
	const reviewId = params.get('reviewId');

	if (!group && !reviewId) {
		content.innerHTML = '<div class="text-sm text-red-600">Parameter review belum tersedia.</div>';
		return;
	}

	try {
		console.log('loadReviewPage: starting', { group, reviewId });
		let detailResp = null;
		let schemaResp = null;

		if (group) {
			const res = await fetch(`/api/star/recognition/draft/${group}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
			if (!res.ok) {
				const text = await res.text().catch(() => null);
				console.error('loadReviewPage: draft fetch failed', res.status, text);
				reviewShowAlert('Gagal memuat detail review: ' + (text || res.status));
				content.innerHTML = '<div class="text-sm text-red-600">Gagal memuat detail review.</div>';
				return;
			}
			detailResp = await res.json();
		} else if (reviewId) {
			const res = await fetch(`/api/star/recognition/${reviewId}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
			if (!res.ok) {
				const text = await res.text().catch(() => null);
				console.error('loadReviewPage: recognition fetch failed', res.status, text);
				reviewShowAlert('Gagal memuat detail review: ' + (text || res.status));
				content.innerHTML = '<div class="text-sm text-red-600">Gagal memuat detail review.</div>';
				return;
			}
			detailResp = await res.json();
		}

		const schemaResult = await fetch('/api/star/schema', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
		if (!schemaResult.ok) {
			const text = await schemaResult.text().catch(() => null);
			console.error('loadReviewPage: schema fetch failed', schemaResult.status, text);
			reviewShowAlert('Gagal memuat skema STAR: ' + (text || schemaResult.status));
			content.innerHTML = '<div class="text-sm text-red-600">Gagal memuat detail review.</div>';
			return;
		}
		schemaResp = await schemaResult.json();

		if (!detailResp || !detailResp.success) {
			throw new Error(detailResp?.message || 'Gagal memuat detail review');
		}

		const data = detailResp.data;
		const schema = schemaResp && schemaResp.success ? schemaResp.data : null;
		const responses = Array.isArray(data.responses) ? data.responses : [];
		const responsesByIndicator = {};
		let totalScore = 0;

		responses.forEach(resp => {
			responsesByIndicator[resp.star_schema_indicator_id] = resp;
			totalScore += Number(resp.response_score || 0);
		});
		const finalScore = data.total_points !== null && data.total_points !== undefined ? Number(data.total_points) : totalScore;
		const scoreLabel = data.total_points !== null && data.total_points !== undefined ? 'SCORE akhir' : 'SCORE sementara';

		const reviewIds = Array.isArray(data.recognition_ids) && data.recognition_ids.length
			? data.recognition_ids
			: [reviewId || data.id].filter(Boolean);

		// Build clickable employee links when possible
		let employeeLinksHtml = '-';
		if (Array.isArray(data.employee_names) && Array.isArray(data.employee_ids) && data.employee_names.length) {
			employeeLinksHtml = data.employee_names.map((n, i) => {
				const id = data.employee_ids[i] || '';
				if (id) return `<a class="text-emerald-800 font-semibold no-underline" href="/employees/${id}">${n}</a>`;
				return `<span>${n}</span>`;
			}).join(', ');
		} else if (data.employee && data.employee.id) {
			employeeLinksHtml = `<a class="text-blue-600 hover:underline" href="/employees/${data.employee.id}">${data.employee.name || '-'}</a>`;
		} else if (data.employee_name) {
			employeeLinksHtml = data.employee_name;
		} else if (Array.isArray(data.employee_names) && data.employee_names.length) {
			employeeLinksHtml = data.employee_names.join(', ');
		}

		// Build manager link when possible
		let managerLinkHtml = data.manager_name || '-';
		const mgrId = data.manager_id || data.manager_employee_id || (data.manager && data.manager.id) || data.manager_user_id;
		if (mgrId) {
			managerLinkHtml = `<a class="text-emerald-800 font-semibold no-underline" href="/employees/${mgrId}">${data.manager_name || (data.manager && data.manager.name) || '-'}</a>`;
		}

		content.innerHTML = `
			<div class="space-y-5">
				<div class="grid grid-cols-2 gap-6">
					<div>${reviewTextBlock('Nama Kegiatan', data.activity_name || '-')}</div>
					<div>${reviewTextBlock('Tanggal Pelaksanaan', reviewFormatDate(data.activity_date))}</div>
				</div>

				<div class="grid grid-cols-2 gap-6">
					<div>${reviewTextBlock('Nama Employee', employeeLinksHtml)}</div>
					<div>${reviewTextBlock('Manager', managerLinkHtml)}</div>
				</div>

				<div class="grid grid-cols-2 gap-6">
					<div>${reviewTextBlock('Tanggal Pengajuan', reviewFormatDate(data.submitted_at))}</div>
					<div class="space-y-1">
						<div class="text-xs font-medium uppercase tracking-wide text-gray-500">Status</div>
						<div>${reviewStatusBadge(data.status)}</div>
					</div>
				</div>

				<div class="pt-2">
					<div class="grid grid-cols-2 gap-6">
						${reviewFileLink('Dokumen Pendukung', data.certificate_path, data.certificate_original_name)}
						${reviewFileLink('Dokumentasi Saat Kegiatan', data.activity_documentation_path, data.activity_documentation_original_name)}
					</div>
				</div>
			</div>

			<div class="space-y-3 pt-2">
				<div class="text-sm font-bold uppercase tracking-wide text-gray-500">Pertanyaan Skema</div>
				${reviewSchemaHtml(schema, responsesByIndicator)}
			</div>

			<div class="border-t border-gray-200 pt-4">
				<div class="mt-2 text-right">
					<div id="approval-action-buttons-placeholder" class="w-full" style="width:100%;">
						<!-- approval buttons inserted here conditionally -->
					</div>
				</div>
			</div>
		`;

		// Open approve modal to allow overrides & adjustment
		// Insert approve/reject buttons only when status needs approval; otherwise show final score and notes
		const actionPlaceholder = document.getElementById('approval-action-buttons-placeholder');
		const normalizedStatus = String(data.status || '').toLowerCase();
		const needsApproval = ['pending_approval', 'submitted', 'diajukan', 'waiting_approval', 'waiting_manager_approval'].includes(normalizedStatus);
		if (actionPlaceholder) {
			if (needsApproval) {
				actionPlaceholder.innerHTML = `<button id="review-approve-btn" class="inline-flex items-center rounded-xl bg-green-700 px-4 py-2 text-sm font-semibold text-white">Approve</button>
					<button id="review-reject-btn" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white">Reject</button>`;
				const btnApprove = document.getElementById('review-approve-btn');
				if (btnApprove) btnApprove.addEventListener('click', () => openApproveModal(reviewIds, schema, responsesByIndicator, data.approval_notes || data.notes || ''));
				const btnReject = document.getElementById('review-reject-btn');
				if (btnReject) btnReject.addEventListener('click', () => openRejectModal(reviewIds));
			} else {
				const isRejected = ['rejected', 'ditolak'].includes(normalizedStatus);
				const notes = data.approval_notes || data.notes || '';
				actionPlaceholder.innerHTML = isRejected ? `
					<div class="w-full pt-4">
						<div class="border-t border-gray-200"></div>

						<div class="mt-4 w-full">
							<div class="text-xs font-medium uppercase tracking-wide text-gray-500 text-left">Catatan Ditolak</div>
							<div class="mt-1 text-base text-gray-900 leading-7 text-left">${notes ? escapeHtml(notes) : '-'}</div>
						</div>
					</div>
				` : `
					<div class="w-full pt-4">
						<div class="border-t border-gray-200"></div>

						<div class="mt-4 space-y-3 w-full">
							<div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 w-full">
								<div class="text-xs font-medium uppercase tracking-wide text-gray-500 text-left">Penyesuaian Nilai</div>
								<div class="text-sm font-semibold text-gray-900 text-right tabular-nums">+0</div>
							</div>

							<div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 w-full">
								<div class="text-xs font-medium uppercase tracking-wide text-gray-500 text-left">Nilai Akhir</div>
								<div class="text-sm font-bold text-gray-900 text-right tabular-nums">${finalScore !== null ? finalScore.toFixed(2) : '-'}</div>
							</div>
						</div>

						<div class="h-4"></div>

						<div class="w-full">
							<div class="text-xs font-medium uppercase tracking-wide text-gray-500 text-left">Catatan</div>
							<div class="mt-1 text-base text-gray-900 leading-7 text-left">${notes ? escapeHtml(notes) : '-'}</div>
						</div>
					</div>
				`;
			}
		}

		// Approve modal controls
		function createApproveModal() {
			const modal = document.getElementById('approve-modal');
			if (!modal) return;
			document.getElementById('approve-modal-close').addEventListener('click', hideApproveModal);
			modal.addEventListener('click', (ev) => { if (ev.target === modal) hideApproveModal(); });
		}

		function showApproveModal() { const m = document.getElementById('approve-modal'); if (m) m.classList.remove('hidden'); }
		function hideApproveModal() { const m = document.getElementById('approve-modal'); if (m) m.classList.add('hidden'); }

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

		function showRejectModal() { const m = document.getElementById('reject-modal'); if (m) m.classList.remove('hidden'); }
		function hideRejectModal() { const m = document.getElementById('reject-modal'); if (m) m.classList.add('hidden'); }

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

			for (const rid of pendingRejectIds) {
				const resp = await fetch(`/api/star/approvals/${rid}/reject`, { method: 'POST', credentials: 'same-origin', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ rejection_reason: reason }) });
				const payload = await resp.json();
				if (!resp.ok || !payload.success) {
					alert('Gagal reject untuk id ' + rid + ': ' + (payload?.message || ''));
					return;
				}
			}

			alert(pendingRejectIds.length > 1 ? 'Semua pengajuan di grup ditolak' : 'Pengajuan ditolak');
			hideRejectModal();
			location.reload();
		}

		function openApproveModal(ids, schemaData, responsesByIndicator, initialNotes = '') {
			createApproveModal();
			const content = document.getElementById('approve-modal-content');
			if (!content) return;
			if (!schemaData || !Array.isArray(schemaData.indicators)) {
				content.innerHTML = '<div class="text-sm text-red-600">Skema tidak tersedia, tidak dapat melakukan approve dengan penyesuaian.</div>';
				showApproveModal();
				return;
			}

			// build form
			let formHtml = `<div class="space-y-4">
				<h3 class="text-lg font-bold">Konfirmasi Approve</h3>
				<p class="text-sm text-gray-600">Sesuaikan kategori per indikator (opsional) dan/atau tambahkan poin penyesuaian.</p>
				<form id="approve-form">
					<div class="space-y-3">`;

			schemaData.indicators.forEach(ind => {
				const resp = responsesByIndicator[ind.id];
					const currentOption = (ind.options || []).find(opt => resp && resp.star_schema_indicator_option_id === opt.id) || (ind.options || [])[0] || null;
					// determine trigger and label classes: if manager already selected (currentOption) show yellow
					const hasManagerSelection = Boolean(currentOption);
					const triggerBtnClass = hasManagerSelection ? 'flex w-full items-center justify-between gap-4 rounded-full border border-yellow-200 bg-yellow-50 px-4 py-3 text-left transition' : 'flex w-full items-center justify-between gap-4 rounded-full border border-gray-200 bg-white/50 px-4 py-3 text-left transition hover:border-gray-300 hover:bg-gray-50';
					const labelClass = hasManagerSelection ? 'text-sm font-semibold text-yellow-900' : 'text-sm font-semibold text-gray-900';
					const scoreSpanClass = hasManagerSelection ? 'inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-bold tabular-nums text-yellow-900' : 'inline-flex items-center rounded-full bg-white/70 px-3 py-1 text-sm font-bold tabular-nums text-gray-900';
					formHtml += `<div class="space-y-2">
						<div class="text-sm font-medium text-gray-700">${ind.label}</div>
						<div class="relative">
							<button type="button" data-indicator-trigger="${ind.id}" class="${triggerBtnClass}">
								<span data-indicator-label="${ind.id}" class="${labelClass}">${currentOption ? currentOption.label : 'Pilih opsi'}</span>
								<span data-indicator-score="${ind.id}" class="${scoreSpanClass}">${currentOption ? currentOption.score : '-'}</span>
							</button>
							<input type="hidden" data-indicator-id="${ind.id}" value="${currentOption ? currentOption.id : ''}">
							<div data-indicator-menu="${ind.id}" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-20 hidden overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
								<div class="max-h-64 overflow-y-auto py-1">`;
					(ind.options || []).forEach(opt => {
						const selected = currentOption && currentOption.id === opt.id;
						// initial manager-selected options -> yellow. When intercomm clicks an option, JS will mark it green.
						const optionClass = selected ? 'bg-yellow-50 text-yellow-900' : 'text-gray-700 hover:bg-gray-50';
						const scoreClass = selected ? 'bg-yellow-100 text-yellow-900' : 'bg-transparent text-green-600';
						formHtml += `<button type="button" data-indicator-option="${ind.id}" data-option-id="${opt.id}" data-option-label="${escapeHtml(opt.label)}" data-option-score="${opt.score}" class="flex w-full items-center justify-between gap-4 px-4 py-2 text-left transition ${optionClass}">
							<span class="text-sm ${selected ? 'font-semibold' : ''}">${opt.label}</span>
							<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold tabular-nums ${scoreClass}">${opt.score}</span>
						</button>`;
					});
					formHtml += `
								</div>
							</div>
						</div>
					</div>`;
			});

			formHtml += `</div>
					<div class="grid grid-cols-2 gap-4 mt-2">
						<div>
							<label class="text-xs font-medium text-gray-500">Penyesuaian poin (opsional)</label>
							<input type="number" step="0.1" id="approve-adjustment" class="w-full rounded-lg border px-3 py-2" value="0">
						</div>
						<div>
							<label class="text-xs font-medium text-gray-500">Jumlah nilai (preview)</label>
							<div id="approve-total" class="text-lg font-semibold text-gray-900">0</div>
						</div>
					</div>
					<div class="mt-3">
						<label class="text-xs font-medium text-gray-500">Catatan (opsional)</label>
						<textarea id="approve-notes" rows="2" class="w-full rounded-lg border px-3 py-2">${escapeHtml(initialNotes)}</textarea>
					</div>
					<div class="mt-4 flex gap-2 justify-end">
						<button type="button" id="approve-cancel" class="px-4 py-2 rounded-lg bg-gray-200">Batal</button>
						<button type="button" id="approve-confirm" class="px-4 py-2 rounded-lg bg-green-700 text-white">Approve</button>
					</div>
				</form>
			</div>`;

			content.innerHTML = formHtml;

			const closeAllDropdowns = () => {
				document.querySelectorAll('[data-indicator-menu]').forEach(menu => menu.classList.add('hidden'));
			};

			document.querySelectorAll('[data-indicator-trigger]').forEach(trigger => {
				trigger.addEventListener('click', (event) => {
					event.stopPropagation();
					const indicatorId = trigger.getAttribute('data-indicator-trigger');
					const menu = document.querySelector(`[data-indicator-menu="${indicatorId}"]`);
					if (!menu) return;
					const isHidden = menu.classList.contains('hidden');
					closeAllDropdowns();
					if (isHidden) {
						menu.classList.remove('hidden');
					}
				});
			});

			document.querySelectorAll('[data-indicator-option]').forEach(option => {
				option.addEventListener('click', (event) => {
					event.stopPropagation();
					const indicatorId = option.getAttribute('data-indicator-option');
					const optionId = option.getAttribute('data-option-id');
					const optionLabel = option.getAttribute('data-option-label') || '';
					const optionScore = option.getAttribute('data-option-score') || '-';
					const hiddenInput = document.querySelector(`[data-indicator-id="${indicatorId}"]`);
					const labelEl = document.querySelector(`[data-indicator-label="${indicatorId}"]`);
					const scoreEl = document.querySelector(`[data-indicator-score="${indicatorId}"]`);
					if (hiddenInput) hiddenInput.value = optionId;
					if (labelEl) labelEl.textContent = optionLabel;
					if (scoreEl) scoreEl.textContent = optionScore;
					closeAllDropdowns();
					computeApproveTotal();

					// Visual: mark selected option green for intercomm, and clear previous selections
					const menu = option.closest('[data-indicator-menu]');
					if (menu) {
						menu.querySelectorAll('[data-indicator-option]').forEach(optBtn => {
							// remove any selection classes (green or yellow)
							optBtn.classList.remove('bg-green-50','text-green-800','bg-yellow-50','text-yellow-900','font-semibold');
							const scoreSpan = optBtn.querySelector('span.inline-flex');
							if (scoreSpan) {
								scoreSpan.classList.remove('bg-green-100','text-green-700','bg-yellow-100','text-yellow-900');
							}
						});

						// apply green classes to clicked option
						option.classList.add('bg-green-50','text-green-800','font-semibold');
						const myScoreSpan = option.querySelector('span.inline-flex');
						if (myScoreSpan) myScoreSpan.classList.add('bg-green-100','text-green-700');

						// update trigger button visuals to green (intercomm selection)
						const triggerBtn = document.querySelector(`[data-indicator-trigger="${indicatorId}"]`);
						if (triggerBtn) {
							// remove yellow classes if present
							triggerBtn.classList.remove('border-yellow-200','bg-yellow-50');
							const lbl = triggerBtn.querySelector(`[data-indicator-label="${indicatorId}"]`);
							const sc = triggerBtn.querySelector(`[data-indicator-score="${indicatorId}"]`);
							if (lbl) {
								lbl.classList.remove('text-yellow-900');
								lbl.classList.add('text-green-800','font-semibold');
							}
							if (sc) {
								sc.classList.remove('bg-yellow-100','text-yellow-900');
								sc.classList.add('bg-green-100','text-green-700');
							}
							triggerBtn.classList.add('border-green-200','bg-green-50');
						}
					}
				});
			});

			// compute total function
			function computeApproveTotal() {
				let total = 0;
				document.querySelectorAll('#approve-form [data-indicator-id]').forEach(sel => {
					const indicatorId = sel.getAttribute('data-indicator-id');
					const optionId = sel.value;
					const opt = document.querySelector(`[data-indicator-option="${indicatorId}"][data-option-id="${optionId}"]`);
					const score = parseFloat(opt?.getAttribute('data-option-score') || '0');
					total += score;
				});
				const adj = parseFloat(document.getElementById('approve-adjustment').value || '0');
				total = Number((total + adj).toFixed(2));
				document.getElementById('approve-total').textContent = total;
				return total;
			}

			// attach events
			document.getElementById('approve-adjustment').addEventListener('input', computeApproveTotal);
			computeApproveTotal();

			document.getElementById('approve-cancel').addEventListener('click', hideApproveModal);

			document.getElementById('approve-confirm').addEventListener('click', async () => {
				const overrides = Array.from(document.querySelectorAll('#approve-form [data-indicator-id]')).map(sel => ({
					indicator_id: Number(sel.getAttribute('data-indicator-id')),
					option_id: Number(sel.value),
				}));
				const adjustment = parseFloat(document.getElementById('approve-adjustment').value || '0');
				const notes = document.getElementById('approve-notes').value || '';
				// disable button
				document.getElementById('approve-confirm').disabled = true;
				for (const id of ids) {
					try {
						const resp = await fetch(`/api/star/approvals/${id}/approve`, {
							method: 'POST',
							credentials: 'same-origin',
							headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
							body: JSON.stringify({ overrides: overrides, adjustment: adjustment, notes: notes }),
						});
						const payload = await resp.json();
						if (!resp.ok || !payload.success) {
							reviewShowAlert('Gagal approve untuk id ' + id + ': ' + (payload?.message || ''));
							document.getElementById('approve-confirm').disabled = false;
							return;
						}
					} catch (err) {
						reviewShowAlert('Gagal melakukan request approve: ' + err.message);
						document.getElementById('approve-confirm').disabled = false;
						return;
					}
				}
				reviewShowAlert('Pengajuan berhasil disetujui.', 'success');
				hideApproveModal();
				setTimeout(() => { window.location.href = approvalListUrl; }, 700);
			});

			showApproveModal();
		}


	} catch (error) {
		console.error('loadReviewPage error', error);
		reviewShowAlert(error?.message || 'Gagal memuat detail review.');
		content.innerHTML = '<div class="text-sm text-red-600">Gagal memuat detail review.</div>';
	}
}

document.addEventListener('DOMContentLoaded', loadReviewPage);
</script>
@endpush