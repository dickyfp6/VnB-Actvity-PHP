@extends('layouts.app')

@section('title', 'VNB Participants - VnB Platform')
@section('page_title', 'VNB Participants')

@section('content')
<div class="px-4 space-y-4">
	<div class="bg-white rounded-xl shadow-sm p-4">
		<div class="flex items-end justify-between gap-3 mb-4 participants-toolbar">
			<div class="flex items-center gap-0 participants-tabs-wrapper">
				<button type="button" class="participants-tab-btn active" data-tab="active" onclick="switchParticipantsTab('active')">Aktif <span id="tab-count-active" class="participants-tab-count">0</span></button>
				<button type="button" class="participants-tab-btn" data-tab="completed" onclick="switchParticipantsTab('completed')">Lulus <span id="tab-count-completed" class="participants-tab-count">0</span></button>
				<button type="button" class="participants-tab-btn" data-tab="canceled" onclick="switchParticipantsTab('canceled')">Cancel <span id="tab-count-canceled" class="participants-tab-count">0</span></button>
			</div>
			<button class="px-4 py-2 rounded-lg text-white font-semibold whitespace-nowrap" style="background-color:#144600;" onclick="openAssignModal()">Assign Employee</button>
		</div>

		<div class="overflow-x-auto">
			<table class="table-modern" id="participants-table" style="width: max-content; min-width: 100%; table-layout: auto;">
				<thead>
					<tr>
						<th>No</th>
						<th>NIP</th>
						<th>Nama Lengkap</th>
						<th>Perusahaan</th>
						<th>Divisi</th>
						<th>Departemen</th>
						<th>Career Stage</th>
						<th>Tanggal Induction</th>
						<th>Fase</th>
						<th>Progress</th>
						<th>Manager Fungsional</th>
						<th>Manager Operasional</th>
						<th>Tanggal Mulai VnB</th>
						<th>Tanggal Selesai VnB</th>
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
			<div class="w-full max-w-5xl bg-white rounded-xl shadow-2xl border border-gray-200 max-h-[90vh] overflow-hidden flex flex-col">
				<div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
					<h3 class="text-lg font-bold text-gray-800">Assign Employee</h3>
					<button type="button" onclick="closeAssignModal()" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
				</div>

				<div class="p-5 space-y-4 overflow-y-auto">
					<div id="assign-step-list" class="space-y-4">
						<div id="assign-warning-unconfigured" class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 hidden">
							<div class="flex items-start gap-3">
								<div class="text-xl">⚠️</div>
								<div>
									<h4 class="font-semibold text-yellow-900">Career Stage Belum Dikonfigurasi</h4>
									<p class="text-sm text-yellow-800 mt-1">Beberapa employee memiliki career stage yang belum dikonfigurasi di VnB Framework. Employee ini tidak bisa didaftarkan sampai framework sudah diatur.</p>
									<p class="text-sm text-yellow-700 mt-2"><strong>Solusi:</strong> Buka menu VnB Framework dan tambahkan konfigurasi untuk career stage yang diperlukan.</p>
								</div>
							</div>
						</div>

						<div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
							<div class="flex flex-wrap items-end gap-3">
								<div class="flex-1 min-w-[240px]">
									<label class="block text-xs font-semibold text-gray-500 mb-1">Cari employee</label>
									<input id="assign-search" type="text" placeholder="Ketik nama, NIP, email, divisi, departemen, dll" class="w-full px-3 py-2 border rounded-lg" />
								</div>
								<div class="flex gap-2">
									<button id="assign-next-btn" class="px-4 py-2 rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed" style="background-color:#144600;" onclick="goToAssignConfirmStep()" disabled>Next</button>
								</div>
							</div>
							<p class="mt-2 text-xs text-gray-500">Menampilkan employee aktif yang sudah tersinkron dan belum mulai VnB. Klik baris untuk memilih.</p>
						</div>

						<div class="flex items-center justify-between gap-3">
							<div class="text-sm text-gray-600">
								<span class="font-semibold text-gray-800" id="assign-results-count">0</span> employee tersedia
								<span class="mx-3 text-gray-400">|</span>
								<span class="font-semibold text-gray-800" id="assign-selected-count-inline">0</span> employee dipilih
							</div>
							<label class="inline-flex items-center gap-2 text-sm text-gray-600">
								<input id="assign-select-all" type="checkbox" class="w-4 h-4" onchange="toggleSelectAllAssignable(this)">
								<span>Pilih semua hasil</span>
							</label>
						</div>

						<div class="overflow-x-auto border rounded-lg">
							<table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
								<thead class="bg-gray-50">
									<tr>
										<th></th>
										<th>NIP</th>
										<th>Nama</th>
										<th>Company</th>
										<th>Divisi</th>
										<th>Department</th>
										<th>Career Stage</th><th>Tanggal Induction</th>
						
									</tr>
								</thead>
								<tbody id="assign-results"></tbody>
							</table>
						</div>

						<div class="pt-2">
							<div id="assign-selection-summary" class="text-sm text-gray-600">Belum ada employee dipilih.</div>
						</div>
					</div>

					<div id="assign-step-confirm" class="space-y-4 hidden">
						<div class="grid grid-cols-1 lg:grid-cols-[1.35fr_0.95fr] gap-4 items-stretch">
							<div class="rounded-lg border border-green-200 bg-green-50 p-4 h-full flex flex-col justify-center">
								<h4 class="font-semibold text-green-900">Konfirmasi pendaftaran ke VnB</h4>
								<p class="text-sm text-green-800 mt-1">Cek lagi daftar employee berikut sebelum didaftarkan ke VnB.</p>
							</div>

							<div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
								<label for="assign-induction-date" class="block text-xs font-semibold text-gray-500 mb-1">Tanggal induction</label>
								<input id="assign-induction-date" type="date" class="w-full px-3 py-2 border rounded-lg bg-white" />
							</div>
						</div>

						<div class="border rounded-lg bg-white" style="overflow: visible;">
							<div style="overflow-x: auto; overflow-y: visible;">
								<table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
									<thead class="bg-gray-50">
										<tr>
											<th>NIP</th>
											<th>Nama</th>
											<th>Divisi</th>
											<th>Department</th>
											<th>Career Stage</th><th>Tanggal Induction</th>
						
											<th>Manager Fungsional</th>
											<th>Manager Operasional</th>
										</tr>
									</thead>
									<tbody id="assign-confirm-list"></tbody>
								</table>
							</div>
						</div>

						<div class="flex items-center justify-between gap-3 pt-1">
							<div class="text-sm text-gray-600"><span id="assign-confirm-count" class="font-semibold text-gray-800">0</span> employee akan didaftarkan.</div>
							<div class="flex items-center gap-2">
								<button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700" onclick="backToAssignListStep()">Kembali</button>
								<button class="px-4 py-2 rounded-lg text-white" style="background-color:#144600;" onclick="confirmAssignEmployees()">Daftarkan ke VnB</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Error Modal -->
	<div id="error-modal" class="fixed inset-0 z-50 hidden">
		<div class="absolute inset-0 bg-black/40" onclick="closeErrorModal()"></div>
		<div class="relative h-full w-full flex items-center justify-center p-4">
			<div class="w-full max-w-md bg-white rounded-xl shadow-2xl border border-gray-200 p-6">
				<div class="flex items-start gap-3 mb-4">
					<div class="flex-1">
						<div id="error-modal-message" class="text-gray-800 text-sm leading-relaxed">Pesan error</div>
					</div>
					<button type="button" onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none flex-shrink-0">&times;</button>
				</div>
				<div class="flex items-center justify-between gap-2" id="error-modal-actions">
					<button type="button" onclick="closeErrorModal()" class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color:#144600;">OK</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('styles')
