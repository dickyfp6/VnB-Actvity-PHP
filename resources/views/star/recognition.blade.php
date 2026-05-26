@extends('layouts.app')

@section('title', 'STAR Recognition - VnB Platform')
@section('page_title', 'STAR Recognition')
@section('page_subtitle', 'Kelola daftar ajuan recognition dan buka skema STAR saat dibutuhkan.')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto px-4">
	<div class="flex items-center justify-between gap-4">
		<button type="button" onclick="openStarSchemaPreview()" class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-white px-4 py-2 text-sm font-semibold text-green-800 transition hover:bg-green-50">
			<i class="fas fa-layer-group text-xs"></i>
			Skema STAR
		</button>
		<a id="add-recognition-btn" href="{{ route('star.recognition.create') }}" class="inline-flex items-center gap-2 rounded-full bg-[#144600] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
			<i class="fas fa-plus text-xs"></i>
			Tambah Ajuan
		</a>
	</div>

	<script>
		// Ensure a lightweight global opener exists so inline onclick never fails
		window.openRecognitionForm = function() {
			try {
				if (typeof toggleRecognitionForm === 'function') {
					toggleRecognitionForm(true);
					setTimeout(function () {
						var el = document.getElementById('activity_name');
						if (el) el.focus();
					}, 200);
					return;
				}
			} catch (e) {
				// swallow
			}
			// Fallback: try to show the form panel directly
			var panel = document.getElementById('recognition-form-panel');
			if (panel) {
				panel.classList.remove('hidden');
			}
		};
	</script>

	<div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
		<div class="overflow-x-auto">
			<table id="recognition-table" class="min-w-full w-full table-fixed divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Employee</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Kegiatan</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Capaian</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
						<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
					</tr>
				</thead>
				<tbody id="recognition-table-body" class="divide-y divide-gray-100 bg-white">
					<tr>
						<td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">Memuat daftar ajuan...</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div id="recognition-form-panel" class="hidden rounded-3xl border border-gray-200 bg-white/80 p-6 shadow-sm backdrop-blur-sm">
		<div class="mb-5 flex items-start justify-between gap-4">
			<div>
				<h3 class="text-lg font-bold text-gray-900">Ajukan Recognition (Tahap 1)</h3>
				<p class="mt-1 text-sm text-gray-600">Isi deskripsi kegiatan dan unggah bukti/sertifikat. Setelah submit, lanjut ke pengisian sesuai skema STAR.</p>
			</div>
			<button type="button" onclick="toggleRecognitionForm(false)" class="rounded-full border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">Tutup</button>
		</div>

		<div id="form-alert" class="hidden mb-4 rounded-xl p-3 text-sm"></div>

		<form id="recognition-form" enctype="multipart/form-data" class="space-y-4">
			<div>
				<label class="block text-sm font-medium text-gray-700">Pilih Karyawan (recipient)</label>
				<div class="relative">
					<select id="recipient_id" name="recipient_id" class="mt-1 block w-full rounded-xl border-gray-200 bg-white shadow-sm pr-10" disabled>
						<option value="">Memuat daftar karyawan...</option>
					</select>
					<div id="recipient-loading" class="absolute inset-y-0 right-2 flex items-center pl-2">
						<svg class="h-4 w-4 animate-spin text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
					</div>
				</div>
				<input id="recipient_search" placeholder="Cari karyawan..." class="mt-2 hidden w-full rounded-xl border-gray-200 shadow-sm" />
				<p id="recipient-help" class="mt-1 text-xs text-gray-500">Jika daftar tidak muncul, masukkan ID karyawan pada kolom teks di bawah.</p>
				<input id="recipient_id_fallback" name="recipient_id_fallback" type="text" placeholder="Masukkan Employee ID (fallback)" class="mt-2 hidden w-full rounded-xl border-gray-200 shadow-sm" />
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
				<input id="activity_name" name="activity_name" type="text" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" required />
			</div>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<div>
					<label class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
					<input id="activity_date" name="activity_date" type="date" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" required />
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Penyelenggara / Dokumentasi</label>
					<input id="organizer" name="organizer" type="text" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" required />
				</div>
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700">Unggah Sertifikat / Bukti (opsional)</label>
				<input id="certificate" name="certificate" type="file" class="mt-1 block w-full rounded-xl border border-gray-200 bg-white p-2 text-sm" accept="image/*,.pdf,.jpg,.jpeg,.png" />
			</div>

			<div class="flex flex-wrap items-center justify-between gap-3">
				<button id="submit-btn" type="button" onclick="submitRecognition()" class="inline-flex items-center gap-2 rounded-xl bg-[#144600] px-4 py-2 font-semibold text-white shadow-sm transition hover:opacity-90">
					<span id="submit-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
					<span>Kirim Pengajuan</span>
				</button>
				<p class="text-sm text-gray-500">Pastikan semua field terisi dengan benar.</p>
			</div>
		</form>
	</div>

	<div id="schema-fill-container" class="hidden rounded-3xl border border-gray-200 bg-white shadow-sm p-6"></div>

	@include('star.partials.schema-preview-modal')
