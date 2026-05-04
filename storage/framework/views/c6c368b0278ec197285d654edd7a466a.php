

<?php $__env->startSection('title', 'VNB Participants - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'VNB Participants'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
	<div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
		<div>
			<h2 class="font-semibold">Participants</h2>
			<p class="text-sm text-gray-500">Daftar employee yang sudah di-assign oleh Intercomm / PCX Manager</p>
		</div>

		<div class="flex items-center gap-3">
			<label class="flex items-center gap-2 text-sm">
				<input id="use-batch-induction" type="checkbox" checked class="w-4 h-4">
				<span>Gunakan tanggal induction sama untuk batch</span>
			</label>
			<input id="batch-induction-date" type="date" class="px-3 py-2 rounded border border-gray-200" />
			<button class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color:#144600;" onclick="openAssignModal()">Assign Employee</button>
		</div>
	</div>

	<div class="bg-white rounded-xl shadow-sm p-4">
		<div class="overflow-x-auto">
			<table class="table-modern w-full" id="participants-table">
				<thead>
					<tr>
						<th>No</th>
						<th>NIP</th>
						<th>Nama Lengkap</th>
						<th>Perusahaan</th>
						<th>Divisi</th>
						<th>Departemen</th>
						<th>Career Stage</th>
						<th>Fase</th>
						<th>Progress</th>
						<th>Manager Fungsional</th>
						<th>Manager Operasional</th>
						<th>Tanggal Mulai VnB</th>
						<th>Tanggal Selesai VnB</th>
						<th>Tanggal Induction</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody id="participants-body">
					<tr><td colspan="15" class="text-center py-8 text-gray-400">Belum ada participants.</td></tr>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Assign Modal -->
	<div id="assign-modal" class="fixed inset-0 z-50 hidden">
		<div class="absolute inset-0 bg-black/40" onclick="closeAssignModal()"></div>
		<div class="relative h-full w-full flex items-center justify-center p-4">
			<div class="w-full max-w-2xl bg-white rounded-xl shadow-2xl border border-gray-200">
				<div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
					<h3 class="text-lg font-bold text-gray-800">Assign Employee</h3>
					<button type="button" onclick="closeAssignModal()" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
				</div>

				<div class="p-5 space-y-4">
					<div class="flex gap-3 items-center">
						<input id="assign-search" type="text" placeholder="Cari nama atau NIP" class="flex-1 px-3 py-2 border rounded" />
						<button class="px-3 py-2 rounded bg-gray-100" onclick="searchEmployees()">Cari</button>
					</div>

					<div id="assign-results" class="max-h-48 overflow-y-auto border rounded p-2"></div>

					<div class="flex items-center gap-3">
						<label class="text-sm">Tanggal Induction (opsional per item)</label>
						<input id="assign-induction-date" type="date" class="px-3 py-2 rounded border border-gray-200" />
						<button class="px-4 py-2 rounded text-white" style="background-color:#144600;" onclick="assignSelected()">Tambahkan ke Participants</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* small styles for participants page */
#participants-table th, #participants-table td { padding: 0.6rem; text-align: left; }
.assign-result { padding: 0.5rem; border-bottom: 1px solid #eee; cursor: pointer }
.assign-result:hover { background: #f7faf7 }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openAssignModal() {
	document.getElementById('assign-modal').classList.remove('hidden');
}
function closeAssignModal() {
	document.getElementById('assign-modal').classList.add('hidden');
}

async function searchEmployees() {
	const q = document.getElementById('assign-search').value.trim();
	const container = document.getElementById('assign-results');
	container.innerHTML = '<div class="text-sm text-gray-500 p-2">Mencari...</div>';
	try {
		// try calling backend search API; fallback to empty list
		const res = await (window.apiGet ? apiGet('/api/employees?search=' + encodeURIComponent(q)) : Promise.resolve([]));
		const rows = res && res.data ? res.data : (Array.isArray(res) ? res : []);
		if (!rows.length) {
			container.innerHTML = '<div class="text-sm text-gray-500 p-2">Tidak ditemukan</div>';
			return;
		}
		container.innerHTML = rows.map(r => `<div class="assign-result" data-emp='${JSON.stringify(r).replace(/'/g, "\\'")}' onclick="selectAssign(this)"><strong>${r.employee_number || r.nip || ''}</strong> — ${r.name}</div>`).join('');
	} catch (err) {
		container.innerHTML = '<div class="text-sm text-red-500 p-2">Gagal mencari</div>';
	}
}

function selectAssign(el) {
	// mark selected
	Array.from(document.querySelectorAll('#assign-results .assign-result')).forEach(x => x.classList.remove('bg-green-50'));
	el.classList.add('bg-green-50');
	el.dataset.selected = '1';
}

function businessDaysAdd(startDateStr, days) {
	if (!startDateStr) return null;
	const d = new Date(startDateStr);
	let added = 0;
	while (added < days) {
		d.setDate(d.getDate() + 1);
		const day = d.getDay();
		if (day === 0 || day === 6) continue; // skip sun(0) sat(6)
		added++;
	}
	return d.toISOString().slice(0,10);
}

function addYears(dateStr, years) {
	if (!dateStr) return null;
	const d = new Date(dateStr);
	d.setFullYear(d.getFullYear() + years);
	return d.toISOString().slice(0,10);
}

function assignSelected() {
	const selectedEl = document.querySelector('#assign-results .assign-result.bg-green-50');
	if (!selectedEl) { alert('Pilih employee dari hasil pencarian.'); return; }
	const emp = JSON.parse(selectedEl.getAttribute('data-emp'));
	const useBatch = document.getElementById('use-batch-induction').checked;
	const batchDate = document.getElementById('batch-induction-date').value;
	const perAssignDate = document.getElementById('assign-induction-date').value;
	const induction = useBatch ? (batchDate || perAssignDate) : (perAssignDate || batchDate);
	addParticipantRow(emp, induction);
	closeAssignModal();
}

function mapLevelToCareerStage(level) {
	if (!level) return '';
	const m = level.toString().toLowerCase();
	if (m.includes('director') || m.includes('kepala')) return 'Manage Function';
	if (m.includes('general manager') || m.includes('general')) return 'Manage Manager';
	if (m.includes('manager')) return 'Manage Others';
	if (m.includes('supervisor') || m.includes('staff')) return 'Manage Self (Staff)';
	return 'Manage Self (Non-Staff)';
}

function addParticipantRow(emp, inductionDate) {
	const tbody = document.getElementById('participants-body');
	// remove empty placeholder
	if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';
	const idx = tbody.children.length + 1;
	const nip = emp.employee_number || emp.nip || emp.employeeNumber || '';
	const name = emp.name || emp.name_display || emp.full_name || '';
	const company = emp.company || '';
	const division = emp.division || '';
	const department = emp.department || '';
	const level = emp.level || emp.position_level || '';
	const career = mapLevelToCareerStage(level);
	const start = inductionDate ? businessDaysAdd(inductionDate, 7) : '';
	const end = start ? addYears(start, 1) : '';

	const row = document.createElement('tr');
	row.innerHTML = `
		<td>${idx}</td>
		<td>${nip}</td>
		<td>${escapeHtml(name)}</td>
		<td>${escapeHtml(company)}</td>
		<td>${escapeHtml(division)}</td>
		<td>${escapeHtml(department)}</td>
		<td>${escapeHtml(career)}</td>
		<td><input type="text" class="px-2 py-1 border rounded w-16" value="1" /></td>
		<td><input type="text" class="px-2 py-1 border rounded w-20" value="0%" /></td>
		<td><input type="text" class="px-2 py-1 border rounded" value="" /></td>
		<td><input type="text" class="px-2 py-1 border rounded" value="" /></td>
		<td class="start-date">${start || ''}</td>
		<td class="end-date">${end || ''}</td>
		<td><input type="date" class="induction-input" value="${inductionDate || ''}" onchange="onInductionChange(this)" /></td>
		<td><button class="px-2 py-1 text-sm rounded border" onclick="removeParticipantRow(this)">Hapus</button></td>
	`;
	tbody.appendChild(row);
}

function onInductionChange(el) {
	const tr = el.closest('tr');
	const val = el.value;
	const startTd = tr.querySelector('.start-date');
	const endTd = tr.querySelector('.end-date');
	const start = businessDaysAdd(val, 7);
	const end = start ? addYears(start, 1) : '';
	if (startTd) startTd.textContent = start || '';
	if (endTd) endTd.textContent = end || '';
}

function removeParticipantRow(btn) {
	const tr = btn.closest('tr');
	tr.remove();
	// reindex
	const tbody = document.getElementById('participants-body');
	Array.from(tbody.querySelectorAll('tr')).forEach((r,i) => r.querySelector('td') && (r.querySelector('td').textContent = i+1));
}

// simple escape
function escapeHtml(value) { return (value||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-participants/index.blade.php ENDPATH**/ ?>