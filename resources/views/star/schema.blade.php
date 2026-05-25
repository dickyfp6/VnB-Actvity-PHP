@extends('layouts.app')

@section('title', 'STAR Framework - VnB Platform')
@section('page_title', 'STAR Framework')

@section('content')
@php
	$canEditSchema = auth()->user()?->hasAnyRole(['intercomm', 'pcx_manager']);
@endphp
<div class="space-y-6" id="star-schema-page">
	<div id="top-notice" class="fixed top-4 left-1/2 -translate-x-1/2 z-[80] hidden w-[calc(100%-2rem)] max-w-2xl">
		<div id="top-notice-inner" class="rounded-xl border px-4 py-3 shadow-lg text-sm font-semibold"></div>
	</div>

	<div class="card-glass rounded-2xl p-6 border border-green-100 shadow-sm">
		<div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
			<div>
				<h2 class="text-2xl font-bold text-gray-900">Schema Penilaian STAR</h2>
				<p class="text-sm text-gray-600 mt-1">
					@if($canEditSchema)
						Isi sederhana: Nama Indikator, Kategori Penilaian, dan Score. Urutan pakai drag dari sisi kiri.
					@else
						Mode preview: kamu bisa lihat hasil akhir schema penilaian tanpa akses edit.
					@endif
				</p>
			</div>
			@if($canEditSchema)
			<div class="flex items-center gap-2">
				<button type="button" onclick="addIndicator()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#144600] text-white font-semibold shadow-sm hover:bg-[#0f3600] transition">
					<i class="fas fa-plus"></i>
					Tambah Indikator
				</button>
			</div>
			@endif
		</div>
	</div>

	@if($canEditSchema)
	<div class="space-y-4" id="star-indicators"></div>

	<div class="flex items-center justify-between gap-3">
		<button type="button" onclick="openPreviewModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
			<i class="fas fa-table"></i>
			Preview
		</button>
		<div class="flex items-center gap-3">
			<button type="button" onclick="resetSchemaForm()" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">Reset</button>
			<button id="save-schema-btn" type="button" onclick="saveSchema()" class="px-5 py-2.5 rounded-xl bg-green-600 text-white font-semibold shadow-sm hover:bg-green-700 transition disabled:bg-gray-300 disabled:text-gray-500 disabled:shadow-none disabled:cursor-not-allowed" disabled>Simpan</button>
		</div>
	</div>

	<div id="preview-modal" class="fixed inset-0 z-[60] hidden">
		<div class="absolute inset-0 bg-black/50" onclick="closePreviewModal()"></div>
		<div class="relative min-h-full w-full flex items-center justify-center p-4">
			<div class="w-full max-w-5xl rounded-2xl bg-white shadow-xl border border-gray-200 overflow-hidden">
				<div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
					<h3 class="text-lg font-bold text-gray-900">Preview Indikator Penilaian</h3>
					<button type="button" onclick="closePreviewModal()" class="w-9 h-9 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">×</button>
				</div>
				<div class="p-5 max-h-[70vh] overflow-auto" id="preview-table-container"></div>
			</div>
		</div>
	</div>
	@else
	<div class="card-glass rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
		<div class="px-5 py-4 border-b border-gray-200 bg-gray-50/80">
			<h3 class="text-lg font-bold text-gray-900">Preview Schema Penilaian</h3>
		</div>
		<div class="p-5 max-h-[75vh] overflow-auto" id="readonly-preview-table-container"></div>
	</div>
	@endif
</div>

<script>
const canEditSchema = @json($canEditSchema);
const starSchemaState = {
	id: null,
	name: 'STAR Schema',
	description: null,
	version: 1,
	is_active: true,
	indicators: [],
};

let lastSavedPayloadHash = '';
let topNoticeTimer = null;