</div>

@endsection

@section('scripts')
<script>
let recognitionRows = [];
let recipientsLoaded = false;
let recipientSearchBound = false;

function toggleRecognitionForm(show) {
	const panel = document.getElementById('recognition-form-panel');
	const schema = document.getElementById('schema-fill-container');
	if (!panel) return;
	panel.classList.toggle('hidden', !show);
	if (!show) {
		if (schema) schema.classList.add('hidden');
		return;
	}
	panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function openRecognitionForm() {
	toggleRecognitionForm(true);
	const activityName = document.getElementById('activity_name');
	if (activityName) {
		setTimeout(() => activityName.focus(), 200);
	}
}

function formatDate(value) {
	if (!value) return '-';
	const parsed = new Date(value);
	if (Number.isNaN(parsed.getTime())) return value;
	return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(parsed);
}

function mapRecognitionStatus(status) {
	const raw = String(status || '').toLowerCase();
	if (['approved', 'disetujui'].includes(raw)) {
		return { label: 'Disetujui', className: 'bg-emerald-50 text-emerald-700 border-emerald-100' };
	}
	if (['rejected', 'ditolak'].includes(raw)) {
		return { label: 'Ditolak', className: 'bg-red-50 text-red-700 border-red-100' };
	}
	return { label: 'Diajukan', className: 'bg-amber-50 text-amber-700 border-amber-100' };
}

function renderRecognitionTable(items) {
	const tbody = document.getElementById('recognition-table-body');
	const table = document.getElementById('recognition-table');
	if (!tbody) return;

	if (!items.length) {
		if (table) {
			table.className = 'min-w-full w-full table-fixed divide-y divide-gray-200';
		}
		tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">Belum ada ajuan recognition.</td></tr>';
		return;
	}

	if (table) {
		table.className = 'w-max min-w-max table-auto divide-y divide-gray-200';
	}

	tbody.innerHTML = items.map((item, index) => {
		const employeeName = item.employee?.name || item.employee?.name_display || item.employee?.employee_number || `Employee #${item.employee_id ?? '-'}`;
		const statusMeta = mapRecognitionStatus(item.status);
		const capai = item.total_points !== null && item.total_points !== undefined ? Number(item.total_points).toFixed(1) : '-';
		return `
			<tr class="hover:bg-gray-50/80">
				<td class="whitespace-nowrap px-5 py-4 text-sm text-gray-900">${index + 1}</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">${employeeName}</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">${item.activity_name || '-'}</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">${formatDate(item.activity_date)}</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900">${capai}</td>
				<td class="whitespace-nowrap px-5 py-4 text-sm"><span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ${statusMeta.className}">${statusMeta.label}</span></td>
				<td class="whitespace-nowrap px-5 py-4 text-sm"><button type="button" onclick="openRecognitionDetail(${item.id})" class="rounded-full border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">Lihat</button></td>
			</tr>
		`;
	}).join('');
}

async function loadRecognitions() {
	const tbody = document.getElementById('recognition-table-body');
	const table = document.getElementById('recognition-table');
	if (table) {
		table.className = 'min-w-full w-full table-fixed divide-y divide-gray-200';
	}

	try {
		const res = await fetch('/api/star/recognition', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
		const payload = await res.json();
		if (!res.ok || !payload.success) throw new Error(payload.message || 'failed');
		recognitionRows = Array.isArray(payload.data) ? payload.data : [];
		renderRecognitionTable(recognitionRows);
	} catch (error) {
		if (tbody) {
			tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-red-600">Gagal memuat daftar ajuan.</td></tr>';
		}
	}
}

async function openRecognitionDetail(recognitionId) {
	toggleRecognitionForm(true);
	await loadRecognitionDetailAndSchema(recognitionId);
}

async function loadEmployeesForSelect() {
	const sel = document.getElementById('recipient_id');
	const loader = document.getElementById('recipient-loading');
	const search = document.getElementById('recipient_search');
	sel.disabled = true;
	loader.classList.remove('hidden');

	try {
		const res = await fetch('/api/employees', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
		if (!res.ok) throw new Error('no-list');
		const payload = await res.json();
		const list = Array.isArray(payload?.data) ? payload.data : payload;
		if (!Array.isArray(list) || list.length === 0) throw new Error('empty');

		sel.innerHTML = '<option value="">Pilih karyawan...</option>' + list.map(e => {
			const label = e.name || e.name_display || e.full_name || e.display_name || e.employee_number || `#${e.id}`;
			return `<option value="${e.id}" data-label="${String(label).toLowerCase()}">${label}</option>`;
		}).join('');
		document.getElementById('recipient_id_fallback').classList.add('hidden');
		sel.disabled = false;
		recipientsLoaded = true;

		if (search) {
			search.classList.remove('hidden');
			if (!recipientSearchBound) {
				recipientSearchBound = true;
				search.addEventListener('input', (ev) => {
					const q = (ev.target.value || '').toLowerCase().trim();
					Array.from(sel.options || []).forEach((opt) => {
						if (!opt.value) return;
						const label = (opt.dataset.label || opt.text).toLowerCase();
						opt.hidden = q !== '' && !label.includes(q);
					});
				});
			}
		}
	} catch (err) {
		sel.innerHTML = '<option value="">Daftar karyawan tidak tersedia</option>';
		document.getElementById('recipient_id_fallback').classList.remove('hidden');
		sel.disabled = true;
	} finally {
		loader.classList.add('hidden');
	}
}

async function submitRecognition() {
	const btn = document.getElementById('submit-btn');
	const spinner = document.getElementById('submit-spinner');
	const alert = document.getElementById('form-alert');
	btn.disabled = true;
	spinner.classList.remove('hidden');

	const fd = new FormData();
	const recipientSel = document.getElementById('recipient_id');
	let recipient = recipientSel.value;
	if (!recipient) {
		const fb = document.getElementById('recipient_id_fallback').value.trim();
		recipient = fb || '';
	}

	fd.append('recipient_id', recipient);
	fd.append('activity_name', document.getElementById('activity_name').value || '');
	fd.append('activity_date', document.getElementById('activity_date').value || '');
	fd.append('organizer', document.getElementById('organizer').value || '');
	const cert = document.getElementById('certificate');
	if (cert && cert.files && cert.files.length) fd.append('certificate', cert.files[0]);

	try {
		const res = await fetch('/api/star/recognition', {
			method: 'POST',
			headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
			credentials: 'same-origin',
			body: fd,
		});

		const payload = await res.json();
		if (!res.ok || !payload.success) {
			alert.className = 'mb-4 rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
			alert.textContent = payload.message || 'Gagal mengirim pengajuan.';
			alert.classList.remove('hidden');
			return;
		}

		alert.className = 'mb-4 rounded-xl p-3 text-sm bg-green-50 text-green-800 border border-green-100';
		alert.textContent = 'Pengajuan berhasil terkirim. Mengalihkan ke pengisian skema...';
		alert.classList.remove('hidden');
		setTimeout(() => { window.location.href = '/star/recognition/' + (payload?.data?.id || ''); }, 700);
	} catch (err) {
		alert.className = 'mb-4 rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
		alert.textContent = 'Terjadi kesalahan. Coba lagi.';
		alert.classList.remove('hidden');
	} finally {
		spinner.classList.add('hidden');
		btn.disabled = false;
	}
}

document.addEventListener('DOMContentLoaded', () => {
		const addButton = document.getElementById('add-recognition-btn');
		if (addButton) {
			addButton.addEventListener('click', openRecognitionForm);
		}
	loadRecognitions();
	loadEmployeesForSelect();
	const match = window.location.pathname.match(/\/star\/recognition\/(\d+)/);
	if (match) {
		toggleRecognitionForm(true);
		loadRecognitionDetailAndSchema(match[1]);
	}
});
</script>
<script>
async function loadRecognitionDetailAndSchema(recognitionId) {
	let container = document.getElementById('schema-fill-container');
	if (!container) {
		container = document.createElement('div');
		container.className = 'rounded-3xl border border-gray-200 bg-white shadow-sm p-6';
		container.id = 'schema-fill-container';
		const formPanel = document.getElementById('recognition-form-panel');
		if (formPanel && formPanel.parentNode) {
			formPanel.parentNode.insertBefore(container, formPanel.nextSibling);
		} else {
			const root = document.querySelector('.space-y-6');
			if (root) root.appendChild(container);
		}
	}
	container.classList.remove('hidden');
	container.innerHTML = `<h3 class="text-lg font-bold text-gray-900">Pengisian Skema STAR (Tahap 2)</h3>
		<p class="mb-4 text-sm text-gray-600">Pilih jawaban untuk setiap indikator sesuai bukti kegiatan.</p>
		<div id="schema-form-inner">Memuat...</div>
		<div class="mt-4 flex items-center gap-3"><button id="submit-responses-btn" class="rounded-xl bg-[#0b5a00] px-4 py-2 text-white">Kirim Jawaban</button><span id="responses-status" class="text-sm text-gray-600"></span></div>`;

	try {
		const [rRes, sRes] = await Promise.all([
			fetch('/api/star/recognition/' + recognitionId, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' }),
			fetch('/api/star/schema', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' }),
		]);

		const rPayload = await rRes.json();
		const sPayload = await sRes.json();

		if (!rRes.ok || !rPayload.success) throw new Error('failed-recognition');
		if (!sRes.ok || !sPayload.success) throw new Error('failed-schema');

		const recognition = rPayload.data;
		const schema = sPayload.data;
		const inner = document.getElementById('schema-form-inner');
		inner.innerHTML = '';

		(schema.indicators || []).forEach((ind) => {
			const pre = (recognition?.responses || []).find((rr) => rr.star_schema_indicator_id === ind.id);
			const optionsHtml = (ind.options || []).map((opt) => {
				const checked = pre && pre.star_schema_indicator_option_id === opt.id ? 'checked' : '';
				return `<label class="mb-2 flex items-center gap-3 rounded-xl border border-gray-200 p-3 transition hover:bg-gray-50">
					<input type="radio" name="indicator_${ind.id}" value="${opt.id}" ${checked} />
					<div>
						<div class="text-sm font-semibold text-gray-900">${opt.label}</div>
						<div class="text-xs text-gray-500">Score: ${opt.score}</div>
					</div>
				</label>`;
			}).join('');

			inner.insertAdjacentHTML('beforeend', `<div class="mb-5 rounded-2xl border border-gray-100 p-4"><h4 class="font-semibold text-gray-900">${ind.label}</h4><div class="mt-3">${optionsHtml}</div></div>`);
		});

		document.getElementById('submit-responses-btn').addEventListener('click', async () => {
			const status = document.getElementById('responses-status');
			const btn = document.getElementById('submit-responses-btn');
			btn.disabled = true;
			status.textContent = 'Menyimpan...';

			const responses = [];
			(schema.indicators || []).forEach((ind) => {
				const sel = document.querySelector(`input[name="indicator_${ind.id}"]:checked`);
				if (sel) {
					responses.push({
						star_schema_indicator_id: ind.id,
						star_schema_indicator_option_id: Number(sel.value),
					});
				}
			});

			try {
				const res = await fetch('/api/star/recognition/' + recognitionId + '/responses', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
					credentials: 'same-origin',
					body: JSON.stringify({ responses }),
				});
				const payload = await res.json();
				if (!res.ok || !payload.success) {
					status.textContent = payload.message || 'Gagal menyimpan responses.';
					btn.disabled = false;
					return;
				}

				status.textContent = 'Responses disimpan. Pengajuan dikirim untuk approval.';
				setTimeout(() => { window.location.href = '/star/recognition'; }, 900);
			} catch (err) {
				status.textContent = 'Terjadi kesalahan.';
				btn.disabled = false;
			}
		});
	} catch (err) {
		container.innerHTML = '<p class="text-sm text-red-600">Gagal memuat data pengisian skema.</p>';
	}
}
</script>
@endsection
