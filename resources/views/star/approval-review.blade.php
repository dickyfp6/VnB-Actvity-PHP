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
					<button id="approve-modal-close" class="absolute top-3 right-3 px-3 py-1 rounded bg-gray-200">Close</button>
					<div id="approve-modal-content" class="max-h-[90vh] overflow-y-auto p-6 pr-4">Memuat...</div>
				</div>
			</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
const approvalListUrl = @json(route('star.star-approval'));

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

function reviewSchemaHtml(schema, responsesByIndicator) {
	if (!schema || !Array.isArray(schema.indicators) || !schema.indicators.length) {
		return '<div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500">Skema STAR tidak tersedia.</div>';
	}

	return schema.indicators.map(ind => {
		const resp = responsesByIndicator[ind.id];
		const options = (ind.options || []).map(opt => {
			const selected = resp && resp.star_schema_indicator_option_id === opt.id;
			return `
				<div class="flex items-center justify-between gap-4 rounded-xl px-3 py-2 ${selected ? 'bg-green-50' : 'bg-white'}">
					<div class="text-sm text-gray-800">${opt.label}${selected ? ' <span class="text-green-700 text-xs font-semibold">(dipilih)</span>' : ''}</div>
					<div class="text-sm font-semibold text-gray-900">${opt.score}</div>
				</div>
			`;
		}).join('');

		return `
			<div class="rounded-2xl border border-gray-200 bg-white p-4">
				<div class="text-sm font-semibold text-gray-900">${ind.label}</div>
				<div class="mt-2 space-y-2">${options}</div>
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
		let detailResp = null;
		let schemaResp = null;

		if (group) {
			const res = await fetch(`/api/star/recognition/draft/${group}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
			detailResp = await res.json();
		} else if (reviewId) {
			const res = await fetch(`/api/star/recognition/${reviewId}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
			detailResp = await res.json();
		}

		const schemaResult = await fetch('/api/star/schema', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
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
					<div>${reviewTextBlock('Status', data.status || '-')}</div>
				</div>

				<div class="pt-2">
					<div class="grid grid-cols-2 gap-6">
						<div class="space-y-1">
							<div class="text-xs font-medium uppercase tracking-wide text-gray-500">Dokumen Pendukung</div>
							<div class="text-base font-semibold text-gray-900 leading-7">${data.certificate_original_name || data.certificate_path || '-'}</div>
						</div>

						<div class="space-y-1">
							<div class="text-xs font-medium uppercase tracking-wide text-gray-500">Dokumentasi Saat Kegiatan</div>
							<div class="text-base font-semibold text-gray-900 leading-7">${data.activity_documentation_original_name || data.activity_documentation_path || '-'}</div>
						</div>
					</div>
				</div>
			</div>

			<div class="space-y-3 pt-2">
				<div class="text-sm font-bold uppercase tracking-wide text-gray-500">Pertanyaan Skema</div>
				${reviewSchemaHtml(schema, responsesByIndicator)}
			</div>

			<div class="flex items-center justify-between gap-4 border-t border-gray-200 pt-4">
				<div>
					<div class="text-sm font-semibold text-gray-900">Jumlah nilai (skema): ${totalScore}</div>
					<div class="text-xs text-gray-500">${reviewIds.length} pengajuan di grup ini</div>
				</div>
				<div class="flex gap-2">
					<button id="review-approve-btn" class="inline-flex items-center rounded-xl bg-green-700 px-4 py-2 text-sm font-semibold text-white">Approve</button>
					<button id="review-reject-btn" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white">Reject</button>
				</div>
			</div>
		`;

		// Open approve modal to allow overrides & adjustment
		document.getElementById('review-approve-btn').addEventListener('click', () => {
			openApproveModal(reviewIds, schema, responsesByIndicator);
		});

		// Approve modal controls
		function createApproveModal() {
			const modal = document.getElementById('approve-modal');
			if (!modal) return;
			document.getElementById('approve-modal-close').addEventListener('click', hideApproveModal);
			modal.addEventListener('click', (ev) => { if (ev.target === modal) hideApproveModal(); });
		}

		function showApproveModal() { const m = document.getElementById('approve-modal'); if (m) m.classList.remove('hidden'); }
		function hideApproveModal() { const m = document.getElementById('approve-modal'); if (m) m.classList.add('hidden'); }

		function openApproveModal(ids, schemaData, responsesByIndicator) {
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
				formHtml += `<div class="p-3 border rounded">
					<div class="text-sm font-medium text-gray-700 mb-2">${ind.label}</div>
					<select data-indicator-id="${ind.id}" class="w-full rounded border px-3 py-2">`;
				ind.options.forEach(opt => {
					const selected = resp && resp.star_schema_indicator_option_id === opt.id ? 'selected' : '';
					formHtml += `<option value="${opt.id}" data-score="${opt.score}" ${selected}>${opt.label} — ${opt.score}</option>`;
				});
				formHtml += `</select></div>`;
			});

			formHtml += `</div>
					<div class="grid grid-cols-2 gap-4 mt-2">
						<div>
							<label class="text-xs font-medium text-gray-500">Penyesuaian poin (opsional)</label>
							<input type="number" step="0.1" id="approve-adjustment" class="w-full rounded border px-3 py-2" value="0">
						</div>
						<div>
							<label class="text-xs font-medium text-gray-500">Jumlah nilai (preview)</label>
							<div id="approve-total" class="text-lg font-semibold text-gray-900">0</div>
						</div>
					</div>
					<div class="mt-3">
						<label class="text-xs font-medium text-gray-500">Catatan (opsional)</label>
						<textarea id="approve-notes" rows="2" class="w-full rounded border px-3 py-2"></textarea>
					</div>
					<div class="mt-4 flex gap-2 justify-end">
						<button type="button" id="approve-cancel" class="px-4 py-2 rounded bg-gray-200">Batal</button>
						<button type="button" id="approve-confirm" class="px-4 py-2 rounded bg-green-700 text-white">Confirm Approve</button>
					</div>
				</form>
			</div>`;

			content.innerHTML = formHtml;

			// compute total function
			function computeApproveTotal() {
				let total = 0;
				document.querySelectorAll('#approve-form select[data-indicator-id]').forEach(sel => {
					const opt = sel.options[sel.selectedIndex];
					const score = parseFloat(opt.getAttribute('data-score') || '0');
					total += score;
				});
				const adj = parseFloat(document.getElementById('approve-adjustment').value || '0');
				total = Number((total + adj).toFixed(2));
				document.getElementById('approve-total').textContent = total;
				return total;
			}

			// attach events
			document.querySelectorAll('#approve-form select[data-indicator-id]').forEach(sel => sel.addEventListener('change', computeApproveTotal));
			document.getElementById('approve-adjustment').addEventListener('input', computeApproveTotal);
			computeApproveTotal();

			document.getElementById('approve-cancel').addEventListener('click', hideApproveModal);

			document.getElementById('approve-confirm').addEventListener('click', async () => {
				const overrides = Array.from(document.querySelectorAll('#approve-form select[data-indicator-id]')).map(sel => ({
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

		document.getElementById('review-reject-btn').addEventListener('click', async () => {
			const reason = prompt('Masukkan alasan penolakan:');
			if (!reason) return;
			for (const id of reviewIds) {
				const resp = await fetch(`/api/star/approvals/${id}/reject`, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
					body: JSON.stringify({ rejection_reason: reason }),
				});
				const payload = await resp.json();
				if (!resp.ok || !payload.success) {
					reviewShowAlert('Gagal reject untuk id ' + id + ': ' + (payload?.message || ''));
					return;
				}
			}
			reviewShowAlert('Pengajuan berhasil ditolak.', 'success');
			setTimeout(() => { window.location.href = approvalListUrl; }, 700);
		});
	} catch (error) {
		content.innerHTML = '<div class="text-sm text-red-600">Gagal memuat detail review.</div>';
	}
}

document.addEventListener('DOMContentLoaded', loadReviewPage);
</script>
@endpush