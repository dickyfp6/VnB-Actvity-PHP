
<?php $__env->startSection('title','Manage New Hire'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Manage New Hire</h1>
        <div class="flex items-center gap-2">
            <button onclick="openImportModal()" class="px-4 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-import mr-1"></i> Import New Hire
            </button>
            <button onclick="openEmployeeModal()" class="text-white px-4 py-2 rounded-lg text-sm transition" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
                <i class="fas fa-plus mr-1"></i> Add New Hire
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center gap-2 mb-3">
            <button id="btn-lifecycle-active" onclick="setLifecycleTab('active')" class="px-3 py-2 rounded-lg text-sm text-white" style="background-color:#144600;">New Hire Active</button>
            <button id="btn-lifecycle-history" onclick="setLifecycleTab('history')" class="px-3 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">History New Hire</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input id="f-search" type="text" placeholder="Cari NIP/Nama/Email" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onkeyup="applyFilters()">
            <select id="f-status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="applyFilters()">
                <option value="">Semua Status Pegawai</option>
            </select>
            <select id="f-phase" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="applyFilters()">
                <option value="">Semua Fase</option>
                <option value="Planning">Planning</option>
                <option value="Fase 1">Fase 1</option>
                <option value="Fase 2">Fase 2</option>
                <option value="Fase 3">Fase 3</option>
                <option value="Selesai">Selesai</option>
            </select>
            <button onclick="loadEmployees()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">No</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">NIP</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Nama New Hire</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Tanggal Induction</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Whatsapp</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Periode Awal</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Periode Akhir</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Career Stage</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Fase</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Progress</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Manager Fungsional</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Manager Operasional</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Perusahaan</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Divisi</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Departemen</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Penempatan</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Golongan</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Status Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Status Lifecycle</th>
                        <th class="px-4 py-3 text-right text-xs uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody id="employee-body" class="divide-y divide-gray-200 text-gray-700">
                    <tr><td colspan="23" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="employee-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-4xl shadow-xl max-h-[90vh] overflow-y-auto">
        <h2 id="employee-modal-title" class="text-lg font-bold text-gray-800 mb-4">Add New Hire</h2>
        <form id="employee-form" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input type="hidden" id="e-id">
            <div><label class="block text-sm text-gray-700 mb-1">NIP</label><input id="e-employee-number" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm text-gray-700 mb-1">Tanggal Masuk</label><input id="e-date-joined" type="date" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm text-gray-700 mb-1">Tanggal Induction</label><input id="e-induction-date" type="date" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm text-gray-700 mb-1">Nama New Hire</label><input id="e-name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm text-gray-700 mb-1">Email</label><input id="e-email" type="email" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
            <div><label class="block text-sm text-gray-700 mb-1">Nomor Whatsapp</label><input id="e-whatsapp" type="text" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Manager Fungsional</label>
                <input id="e-manager-functional" type="text" list="manager-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik nama/email manager">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Manager Operasional</label>
                <input id="e-manager-operational" type="text" list="manager-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik nama/email manager">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Perusahaan</label>
                <input id="e-company" type="text" list="company-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik perusahaan" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Divisi</label>
                <input id="e-division" type="text" list="division-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik divisi" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Departemen</label>
                <input id="e-department" type="text" list="department-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik departemen" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Jabatan</label>
                <input id="e-position" type="text" list="position-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik jabatan" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Penempatan</label>
                <input id="e-placement" type="text" list="placement-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik penempatan" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Golongan</label>
                <input id="e-level" type="text" list="level-options-list" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ketik golongan" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Status Pegawai</label>
                <input id="e-employee-status" type="text" list="employee-status-options" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <datalist id="employee-status-options"></datalist>
            </div>

            <datalist id="manager-options-list"></datalist>
            <datalist id="company-options-list"></datalist>
            <datalist id="division-options-list"></datalist>
            <datalist id="department-options-list"></datalist>
            <datalist id="position-options-list"></datalist>
            <datalist id="placement-options-list"></datalist>
            <datalist id="level-options-list"></datalist>

            <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEmployeeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm" style="background-color:#144600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="import-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-4xl shadow-xl">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-bold text-gray-800">Import New Hire</h2>
            <a href="/api/employees/import/template" class="px-3 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                Download Template XLSX
            </a>
        </div>
        <p class="text-sm text-gray-500 mb-4">Pilih Upload file CSV/XLSX atau Paste data dari Excel. Data tidak akan langsung tersimpan, akan masuk preview validasi dulu.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">Upload File</h3>
                <input type="file" id="import-file" accept=".csv,.xlsx" class="w-full border rounded px-2 py-2 text-sm">
                <button onclick="submitFileImport()" class="mt-3 px-4 py-2 text-white rounded text-sm" style="background:#144600;">Preview Import File</button>
            </div>
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">Paste from Excel</h3>
                <textarea id="import-paste" rows="8" class="w-full border rounded px-2 py-2 text-sm" placeholder="Urutan kolom: NIP[TAB]Tanggal Masuk[TAB]Tanggal Induction[TAB]Nama[TAB]Email[TAB]Whatsapp[TAB]Manager Fungsional[TAB]Manager Operasional[TAB]Perusahaan[TAB]Divisi[TAB]Departemen[TAB]Jabatan[TAB]Penempatan[TAB]Golongan[TAB]Status"></textarea>
                <button onclick="submitPasteImport()" class="mt-3 px-4 py-2 text-white rounded text-sm" style="background:#144600;">Preview Import Paste</button>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button onclick="closeImportModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Tutup</button>
        </div>
    </div>
</div>

<div id="import-validation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-6xl shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-800">Preview Validasi Import</h2>
            <button onclick="closeImportValidationModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>

        <div id="import-summary" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4"></div>

        <div id="missing-master-options" class="mb-4 p-3 border rounded-lg bg-yellow-50 hidden">
            <div class="text-sm font-semibold text-yellow-800 mb-2">Data master belum tersedia</div>
            <div id="missing-master-checkboxes" class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm"></div>
        </div>

        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Row</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">NIP</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Tanggal Masuk</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Tanggal Induction</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Nama</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Email</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Whatsapp</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Manager Fungsional</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Manager Operasional</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Perusahaan</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Divisi</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Departemen</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Jabatan</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Penempatan</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Golongan</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Status</th>
                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Error</th>
                    </tr>
                </thead>
                <tbody id="import-validation-body" class="divide-y divide-gray-200 text-gray-700"></tbody>
            </table>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button onclick="closeImportValidationModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Batal</button>
            <button onclick="confirmImportValidation()" class="px-4 py-2 text-white rounded-lg text-sm" style="background:#144600;">Simpan New Hire Valid</button>
        </div>
    </div>
</div>

<div id="credential-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-800">Kredensial Akun New Hire</h2>
            <button onclick="closeCredentialModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>
        <p class="text-sm text-gray-600 mb-3">Simpan kredensial ini sekarang. Password hanya ditampilkan sekali untuk kebutuhan prototype.</p>
        <textarea id="credential-content" class="w-full border rounded-lg px-3 py-2 text-sm h-64" readonly></textarea>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="copyCredentialText()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Copy</button>
            <button onclick="closeCredentialModal()" class="px-4 py-2 text-white rounded-lg text-sm" style="background-color:#144600;">Tutup</button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let allEmployees = [];
let filteredEmployees = [];
let currentLifecycle = 'active';
let master = { companies: [], divisions: [], departments: [], positions: [], placements: [], levels: [], employee_statuses: [] };
let managerOptions = [];
let importPreviewRows = [];
let importMissingMaster = { companies: [], divisions: [], departments: [], positions: [], placements: [], levels: [], employee_statuses: [] };
let currentDetailEmployeeId = null;

function renderDatalist(listId, list, valueBuilder = (item) => item.name) {
    const datalist = document.getElementById(listId);
    if (!datalist) return;
    datalist.innerHTML = '';
    list.forEach(item => {
        const option = document.createElement('option');
        option.value = valueBuilder(item);
        datalist.appendChild(option);
    });
}

function normalizeValue(value) {
    return (value || '').toString().trim().toLowerCase();
}

function normalizeWhatsappKey(value) {
    return (value || '').toString().replace(/\D+/g, '');
}

function normalizeEmployeeStatusInput(value) {
    const normalized = (value || '').toString().trim();
    if (!normalized) return 'PKWTT';

    const key = normalizeValue(normalized).replace(/[^a-z]/g, '');
    const map = {
        PKWTT: ['pkwtt', 'tetap', 'permanent', 'active', 'aktif'],
        PKWT: ['pkwt', 'kontrak', 'contract', 'inactive', 'nonactive', 'nonaktif', 'resigned', 'resign', 'terminated', 'terminate', 'phk', 'leave', 'cuti'],
        OS: ['os', 'outsourcing', 'outsource'],
    };

    for (const [target, aliases] of Object.entries(map)) {
        if (aliases.some(alias => alias.replace(/[^a-z]/g, '') === key)) {
            return target;
        }
    }

    return normalized.toUpperCase();
}

function renderStatusFilterOptions() {
    const select = document.getElementById('f-status');
    if (!select) return;

    const currentValue = select.value;
    const statusRows = (master.employee_statuses || []).length
        ? master.employee_statuses
        : [{ name: 'PKWTT' }, { name: 'PKWT' }, { name: 'OS' }];
    const options = statusRows.map(item => `<option value="${escapeHtml(item.name)}">${escapeHtml(item.name)}</option>`).join('');
    select.innerHTML = `<option value="">Semua Status Pegawai</option>${options}`;
    if (statusRows.some(item => item.name === currentValue)) {
        select.value = currentValue;
    }
}

function resolveMasterIdByName(options, inputValue) {
    const needle = normalizeValue(inputValue);
    if (!needle) return null;
    const found = options.find(item => normalizeValue(item.name) === needle);
    return found ? found.id : null;
}

function resolveManagerId(inputValue) {
    const needle = normalizeValue(inputValue);
    if (!needle) return null;
    const found = managerOptions.find(item => {
        return String(item.id) === needle
            || normalizeValue(item.name) === needle
            || normalizeValue(item.email) === needle;
    });
    return found ? found.id : null;
}

function resolveManagerLabelById(id) {
    if (!id) return '';
    const found = managerOptions.find(item => String(item.id) === String(id));
    return found ? (found.label || `${found.name} (${found.email})`) : '';
}

async function loadMasterOptions() {
    const [companies, divisions, departments, positions, placements, levels, employeeStatuses, managers] = await Promise.all([
        apiGet('/api/master/companies'), apiGet('/api/master/divisions'), apiGet('/api/master/departments'),
        apiGet('/api/master/positions'), apiGet('/api/master/placements'), apiGet('/api/master/levels'),
        apiGet('/api/master/employee_statuses'),
        apiGet('/api/employees/manager-options')
    ]);
    master.companies = companies.data || [];
    master.divisions = divisions.data || [];
    master.departments = departments.data || [];
    master.positions = positions.data || [];
    master.placements = placements.data || [];
    master.levels = levels.data || [];
    master.employee_statuses = employeeStatuses.data || [];
    managerOptions = managers.data || [];

    renderDatalist('company-options-list', master.companies, i => i.name);
    renderDatalist('division-options-list', master.divisions, i => i.name);
    renderDatalist('department-options-list', master.departments, i => i.name);
    renderDatalist('position-options-list', master.positions, i => i.name);
    renderDatalist('placement-options-list', master.placements, i => i.name);
    renderDatalist('level-options-list', master.levels, i => i.name);
    renderDatalist('employee-status-options', master.employee_statuses, i => i.name);
    renderDatalist('manager-options-list', managerOptions, i => i.label || `${i.name} (${i.email})`);
    renderStatusFilterOptions();
}

function updateLifecycleButtons() {
    const activeBtn = document.getElementById('btn-lifecycle-active');
    const historyBtn = document.getElementById('btn-lifecycle-history');
    if (!activeBtn || !historyBtn) return;

    if (currentLifecycle === 'active') {
        activeBtn.className = 'px-3 py-2 rounded-lg text-sm text-white';
        activeBtn.style.backgroundColor = '#144600';
        historyBtn.className = 'px-3 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50';
    } else {
        historyBtn.className = 'px-3 py-2 rounded-lg text-sm text-white';
        historyBtn.style.backgroundColor = '#144600';
        activeBtn.className = 'px-3 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50';
    }
}

function setLifecycleTab(tab) {
    currentLifecycle = tab === 'history' ? 'history' : 'active';
    updateLifecycleButtons();
    loadEmployees();
}

function getEmploymentStateLabel(state) {
    const key = normalizeValue(state);
    if (!key || key === 'active') return 'Active';
    if (key === 'resigned') return 'Mengundurkan Diri';
    if (key === 'terminated') return 'Dikeluarkan';
    if (key === 'graduated') return 'Lulus';
    return state;
}

async function loadEmployees() {
    document.getElementById('employee-body').innerHTML = '<tr><td colspan="23" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
    const res = await apiGet(`/api/employees?lifecycle=${encodeURIComponent(currentLifecycle)}`);
    allEmployees = res.data || [];
    applyFilters();
}

function applyFilters() {
    const search = (document.getElementById('f-search').value || '').toLowerCase();
    const status = document.getElementById('f-status').value;
    const phase = document.getElementById('f-phase').value;

    filteredEmployees = allEmployees.filter(row => {
        const textMatch = !search || [row.employee_number, row.name, row.email].join(' ').toLowerCase().includes(search);
        const statusMatch = !status || row.employee_status === status;
        const phaseMatch = !phase || row.phase === phase;
        return textMatch && statusMatch && phaseMatch;
    });

    renderTable();
}

function renderTable() {
    const tbody = document.getElementById('employee-body');
    if (!filteredEmployees.length) {
        const emptyText = currentLifecycle === 'history' ? 'Belum ada data history new hire' : 'Belum ada data new hire active';
        tbody.innerHTML = `<tr><td colspan="23" class="text-center py-8 text-gray-400">${emptyText}</td></tr>`;
        return;
    }

    tbody.innerHTML = filteredEmployees.map(row => `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2">${row.code ?? '-'}</td>
            <td class="px-4 py-2">${row.employee_number ?? '-'}</td>
            <td class="px-4 py-2 font-medium">
                <div class="inline-flex items-center gap-2">
                    <span>${row.name_display ?? row.name ?? '-'}</span>
                    <button onclick="openDetailModal(${row.id})" class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700" title="Lihat detail ${row.name_display ?? row.name ?? 'New Hire'}">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                    </button>
                </div>
            </td>
            <td class="px-4 py-2">${row.date_joined ?? '-'}</td>
            <td class="px-4 py-2">${row.induction_date ?? '-'}</td>
            <td class="px-4 py-2">${row.email ?? '-'}</td>
            <td class="px-4 py-2">${row.whatsapp ?? '-'}</td>
            <td class="px-4 py-2">${row.vnb_period_start ?? '-'}</td>
            <td class="px-4 py-2">${row.vnb_period_end ?? '-'}</td>
            <td class="px-4 py-2">${row.career_stage ?? '-'}</td>
            <td class="px-4 py-2">${row.phase ?? '-'}</td>
            <td class="px-4 py-2 font-medium">${row.progress ?? 0}%</td>
            <td class="px-4 py-2">${escapeHtml(normalizeDisplayValue(row.manager_functional))}</td>
            <td class="px-4 py-2">${escapeHtml(normalizeDisplayValue(row.manager_operational))}</td>
            <td class="px-4 py-2">${row.company ?? '-'}</td>
            <td class="px-4 py-2">${row.division ?? '-'}</td>
            <td class="px-4 py-2">${row.department ?? '-'}</td>
            <td class="px-4 py-2">${row.position ?? '-'}</td>
            <td class="px-4 py-2">${row.placement ?? '-'}</td>
            <td class="px-4 py-2">${row.level ?? '-'}</td>
            <td class="px-4 py-2">${row.employee_status ?? '-'}</td>
            <td class="px-4 py-2">${getEmploymentStateLabel(row.employment_state)}</td>
            <td class="px-4 py-2 text-right space-x-2 whitespace-nowrap">
                <button onclick="openEmployeeModal(${row.id})" class="text-sm" style="color:#144600;"><i class="fas fa-edit"></i></button>
                ${normalizeValue(row.employment_state) === 'active' ? `<button onclick="mutateEmployeeLifecycle(${row.id})" class="text-sm text-orange-600" title="Mutasi Status"><i class="fas fa-random"></i></button>` : ''}
                <button onclick="deleteEmployee(${row.id}, '${(row.name || '').replace(/'/g, "\\'")}')" class="text-sm text-red-500"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

async function openDetailModal(id) {
    const fallbackRow = allEmployees.find(x => x.id === id);
    if (!fallbackRow) return;

    currentDetailEmployeeId = id;
    const body = document.getElementById('detail-body');
    body.innerHTML = '<div class="text-sm text-gray-500 py-2">Memuat detail...</div>';

    let row = fallbackRow;
    let credential = null;

    const detailRes = await apiGet(`/api/employees/${id}`);
    if (detailRes && detailRes.success === true && detailRes.data) {
        row = detailRes.data;
        credential = detailRes.data.account_credential_preview || null;
    }

    const managerFunctionalLabel = normalizeDisplayValue(row.manager_functional, null)
        || row.managerFunctional?.name
        || row.managerFunctional?.email
        || '-';
    const managerOperationalLabel = normalizeDisplayValue(row.manager_operational, null)
        || row.managerOperational?.name
        || row.managerOperational?.email
        || '-';
    const divisionLabel = row.division?.name || row.division || '-';
    const departmentLabel = row.department?.name || row.department || '-';
    const positionLabel = row.position?.name || row.position || '-';

    const fields = [
        ['NIP', row.employee_number], ['Nama', row.name_display || row.name], ['Tanggal Masuk', row.date_joined],
        ['Username Login', credential?.username || row.employee_number],
        ['Password Sementara', credential?.temporary_password || '-'],
        ['Waktu Generate Password', credential?.temporary_password_generated_at || '-'],
        ['Tanggal Induction', row.induction_date], ['Email', row.email], ['Whatsapp', row.whatsapp], ['Periode Awal', row.vnb_period_start],
        ['Periode Akhir', row.vnb_period_end], ['Career Stage', row.career_stage], ['Fase', row.phase], ['Progress', `${row.progress ?? 0}%`],
        ['Manager Fungsional', managerFunctionalLabel], ['Manager Operasional', managerOperationalLabel], ['Perusahaan', row.company],
        ['Divisi', divisionLabel], ['Departemen', departmentLabel], ['Jabatan', positionLabel], ['Penempatan', row.placement],
        ['Golongan', row.level], ['Status Pegawai', row.employee_status], ['Status Lifecycle', getEmploymentStateLabel(row.employment_state)],
        ['Catatan Perubahan Status', row.status_change_reason], ['Waktu Perubahan Status', row.status_changed_at]
    ];

    body.innerHTML = fields.map(([k, v]) => `
        <div class="grid grid-cols-3 gap-2 text-sm py-1">
            <div class="text-gray-500">${escapeHtml(k)}</div>
            <div class="col-span-2 font-medium text-gray-800">${escapeHtml(v || '-')}</div>
        </div>
    `).join('');

    document.getElementById('detail-modal').classList.remove('hidden');
}

async function resetDetailCredential() {
    if (!currentDetailEmployeeId) return;

    const confirmed = confirm('Generate ulang password sementara untuk New Hire ini?');
    if (!confirmed) return;

    const res = await apiPost(`/api/employees/${currentDetailEmployeeId}/reset-credential`, {}, 'POST');
    if (res && res.success === true) {
        const data = res.data || {};
        showAlert(res.message || 'Password sementara berhasil di-generate ulang');

        if (data.username && data.temporary_password) {
            openCredentialModal([
                {
                    name: allEmployees.find(item => item.id === currentDetailEmployeeId)?.name || '-',
                    username: data.username,
                    password: data.temporary_password,
                    email: data.email || '-',
                    delivery: data.delivery || { email_sent: false, popup_available: true },
                }
            ]);
        }

        await openDetailModal(currentDetailEmployeeId);
    } else {
        showAlert(res?.message || res?.error || 'Gagal generate ulang password', 'error');
    }
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
    currentDetailEmployeeId = null;
}

function openEmployeeModal(id = null) {
    document.getElementById('employee-modal-title').textContent = id ? 'Edit New Hire' : 'Add New Hire';
    document.getElementById('e-id').value = id || '';
    const data = id ? allEmployees.find(x => x.id === id) : null;

    document.getElementById('e-employee-number').value = data?.employee_number || '';
    document.getElementById('e-date-joined').value = data?.date_joined || '';
    document.getElementById('e-induction-date').value = data?.induction_date || '';
    document.getElementById('e-name').value = data?.name || '';
    document.getElementById('e-email').value = data?.email || '';
    document.getElementById('e-whatsapp').value = data?.whatsapp || '';
    document.getElementById('e-manager-functional').value = resolveManagerLabelById(data?.manager_functional_id);
    document.getElementById('e-manager-operational').value = resolveManagerLabelById(data?.manager_operational_id);
    document.getElementById('e-company').value = data?.company || '';
    document.getElementById('e-division').value = data?.division || '';
    document.getElementById('e-department').value = data?.department || '';
    document.getElementById('e-position').value = data?.position || '';
    document.getElementById('e-placement').value = data?.placement || '';
    document.getElementById('e-level').value = data?.level || '';
    document.getElementById('e-employee-status').value = data
        ? normalizeEmployeeStatusInput(data?.employee_status)
        : '';

    document.getElementById('employee-modal').classList.remove('hidden');
}

function closeEmployeeModal() {
    document.getElementById('employee-modal').classList.add('hidden');
}

document.getElementById('employee-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const id = document.getElementById('e-id').value;

    const managerFunctionalInput = document.getElementById('e-manager-functional').value;
    const managerOperationalInput = document.getElementById('e-manager-operational').value;
    const divisionInput = document.getElementById('e-division').value;
    const departmentInput = document.getElementById('e-department').value;
    const positionInput = document.getElementById('e-position').value;

    const managerFunctionalId = resolveManagerId(managerFunctionalInput);
    const managerOperationalId = resolveManagerId(managerOperationalInput);
    const divisionId = resolveMasterIdByName(master.divisions, divisionInput);
    const departmentId = resolveMasterIdByName(master.departments, departmentInput);
    const positionId = resolveMasterIdByName(master.positions, positionInput);

    if (managerFunctionalInput && !managerFunctionalId) {
        showAlert('Manager Fungsional tidak ditemukan. Silakan pilih dari rekomendasi.', 'error');
        return;
    }

    if (managerOperationalInput && !managerOperationalId) {
        showAlert('Manager Operasional tidak ditemukan. Silakan pilih dari rekomendasi.', 'error');
        return;
    }

    if (!divisionId || !departmentId || !positionId) {
        showAlert('Divisi/Departemen/Jabatan harus dipilih dari rekomendasi yang tersedia.', 'error');
        return;
    }

    const employeeStatus = normalizeEmployeeStatusInput(document.getElementById('e-employee-status').value);
    const allowedStatuses = ((master.employee_statuses || []).length
        ? master.employee_statuses
        : [{ name: 'PKWTT' }, { name: 'PKWT' }, { name: 'OS' }])
        .map(item => normalizeEmployeeStatusInput(item.name));
    if (!allowedStatuses.includes(employeeStatus)) {
        showAlert('Status Pegawai tidak valid. Gunakan rekomendasi yang tersedia.', 'error');
        return;
    }

    const payload = {
        employee_number: document.getElementById('e-employee-number').value,
        date_joined: document.getElementById('e-date-joined').value,
        induction_date: document.getElementById('e-induction-date').value,
        name: document.getElementById('e-name').value,
        email: document.getElementById('e-email').value,
        whatsapp: document.getElementById('e-whatsapp').value || null,
        manager_functional_id: managerFunctionalId,
        manager_operational_id: managerOperationalId,
        company: document.getElementById('e-company').value,
        division_id: divisionId,
        department_id: departmentId,
        position_id: positionId,
        placement: document.getElementById('e-placement').value,
        level: document.getElementById('e-level').value,
        employee_status: employeeStatus,
    };

    const res = await apiPost(id ? `/api/employees/${id}` : '/api/employees', payload, id ? 'PUT' : 'POST');
    if (res && res.success === true) {
        showAlert(res.message || 'Data berhasil disimpan');
        const createdCredential = res?.data?.account_credential;
        if (!id && createdCredential) {
            openCredentialModal([createdCredential]);
        }
        closeEmployeeModal();
        loadEmployees();
    } else {
        showAlert(res?.message || res?.error || 'Gagal menyimpan data', 'error');
    }
});

async function mutateEmployeeLifecycle(id) {
    const row = allEmployees.find(item => item.id === id);
    if (!row) return;

    const choice = (prompt('Mutasi status New Hire:\n1 = Mengundurkan Diri\n2 = Dikeluarkan\n3 = Lulus\n\nMasukkan angka (1/2/3):', '') || '').trim();
    const stateMap = { '1': 'resigned', '2': 'terminated', '3': 'graduated' };
    const selectedState = stateMap[choice];

    if (!selectedState) {
        showAlert('Pilihan status tidak valid', 'error');
        return;
    }

    const reason = prompt('Catatan mutasi status (opsional):', '') || '';
    const confirmed = confirm(`Ubah status ${row.name || '-'} menjadi ${getEmploymentStateLabel(selectedState)}?`);
    if (!confirmed) return;

    const res = await apiPost(`/api/employees/${id}/lifecycle`, {
        employment_state: selectedState,
        status_change_reason: reason,
    }, 'POST');

    if (res && res.success === true) {
        showAlert(res.message || 'Status lifecycle berhasil diperbarui');
        loadEmployees();
    } else {
        showAlert(res?.message || res?.error || 'Gagal mutasi status', 'error');
    }
}

async function deleteEmployee(id, name) {
    if (!confirm(`Hapus data New Hire ${name}?`)) return;
    const res = await apiPost(`/api/employees/${id}`, {}, 'DELETE');
    if (res && res.success === true) {
        showAlert(res.message || 'Data New Hire berhasil dihapus');
        loadEmployees();
    } else {
        showAlert(res?.message || res?.error || 'Gagal menghapus data', 'error');
    }
}

function openImportModal() {
    document.getElementById('import-modal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
}

function closeImportValidationModal() {
    document.getElementById('import-validation-modal').classList.add('hidden');
}

function buildCredentialText(credentials) {
    const rows = Array.isArray(credentials) ? credentials : [];
    if (!rows.length) return '';

    return rows.map((item, index) => {
        const emailSent = item?.delivery?.email_sent ? 'Terkirim' : 'Pending / tidak tersedia';
        return [
            `Akun ${index + 1}`,
            `Nama      : ${item.name || '-'}`,
            `Username  : ${item.username || '-'}`,
            `Password  : ${item.password || '-'}`,
            `Email     : ${item.email || '-'}`,
            `Status Email: ${emailSent}`,
        ].join('\n');
    }).join('\n\n-----------------------------\n\n');
}

function openCredentialModal(credentials) {
    const text = buildCredentialText(credentials);
    if (!text) return;

    const area = document.getElementById('credential-content');
    area.value = text;
    document.getElementById('credential-modal').classList.remove('hidden');
}

function closeCredentialModal() {
    document.getElementById('credential-modal').classList.add('hidden');
}

async function copyCredentialText() {
    const area = document.getElementById('credential-content');
    const text = area?.value || '';
    if (!text) return;

    try {
        await navigator.clipboard.writeText(text);
        showAlert('Kredensial berhasil disalin');
    } catch (error) {
        area.select();
        document.execCommand('copy');
        showAlert('Kredensial berhasil disalin');
    }
}

function escapeHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function normalizeDisplayValue(value, fallback = '-') {
    if (value === null || value === undefined || value === '') return fallback;
    if (typeof value === 'object') {
        return value.name || value.email || value.label || fallback;
    }
    return value;
}

function getMasterListByCategory(category) {
    if (category === 'companies') return master.companies || [];
    if (category === 'divisions') return master.divisions || [];
    if (category === 'departments') return master.departments || [];
    if (category === 'positions') return master.positions || [];
    if (category === 'placements') return master.placements || [];
    if (category === 'levels') return master.levels || [];
    if (category === 'employee_statuses') {
        return (master.employee_statuses || []).length
            ? master.employee_statuses
            : [{ name: 'PKWTT', code: 1 }, { name: 'PKWT', code: 2 }, { name: 'OS', code: 3 }];
    }
    return [];
}

function resolveMasterValueByInput(category, value) {
    const raw = (value || '').toString().trim();
    if (!raw) return '';

    const list = getMasterListByCategory(category);
    if (!list.length) return raw;

    if (/^\d+$/.test(raw)) {
        const num = Number(raw);
        const byCode = list.find(item => Number(item.code) === num);
        if (byCode?.name) return byCode.name;
        const byIndex = list[num - 1];
        if (byIndex?.name) return byIndex.name;
    }

    const byName = list.find(item => normalizeValue(item.name) === normalizeValue(raw));
    return byName?.name || raw;
}

function normalizePreviewRow(row) {
    const normalized = { ...row };
    normalized.company = resolveMasterValueByInput('companies', normalized.company);
    normalized.division = resolveMasterValueByInput('divisions', normalized.division);
    normalized.department = resolveMasterValueByInput('departments', normalized.department);
    normalized.position = resolveMasterValueByInput('positions', normalized.position);
    normalized.placement = resolveMasterValueByInput('placements', normalized.placement);
    normalized.level = resolveMasterValueByInput('levels', normalized.level);
    normalized.employee_status = resolveMasterValueByInput('employee_statuses', normalizeEmployeeStatusInput(normalized.employee_status));
    return normalized;
}

function isValidImportDate(value) {
    const raw = (value || '').toString().trim();
    if (!raw) return false;

    if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
        const date = new Date(`${raw}T00:00:00`);
        return !Number.isNaN(date.getTime());
    }

    const dmy = raw.match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/);
    if (dmy) {
        const day = Number(dmy[1]);
        const month = Number(dmy[2]);
        const year = Number(dmy[3]);
        const date = new Date(year, month - 1, day);
        return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day;
    }

    return !Number.isNaN(Date.parse(raw.replace(/\./g, '/')));
}

function getPreviewFieldConfigs() {
    return {
        employee_number: { label: 'NIP', required: true },
        date_joined: { label: 'Tanggal Masuk', required: true },
        induction_date: { label: 'Tanggal Induction', required: true },
        name: { label: 'Nama', required: true },
        email: { label: 'Email', required: true },
        whatsapp: { label: 'Whatsapp', required: true },
        manager_functional_input: { label: 'Manager Fungsional', required: true },
        manager_operational_input: { label: 'Manager Operasional', required: false },
        company: { label: 'Perusahaan', required: true, category: 'companies', datalist: 'company-options-list' },
        division: { label: 'Divisi', required: true, category: 'divisions', datalist: 'division-options-list' },
        department: { label: 'Departemen', required: true, category: 'departments', datalist: 'department-options-list' },
        position: { label: 'Jabatan', required: true, category: 'positions', datalist: 'position-options-list' },
        placement: { label: 'Penempatan', required: true, category: 'placements', datalist: 'placement-options-list' },
        level: { label: 'Golongan', required: true, category: 'levels', datalist: 'level-options-list' },
        employee_status: { label: 'Status Pegawai', required: true, category: 'employee_statuses', datalist: 'employee-status-options' },
    };
}

function validatePreviewRows(rows) {
    const errorsByRow = rows.map(() => []);
    const missingMaster = {
        companies: [],
        divisions: [],
        departments: [],
        positions: [],
        placements: [],
        levels: [],
        employee_statuses: [],
    };

    const existingEmails = new Set((allEmployees || []).map(item => normalizeValue(item.email)).filter(Boolean));
    const existingWhatsapps = new Set((allEmployees || []).map(item => normalizeWhatsappKey(item.whatsapp)).filter(Boolean));
    const existingEmployeeNumbers = new Set((allEmployees || []).map(item => normalizeValue(item.employee_number)).filter(Boolean));
    const existingNameDivision = new Set(
        (allEmployees || [])
            .map(item => `${normalizeValue(item.name)}|${normalizeValue(item.division)}`)
            .filter(item => item !== '|' && !item.startsWith('|') && !item.endsWith('|'))
    );

    const emailCounts = {};
    const whatsappCounts = {};
    const employeeNumberCounts = {};
    const nameDivisionCounts = {};

    rows.forEach(row => {
        const emailKey = normalizeValue(row.email);
        if (emailKey) {
            emailCounts[emailKey] = (emailCounts[emailKey] || 0) + 1;
        }

        const whatsappKey = normalizeWhatsappKey(row.whatsapp);
        if (whatsappKey) {
            whatsappCounts[whatsappKey] = (whatsappCounts[whatsappKey] || 0) + 1;
        }

        const employeeNumberKey = normalizeValue(row.employee_number);
        if (employeeNumberKey) {
            employeeNumberCounts[employeeNumberKey] = (employeeNumberCounts[employeeNumberKey] || 0) + 1;
        }

        const nameDivisionKey = `${normalizeValue(row.name)}|${normalizeValue(row.division)}`;
        if (nameDivisionKey !== '|' && !nameDivisionKey.startsWith('|') && !nameDivisionKey.endsWith('|')) {
            nameDivisionCounts[nameDivisionKey] = (nameDivisionCounts[nameDivisionKey] || 0) + 1;
        }
    });

    const fieldConfigs = getPreviewFieldConfigs();

    rows.forEach((rawRow, rowIndex) => {
        const row = normalizePreviewRow(rawRow);
        rows[rowIndex] = row;
        const rowErrors = [];

        Object.entries(fieldConfigs).forEach(([field, config]) => {
            const value = (row[field] || '').toString().trim();
            if (config.required && !value) {
                rowErrors.push({ field, message: `${config.label} wajib diisi`, type: 'validation' });
            }
        });

        if (row.date_joined && !isValidImportDate(row.date_joined)) {
            rowErrors.push({ field: 'date_joined', message: 'Format Tanggal Masuk tidak valid', type: 'validation' });
        }

        if (row.induction_date && !isValidImportDate(row.induction_date)) {
            rowErrors.push({ field: 'induction_date', message: 'Format Tanggal Induction tidak valid', type: 'validation' });
        }

        if (row.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(row.email)) {
            rowErrors.push({ field: 'email', message: 'Format email tidak valid', type: 'validation' });
        }

        const emailKey = normalizeValue(row.email);
        if (emailKey) {
            if (existingEmails.has(emailKey)) {
                rowErrors.push({ field: 'email', message: 'Email sudah terdaftar di sistem', type: 'validation' });
            }
            if ((emailCounts[emailKey] || 0) > 1) {
                rowErrors.push({ field: 'email', message: 'Email duplikat pada file import', type: 'validation' });
            }
        }

        const whatsappKey = normalizeWhatsappKey(row.whatsapp);
        if (whatsappKey) {
            if (existingWhatsapps.has(whatsappKey)) {
                rowErrors.push({ field: 'whatsapp', message: 'Nomor Whatsapp sudah terdaftar di sistem', type: 'validation' });
            }
            if ((whatsappCounts[whatsappKey] || 0) > 1) {
                rowErrors.push({ field: 'whatsapp', message: 'Nomor Whatsapp duplikat pada file import', type: 'validation' });
            }
        }

        const employeeNumberKey = normalizeValue(row.employee_number);
        if (employeeNumberKey) {
            if (existingEmployeeNumbers.has(employeeNumberKey)) {
                rowErrors.push({ field: 'employee_number', message: 'NIP sudah terdaftar di sistem', type: 'validation' });
            }
            if ((employeeNumberCounts[employeeNumberKey] || 0) > 1) {
                rowErrors.push({ field: 'employee_number', message: 'NIP duplikat pada file import', type: 'validation' });
            }
        }

        if (row.manager_functional_input && !resolveManagerId(row.manager_functional_input)) {
            rowErrors.push({ field: 'manager_functional_input', message: 'Manager Fungsional tidak ditemukan', type: 'validation' });
        }

        if (row.manager_operational_input && !resolveManagerId(row.manager_operational_input)) {
            rowErrors.push({ field: 'manager_operational_input', message: 'Manager Operasional tidak ditemukan', type: 'validation' });
        }

        const nameDivisionKey = `${normalizeValue(row.name)}|${normalizeValue(row.division)}`;
        if (nameDivisionKey !== '|' && !nameDivisionKey.startsWith('|') && !nameDivisionKey.endsWith('|')) {
            if (existingNameDivision.has(nameDivisionKey)) {
                rowErrors.push({ field: 'name', message: `Nama sama persis sudah terdaftar pada Divisi ${row.division}`, type: 'validation' });
            }

            if ((nameDivisionCounts[nameDivisionKey] || 0) > 1) {
                rowErrors.push({ field: 'name', message: `Nama duplikat dalam file import pada Divisi ${row.division}`, type: 'validation' });
            }
        }

        Object.entries(fieldConfigs).forEach(([field, config]) => {
            if (!config.category) return;
            const value = (row[field] || '').toString().trim();
            if (!value) return;

            const list = getMasterListByCategory(config.category);
            const exists = list.some(item => normalizeValue(item.name) === normalizeValue(value));
            if (!exists) {
                rowErrors.push({
                    field,
                    message: 'Data master belum tersedia',
                    type: 'master_missing',
                    category: config.category,
                    value,
                });
                missingMaster[config.category].push(value);
            }
        });

        errorsByRow[rowIndex] = rowErrors;
    });

    Object.keys(missingMaster).forEach(category => {
        missingMaster[category] = Array.from(new Set(missingMaster[category].map(v => (v || '').toString().trim()).filter(Boolean)));
    });

    const invalidCount = errorsByRow.filter(errs => errs.length > 0).length;

    return {
        summary: {
            total: rows.length,
            valid: rows.length - invalidCount,
            invalid: invalidCount,
        },
        errorsByRow,
        missingMaster,
    };
}

function renderPreviewValidationRows() {
    const body = document.getElementById('import-validation-body');
    const fieldConfigs = getPreviewFieldConfigs();
    const orderedFields = [
        'employee_number', 'date_joined', 'induction_date', 'name', 'email', 'whatsapp',
        'manager_functional_input', 'manager_operational_input',
        'company', 'division', 'department', 'position', 'placement', 'level', 'employee_status'
    ];

    body.innerHTML = importPreviewRows.map((row, rowIndex) => {
        const cells = orderedFields.map(field => {
            const config = fieldConfigs[field];
            const listAttr = config.datalist ? `list="${config.datalist}"` : '';
            const placeholder = config.label;
            return `
                <td class="px-2 py-2 align-top min-w-[170px]">
                    <input
                        type="text"
                        ${listAttr}
                        value="${escapeHtml(row[field] || '')}"
                        placeholder="${escapeHtml(placeholder)}"
                        oninput="onPreviewCellInput(${rowIndex}, '${field}', this.value)"
                        onblur="onPreviewCellBlur(${rowIndex}, '${field}', this)"
                        class="w-full border rounded px-2 py-1 text-xs"
                    >
                </td>
            `;
        }).join('');

        return `
            <tr id="preview-row-${rowIndex}">
                <td class="px-3 py-2 align-top">${rowIndex + 1}</td>
                ${cells}
                <td id="preview-error-${rowIndex}" class="px-3 py-2 text-xs align-top min-w-[260px]"></td>
            </tr>
        `;
    }).join('');
}

function renderPreviewSummary(summary) {
    document.getElementById('import-summary').innerHTML = `
        <div class="rounded-lg border p-3 bg-gray-50"><div class="text-xs text-gray-500">Total Row</div><div class="text-xl font-bold text-gray-800">${summary.total}</div></div>
        <div class="rounded-lg border p-3 bg-green-50"><div class="text-xs text-green-600">Valid</div><div class="text-xl font-bold text-green-700">${summary.valid}</div></div>
        <div class="rounded-lg border p-3 bg-red-50"><div class="text-xs text-red-600">Error</div><div class="text-xl font-bold text-red-700">${summary.invalid}</div></div>
    `;
}

function renderMissingMasterOptions(missingMaster) {
    importMissingMaster = missingMaster;

    const categoryLabels = {
        companies: 'Perusahaan',
        divisions: 'Divisi',
        departments: 'Departemen',
        positions: 'Jabatan',
        placements: 'Penempatan',
        levels: 'Golongan',
        employee_statuses: 'Status Pegawai',
    };

    const missingContainer = document.getElementById('missing-master-options');
    const missingCheckboxes = document.getElementById('missing-master-checkboxes');
    const previous = collectAddMissingMasterOptions();
    const missingEntries = Object.entries(missingMaster).filter(([_, values]) => (values || []).length > 0);

    if (!missingEntries.length) {
        missingContainer.classList.add('hidden');
        missingCheckboxes.innerHTML = '';
        return;
    }

    missingContainer.classList.remove('hidden');
    missingCheckboxes.innerHTML = missingEntries.map(([key, values]) => {
        const list = values.map(v => `<span class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-xs mr-1 mb-1">${escapeHtml(v)}</span>`).join('');
        const checked = Object.prototype.hasOwnProperty.call(previous, key) ? previous[key] : true;
        return `
            <label class="block border rounded p-2 bg-white">
                <div class="flex items-center gap-2 mb-1">
                    <input type="checkbox" class="import-missing-master-checkbox" data-category="${key}" ${checked ? 'checked' : ''}>
                    <span class="font-medium">Tambah ke Master ${categoryLabels[key]}</span>
                </div>
                <div>${list}</div>
            </label>
        `;
    }).join('');
}

