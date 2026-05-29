@extends('layouts.app')

@section('title', 'STAR Achievements - WisCore')
@section('page_title', 'STAR Achievements')

@section('content')
<div class="space-y-6">
	<div class="card-glass rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
		<div class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
			<div>
				<p class="text-xs font-bold uppercase tracking-[0.22em] text-green-700">STAR Framework</p>
				<h2 class="mt-1 text-xl font-bold text-gray-900">Achievements</h2>
				<p class="mt-1 text-sm text-gray-600">Daftar capaian STAR yang sudah disetujui untuk akun employee yang sedang login.</p>
			</div>
			<div class="grid grid-cols-2 gap-3 text-sm">
				<div class="rounded-2xl border border-gray-200 bg-white px-4 py-3">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Capaian</div>
					<div id="achievement-count" class="mt-1 text-2xl font-bold text-gray-900">0</div>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white px-4 py-3">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Score</div>
					<div id="achievement-score" class="mt-1 text-2xl font-bold text-green-700">0</div>
				</div>
			</div>
		</div>
	</div>

	<div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
		<table class="table-modern w-max min-w-full" style="table-layout: auto;">
			<thead class="bg-emerald-50">
				<tr>
					<th class="rounded-tl-lg whitespace-nowrap">Nama Kegiatan</th>
					<th class="whitespace-nowrap">Tanggal Kegiatan</th>
					<th class="whitespace-nowrap">Manager</th>
					<th class="whitespace-nowrap">Tanggal Approve</th>
					<th class="whitespace-nowrap">Score Akhir</th>
					<th class="whitespace-nowrap">Status</th>
					<th class="text-center rounded-tr-lg whitespace-nowrap">Aksi</th>
				</tr>
			</thead>
			<tbody id="star-achievements-body">
				<tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
			</tbody>
		</table>
	</div>
</div>

<div id="achievement-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
	<div class="absolute inset-0 bg-black/40"></div>
	<div class="relative flex w-full max-w-3xl max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl mx-auto">
		<button id="achievement-modal-close" class="absolute top-3 right-3 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700" aria-label="Tutup">×</button>
		<div id="achievement-modal-content" class="max-h-[90vh] overflow-y-auto p-6 pr-4">Memuat...</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
const achievementSchemaUrl = @json(route('star.schema'));

function achievementFormatDate(value) {
	if (!value) return '-';
	const parsed = new Date(value);
	if (Number.isNaN(parsed.getTime())) return value;
	return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(parsed);
}

function achievementFormatScore(value) {
	const score = Number(value ?? 0);
	return Number.isFinite(score) ? score.toFixed(2).replace(/\.00$/, '') : '-';
}

function achievementStatusBadge(status) {
	const normalized = String(status || '').toLowerCase();
	if (normalized === 'approved') {
		return '<span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 border border-green-200">Disetujui</span>';
	}
	if (normalized === 'rejected') {
		return '<span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-200">Ditolak</span>';
	}
	return '<span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 border border-gray-200">Butuh Persetujuan</span>';
}

function achievementSchemaHtml(schema, responsesByIndicator) {
	if (!schema || !Array.isArray(schema.indicators) || !schema.indicators.length) {
		return '<div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500">Skema STAR tidak tersedia.</div>';
	}

	const selectedItems = schema.indicators.map(ind => {
		const resp = responsesByIndicator[ind.id];
		if (!resp) return null;

		const selectedOption = (ind.options || []).find(opt => resp.star_schema_indicator_option_id === opt.id);
		if (!selectedOption) return null;

		return `
			<div class="rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
				<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">${ind.label}</div>
				<div class="mt-2 flex flex-wrap items-center justify-between gap-3">
					<div class="text-sm font-semibold text-gray-900">${selectedOption.label}</div>
					<div class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-bold text-green-700 border border-green-200 tabular-nums">${achievementFormatScore(selectedOption.score)}</div>
				</div>
			</div>
		`;
	}).filter(Boolean).join('');

	if (!selectedItems) {
		return '<div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500">Belum ada jawaban skema yang dipilih.</div>';
	}

	return `<div class="space-y-3">${selectedItems}</div>`;
}

function openAchievementModal() {
	const modal = document.getElementById('achievement-modal');
	if (modal) modal.classList.remove('hidden');
}

function closeAchievementModal() {
	const modal = document.getElementById('achievement-modal');
	if (modal) modal.classList.add('hidden');
}

