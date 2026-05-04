

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
			<span class="text-sm text-gray-500 hidden md:inline">Pilih employee yang status VnB-nya belum dimulai</span>
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
			<div class="w-full max-w-5xl bg-white rounded-xl shadow-2xl border border-gray-200 max-h-[90vh] overflow-hidden flex flex-col">
				<div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
					<h3 class="text-lg font-bold text-gray-800">Assign Employee</h3>
					<button type="button" onclick="closeAssignModal()" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
				</div>

				<div class="p-5 space-y-4 overflow-y-auto">
					<div id="assign-step-list" class="space-y-4">
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
										<th>Career Stage</th>
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
						<div class="rounded-lg border border-green-200 bg-green-50 p-4">
							<h4 class="font-semibold text-green-900">Konfirmasi pendaftaran ke VnB</h4>
							<p class="text-sm text-green-800 mt-1">Cek lagi daftar employee berikut sebelum didaftarkan ke VnB.</p>
						</div>

						<div class="grid grid-cols-1 lg:grid-cols-[1.35fr_0.95fr] gap-4 items-start">
							<div class="overflow-x-auto border rounded-lg bg-white">
								<table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
									<thead class="bg-gray-50">
										<tr>
											<th>NIP</th>
											<th>Nama</th>
											<th>Divisi</th>
											<th>Department</th>
											<th>Career Stage</th>
											<th>Manager Fungsional</th>
											<th>Manager Operasional</th>
										</tr>
									</thead>
									<tbody id="assign-confirm-list"></tbody>
								</table>
							</div>

							<div class="space-y-4">
								<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-4">
									<div>
										<label for="assign-induction-date" class="block text-xs font-semibold text-gray-500 mb-1">Tanggal induction</label>
										<input id="assign-induction-date" type="date" class="w-full px-3 py-2 border rounded-lg bg-white" />
										<p class="mt-2 text-xs text-gray-500">Tanggal ini dipakai untuk semua employee yang dipilih dalam batch ini.</p>
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
.assign-result.selected { background: #ecfdf5; }
.assign-table-row.selected { background: #f0fdf4; }
.assign-table-row { cursor: pointer; }
.assign-checkbox { width: 1.2rem; height: 1.2rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let assignableEmployees = [];
let selectedAssignEmployeeIds = new Set();
let assignModalStep = 'list';
let managerDirectory = [];
let assignManagerSelections = {};

function openAssignModal() {
	document.getElementById('assign-modal').classList.remove('hidden');
	assignModalStep = 'list';
	selectedAssignEmployeeIds = new Set();
	assignManagerSelections = {};
	updateAssignModalStep();
	loadAssignableEmployees();
	loadManagerDirectory();
	bindAssignSearchInput();
}
function closeAssignModal() {
	document.getElementById('assign-modal').classList.add('hidden');
	assignModalStep = 'list';
	selectedAssignEmployeeIds = new Set();
	assignManagerSelections = {};
	assignableEmployees = [];
	document.getElementById('assign-search').value = '';
	document.getElementById('assign-induction-date').value = '';
	document.getElementById('assign-results').innerHTML = '';
	document.getElementById('assign-confirm-list').innerHTML = '';
	document.getElementById('assign-selection-summary').textContent = 'Belum ada employee dipilih.';
	document.getElementById('assign-results-count').textContent = '0';
	document.getElementById('assign-confirm-count').textContent = '0';
	document.getElementById('assign-select-all').checked = false;
}

async function loadManagerDirectory() {
	try {
		const response = await fetch('/api/managers-list', {
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json' },
		});
		const data = await response.json();
		managerDirectory = Array.isArray(data?.data) ? data.data : [];
	} catch (error) {
		managerDirectory = [];
	}
	if (assignModalStep === 'confirm') {
		updateAssignConfirmList();
	}
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

function isGeneralDepartment(name) {
	return String(name || '').trim().toLowerCase() === 'general';
}

function getSuggestedFunctionalManagers(employee) {
	if (!employee) return [];
	const divisionName = String(employee.division || employee.division_name || '').trim().toLowerCase();
	const departmentName = String(employee.department || employee.department_name || '').trim().toLowerCase();
	const sameDivisionSameDepartment = managerDirectory.filter((manager) =>
		String(manager.division || '').trim().toLowerCase() === divisionName && String(manager.department || '').trim().toLowerCase() === departmentName
	);
	const sameDivisionGeneral = managerDirectory.filter((manager) =>
		String(manager.division || '').trim().toLowerCase() === divisionName && isGeneralDepartment(manager.department)
	);
	return [...sameDivisionSameDepartment, ...sameDivisionGeneral].filter((manager, index, array) =>
		array.findIndex((item) => String(item.id) === String(manager.id)) === index
	);
}

function buildManagerOptionsHtml(managers, includeEmpty = true, includeSuggestedLabel = false) {
	const options = [];
	if (includeEmpty) {
		options.push('<option value="">Kosong</option>');
	}
	if (includeSuggestedLabel && managers.length) {
		options.push('<option value="__suggested__" disabled>Suggested</option>');
	}
	managers.forEach((manager) => {
		const label = `${manager.name}${manager.division ? ' • ' + manager.division : ''}${manager.department ? ' • ' + manager.department : ''}`;
		options.push(`<option value="${escapeHtml(String(manager.id))}">${escapeHtml(label)}</option>`);
	});
	return options.join('');
}

function getEmployeeManagerSelection(employee) {
	const key = String(employee.id);
	if (!assignManagerSelections[key]) {
		const suggestedFunctional = getSuggestedFunctionalManagers(employee);
		const firstSuggestion = suggestedFunctional[0] || null;
		assignManagerSelections[key] = {
			functional_id: firstSuggestion ? String(firstSuggestion.id) : '',
			functional_name: firstSuggestion ? (firstSuggestion.name || '') : '',
			operational_id: '',
			operational_name: '',
		};
	}
	return assignManagerSelections[key];
}

function buildFunctionalOptionsForEmployee(employee) {
	const suggestedFunctional = getSuggestedFunctionalManagers(employee);
	const functionalOptions = [
		...suggestedFunctional,
		...managerDirectory.filter((manager) => !suggestedFunctional.some((item) => String(item.id) === String(manager.id))),
	];
	return buildManagerOptionsHtml(functionalOptions, true, suggestedFunctional.length > 0);
}

function onRowFunctionalManagerSelectChange(employeeId, selectEl) {
	if (!selectEl || selectEl.value === '__suggested__') return;
	const selectedOption = selectEl.selectedOptions[0];
	const rowTextInput = document.getElementById(`assign-functional-text-${employeeId}`);
	const current = assignManagerSelections[String(employeeId)] || {
		functional_id: '',
		functional_name: '',
		operational_id: '',
		operational_name: '',
	};
	current.functional_id = selectEl.value;
	current.functional_name = selectedOption ? selectedOption.text.split(' • ')[0] : '';
	assignManagerSelections[String(employeeId)] = current;
	if (rowTextInput) {
		rowTextInput.value = current.functional_name;
	}
}

function syncRowFunctionalManagerManual(employeeId, value) {
	const current = assignManagerSelections[String(employeeId)] || {
		functional_id: '',
		functional_name: '',
		operational_id: '',
		operational_name: '',
	};
	current.functional_name = value;
	assignManagerSelections[String(employeeId)] = current;
}

function onRowOperationalManagerSelectChange(employeeId, selectEl) {
	if (!selectEl) return;
	const selectedOption = selectEl.selectedOptions[0];
	const rowTextInput = document.getElementById(`assign-operational-text-${employeeId}`);
	const current = assignManagerSelections[String(employeeId)] || {
		functional_id: '',
		functional_name: '',
		operational_id: '',
		operational_name: '',
	};
	current.operational_id = selectEl.value;
	current.operational_name = selectedOption ? selectedOption.text.split(' • ')[0] : '';
	assignManagerSelections[String(employeeId)] = current;
	if (rowTextInput) {
		rowTextInput.value = current.operational_name;
	}
}

function syncRowOperationalManagerManual(employeeId, value) {
	const current = assignManagerSelections[String(employeeId)] || {
		functional_id: '',
		functional_name: '',
		operational_id: '',
		operational_name: '',
	};
	current.operational_name = value;
	assignManagerSelections[String(employeeId)] = current;
}

async function loadAssignableEmployees(overrideQuery = null) {
	const q = overrideQuery !== null ? overrideQuery : document.getElementById('assign-search').value.trim();
	const container = document.getElementById('assign-results');
	container.innerHTML = '<tr><td colspan="7" class="text-sm text-gray-500 p-4">Mencari...</td></tr>';
	try {
		const params = new URLSearchParams();
		params.set('vnb_status', 'not_started');
		params.set('status', 'Aktif');
		if (q) {
			params.set('search', q);
		}
		const response = await fetch(`/api/employees?${params.toString()}`, {
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json' },
		});
		const data = await response.json();
		assignableEmployees = Array.isArray(data?.data) ? data.data : [];
		renderAssignableEmployees();
	} catch (err) {
		container.innerHTML = '<tr><td colspan="7" class="text-sm text-red-500 p-4">Gagal memuat data employee</td></tr>';
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
		alert('Pilih minimal satu employee terlebih dahulu.');
		return;
	}
	assignModalStep = 'confirm';
	updateAssignModalStep();
}

function backToAssignListStep() {
	assignModalStep = 'list';
	updateAssignModalStep();
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
		const selection = getEmployeeManagerSelection(employee);
		const functionalOptions = buildFunctionalOptionsForEmployee(employee);
		const operationalOptions = buildManagerOptionsHtml(managerDirectory, true, false);
		return `
		<tr>
			<td class="font-medium text-gray-800">${escapeHtml(employee.employee_number || '-')}</td>
			<td>${escapeHtml(employee.name_display || employee.name || '-')}</td>
			<td>${escapeHtml(employee.division || '-')}</td>
			<td>${escapeHtml(employee.department || '-')}</td>
			<td>${escapeHtml(employee.career_stage || '-')}</td>
			<td class="min-w-[280px]">
				<div class="space-y-2">
					<select class="w-full px-2 py-1.5 border rounded bg-white text-sm" onchange="onRowFunctionalManagerSelectChange(${employee.id}, this)">
						${functionalOptions}
					</select>
					<input id="assign-functional-text-${employee.id}" type="text" class="w-full px-2 py-1.5 border rounded bg-white text-sm" value="${escapeHtml(selection.functional_name || '')}" placeholder="Nama manager fungsional" oninput="syncRowFunctionalManagerManual(${employee.id}, this.value)" />
				</div>
			</td>
			<td class="min-w-[280px]">
				<div class="space-y-2">
					<select class="w-full px-2 py-1.5 border rounded bg-white text-sm" onchange="onRowOperationalManagerSelectChange(${employee.id}, this)">
						${operationalOptions}
					</select>
					<input id="assign-operational-text-${employee.id}" type="text" class="w-full px-2 py-1.5 border rounded bg-white text-sm" value="${escapeHtml(selection.operational_name || '')}" placeholder="Kosong / optional" oninput="syncRowOperationalManagerManual(${employee.id}, this.value)" />
				</div>
			</td>
		</tr>
		`;
	}).join('');

	selectedEmployees.forEach((employee) => {
		const key = String(employee.id);
		const selection = assignManagerSelections[key] || null;
		if (!selection) return;
		const functionalSelect = container.querySelector(`select[onchange="onRowFunctionalManagerSelectChange(${employee.id}, this)"]`);
		const operationalSelect = container.querySelector(`select[onchange="onRowOperationalManagerSelectChange(${employee.id}, this)"]`);
		if (functionalSelect) {
			functionalSelect.value = selection.functional_id || '';
		}
		if (operationalSelect) {
			operationalSelect.value = selection.operational_id || '';
		}
	});
}

async function confirmAssignEmployees() {
	const selectedEmployees = getSelectedAssignableEmployees();
	if (!selectedEmployees.length) {
		alert('Pilih minimal satu employee terlebih dahulu.');
		return;
	}

	const confirmButton = document.querySelector('#assign-step-confirm button[onclick="confirmAssignEmployees()"]');
	if (confirmButton) {
		confirmButton.disabled = true;
		confirmButton.textContent = 'Mendaftarkan...';
	}

	try {
		for (const employee of selectedEmployees) {
			const managerSelection = getEmployeeManagerSelection(employee);
			try {
				await fetch(`/api/vnb/participants/${employee.id}/assign`, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json',
						'X-Requested-With': 'XMLHttpRequest',
					},
					body: JSON.stringify({
						induction_date: getAssignInductionDate(),
							manager_functional_id: managerSelection.functional_id || null,
							manager_functional_name: managerSelection.functional_name || null,
							manager_operational_id: managerSelection.operational_id || null,
							manager_operational_name: managerSelection.operational_name || null,
					}),
				});
			} catch (error) {
				// Continue with local preview update even if the placeholder endpoint fails.
			}
			addParticipantRow({
				...employee,
				induction_date: getAssignInductionDate(),
					manager_functional_name: managerSelection.functional_name || '',
					manager_operational_name: managerSelection.operational_name || '',
			});
		}
		closeAssignModal();
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
	const functionalManagerName = emp.manager_functional_name || '';
	const operationalManagerName = emp.manager_operational_name || '';

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
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views\vnb-participants\index.blade.php ENDPATH**/ ?>