function defaultIndicator(index = 0) {
	return {
		id: null,
		indicator_key: '',
		label: '',
		sort_order: index + 1,
		is_locked: false,
		is_collapsed: false,
		options: [
			{ id: null, label: '', score: 0, sort_order: 1 },
		],
	};
}

function clone(value) {
	return JSON.parse(JSON.stringify(value));
}

function normalizeSchema(data) {
	const indicators = Array.isArray(data?.indicators) ? data.indicators : [];
	return {
		id: data?.id ?? null,
		name: data?.name ?? 'STAR Schema',
		description: data?.description ?? null,
		version: data?.version ?? 1,
		is_active: data?.is_active !== false,
		indicators: indicators.map((indicator, index) => ({
			id: indicator?.id ?? null,
			indicator_key: indicator?.indicator_key ?? '',
			label: indicator?.label ?? '',
			sort_order: indicator?.sort_order ?? (index + 1),
			is_locked: true,
			is_collapsed: true,
			options: (Array.isArray(indicator?.options) ? indicator.options : []).map((option, optionIndex) => ({
				id: option?.id ?? null,
				label: option?.label ?? '',
				score: option?.score ?? 0,
				sort_order: option?.sort_order ?? (optionIndex + 1),
			})),
		})),
	};
}

function syncInputsToState() {
	document.querySelectorAll('.star-indicator-label').forEach((input) => {
		const index = Number(input.dataset.index);
		if (starSchemaState.indicators[index]) starSchemaState.indicators[index].label = input.value;
	});
	document.querySelectorAll('.star-option-label').forEach((input) => {
		const indicatorIndex = Number(input.dataset.indicatorIndex);
		const optionIndex = Number(input.dataset.optionIndex);
		if (starSchemaState.indicators[indicatorIndex]?.options?.[optionIndex]) {
			starSchemaState.indicators[indicatorIndex].options[optionIndex].label = input.value;
		}
	});
	document.querySelectorAll('.star-option-score').forEach((input) => {
		const indicatorIndex = Number(input.dataset.indicatorIndex);
		const optionIndex = Number(input.dataset.optionIndex);
		if (starSchemaState.indicators[indicatorIndex]?.options?.[optionIndex]) {
			starSchemaState.indicators[indicatorIndex].options[optionIndex].score = Number(input.value || 0);
		}
	});
}

function renderOptionReadonly(option) {
	return `
		<div class="grid gap-3 md:grid-cols-[1fr_140px] items-center rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
			<div class="text-sm text-gray-700">${escapeHtml(option.label || '-')}</div>
			<div class="text-sm font-semibold text-gray-800">${Number(option.score ?? 0)}</div>
		</div>
	`;
}