function applyPreviewValidationState(state) {
    renderPreviewSummary(state.summary);
    renderMissingMasterOptions(state.missingMaster);

    state.errorsByRow.forEach((errors, rowIndex) => {
        const rowEl = document.getElementById(`preview-row-${rowIndex}`);
        const errorEl = document.getElementById(`preview-error-${rowIndex}`);
        if (!rowEl || !errorEl) return;

        rowEl.className = errors.length ? 'bg-red-50' : '';
        errorEl.innerHTML = errors.length
            ? errors.map(err => `${escapeHtml(err.field)}: ${escapeHtml(err.message)}`).join('<br>')
            : '<span class="text-green-600">OK</span>';
    });
}

function recomputePreviewValidation() {
    const state = validatePreviewRows(importPreviewRows);
    applyPreviewValidationState(state);
}

function onPreviewCellInput(rowIndex, field, value) {
    if (!importPreviewRows[rowIndex]) return;
    importPreviewRows[rowIndex][field] = value;
    recomputePreviewValidation();
}

function onPreviewCellBlur(rowIndex, field, inputEl) {
    if (!importPreviewRows[rowIndex]) return;

    if (['company', 'division', 'department', 'position', 'placement', 'level'].includes(field)) {
        const category = `${field}${field.endsWith('s') ? 'es' : 's'}`;
        const map = {
            company: 'companies',
            division: 'divisions',
            department: 'departments',
            position: 'positions',
            placement: 'placements',
            level: 'levels',
        };
        importPreviewRows[rowIndex][field] = resolveMasterValueByInput(map[field] || category, importPreviewRows[rowIndex][field]);
    }

    if (field === 'employee_status') {
        importPreviewRows[rowIndex][field] = resolveMasterValueByInput('employee_statuses', normalizeEmployeeStatusInput(importPreviewRows[rowIndex][field]));
    }

    importPreviewRows[rowIndex][field] = (importPreviewRows[rowIndex][field] || '').toString().trim();
    if (inputEl) {
        inputEl.value = importPreviewRows[rowIndex][field];
    }

    recomputePreviewValidation();
}