async function showAchievementDetail(id) {
	openAchievementModal();
	const content = document.getElementById('achievement-modal-content');
	if (!content) return;
	content.innerHTML = 'Memuat detail...';

	const [achievementResp, schemaResp] = await Promise.all([
		apiGet(`/api/star/achievements/${id}`),
		apiGet('/api/star/schema')
	]);

	if (!achievementResp || achievementResp.success !== true) {
		content.innerHTML = '<div class="text-sm text-red-600">Gagal memuat detail achievement.</div>';
		return;
	}

	const rec = achievementResp.data || {};
	const schema = (schemaResp && schemaResp.success) ? schemaResp.data : null;
	const responsesByIndicator = {};
	let totalExpected = 0;
	(rec.responses || []).forEach(resp => {
		responsesByIndicator[resp.star_schema_indicator_id] = resp;
		totalExpected += Number(resp.response_score || 0);
	});
	const finalScore = rec.total_points !== null && rec.total_points !== undefined ? Number(rec.total_points) : totalExpected;
	const managerLabel = rec.manager?.name || rec.manager?.email || rec.manager_name || '-';
	const approvalNotes = String(rec.approval_notes || '').trim();

	let html = `
		<div class="mx-auto max-w-3xl space-y-5">
			<div>
				<h3 class="text-center text-lg font-bold text-gray-900">Detail Achievement</h3>
				<p class="text-sm text-gray-500 mt-1">${rec.activity_name || '-'}</p>
			</div>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Kegiatan</div>
					<div class="mt-1 text-sm font-semibold text-gray-900">${achievementFormatDate(rec.activity_date)}</div>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Manager</div>
					<div class="mt-1 text-sm font-semibold text-gray-900">${managerLabel}</div>
				</div>
			</div>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Submit</div>
					<div class="mt-1 text-sm font-semibold text-gray-900">${achievementFormatDate(rec.submitted_at)}</div>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Approve</div>
					<div class="mt-1 text-sm font-semibold text-gray-900">${achievementFormatDate(rec.approved_at)}</div>
				</div>
				<div class="rounded-2xl border border-green-200 bg-green-50 p-4">
					<div class="text-xs font-semibold uppercase tracking-wide text-green-700">Score Akhir</div>
					<div class="mt-1 text-2xl font-bold text-green-700">${achievementFormatScore(finalScore)}</div>
				</div>
			</div>

			<div class="space-y-3 pt-2">
				<div class="text-sm font-bold uppercase tracking-wide text-gray-500">Jawaban Skema</div>
				${achievementSchemaHtml(schema, responsesByIndicator)}
			</div>

			<div class="space-y-2 pt-1">
				<div class="text-sm font-bold uppercase tracking-wide text-gray-500">Catatan Intercomm / Approver</div>
				<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 whitespace-pre-wrap">${approvalNotes || 'Tidak ada catatan.'}</div>
			</div>
		</div>
	`;

	content.innerHTML = html;
}

async function loadStarAchievements() {
	const body = document.getElementById('star-achievements-body');
	body.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

	const res = await apiGet('/api/star/achievements');
	if (!res || res.success !== true) {
		body.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500">Gagal memuat achievements</td></tr>';
		return;
	}

	const items = res.data || [];
	document.getElementById('achievement-count').textContent = items.length;
	document.getElementById('achievement-score').textContent = achievementFormatScore(items.reduce((sum, item) => sum + Number(item.total_points || 0), 0));

	if (!items.length) {
		body.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Belum ada achievement yang disetujui.</td></tr>';
		return;
	}

	body.innerHTML = items.map(item => `
		<tr class="hover:bg-gray-50">
			<td class="px-4 py-3 whitespace-nowrap">${item.activity_name || '-'}</td>
			<td class="px-4 py-3 whitespace-nowrap">${achievementFormatDate(item.activity_date)}</td>
			<td class="px-4 py-3 whitespace-nowrap">${item.manager_name || '-'}</td>
			<td class="px-4 py-3 whitespace-nowrap">${achievementFormatDate(item.approved_at)}</td>
			<td class="px-4 py-3 whitespace-nowrap font-semibold text-green-700">${achievementFormatScore(item.total_points)}</td>
			<td class="px-4 py-3 whitespace-nowrap">${achievementStatusBadge(item.status)}</td>
			<td class="px-4 py-3 text-center whitespace-nowrap">
				<button type="button" data-achievement-id="${item.id}" class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-700">Lihat</button>
			</td>
		</tr>
	`).join('');

	document.querySelectorAll('[data-achievement-id]').forEach(btn => {
		btn.addEventListener('click', () => showAchievementDetail(btn.getAttribute('data-achievement-id')));
	});
}

document.addEventListener('DOMContentLoaded', () => {
	loadStarAchievements();
	const closeBtn = document.getElementById('achievement-modal-close');
	if (closeBtn) closeBtn.addEventListener('click', closeAchievementModal);
	const modal = document.getElementById('achievement-modal');
	if (modal) {
		modal.addEventListener('click', ev => {
			if (ev.target === modal) closeAchievementModal();
		});
	}
});
</script>
@endpush