<style>
/* small styles for participants page */
#participants-table th, #participants-table td { padding: 0.6rem; text-align: left; white-space: nowrap; }
.assign-result { padding: 0.5rem; border-bottom: 1px solid #eee; cursor: pointer }
.assign-result:hover { background: #f7faf7 }
.assign-result.selected { background: #ecfdf5; }
.assign-table-row { cursor: pointer; }
.assign-checkbox { width: 1.2rem; height: 1.2rem; }
.participants-tab-btn {
	display: inline-flex;
	align-items: center;
	gap: 0.3rem;
	padding: 0.75rem 1rem;
	border: none;
	background: transparent;
	color: #6b7280;
	font-weight: 600;
	font-size: 0.95rem;
	cursor: pointer;
	border-bottom: 3px solid transparent;
	transition: all 0.2s ease;
	margin-bottom: -1px;
}
.participants-tab-btn:hover {
	color: #374151;
}
.participants-tab-btn.active {
	background: transparent;
	color: #144600;
	border-bottom-color: #144600;
	font-weight: 700;
}
.participants-tab-count {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-width: 1.5rem;
	height: 1.5rem;
	margin-left: 0.4rem;
	padding: 0 0.25rem;
	border-radius: 50%;
	background: #e5e7eb;
	color: #374151;
	font-size: 0.7rem;
	font-weight: 700;
}
.participants-tab-btn.active .participants-tab-count {
	background: #144600;
	color: #ffffff;
}
.participants-toolbar { align-items: flex-end; }
.participants-tabs-wrapper { flex: 1; min-width: 0; }
@media (max-width: 768px) {
	.participants-toolbar {
		flex-direction: column;
		align-items: stretch;
	}
	.participants-toolbar > button {
		width: 100%;
	}
}
.participants-manager-link { color: #144600; font-weight: 700; }
.participants-cancel-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 2rem;
	height: 2rem;
	border-radius: 0.5rem;
	border: 1px solid #dc2626;
	background: #dc2626;
	color: #ffffff;
	font-size: 0.875rem;
	font-weight: 700;
}
.participants-cancel-btn:hover { background: #b91c1c; border-color: #b91c1c; }
</style>
@endpush

@push('scripts')
<script>
let selectedAssignEmployeeIds = new Set();
let assignModalStep = 'list';
let assignableEmployees = [];
let allAssignableEmployees = [];
let managerEmployeeDirectory = [];
let unconfiguredCareerStages = new Set();
let participantsRows = [];
let currentParticipantsTab = 'active';
let pendingReassignEmployeeId = null;

function showErrorModal(message, showFrameworkButton = false) {
	const modal = document.getElementById('error-modal');
	const msgEl = document.getElementById('error-modal-message');
	const actionsEl = document.getElementById('error-modal-actions');
	if (modal && msgEl && actionsEl) {
		msgEl.textContent = message || 'Terjadi kesalahan.';
		
		if (showFrameworkButton) {
			actionsEl.innerHTML = `
				<a href="/vnb/framework" class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color:#144600; text-decoration: none; display: inline-block;">Framework VnB</a>
				<button type="button" onclick="closeErrorModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold">OK</button>
			`;
		} else {
			actionsEl.innerHTML = `<button type="button" onclick="closeErrorModal()" class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color:#144600;">OK</button>`;
		}
		modal.classList.remove('hidden');
	}
}

function closeErrorModal() {
	const modal = document.getElementById('error-modal');
	if (modal) {
		modal.classList.add('hidden');
	}
}

async function loadParticipants() {
	const tbody = document.getElementById('participants-body');
	if (!tbody) {
		return;
	}

	tbody.innerHTML = '<tr><td colspan="15" class="text-center py-8 text-gray-400">Memuat participants...</td></tr>';

	try {
		const response = await fetch('/api/vnb/participants', {
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json' },
		});
		const data = await response.json();
		participantsRows = Array.isArray(data?.data) ? data.data : [];
		updateParticipantsTabCounts();
		renderParticipantsTable();
	} catch (error) {
		tbody.innerHTML = '<tr><td colspan="15" class="text-center py-8 text-red-500">Gagal memuat participants.</td></tr>';
	}
}

function renderParticipantsTable() {
	const tbody = document.getElementById('participants-body');
	if (!tbody) {
		return;
	}

	const rows = participantsRows.filter((row) => String(row.vnb_status || 'active') === currentParticipantsTab);
	updateParticipantsTabCounts();

	if (!rows.length) {
		const emptyMessage = currentParticipantsTab === 'completed'
			? 'Belum ada participant yang lulus VnB.'
			: currentParticipantsTab === 'canceled'
				? 'Belum ada participant yang di-cancel.'
				: 'Belum ada participants aktif.';
		tbody.innerHTML = `<tr><td colspan="15" class="text-center py-8 text-gray-400">${emptyMessage}</td></tr>`;
		return;
	}

	tbody.innerHTML = rows.map((row, index) => {
		const progress = Math.max(0, Math.min(Number(row.progress || 0), 100));
		const functionalManagerName = resolveFunctionalManagerDisplay(row);
		const operationalManagerName = resolveOperationalManagerDisplay(row);
		const functionalManagerLink = row.manager_functional_id ? `/employees?manager_id=${encodeURIComponent(row.manager_functional_id)}` : null;
		const operationalManagerLink = row.manager_operational_id ? `/employees?manager_id=${encodeURIComponent(row.manager_operational_id)}` : null;
		const actionHtml = renderParticipantAction(row);

		return `
			<tr>
				<td>${index + 1}</td>
				<td>${escapeHtml(row.employee_number || '-')}</td>
				<td>${escapeHtml(row.name || '-')}</td>
				<td>${escapeHtml(row.company || '-')}</td>
				<td>${escapeHtml(row.division || '-')}</td>
				<td>${escapeHtml(row.department || '-')}</td>
				<td>${escapeHtml(row.career_stage || '-')}</td>
				<td>${escapeHtml(row.induction_date || '-')}</td>
				<td>${escapeHtml(row.phase || 'Planning')}</td>
				<td>
					<div class="flex items-center gap-2 min-w-[180px]">
						<div class="h-2 w-28 rounded-full bg-gray-200 overflow-hidden">
							<div class="h-full rounded-full" style="width:${progress}%; background: linear-gradient(90deg, #8dc63f, #144600);"></div>
						</div>
						<span class="text-sm font-semibold text-gray-700">${progress.toFixed(1).replace(/\.0$/, '')}%</span>
					</div>
				</td>
				<td>
					${functionalManagerLink
						? `<a href="${functionalManagerLink}" class="participants-manager-link hover:underline">${escapeHtml(functionalManagerName)}</a>`
						: escapeHtml(functionalManagerName)}
				</td>
				<td>
					${operationalManagerLink
						? `<a href="${operationalManagerLink}" class="participants-manager-link hover:underline">${escapeHtml(operationalManagerName)}</a>`
						: escapeHtml(operationalManagerName)}
				</td>
				<td>${escapeHtml(row.vnb_period_start || '-')}</td>
				<td>${escapeHtml(row.vnb_period_end || '-')}</td>
				<td>
					<div class="flex items-center gap-2">
						${actionHtml}
					</div>
				</td>
			</tr>
		`;
	}).join('');
}

function switchParticipantsTab(tab) {
	currentParticipantsTab = tab;
	document.querySelectorAll('.participants-tab-btn').forEach((button) => {
		button.classList.toggle('active', button.dataset.tab === tab);
	});
	renderParticipantsTable();
}

function updateParticipantsTabCounts() {
	const counts = {
		active: participantsRows.filter((row) => String(row.vnb_status || 'active') === 'active').length,
		completed: participantsRows.filter((row) => String(row.vnb_status || '') === 'completed').length,
		canceled: participantsRows.filter((row) => String(row.vnb_status || '') === 'canceled').length,
	};

	document.getElementById('tab-count-active').textContent = counts.active.toString();
	document.getElementById('tab-count-completed').textContent = counts.completed.toString();
	document.getElementById('tab-count-canceled').textContent = counts.canceled.toString();
}

function getParticipantStatusLabel(status) {
	switch (String(status || 'active')) {
		case 'completed':
			return 'Lulus';
		case 'canceled':
			return 'Cancel';
		default:
			return 'Aktif';
	}
}

function getParticipantStatusBadgeClass(status) {
	switch (String(status || 'active')) {
		case 'completed':
			return 'bg-emerald-100 text-emerald-700';
		case 'canceled':
			return 'bg-rose-100 text-rose-700';
		default:
			return 'bg-green-100 text-green-700';
	}
}

function renderParticipantAction(row) {
	const status = String(row.vnb_status || 'active');
	if (status === 'active') {
		return `<button type="button" class="participants-cancel-btn" title="Cancel VnB" aria-label="Cancel VnB" onclick="cancelParticipantVnb(${row.employee_id}, ${JSON.stringify(row.name || row.employee_number || 'employee').replace(/"/g, '&quot;')})"><i class="fas fa-trash"></i></button>`;
	}

	if (status === 'canceled') {
		return `<button type="button" class="participants-cancel-btn" title="Assign ulang" aria-label="Assign ulang" onclick="openAssignModalForEmployee(${row.employee_id})"><i class="fas fa-rotate-right"></i></button>`;
	}

	return `<span class="text-xs text-gray-400">-</span>`;
}

	async function cancelParticipantVnb(employeeId, employeeName) {
		if (!employeeId) {
			showErrorModal('Employee tidak valid.');
			return;
		}

		if (!confirm(`Yakin ingin cancel VnB untuk ${employeeName}?`)) {
			return;
		}

		const notesInput = prompt('Masukkan alasan cancel VnB. Kosongkan untuk pakai alasan default.', `Dibatalkan dari daftar participant VnB: ${employeeName}`);
		if (notesInput === null) {
			return;
		}

		const notes = notesInput.trim() || `Dibatalkan dari daftar participant VnB: ${employeeName}`;

		try {
			const response = await fetch(`/api/vnb/participants/${employeeId}/revoke`, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify({
					reason: 'others',
					notes,
				}),
			});

			const result = await response.json();
			if (!response.ok || result?.success === false) {
				throw new Error(result?.message || 'Gagal cancel participant VnB.');
			}

			await loadParticipants();
		} catch (error) {
			showErrorModal(error?.message || 'Gagal cancel participant VnB.');
		}
	}

function openAssignModal(preservePendingReassign = false) {
	document.getElementById('assign-modal').classList.remove('hidden');
	assignModalStep = 'list';
	selectedAssignEmployeeIds = new Set();
	if (!preservePendingReassign) {
		pendingReassignEmployeeId = null;
	}
	updateAssignModalStep();
	loadAssignableEmployees();
	loadManagerEmployeeDirectory();
	bindAssignSearchInput();
}
function closeAssignModal() {
	document.getElementById('assign-modal').classList.add('hidden');
	assignModalStep = 'list';
	selectedAssignEmployeeIds = new Set();
	assignableEmployees = [];
	document.getElementById('assign-search').value = '';
	document.getElementById('assign-induction-date').value = '';
	document.getElementById('assign-results').innerHTML = '';
	document.getElementById('assign-confirm-list').innerHTML = '';
	document.getElementById('assign-selection-summary').textContent = 'Belum ada employee dipilih.';
	document.getElementById('assign-results-count').textContent = '0';
	document.getElementById('assign-confirm-count').textContent = '0';
	document.getElementById('assign-select-all').checked = false;
	pendingReassignEmployeeId = null;
}

async function loadManagerEmployeeDirectory() {
	try {
		const response = await fetch('/api/managers-list', {
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json' },
		});
		const data = await response.json();
		managerEmployeeDirectory = Array.isArray(data?.data) ? data.data : [];
	} catch (error) {
		managerEmployeeDirectory = [];
	}

	if (assignModalStep === 'confirm') {
		updateAssignConfirmList();
	}
}

function normalizeText(value) {
	return String(value || '').trim().toLowerCase();
}

function resolveGeneralManagerOfDivision(divisionId) {
	const divisionKey = String(divisionId || '');
	if (!divisionKey) return null;
	return managerEmployeeDirectory.find((manager) => {
		const sameDivision = String(manager.division_id || '') === divisionKey;
		const isGeneralDept = normalizeText(manager.department) === 'general';
		return sameDivision && isGeneralDept;
	}) || null;
}

function resolveFunctionalManagerByHierarchy(employee) {
	if (!managerEmployeeDirectory.length) {
		return null;
	}

	const level = normalizeText(employee.level);
	const careerStage = normalizeText(employee.career_stage);
	const divisionKey = String(employee.division_id || '');
	const departmentKey = String(employee.department_id || '');

	const isOutsource = ['os', 'outsource', 'outsourcing', 'harian', 'mingguan', 'borongan'].some((k) => level.includes(k));
	if (isOutsource) {
		return null;
	}

	const isTopLevel = careerStage.includes('manage function') || careerStage.includes('manage manager') || level.includes('director') || level.includes('direktur');
	if (isTopLevel) {
		return null;
	}

	const isManagerLevel = careerStage.includes('manage other') || (level.includes('manager') && !level.includes('general manager'));
	if (isManagerLevel) {
		return resolveGeneralManagerOfDivision(divisionKey);
	}

	const deptManager = managerEmployeeDirectory.find((manager) => {
		const sameDivision = String(manager.division_id || '') === divisionKey;
		const sameDepartment = String(manager.department_id || '') === departmentKey;
		return sameDivision && sameDepartment;
	});

	if (deptManager) {
		return deptManager;
	}

	return resolveGeneralManagerOfDivision(divisionKey);
}

function resolveOperationalManagerByHierarchy(employee) {
	if (!managerEmployeeDirectory.length) {
		return null;
	}

	const level = normalizeText(employee.level);
	const careerStage = normalizeText(employee.career_stage);
	const divisionKey = String(employee.division_id || '');
	const departmentKey = String(employee.department_id || '');

	const isOutsource = ['os', 'outsource', 'outsourcing', 'harian', 'mingguan', 'borongan'].some((k) => level.includes(k));
	if (isOutsource) {
		return null;
	}

	const isTopLevel = careerStage.includes('manage function') || careerStage.includes('manage manager') || level.includes('director') || level.includes('direktur');
	if (isTopLevel) {
		return null;
	}

	const deptManager = managerEmployeeDirectory.find((manager) => {
		const sameDivision = String(manager.division_id || '') === divisionKey;
		const sameDepartment = String(manager.department_id || '') === departmentKey;
		return sameDivision && sameDepartment;
	});

	if (deptManager && deptManager.name) {
		return deptManager;
	}

	// If no operational manager is found, fallback to the resolved functional manager.
	return resolveFunctionalManagerByHierarchy(employee);
}

function resolveFunctionalManagerDisplay(employee) {
	const fromEmployee = employee.manager_functional_label || employee.manager_functional_name || employee.manager_functional || '';
	if (String(fromEmployee || '').trim() !== '') {
		return fromEmployee;
	}

	const derived = resolveFunctionalManagerByHierarchy(employee);
	if (derived && derived.name) {
		return derived.name;
	}

	return '-';
}

function resolveOperationalManagerDisplay(employee) {
	const fromEmployee = employee.manager_operational_label || employee.manager_operational_name || employee.manager_operational || '';
	if (String(fromEmployee || '').trim() !== '') {
		return fromEmployee;
	}

	const derived = resolveOperationalManagerByHierarchy(employee);
	if (derived && derived.name) {
		return derived.name;
	}

	return '-';
}

function updateAssignModalStep() {
	const listStep = document.getElementById('assign-step-list');
	const confirmStep = document.getElementById('assign-step-confirm');
	if (assignModalStep === 'confirm') {
		listStep.classList.add('hidden');
		confirmStep.classList.remove('hidden');
	} else {
		confirmStep.classList.add('hidden');
		listStep.classList.remove('hidden');
	}
	updateAssignSelectionSummary();
	updateAssignConfirmList();
	updateAssignNextButton();
	updateAssignSelectAllState();

	const searchInput = document.getElementById('assign-search');
	if (searchInput) {
		searchInput.focus();
	}
	if (assignModalStep === 'list') {
		const selectAll = document.getElementById('assign-select-all');
		if (selectAll) {
			selectAll.checked = false;
		}
	}
}

async function loadAssignableEmployees(overrideQuery = null) {
	const q = overrideQuery !== null ? overrideQuery : document.getElementById('assign-search').value.trim();
	const container = document.getElementById('assign-results');
	container.innerHTML = '<tr><td colspan="7" class="text-sm text-gray-500 p-4">Mencari...</td></tr>';
	try {
		const params = new URLSearchParams();
		params.set('vnb_status', 'assignable');
		params.set('status', 'Aktif');
		if (q) {
			params.set('search', q);
		}
		const response = await fetch(`/api/employees?${params.toString()}`, {
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json' },
		});
		const data = await response.json();
		allAssignableEmployees = Array.isArray(data?.data) ? data.data : [];
		unconfiguredCareerStages.clear();
		assignableEmployees = allAssignableEmployees.filter((emp) => {
			const status = String(emp.vnb_status || 'not_started');
			if (!['not_started', 'canceled'].includes(status)) {
				return false;
			}
			if (!emp.career_stage || String(emp.career_stage || '').trim() === '') {
				unconfiguredCareerStages.add(emp.level || 'Unknown');
				return false;
			}
			return true;
		});
		updateWarningMessage();
		renderAssignableEmployees();
		if (pendingReassignEmployeeId) {
			selectedAssignEmployeeIds = new Set([pendingReassignEmployeeId]);
			pendingReassignEmployeeId = null;
			assignModalStep = 'confirm';
			updateAssignModalStep();
		}
	} catch (err) {
		container.innerHTML = '<tr><td colspan="7" class="text-sm text-red-500 p-4">Gagal memuat data employee</td></tr>';
	}
}

function updateWarningMessage() {
	const warningEl = document.getElementById('assign-warning-unconfigured');
	if (!warningEl) return;
	if (unconfiguredCareerStages.size > 0) {
		const stageList = Array.from(unconfiguredCareerStages).join(', ');
		showErrorModal(`Career Stage Belum Dikonfigurasi: ${stageList}. Beberapa employee memiliki career stage yang belum dikonfigurasi di VnB Framework. Employee ini tidak bisa didaftarkan sampai framework sudah diatur.`, true);
	}
}

function renderAssignableEmployees() {
	const container = document.getElementById('assign-results');
	const rows = [
		...assignableEmployees.filter((employee) => selectedAssignEmployeeIds.has(String(employee.id))),
		...assignableEmployees.filter((employee) => !selectedAssignEmployeeIds.has(String(employee.id))),
	];
	document.getElementById('assign-results-count').textContent = rows.length.toString();

	if (!rows.length) {
		container.innerHTML = '<tr><td colspan="7" class="text-center text-sm text-gray-500 p-4">Tidak ada employee dengan status VnB belum dimulai.</td></tr>';
		updateAssignSelectionSummary();
		return;
	}

	container.innerHTML = rows.map((employee) => {
		const isSelected = selectedAssignEmployeeIds.has(String(employee.id));
		return `
			<tr class="assign-table-row ${isSelected ? 'selected' : ''}" data-employee-id="${employee.id}" onclick="onAssignRowClick(${employee.id}, event)">
				<td class="text-center">
					<input type="checkbox" class="assign-checkbox" ${isSelected ? 'checked' : ''} onchange="toggleAssignEmployeeSelection(${employee.id}, this.checked)" onclick="event.stopPropagation()">
				</td>
				<td class="font-medium text-gray-800">${escapeHtml(employee.employee_number || '-')}</td>
				<td>${escapeHtml(employee.name_display || employee.name || '-')}</td>
				<td>${escapeHtml(employee.company || '-')}</td>
				<td>${escapeHtml(employee.division || '-')}</td>
				<td>${escapeHtml(employee.department || '-')}</td>
				<td>${escapeHtml(employee.career_stage || '-')}</td>
			</tr>
		`;
	}).join('');

	updateAssignSelectionSummary();
	updateAssignNextButton();
	updateAssignSelectAllState();
}

function toggleAssignEmployeeSelection(employeeId, checked) {
	const key = String(employeeId);
	if (checked) {
		selectedAssignEmployeeIds.add(key);
	} else {
		selectedAssignEmployeeIds.delete(key);
	}

	const row = document.querySelector(`.assign-table-row[data-employee-id="${employeeId}"]`);
	if (row) {
		row.classList.toggle('selected', checked);
	}

	const checkbox = row ? row.querySelector('input[type="checkbox"]') : null;
	if (checkbox && checkbox.checked !== checked) {
		checkbox.checked = checked;
	}

	updateAssignSelectionSummary();
	updateAssignNextButton();
	updateAssignSelectAllState();
}

function onAssignRowClick(employeeId, event) {
	if (event?.target?.closest('input, button, a, label')) {
		return;
	}
	const key = String(employeeId);
	const nextChecked = !selectedAssignEmployeeIds.has(key);
	toggleAssignEmployeeSelection(employeeId, nextChecked);

	const searchInput = document.getElementById('assign-search');
	if (searchInput && searchInput.value.trim() !== '') {
		searchInput.value = '';
		loadAssignableEmployees('');
	}
}

let assignSearchDebounceTimer = null;

function bindAssignSearchInput() {
	const searchInput = document.getElementById('assign-search');
	if (!searchInput || searchInput.dataset.bound === '1') {
		return;
	}

	searchInput.addEventListener('input', () => {
		clearTimeout(assignSearchDebounceTimer);
		assignSearchDebounceTimer = setTimeout(() => {
			loadAssignableEmployees(searchInput.value.trim());
		}, 200);
	});

	searchInput.addEventListener('keydown', (event) => {
		if (event.key === 'Enter') {
			event.preventDefault();
			loadAssignableEmployees(searchInput.value.trim());
		}
	});

	searchInput.dataset.bound = '1';
}

function toggleSelectAllAssignable(el) {
	const checked = !!el.checked;
	assignableEmployees.forEach((employee) => {
		const key = String(employee.id);
		if (checked) {
			selectedAssignEmployeeIds.add(key);
		} else {
			selectedAssignEmployeeIds.delete(key);
		}
	});
	renderAssignableEmployees();
}

function updateAssignSelectAllState() {
	const selectAll = document.getElementById('assign-select-all');
	if (!selectAll) return;
	if (!assignableEmployees.length) {
		selectAll.checked = false;
		selectAll.indeterminate = false;
		return;
	}
	const selectedVisibleCount = assignableEmployees.filter((employee) => selectedAssignEmployeeIds.has(String(employee.id))).length;
	selectAll.checked = selectedVisibleCount > 0 && selectedVisibleCount === assignableEmployees.length;
	selectAll.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < assignableEmployees.length;
}

function updateAssignSelectionSummary() {
	const selectedEmployees = getSelectedAssignableEmployees();
	const summaryEl = document.getElementById('assign-selection-summary');
	const selectedInlineEl = document.getElementById('assign-selected-count-inline');
	const count = selectedEmployees.length;
	if (selectedInlineEl) {
		selectedInlineEl.textContent = count.toString();
	}
	if (!summaryEl) return;
	if (!count) {
		summaryEl.textContent = 'Belum ada employee dipilih.';
		return;
	}
	summaryEl.textContent = `${count} employee dipilih.`;
}

function updateAssignNextButton() {
	const button = document.getElementById('assign-next-btn');
	if (!button) return;
	button.disabled = selectedAssignEmployeeIds.size === 0;
}

function getSelectedAssignableEmployees() {
	return assignableEmployees.filter((employee) => selectedAssignEmployeeIds.has(String(employee.id)));
}

function goToAssignConfirmStep() {
	if (!selectedAssignEmployeeIds.size) {
		showErrorModal('Pilih minimal satu employee terlebih dahulu.');
		return;
	}
	assignModalStep = 'confirm';
	updateAssignModalStep();
}

function backToAssignListStep() {
	assignModalStep = 'list';
	updateAssignModalStep();
}

function openAssignModalForEmployee(employeeId) {
	pendingReassignEmployeeId = employeeId ? String(employeeId) : null;
	openAssignModal(true);
}

function updateAssignConfirmList() {
	const container = document.getElementById('assign-confirm-list');
	const selectedEmployees = getSelectedAssignableEmployees();
	document.getElementById('assign-confirm-count').textContent = selectedEmployees.length.toString();

	if (!selectedEmployees.length) {
		container.innerHTML = '<tr><td colspan="7" class="text-center text-sm text-gray-500 p-4">Belum ada employee dipilih.</td></tr>';
		return;
	}

	container.innerHTML = selectedEmployees.map((employee) => {
		const functionalManagerName = resolveFunctionalManagerDisplay(employee);
		const operationalManagerName = resolveOperationalManagerDisplay(employee);
		return `
		<tr>
			<td class="font-medium text-gray-800">${escapeHtml(employee.employee_number || '-')}</td>
			<td>${escapeHtml(employee.name_display || employee.name || '-')}</td>
			<td>${escapeHtml(employee.division || '-')}</td>
			<td>${escapeHtml(employee.department || '-')}</td>
			<td>${escapeHtml(employee.career_stage || '-')}</td>
			<td>${escapeHtml(functionalManagerName)}</td>
			<td>${escapeHtml(operationalManagerName)}</td>
		</tr>
		`;
	}).join('');
}

async function confirmAssignEmployees() {
	const selectedEmployees = getSelectedAssignableEmployees();
	if (!selectedEmployees.length) {
			showErrorModal('Pilih minimal satu employee terlebih dahulu.');
	}

	const inductionDate = getAssignInductionDate();
	if (!inductionDate || String(inductionDate).trim() === '') {
		showErrorModal('Tanggal induction harus diisi sebelum mendaftarkan employee ke VnB.');
		return;
	}

	const confirmButton = document.querySelector('#assign-step-confirm button[onclick="confirmAssignEmployees()"]');
	if (confirmButton) {
		confirmButton.disabled = true;
		confirmButton.textContent = 'Mendaftarkan...';
	}

	try {
		for (const employee of selectedEmployees) {
			const response = await fetch(`/api/vnb/participants/${employee.id}/assign`, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify({
					induction_date: inductionDate,
				}),
			});
			const result = await response.json();
			if (!response.ok || result?.success === false) {
				throw new Error(result?.message || 'Gagal mendaftarkan employee ke VnB.');
			}
		}
		await loadParticipants();
		closeAssignModal();
	} catch (error) {
		showErrorModal(error?.message || 'Gagal mendaftarkan employee ke VnB.');
	} finally {
		if (confirmButton) {
			confirmButton.disabled = false;
			confirmButton.textContent = 'Daftarkan ke VnB';
		}
	}
}

function getAssignInductionDate() {
	const input = document.getElementById('assign-induction-date');
	return input ? input.value : '';
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

function addParticipantRow(emp) {
	const tbody = document.getElementById('participants-body');
	if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';
	const idx = tbody.children.length + 1;
	const nip = emp.employee_number || emp.nip || emp.employeeNumber || '';
	const name = emp.name || emp.name_display || emp.full_name || '';
	const company = emp.company || '';
	const division = emp.division || '';
	const department = emp.department || '';
	const career = emp.career_stage || mapLevelToCareerStage(emp.level || emp.position_level || '');
	const periodStart = emp.vnb_period_start || '';
	const periodEnd = emp.vnb_period_end || '';
	const inductionDate = emp.induction_date || '';
	const functionalManagerName = resolveFunctionalManagerDisplay(emp);
	const operationalManagerName = resolveOperationalManagerDisplay(emp);

	const row = document.createElement('tr');
	row.innerHTML = `
		<td>${idx}</td>
		<td>${escapeHtml(nip)}</td>
		<td>${escapeHtml(name)}</td>
		<td>${escapeHtml(company)}</td>
		<td>${escapeHtml(division)}</td>
		<td>${escapeHtml(department)}</td>
		<td>${escapeHtml(career)}</td>
		<td><input type="text" class="px-2 py-1 border rounded w-16" value="1" /></td>
		<td><input type="text" class="px-2 py-1 border rounded w-20" value="0%" /></td>
		<td><input type="text" class="px-2 py-1 border rounded" value="${escapeHtmlAttr(functionalManagerName)}" /></td>
		<td><input type="text" class="px-2 py-1 border rounded" value="${escapeHtmlAttr(operationalManagerName)}" /></td>
		<td class="start-date">${escapeHtml(periodStart)}</td>
		<td class="end-date">${escapeHtml(periodEnd)}</td>
		<td>${escapeHtml(inductionDate || '-')}</td>
		<td><button class="px-2 py-1 text-sm rounded border" onclick="removeParticipantRow(this)">Hapus</button></td>
	`;
	tbody.appendChild(row);
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
function escapeHtmlAttr(value) { return escapeHtml(value).replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }

loadParticipants();
</script>
@endpush