function openImportValidationModal(preview) {
    importPreviewRows = (preview?.rows || []).map(row => normalizePreviewRow(row.data || {}));

    renderPreviewValidationRows();
    recomputePreviewValidation();

    document.getElementById('import-validation-modal').classList.remove('hidden');
}

function collectAddMissingMasterOptions() {
    const options = {
        companies: false,
        divisions: false,
        departments: false,
        positions: false,
        placements: false,
        levels: false,
        employee_statuses: false,
    };

    document.querySelectorAll('.import-missing-master-checkbox').forEach((checkbox) => {
        const category = checkbox.dataset.category;
        if (Object.prototype.hasOwnProperty.call(options, category)) {
            options[category] = checkbox.checked;
        }
    });

    return options;
}

async function confirmImportValidation() {
    if (!importPreviewRows.length) {
        showAlert('Belum ada data preview untuk disimpan', 'error');
        return;
    }

    const payload = {
        rows: importPreviewRows,
        add_missing_master: collectAddMissingMasterOptions(),
    };

    const res = await apiPost('/api/employees/import/confirm', payload, 'POST');
    if (res && res.success === true) {
        showAlert(`Import final: ${res.data.inserted} berhasil, ${res.data.failed} gagal`);
        const failedRows = Array.isArray(res?.data?.errors) ? res.data.errors : [];
        if (failedRows.length) {
            const failedText = failedRows
                .slice(0, 8)
                .map(item => `Row ${item.row}: ${item.message}`)
                .join('\n');
            const moreText = failedRows.length > 8 ? `\n...dan ${failedRows.length - 8} error lainnya` : '';
            showAlert(`Detail error import:\n${failedText}${moreText}`, 'error');
        }
        const createdCredentials = Array.isArray(res?.data?.credentials) ? res.data.credentials : [];
        if (createdCredentials.length) {
            openCredentialModal(createdCredentials);
        }
        closeImportValidationModal();
        closeImportModal();
        loadMasterOptions();
        loadEmployees();
    } else {
        showAlert(res?.message || res?.error || 'Konfirmasi import gagal', 'error');
    }
}