function renderIndicators() {
	const root = document.getElementById('star-indicators');
	if (!root) return;

	root.innerHTML = starSchemaState.indicators.map((indicator, index) => `
		<div class="card-glass rounded-2xl border border-gray-200 shadow-sm overflow-hidden star-indicator-card" draggable="true" data-index="${index}" ondragstart="onIndicatorDragStart(event, ${index})" ondragover="onIndicatorDragOver(event)" ondrop="onIndicatorDrop(event, ${index})">
			<div class="px-5 py-4 border-b border-gray-200 bg-gray-50/80 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
				<div class="flex items-center gap-3 lg:flex-1">
					<button type="button" class="w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-500 cursor-grab" title="Geser urutan indikator" aria-label="Geser urutan indikator">
						<i class="fas fa-grip-vertical"></i>
					</button>
					<div>
						<p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Indikator ${index + 1}</p>
						<p class="text-sm font-semibold text-gray-900">${escapeHtml(indicator.label || 'Indikator Baru')}</p>
					</div>
				</div>
				<div class="flex items-center gap-2">
					${indicator.is_locked ? `<button type="button" onclick="toggleIndicatorCollapse(${index})" class="px-3 h-9 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50">${indicator.is_collapsed ? 'Buka' : 'Tutup'}</button>` : ''}
					${indicator.is_locked ? `<button type="button" onclick="editIndicator(${index})" class="px-3 h-9 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50">Edit</button>` : `<button type="button" onclick="saveIndicatorDraft(${index})" class="px-3 h-9 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700">Simpan Indikator</button>`}
					<button type="button" onclick="removeIndicator(${index})" class="w-10 h-10 rounded-xl border border-red-200 bg-red-50 text-red-600 hover:bg-red-100" title="Hapus">×</button>
				</div>
			</div>
			<div class="p-5 space-y-4 ${indicator.is_locked && indicator.is_collapsed ? 'hidden' : ''}">
				<div>
					<label class="block text-xs font-semibold text-gray-600 mb-2">Nama Indikator</label>
					<input type="text" class="star-indicator-label w-full rounded-xl border border-gray-300 px-4 py-3 ${indicator.is_locked ? 'bg-gray-50 text-gray-600' : ''}" data-index="${index}" value="${escapeHtml(indicator.label || '')}" placeholder="Contoh: Tingkat Penghargaan / Rekognisi" ${indicator.is_locked ? 'readonly' : ''}>
				</div>
				<div class="space-y-3">
					${(indicator.options || []).map((option, optionIndex) => indicator.is_locked
						? renderOptionReadonly(option)
						: `
							<div class="grid gap-3 md:grid-cols-[auto_1fr_140px_auto] items-start rounded-xl border border-gray-200 bg-white p-3 star-option-row" draggable="true" data-indicator-index="${index}" data-option-index="${optionIndex}" ondragstart="onOptionDragStart(event, ${index}, ${optionIndex})" ondragover="onOptionDragOver(event)" ondrop="onOptionDrop(event, ${index}, ${optionIndex})">
								<div class="pt-2">
									<button type="button" class="w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-500 cursor-grab" title="Geser urutan kategori" aria-label="Geser urutan kategori">
										<i class="fas fa-grip-vertical"></i>
									</button>
								</div>
								<div>
									<label class="block text-xs font-semibold text-gray-600 mb-2">Kategori Penilaian</label>
									<input type="text" class="star-option-label w-full rounded-xl border border-gray-300 px-4 py-3" data-indicator-index="${index}" data-option-index="${optionIndex}" value="${escapeHtml(option.label || '')}" placeholder="Contoh: Internal Departemen / Divisi">
								</div>
								<div>
									<label class="block text-xs font-semibold text-gray-600 mb-2">Score</label>
									<input type="number" step="0.1" class="star-option-score w-full rounded-xl border border-gray-300 px-4 py-3" data-indicator-index="${index}" data-option-index="${optionIndex}" value="${option.score ?? 0}">
								</div>
								<div class="pt-7">
									<button type="button" onclick="removeOption(${index}, ${optionIndex})" class="w-10 h-10 rounded-xl border border-red-200 bg-red-50 text-red-600 hover:bg-red-100" title="Hapus">×</button>
								</div>
							</div>
						`
					).join('')}
				</div>
				${indicator.is_locked ? '' : `<button type="button" onclick="addOption(${index})" class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 hover:text-green-800"><i class="fas fa-plus"></i> Tambah Kategori</button>`}
			</div>
		</div>
	`).join('');

	refreshSaveButtonState(false);
}

function addIndicator() {
	if (!canEditSchema) return;
	syncInputsToState();
	const hasOpenDraft = starSchemaState.indicators.some((indicator) => !indicator.is_locked);
	if (hasOpenDraft) {
		showTopNotice('Selesaikan dan simpan indikator yang sedang dibuka dulu.', 'warning');
		return;
	}
	starSchemaState.indicators.push(defaultIndicator(starSchemaState.indicators.length));
	renderIndicators();
}

function removeIndicator(index) {
	if (!canEditSchema) return;
	syncInputsToState();
	starSchemaState.indicators.splice(index, 1);
	starSchemaState.indicators = starSchemaState.indicators.map((indicator, newIndex) => ({ ...indicator, sort_order: newIndex + 1 }));
	renderIndicators();
}

function toggleIndicatorCollapse(index) {
	if (!canEditSchema) return;
	syncInputsToState();
	const indicator = starSchemaState.indicators[index];
	if (!indicator || !indicator.is_locked) return;
	indicator.is_collapsed = !indicator.is_collapsed;
	renderIndicators();
}

function editIndicator(index) {
	if (!canEditSchema) return;
	syncInputsToState();
	const indicator = starSchemaState.indicators[index];
	if (!indicator) return;
	indicator.is_locked = false;
	indicator.is_collapsed = false;
	renderIndicators();
}

function saveIndicatorDraft(index) {
	if (!canEditSchema) return;
	syncInputsToState();
	const indicator = starSchemaState.indicators[index];
	if (!indicator) return;

	if (!(indicator.label || '').trim()) {
		showTopNotice('Nama indikator wajib diisi.', 'warning');
		return;
	}

	const filledOptions = (indicator.options || []).filter((option) => (option.label || '').trim() !== '');
	if (!filledOptions.length) {
		showTopNotice('Minimal ada 1 kategori penilaian.', 'warning');
		return;
	}

	indicator.options = filledOptions.map((option, optionIndex) => ({
		...option,
		label: (option.label || '').trim(),
		score: Number(option.score || 0),
		sort_order: optionIndex + 1,
	}));
	indicator.is_locked = true;
	indicator.is_collapsed = true;
	indicator.sort_order = index + 1;
	if (!indicator.indicator_key) {
		indicator.indicator_key = slugify(indicator.label);
	}
	renderIndicators();
}

function addOption(indicatorIndex) {
	if (!canEditSchema) return;
	syncInputsToState();
	const indicator = starSchemaState.indicators[indicatorIndex];
	if (!indicator || indicator.is_locked) return;
	indicator.options.push({ label: '', score: 0, sort_order: indicator.options.length + 1 });
	renderIndicators();
}

function removeOption(indicatorIndex, optionIndex) {
	if (!canEditSchema) return;
	syncInputsToState();
	const indicator = starSchemaState.indicators[indicatorIndex];
	if (!indicator || indicator.is_locked) return;
	indicator.options.splice(optionIndex, 1);
	if (!indicator.options.length) {
		indicator.options.push({ label: '', score: 0, sort_order: 1 });
	}
	indicator.options = indicator.options.map((option, newIndex) => ({ ...option, sort_order: newIndex + 1 }));
	renderIndicators();
}

function onIndicatorDragStart(event, index) {
	event.dataTransfer.setData('text/plain', JSON.stringify({ type: 'indicator', fromIndex: index }));
}

function onIndicatorDragOver(event) {
	event.preventDefault();
}

function onIndicatorDrop(event, targetIndex) {
	if (!canEditSchema) return;
	event.preventDefault();
	syncInputsToState();
	const raw = event.dataTransfer.getData('text/plain');
	if (!raw) return;

	let data;
	try {
		data = JSON.parse(raw);
	} catch (_error) {
		return;
	}

	if (data.type !== 'indicator') return;
	const fromIndex = Number(data.fromIndex);
	if (Number.isNaN(fromIndex) || fromIndex === targetIndex) return;
	if (fromIndex < 0 || fromIndex >= starSchemaState.indicators.length) return;
	if (targetIndex < 0 || targetIndex >= starSchemaState.indicators.length) return;

	const [moved] = starSchemaState.indicators.splice(fromIndex, 1);
	starSchemaState.indicators.splice(targetIndex, 0, moved);
	starSchemaState.indicators = starSchemaState.indicators.map((indicator, index) => ({ ...indicator, sort_order: index + 1 }));
	renderIndicators();
}

function onOptionDragStart(event, indicatorIndex, optionIndex) {
	event.dataTransfer.setData('text/plain', JSON.stringify({
		type: 'option',
		indicatorIndex,
		fromIndex: optionIndex,
	}));
}

function onOptionDragOver(event) {
	event.preventDefault();
}

function onOptionDrop(event, indicatorIndex, targetOptionIndex) {
	if (!canEditSchema) return;
	event.preventDefault();
	syncInputsToState();
	const raw = event.dataTransfer.getData('text/plain');
	if (!raw) return;

	let data;
	try {
		data = JSON.parse(raw);
	} catch (_error) {
		return;
	}

	if (data.type !== 'option') return;
	if (Number(data.indicatorIndex) !== Number(indicatorIndex)) return;

	const indicator = starSchemaState.indicators[indicatorIndex];
	if (!indicator || indicator.is_locked) return;

	const fromIndex = Number(data.fromIndex);
	if (Number.isNaN(fromIndex) || fromIndex === targetOptionIndex) return;
	if (fromIndex < 0 || fromIndex >= indicator.options.length) return;
	if (targetOptionIndex < 0 || targetOptionIndex >= indicator.options.length) return;

	const [moved] = indicator.options.splice(fromIndex, 1);
	indicator.options.splice(targetOptionIndex, 0, moved);
	indicator.options = indicator.options.map((option, optionIndex) => ({ ...option, sort_order: optionIndex + 1 }));
	renderIndicators();
}

function resetSchemaForm() {
	if (!canEditSchema) return;
	loadSchema();
}

function buildSchemaPayload() {
	return {
		name: (starSchemaState.name || 'STAR Schema').trim(),
		description: starSchemaState.description,
		indicators: starSchemaState.indicators.map((indicator, index) => ({
			indicator_key: (indicator.indicator_key || slugify(indicator.label || `indicator_${index + 1}`)).trim(),
			label: (indicator.label || '').trim(),
			sort_order: index + 1,
			options: (indicator.options || []).map((option, optionIndex) => ({
				label: (option.label || '').trim(),
				score: Number(option.score || 0),
				sort_order: optionIndex + 1,
			})),
		})),
	};
}

function refreshSaveButtonState(shouldSync = true) {
	if (shouldSync) {
		syncInputsToState();
	}

	const saveBtn = document.getElementById('save-schema-btn');
	if (!saveBtn) return;

	const payload = buildSchemaPayload();
	const currentHash = JSON.stringify(payload);
	const isDirty = currentHash !== lastSavedPayloadHash;

	saveBtn.disabled = !isDirty;
}

function buildPreviewRows() {
	const rows = [];
	starSchemaState.indicators.forEach((indicator, indicatorIndex) => {
		const indicatorName = (indicator.label || '').trim() || '-';
		const options = Array.isArray(indicator.options) ? indicator.options : [];
		if (!options.length) {
			rows.push({
				no: indicatorIndex + 1,
				indicator: indicatorName,
				category: '-',
				score: '-',
				isDraft: !indicator.is_locked,
			});
			return;
		}

		options.forEach((option, optionIndex) => {
			rows.push({
				no: optionIndex === 0 ? (indicatorIndex + 1) : '',
				indicator: optionIndex === 0 ? indicatorName : '',
				category: (option.label || '').trim() || '-',
				score: Number(option.score ?? 0),
				isDraft: !indicator.is_locked,
			});
		});
	});

	return rows;
}

function renderPreviewTable(containerId = 'preview-table-container', showDraftTag = true) {
	const container = document.getElementById(containerId);
	if (!container) return;

	const rows = buildPreviewRows();
	if (!rows.length) {
		container.innerHTML = '<p class="text-sm text-gray-600">Belum ada indikator untuk ditampilkan.</p>';
		return;
	}

	container.innerHTML = `
		<div class="overflow-x-auto">
			<table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200 w-14">No</th>
						<th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200">Nama Indikator</th>
						<th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200">Kategori Penilaian</th>
						<th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200 w-28">Score</th>
					</tr>
				</thead>
				<tbody>
					${rows.map((row) => `
						<tr class="bg-white hover:bg-gray-50/70">
							<td class="px-4 py-3 text-sm text-gray-700 border-b border-gray-100">${row.no}</td>
							<td class="px-4 py-3 text-sm text-gray-900 border-b border-gray-100">${escapeHtml(row.indicator)}${showDraftTag && row.isDraft && row.indicator ? ' <span class="ml-2 inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">DRAFT</span>' : ''}</td>
							<td class="px-4 py-3 text-sm text-gray-700 border-b border-gray-100">${escapeHtml(String(row.category))}</td>
							<td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-gray-100">${row.score}</td>
						</tr>
					`).join('')}
				</tbody>
			</table>
		</div>
	`;
}

function openPreviewModal() {
	if (!canEditSchema) return;
	syncInputsToState();
	renderPreviewTable('preview-table-container', true);
	document.getElementById('preview-modal')?.classList.remove('hidden');
}

function closePreviewModal() {
	document.getElementById('preview-modal')?.classList.add('hidden');
}

async function loadSchema() {
	const response = await fetch('/api/star/schema', {
		headers: { 'Accept': 'application/json' },
	});
	const payload = await response.json();
	const data = normalizeSchema(payload?.data || {});

	Object.assign(starSchemaState, clone(data));
	lastSavedPayloadHash = JSON.stringify(buildSchemaPayload());
	if (canEditSchema) {
		renderIndicators();
		return;
	}
	renderPreviewTable('readonly-preview-table-container', false);
}

async function saveSchema() {
	if (!canEditSchema) return;
	syncInputsToState();

	const openDraft = starSchemaState.indicators.find((indicator) => !indicator.is_locked);
	if (openDraft) {
		showTopNotice('Masih ada indikator yang belum disimpan. Simpan indikator draft dulu.', 'warning');
		return;
	}

	const payload = buildSchemaPayload();

	const response = await fetch('/api/star/schema', {
		method: 'POST',
		headers: {
			'Accept': 'application/json',
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
		},
		body: JSON.stringify(payload),
	});

	const result = await response.json();
	if (!response.ok || !result.success) {
		showTopNotice(result.message || 'Gagal menyimpan schema STAR.', 'error');
		return;
	}

	showTopNotice('Schema STAR berhasil disimpan.', 'success');
	lastSavedPayloadHash = JSON.stringify(payload);
	refreshSaveButtonState(false);
	await loadSchema();
}

function showTopNotice(message, type = 'success') {
	const wrap = document.getElementById('top-notice');
	const inner = document.getElementById('top-notice-inner');
	if (!wrap || !inner) return;

	const palette = {
		success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
		warning: 'border-amber-200 bg-amber-50 text-amber-800',
		error: 'border-red-200 bg-red-50 text-red-800',
	};

	inner.className = `rounded-xl border px-4 py-3 shadow-lg text-sm font-semibold ${palette[type] || palette.success}`;
	inner.textContent = message;
	wrap.classList.remove('hidden');

	if (topNoticeTimer) {
		clearTimeout(topNoticeTimer);
	}

	topNoticeTimer = setTimeout(() => {
		wrap.classList.add('hidden');
	}, 3000);
}

function slugify(value) {
	return String(value || '')
		.toLowerCase()
		.trim()
		.replace(/[^a-z0-9]+/g, '_')
		.replace(/^_+|_+$/g, '') || 'indicator';
}

function escapeHtml(value) {
	return String(value ?? '')
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', loadSchema);

document.addEventListener('input', (event) => {
	if (!canEditSchema) return;
	const target = event.target;
	if (!(target instanceof HTMLElement)) return;
	if (!target.matches('.star-indicator-label, .star-option-label, .star-option-score')) return;
	refreshSaveButtonState(true);
});
</script>
@endsection
