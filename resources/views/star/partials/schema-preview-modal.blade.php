<div id="star-schema-preview-modal" class="fixed inset-0 z-[60] hidden">
	<div class="absolute inset-0 bg-black/50" onclick="closeStarSchemaPreview()"></div>
	<div class="relative min-h-full w-full flex items-center justify-center p-4">
		<div class="w-full max-w-5xl rounded-2xl bg-white shadow-xl border border-gray-200 overflow-hidden">
			<div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
				<h3 class="text-lg font-bold text-gray-900">Preview Indikator Penilaian</h3>
				<button type="button" onclick="closeStarSchemaPreview()" class="w-9 h-9 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">×</button>
			</div>
			<div class="p-5 max-h-[70vh] overflow-auto" id="star-schema-preview-table-container"></div>
		</div>
	</div>
</div>

<script>
const starSchemaPreviewCache = {
	indicators: null,
	fetchedAt: 0,
};
const starSchemaPreviewCacheTtlMs = 5 * 60 * 1000;

function closeStarSchemaPreview() {
	document.getElementById('star-schema-preview-modal')?.classList.add('hidden');
}

function escapeStarSchemaHtml(value) {
	return String(value ?? '')
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/\"/g, '&quot;')
		.replace(/'/g, '&#039;');
}

function buildStarSchemaRows(indicators) {
	const rows = [];
	(indicators || []).forEach((indicator, indicatorIndex) => {
		const indicatorName = (indicator?.label || '').trim() || '-';
		const options = Array.isArray(indicator?.options) ? indicator.options : [];

		if (!options.length) {
			rows.push({
				no: indicatorIndex + 1,
				indicator: indicatorName,
				category: '-',
				score: '-',
			});
			return;
		}

		options.forEach((option, optionIndex) => {
			rows.push({
				no: optionIndex === 0 ? (indicatorIndex + 1) : '',
				indicator: optionIndex === 0 ? indicatorName : '',
				category: (option?.label || '').trim() || '-',
				score: Number(option?.score ?? 0),
			});
		});
	});

	return rows;
}

function renderStarSchemaPreview(containerId, indicators) {
	const container = document.getElementById(containerId);
	if (!container) return;

	const rows = buildStarSchemaRows(indicators);
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
							<td class="px-4 py-3 text-sm text-gray-900 border-b border-gray-100">${escapeStarSchemaHtml(row.indicator)}</td>
							<td class="px-4 py-3 text-sm text-gray-700 border-b border-gray-100">${escapeStarSchemaHtml(row.category)}</td>
							<td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-gray-100">${row.score}</td>
						</tr>
					`).join('')}
				</tbody>
			</table>
		</div>
	`;
}

function hasFreshStarSchemaCache() {
	return Array.isArray(starSchemaPreviewCache.indicators)
		&& (Date.now() - starSchemaPreviewCache.fetchedAt) < starSchemaPreviewCacheTtlMs;
}

function setStarSchemaCache(indicators) {
	starSchemaPreviewCache.indicators = Array.isArray(indicators) ? indicators : [];
	starSchemaPreviewCache.fetchedAt = Date.now();
}

async function fetchStarSchemaIndicators() {
	const response = await fetch('/api/star/schema', {
		headers: { 'Accept': 'application/json' },
	});
	const payload = await response.json();
	if (!response.ok) {
		throw new Error(payload?.message || 'Gagal mengambil schema STAR.');
	}

	const indicators = Array.isArray(payload?.data?.indicators) ? payload.data.indicators : [];
	setStarSchemaCache(indicators);
	return indicators;
}

async function openStarSchemaPreview() {
	const modal = document.getElementById('star-schema-preview-modal');
	const container = document.getElementById('star-schema-preview-table-container');
	if (!modal || !container) return;

	modal.classList.remove('hidden');

	if (hasFreshStarSchemaCache()) {
		renderStarSchemaPreview('star-schema-preview-table-container', starSchemaPreviewCache.indicators);
		return;
	}

	container.innerHTML = '<p class="text-sm text-gray-600">Memuat schema STAR...</p>';

	try {
		const indicators = await fetchStarSchemaIndicators();
		renderStarSchemaPreview('star-schema-preview-table-container', indicators);
	} catch (_error) {
		if (Array.isArray(starSchemaPreviewCache.indicators)) {
			renderStarSchemaPreview('star-schema-preview-table-container', starSchemaPreviewCache.indicators);
			return;
		}
		container.innerHTML = '<p class="text-sm text-red-600">Schema STAR belum bisa ditampilkan. Coba lagi beberapa saat.</p>';
	}
}

document.addEventListener('keydown', (event) => {
	if (event.key !== 'Escape') return;
	const modal = document.getElementById('star-schema-preview-modal');
	if (modal && !modal.classList.contains('hidden')) {
		closeStarSchemaPreview();
	}
});
</script>