async function submitFileImport() {
    const fileInput = document.getElementById('import-file');
    if (!fileInput.files.length) {
        showAlert('Pilih file terlebih dahulu', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);

    const r = await fetch('/api/employees/import/file', {
        method: 'POST',
        credentials: 'include',
        headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' },
        body: formData,
    });

    const res = await r.json();
    if (res && res.success === true) {
        openImportValidationModal(res.data);
    } else {
        showAlert(res?.message || 'Import file gagal', 'error');
    }
}

async function submitPasteImport() {
    const raw = document.getElementById('import-paste').value || '';
    const lines = raw.split(/\r?\n/).filter(Boolean);
    if (!lines.length) {
        showAlert('Paste data terlebih dahulu', 'error');
        return;
    }

    const isKnownCompany = value => {
        const needle = normalizeValue(value);
        if (!needle) return false;
        return (master.companies || []).some(item => normalizeValue(item.name) === needle);
    };

    const isKnownDivision = value => {
        const needle = normalizeValue(value);
        if (!needle) return false;
        return (master.divisions || []).some(item => normalizeValue(item.name) === needle);
    };

    const isKnownDepartment = value => {
        const needle = normalizeValue(value);
        if (!needle) return false;
        return (master.departments || []).some(item => normalizeValue(item.name) === needle);
    };

    const rows = lines.map(line => {
        const c = line.split('\t').map(item => (item || '').trim());
        const hasNipColumn = c.length >= 15 || !isValidImportDate(c[0] || '');

        if (hasNipColumn && c.length === 14 && isKnownCompany(c[7])) {
            c.splice(7, 0, '');
        }

        if (!hasNipColumn && c.length === 13 && isKnownCompany(c[6])) {
            c.splice(6, 0, '');
        }

        const base = hasNipColumn ? 1 : 0;
        const row = {
            employee_number: hasNipColumn ? (c[0] || '') : '',
            date_joined: c[base + 0] || '',
            induction_date: c[base + 1] || '',
            name: c[base + 2] || '',
            email: c[base + 3] || '',
            whatsapp: c[base + 4] || '',
            manager_functional_input: c[base + 5] || '',
            manager_operational_input: c[base + 6] || '',
            company: c[base + 7] || '',
            division: c[base + 8] || '',
            department: c[base + 9] || '',
            position: c[base + 10] || '',
            placement: c[base + 11] || '',
            level: c[base + 12] || '',
            employee_status: c[base + 13] || '',
        };

        const companyShiftedToManagerOperational =
            !!row.manager_operational_input
            && isKnownCompany(row.manager_operational_input)
            && !isKnownCompany(row.company)
            && (isKnownDivision(row.company) || isKnownDepartment(row.division));

        if (companyShiftedToManagerOperational) {
            row.employee_status = row.level;
            row.level = row.placement;
            row.placement = row.position;
            row.position = row.department;
            row.department = row.division;
            row.division = row.company;
            row.company = row.manager_operational_input;
            row.manager_operational_input = '';
        }

        return row;
    });

    const res = await apiPost('/api/employees/import/paste', { rows }, 'POST');
    if (res && res.success === true) {
        openImportValidationModal(res.data);
    } else {
        showAlert(res?.message || res?.error || 'Import paste gagal', 'error');
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    updateLifecycleButtons();
    await loadMasterOptions();
    await loadEmployees();
});
</script>
<?php $__env->stopPush(); ?>

<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Detail New Hire</h2>
            <div class="flex items-center gap-2">
                <button id="detail-reset-credential-btn" onclick="resetDetailCredential()" class="px-3 py-1 border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">Generate Ulang Password</button>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div id="detail-body" class="divide-y divide-gray-100"></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/employees/index.blade.php ENDPATH**/ ?>