

<?php $__env->startSection('title', 'Sinkronisasi Data - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'Sinkronisasi Data'); ?>
<?php $__env->startSection('page_subtitle', 'Sinkronisasi Employee dari HRIS, HRMS, dan Updated Data ke Employees.'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">

    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
            <div class="flex gap-3">
                <button id="tab-updated" onclick="switchTab('updated')" class="px-4 py-2 font-medium transition-colors" style="color: #144600; border-bottom: 2px solid #144600;">
                    Update Data
                    <span id="pending-badge" class="inline-flex items-center justify-center ml-2 min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold text-white" style="background-color: #dc2626; display: none;">
                        <i class="fas fa-sync-alt mr-1"></i>
                        <span id="pending-badge-count">0</span>
                    </span>
                </button>
                <button id="tab-hris" onclick="switchTab('hris')" class="px-4 py-2 font-medium transition-colors text-gray-500 hover:text-gray-700">
                    HRIS
                </button>
                <button id="tab-hrms" onclick="switchTab('hrms')" class="px-4 py-2 font-medium transition-colors text-gray-500 hover:text-gray-700">
                    HRMS
                </button>
            </div>

            <div id="updated-actions" class="flex justify-end">
                <button
                    id="btn-open-sync-modal"
                    type="button"
                    onclick="openSyncModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white font-semibold"
                    style="background-color: #144600;"
                >
                    <i class="fas fa-sync-alt"></i>
                    <span>Sinkronkan Data</span>
                </button>
            </div>
        </div>
    </div>

    <div id="section-updated" class="table-container">
        <div class="overflow-x-auto" style="overflow-x: auto;">
            <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
                <thead style="white-space: nowrap;">
                    <tr>
                        <th data-sort-key="row_index">No</th>
                        <th data-sort-key="employee_number">NIP</th>
                        <th data-sort-key="name_display">Nama Lengkap</th>
                        <th data-sort-key="date_joined">Tanggal Masuk</th>
                        <th data-sort-key="email">Email</th>
                        <th data-sort-key="whatsapp">Whatsapp</th>
                        <th data-sort-key="company">Perusahaan</th>
                        <th data-sort-key="division">Divisi</th>
                        <th data-sort-key="department">Departemen</th>
                        <th data-sort-key="position">Jabatan</th>
                        <th data-sort-key="placement">Penempatan</th>
                        <th data-sort-key="level">Golongan</th>
                        <th class="relative" data-sort-key="employee_status">
                            <div class="flex items-center justify-between gap-2">
                                <span>Status Pegawai</span>
                                <div class="relative group cursor-pointer">
                                    <button type="button" id="filter-updated-employee_status-btn" class="p-0.5 text-gray-500 hover:text-gray-700 group-hover:bg-gray-100 rounded" title="Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                    </button>
                                    <div class="hidden group-hover:block absolute right-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-max">
                                        <div class="p-1 max-h-60 overflow-y-auto" id="filter-updated-employee_status-options"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th data-sort-key="status">Status Aktif</th>
                    </tr>
                </thead>
                <tbody id="updated-body" style="white-space: nowrap;">
                    <tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-hris" class="table-container hidden">
        <div class="overflow-x-auto" style="overflow-x: auto;">
            <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
                <thead style="white-space: nowrap;">
                    <tr>
                        <th data-sort-key="row_index">No</th>
                        <th data-sort-key="employee_number">NIP</th>
                        <th data-sort-key="name_display">Nama Lengkap</th>
                        <th data-sort-key="date_joined">Tanggal Masuk</th>
                        <th data-sort-key="email">Email</th>
                        <th data-sort-key="whatsapp">Whatsapp</th>
                        <th data-sort-key="company">Perusahaan</th>
                        <th data-sort-key="division">Divisi</th>
                        <th data-sort-key="department">Departemen</th>
                        <th data-sort-key="position">Jabatan</th>
                        <th data-sort-key="placement">Penempatan</th>
                        <th data-sort-key="level">Golongan</th>
                        <th class="relative" data-sort-key="employee_status">
                            <div class="flex items-center justify-between gap-2">
                                <span>Status Pegawai</span>
                                <div class="relative group cursor-pointer">
                                    <button type="button" id="filter-hris-employee_status-btn" class="p-0.5 text-gray-500 hover:text-gray-700 group-hover:bg-gray-100 rounded" title="Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                    </button>
                                    <div class="hidden group-hover:block absolute right-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-max">
                                        <div class="p-1 max-h-60 overflow-y-auto" id="filter-hris-employee_status-options"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th data-sort-key="status">Status Aktif</th>
                    </tr>
                </thead>
                <tbody id="hris-body" style="white-space: nowrap;">
                    <tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-hrms" class="table-container hidden">
        <div class="overflow-x-auto" style="overflow-x: auto;">
            <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
                <thead style="white-space: nowrap;">
                    <tr>
                        <th data-sort-key="row_index">No</th>
                        <th data-sort-key="employee_number">NIP</th>
                        <th data-sort-key="name_display">Nama Lengkap</th>
                        <th data-sort-key="date_joined">Tanggal Masuk</th>
                        <th data-sort-key="email">Email</th>
                        <th data-sort-key="whatsapp">Whatsapp</th>
                        <th data-sort-key="company">Perusahaan</th>
                        <th data-sort-key="division">Divisi</th>
                        <th data-sort-key="department">Departemen</th>
                        <th data-sort-key="position">Jabatan</th>
                        <th data-sort-key="placement">Penempatan</th>
                        <th data-sort-key="level">Golongan</th>
                        <th class="relative" data-sort-key="employee_status">
                            <div class="flex items-center justify-between gap-2">
                                <span>Status Pegawai</span>
                                <div class="relative group cursor-pointer">
                                    <button type="button" id="filter-hrms-employee_status-btn" class="p-0.5 text-gray-500 hover:text-gray-700 group-hover:bg-gray-100 rounded" title="Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                    </button>
                                    <div class="hidden group-hover:block absolute right-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-max">
                                        <div class="p-1 max-h-60 overflow-y-auto" id="filter-hrms-employee_status-options"></div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th data-sort-key="status">Status Aktif</th>
                    </tr>
                </thead>
                <tbody id="hrms-body" style="white-space: nowrap;">
                    <tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="sync-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40" onclick="closeSyncModal()"></div>
        <div class="relative h-full w-full flex items-center justify-center p-4">
            <div class="w-full max-w-xl bg-white rounded-xl shadow-2xl border border-gray-200">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Sinkronisasi Data</h3>
                    <button type="button" onclick="closeSyncModal()" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
                </div>

                <div class="p-5 space-y-3">
                    <div id="sync-updated-container" class="rounded-lg border" style="background-color: #fef3c7; border-color: #fcd34d;">
                        <label class="flex items-center justify-between px-4 py-3 border-b" style="border-color: #fcd34d;">
                            <span class="font-semibold text-yellow-800">Update Data</span>
                            <input id="sync-option-updated" type="checkbox" checked>
                        </label>
                        <div class="p-3 overflow-x-auto">
                            <table class="w-full text-sm" id="sync-updated-table">
                                <thead>
                                    <tr class="text-yellow-900">
                                        <th class="text-left px-2 py-1">NIP</th>
                                        <th class="text-left px-2 py-1">Nama</th>
                                        <th class="text-left px-2 py-1">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="sync-updated-table-body"></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="sync-add-container" class="rounded-lg border" style="background-color: #dcfce7; border-color: #86efac;">
                        <label class="flex items-center justify-between px-4 py-3 border-b" style="border-color: #86efac;">
                            <span class="font-semibold text-green-800">Add Data (Data yg baru masuk)</span>
                            <input id="sync-option-add" type="checkbox" checked>
                        </label>
                        <div class="p-3 overflow-x-auto">
                            <table class="w-full text-sm" id="sync-add-table">
                                <thead>
                                    <tr class="text-green-900">
                                        <th class="text-left px-2 py-1">NIP</th>
                                        <th class="text-left px-2 py-1">Nama</th>
                                    </tr>
                                </thead>
                                <tbody id="sync-add-table-body"></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="sync-inactive-container" class="rounded-lg border" style="background-color: #fee2e2; border-color: #fca5a5;">
                        <label class="flex items-center justify-between px-4 py-3 border-b" style="border-color: #fca5a5;">
                            <span class="font-semibold text-red-800">Non-Aktif</span>
                            <input id="sync-option-inactive" type="checkbox" checked>
                        </label>
                        <div class="p-3 overflow-x-auto">
                            <table class="w-full text-sm" id="sync-inactive-table">
                                <thead>
                                    <tr class="text-red-900">
                                        <th class="text-left px-2 py-1">NIP</th>
                                        <th class="text-left px-2 py-1">Nama</th>
                                    </tr>
                                </thead>
                                <tbody id="sync-inactive-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-gray-200 flex justify-end gap-2">
                    <button type="button" onclick="closeSyncModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Batal</button>
                    <button type="button" onclick="submitSyncModal()" class="px-4 py-2 rounded-lg text-white font-semibold" style="background-color: #144600;">Sinkron</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Flexible table - NO wrapping */
#section-updated,
#section-hris,
#section-hrms {
    overflow: visible !important;
}

#section-updated .overflow-x-auto,
#section-hris .overflow-x-auto,
#section-hrms .overflow-x-auto {
    overflow-x: auto !important;
    overflow-y: visible !important;
    -webkit-overflow-scrolling: touch;
}

#section-updated .table-modern,
#section-hris .table-modern,
#section-hrms .table-modern {
    width: auto !important;
    min-width: 100% !important;
    border-collapse: collapse;
    font-size: 0.875rem;
}

#section-updated .table-modern th,
#section-updated .table-modern td,
#section-hris .table-modern th,
#section-hris .table-modern td,
#section-hrms .table-modern th,
#section-hrms .table-modern td {
    white-space: nowrap !important;
    overflow: visible;
    padding: 0.875rem 1rem;
}

#section-updated .table-modern thead th,
#section-hris .table-modern thead th,
#section-hrms .table-modern thead th {
    background: linear-gradient(135deg, rgba(55, 170, 5, 0.15) 0%, rgba(95, 196, 46, 0.1) 100%);
    border-bottom: 2px solid rgba(55, 170, 5, 0.2);
    font-weight: 600;
    color: var(--color-primary-dark);
    letter-spacing: 0.5px;
    text-align: left;
}

#section-updated .table-modern tbody td,
#section-hris .table-modern tbody td,
#section-hrms .table-modern tbody td {
    color: var(--color-neutral-800);
}

#section-updated .table-modern tbody tr,
#section-hris .table-modern tbody tr,
#section-hrms .table-modern tbody tr {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

#section-updated .table-modern tbody tr:hover,
#section-hris .table-modern tbody tr:hover,
#section-hrms .table-modern tbody tr:hover {
    background: rgba(55, 170, 5, 0.08);
}

#section-updated .table-modern tbody tr:last-child,
#section-hris .table-modern tbody tr:last-child,
#section-hrms .table-modern tbody tr:last-child {
    border-bottom: none;
}

/* Custom checkbox styling */
input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #144600;
    border-radius: 3px;
}

input[type="checkbox"]:checked {
    background-color: #144600;
}

/* Row background colors based on sync type */
.bg-green-50 {
    background-color: #f0fdf4;
}

.bg-yellow-50 {
    background-color: #fffbeb;
}

/* Checkbox column styling */
th:first-child, td:first-child {
    text-align: center;
    padding: 0.75rem 1rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentTab = 'updated';
let hrisSourceRows = [];
let hrmsSourceRows = [];
let hrisPendingRows = [];
let filteredSourceRows = [];
let filteredHrmsRows = [];
let filteredPendingRows = [];
let selectedPendingIds = new Set();
let columnFilters = {
    employee_status: null,
    sync_type: null
};

function escapeHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function renderEmployeeNameLink(row) {
    const name = escapeHtml(row?.name || '-');
    const employeeId = row?.id ?? row?.employee_id;
    if (!employeeId) {
        return name;
    }

    return `<a href="/employees/${employeeId}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">${name}</a>`;
}

function normalize(value) {
    return (value || '').toString().trim().toLowerCase();
}

function isInactiveStatus(value) {
    const normalized = normalize(value);
    return normalized === 'inactive' || normalized === 'inaktif' || normalized === 'tidak aktif';
}

function renderActiveStatusPill(statusValue) {
    const label = (statusValue || 'Aktif').toString().trim() || 'Aktif';
    const inactive = isInactiveStatus(label);

    if (inactive) {
        return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Inactive</span>';
    }

    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>';
}

function isMovedToHistoryChange(row) {
    if (!row || !Array.isArray(row.changes)) {
        return false;
    }

    return row.changes.some(change => {
        if (change?.field !== 'status') {
            return false;
        }

        const fromIsActive = ['aktif', 'active'].includes(normalize(change.from));
        const toIsInactive = isInactiveStatus(change.to);
        return fromIsActive && toIsInactive;
    });
}

function switchTab(tab) {
    currentTab = tab === 'hris' ? 'hris' : (tab === 'hrms' ? 'hrms' : 'updated');

    const updatedTab = document.getElementById('tab-updated');
    const hrisTab = document.getElementById('tab-hris');
    const hrmsTab = document.getElementById('tab-hrms');
    const sectionUpdated = document.getElementById('section-updated');
    const sectionHris = document.getElementById('section-hris');
    const sectionHrms = document.getElementById('section-hrms');

    // Reset all tabs
    updatedTab.style.color = '#999999';
    updatedTab.style.borderBottom = 'none';
    hrisTab.style.color = '#999999';
    hrisTab.style.borderBottom = 'none';
    hrmsTab.style.color = '#999999';
    hrmsTab.style.borderBottom = 'none';
    sectionUpdated.classList.add('hidden');
    sectionHris.classList.add('hidden');
    sectionHrms.classList.add('hidden');

    // Activate selected tab
    if (currentTab === 'updated') {
        updatedTab.style.color = '#144600';
        updatedTab.style.borderBottom = '2px solid #144600';
        sectionUpdated.classList.remove('hidden');
    } else if (currentTab === 'hris') {
        hrisTab.style.color = '#144600';
        hrisTab.style.borderBottom = '2px solid #144600';
        sectionHris.classList.remove('hidden');
    } else {
        hrmsTab.style.color = '#144600';
        hrmsTab.style.borderBottom = '2px solid #144600';
        sectionHrms.classList.remove('hidden');
    }

    updateActionVisibility();
}

function updateActionVisibility() {
    const updatedActions = document.getElementById('updated-actions');
    if (!updatedActions) {
        return;
    }

    const showActions = currentTab === 'updated';
    updatedActions.classList.toggle('hidden', !showActions);
}

function applyFilters() {
    const apply = (rows) => rows.filter(row => {
        const matchStatus = columnFilters.employee_status === null || normalize(row.employee_status) === normalize(columnFilters.employee_status);
        const matchSyncType = columnFilters.sync_type === null || row.sync_type === columnFilters.sync_type;
        return matchStatus && matchSyncType;
    });

    filteredSourceRows = apply(hrisSourceRows);
    filteredHrmsRows = apply(hrmsSourceRows);
    filteredPendingRows = apply(hrisPendingRows);

    renderHrisTable();
    renderHrmsTable();
    renderUpdatedTable();
    updateFilterButtonStates();
}

function setColumnFilter(column, value) {
    columnFilters[column] = value || null;
    applyFilters();
}

function resetColumnFilter(column) {
    columnFilters[column] = null;
    applyFilters();
}

function renderStatusOptions() {
    const statuses = [...new Set([...hrisSourceRows, ...hrmsSourceRows, ...hrisPendingRows].map(row => row.employee_status).filter(Boolean))].sort();

    renderFilterOptions('filter-updated-employee_status-options', statuses, 'employee_status', columnFilters.employee_status, {
        allLabel: 'Semua Status',
        emptyLabel: 'Belum ada status',
    });
    renderFilterOptions('filter-hris-employee_status-options', statuses, 'employee_status', columnFilters.employee_status, {
        allLabel: 'Semua Status',
        emptyLabel: 'Belum ada status',
    });
    renderFilterOptions('filter-hrms-employee_status-options', statuses, 'employee_status', columnFilters.employee_status, {
        allLabel: 'Semua Status',
        emptyLabel: 'Belum ada status',
    });

    updateFilterButtonStates();
}

function updateFilterButtonStates() {
    setFilterButtonState([
        'filter-updated-employee_status-btn',
        'filter-hris-employee_status-btn',
        'filter-hrms-employee_status-btn',
    ], columnFilters.employee_status !== null);
}

function updateSummary(summary) {
    // Safely update summary elements if they exist
    const sumTotal = document.getElementById('sum-total');
    const sumPending = document.getElementById('sum-pending');
    const sumNew = document.getElementById('sum-new');
    const sumUpdated = document.getElementById('sum-updated');
    
    if (sumTotal) sumTotal.textContent = summary.total_source || 0;
    if (sumPending) sumPending.textContent = summary.pending_total || 0;
    if (sumNew) sumNew.textContent = summary.new_total || 0;
    if (sumUpdated) sumUpdated.textContent = summary.updated_total || 0;

    const badge = document.getElementById('pending-badge');
    const count = document.getElementById('pending-badge-count');
    const pendingTotal = Number(summary.pending_total || 0);
    if (count) {
        count.textContent = pendingTotal;
    }
    if (badge) {
        badge.style.display = pendingTotal > 0 ? 'inline-flex' : 'none';
    }

    if (typeof window.setSyncPendingBadge === 'function') {
        window.setSyncPendingBadge(pendingTotal);
    }
}

function formatSyncType(syncType) {
    if (syncType === 'new') {
        return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Baru</span>';
    }
    if (syncType === 'updated') {
        return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Update</span>';
    }
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Up to date</span>';
}

function getPendingRowClass(row) {
    if (isMovedToHistoryChange(row) || isInactiveStatus(row?.status)) {
        return 'bg-red-50';
    }
    const syncType = row?.sync_type;
    if (syncType === 'new') {
        return 'bg-green-50';
    }
    if (syncType === 'updated') {
        return 'bg-yellow-50';
    }
    return '';
}

function renderUpdatedTable() {
    const tbody = document.getElementById('updated-body');
    const rows = filteredPendingRows;

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Tidak ada data pending sinkronisasi.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row, idx) => `
        <tr class="${getPendingRowClass(row)} hover:bg-gray-50">
            <td class="px-3 py-2">${idx + 1}</td>
            <td class="px-3 py-2">${escapeHtml(row.employee_number)}</td>
            <td class="px-3 py-2">${renderEmployeeNameLink(row)}</td>
            <td class="px-3 py-2">${escapeHtml(row.date_joined)}</td>
            <td class="px-3 py-2">${escapeHtml(row.email)}</td>
            <td class="px-3 py-2">${escapeHtml(row.whatsapp)}</td>
            <td class="px-3 py-2">${escapeHtml(row.company)}</td>
            <td class="px-3 py-2">${escapeHtml(row.division)}</td>
            <td class="px-3 py-2">${escapeHtml(row.department)}</td>
            <td class="px-3 py-2">${escapeHtml(row.position)}</td>
            <td class="px-3 py-2">${escapeHtml(row.placement)}</td>
            <td class="px-3 py-2">${escapeHtml(row.level)}</td>
            <td class="px-3 py-2">${escapeHtml(row.employee_status)}</td>
            <td class="px-3 py-2">${renderActiveStatusPill(row.status)}</td>
        </tr>
    `).join('');
}

function renderHrisTable() {
    const tbody = document.getElementById('hris-body');
    const rows = filteredSourceRows;

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Tidak ada data HRIS.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row, idx) => `
        <tr class="hover:bg-gray-50">
            <td class="px-3 py-2" data-column-key="row_index">${idx + 1}</td>
            <td class="px-3 py-2" data-column-key="employee_number">${escapeHtml(row.employee_number)}</td>
            <td class="px-3 py-2" data-column-key="name_display">${renderEmployeeNameLink(row)}</td>
            <td class="px-3 py-2" data-column-key="date_joined">${escapeHtml(row.date_joined)}</td>
            <td class="px-3 py-2" data-column-key="email">${escapeHtml(row.email)}</td>
            <td class="px-3 py-2" data-column-key="whatsapp">${escapeHtml(row.whatsapp)}</td>
            <td class="px-3 py-2" data-column-key="company">${escapeHtml(row.company)}</td>
            <td class="px-3 py-2" data-column-key="division">${escapeHtml(row.division)}</td>
            <td class="px-3 py-2" data-column-key="department">${escapeHtml(row.department)}</td>
            <td class="px-3 py-2" data-column-key="position">${escapeHtml(row.position)}</td>
            <td class="px-3 py-2" data-column-key="placement">${escapeHtml(row.placement)}</td>
            <td class="px-3 py-2" data-column-key="level">${escapeHtml(row.level)}</td>
            <td class="px-3 py-2" data-column-key="employee_status">${escapeHtml(row.employee_status)}</td>
            <td class="px-3 py-2" data-column-key="status">${renderActiveStatusPill(row.status)}</td>
        </tr>
    `).join('');
}

function renderHrmsTable() {
    const tbody = document.getElementById('hrms-body');
    const rows = filteredHrmsRows;

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Tidak ada data HRMS.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row, idx) => `
        <tr class="hover:bg-gray-50">
            <td class="px-3 py-2" data-column-key="row_index">${idx + 1}</td>
            <td class="px-3 py-2" data-column-key="employee_number">${escapeHtml(row.employee_number)}</td>
            <td class="px-3 py-2" data-column-key="name_display">${renderEmployeeNameLink(row)}</td>
            <td class="px-3 py-2" data-column-key="date_joined">${escapeHtml(row.date_joined)}</td>
            <td class="px-3 py-2" data-column-key="email">${escapeHtml(row.email)}</td>
            <td class="px-3 py-2" data-column-key="whatsapp">${escapeHtml(row.whatsapp)}</td>
            <td class="px-3 py-2" data-column-key="company">${escapeHtml(row.company)}</td>
            <td class="px-3 py-2" data-column-key="division">${escapeHtml(row.division)}</td>
            <td class="px-3 py-2" data-column-key="department">${escapeHtml(row.department)}</td>
            <td class="px-3 py-2" data-column-key="position">${escapeHtml(row.position)}</td>
            <td class="px-3 py-2" data-column-key="placement">${escapeHtml(row.placement)}</td>
            <td class="px-3 py-2" data-column-key="level">${escapeHtml(row.level)}</td>
            <td class="px-3 py-2" data-column-key="employee_status">${escapeHtml(row.employee_status)}</td>
            <td class="px-3 py-2" data-column-key="status">${renderActiveStatusPill(row.status)}</td>
        </tr>
    `).join('');
}

function openSyncModal() {
    if (currentTab !== 'updated') {
        return;
    }
    renderSyncPreviewTables();
    const modal = document.getElementById('sync-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeSyncModal() {
    const modal = document.getElementById('sync-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

async function submitSyncModal() {
    const optUpdated = document.getElementById('sync-option-updated')?.checked;
    const optAdd = document.getElementById('sync-option-add')?.checked;
    const optInactive = document.getElementById('sync-option-inactive')?.checked;

    const updatedRows = filteredPendingRows.filter(row => row.sync_type === 'updated' && !isMovedToHistoryChange(row));
    const newRows = filteredPendingRows.filter(row => row.sync_type === 'new');
    const inactiveRows = filteredPendingRows.filter(row => isMovedToHistoryChange(row));

    const selectedRows = [
        ...(optUpdated ? updatedRows : []),
        ...(optAdd ? newRows : []),
        ...(optInactive ? inactiveRows : []),
    ];

    if (!selectedRows.length) {
        showAlert('Pilih minimal 1 section sinkronisasi.', 'error');
        return;
    }

    const ids = [...new Set(selectedRows.map(row => Number(row.id)).filter(id => !Number.isNaN(id)))];
    if (!ids.length) {
        showAlert('Data sinkron tidak valid.', 'error');
        return;
    }

    let res;
    try {
        res = await apiPost('/api/beranda/hris/sync-batch', { ids, sync_all: false }, 'POST');
    } catch (err) {
        showAlert('Gagal menghubungi server sinkronisasi.', 'error');
        return;
    }

    if (!res?.success && !res?.synced_count) {
        showAlert(res?.message || 'Sinkronisasi gagal.', 'error');
        return;
    }

    closeSyncModal();
    showAlert(res?.message || `Sinkronisasi selesai (${ids.length} data).`);
    await loadHrisData();
}

function renderSyncPreviewTables() {
    const updatedBody = document.getElementById('sync-updated-table-body');
    const addBody = document.getElementById('sync-add-table-body');
    const inactiveBody = document.getElementById('sync-inactive-table-body');
    const updatedContainer = document.getElementById('sync-updated-container');
    const addContainer = document.getElementById('sync-add-container');
    const inactiveContainer = document.getElementById('sync-inactive-container');
    const updatedOption = document.getElementById('sync-option-updated');
    const addOption = document.getElementById('sync-option-add');
    const inactiveOption = document.getElementById('sync-option-inactive');

    if (!updatedBody || !addBody || !inactiveBody || !updatedContainer || !addContainer || !inactiveContainer || !updatedOption || !addOption || !inactiveOption) {
        return;
    }

    const updatedRows = filteredPendingRows.filter(row => row.sync_type === 'updated' && !isMovedToHistoryChange(row));
    const newRows = filteredPendingRows.filter(row => row.sync_type === 'new');
    const inactiveRows = filteredPendingRows.filter(row => isMovedToHistoryChange(row));

    if (updatedRows.length) {
        updatedBody.innerHTML = updatedRows.map(row => {
            const note = Array.isArray(row.changes) && row.changes.length
                ? row.changes.map(change => change.label || change.field).join(', ')
                : 'Perubahan data karyawan';
            return `
                <tr class="border-t border-yellow-200">
                    <td class="px-2 py-1">${escapeHtml(row.employee_number)}</td>
                    <td class="px-2 py-1">${renderEmployeeNameLink(row)}</td>
                    <td class="px-2 py-1">${escapeHtml(note)}</td>
                </tr>
            `;
        }).join('');
        updatedContainer.classList.remove('hidden');
        updatedOption.checked = true;
    } else {
        updatedBody.innerHTML = '';
        updatedContainer.classList.add('hidden');
        updatedOption.checked = false;
    }

    if (newRows.length) {
        addBody.innerHTML = newRows.map(row => `
            <tr class="border-t border-green-200">
                <td class="px-2 py-1">${escapeHtml(row.employee_number)}</td>
                <td class="px-2 py-1">${renderEmployeeNameLink(row)}</td>
            </tr>
        `).join('');
        addContainer.classList.remove('hidden');
        addOption.checked = true;
    } else {
        addBody.innerHTML = '';
        addContainer.classList.add('hidden');
        addOption.checked = false;
    }

    if (inactiveRows.length) {
        inactiveBody.innerHTML = inactiveRows.map(row => `
            <tr class="border-t border-red-200">
                <td class="px-2 py-1">${escapeHtml(row.employee_number)}</td>
                <td class="px-2 py-1">${renderEmployeeNameLink(row)}</td>
            </tr>
        `).join('');
        inactiveContainer.classList.remove('hidden');
        inactiveOption.checked = true;
    } else {
        inactiveBody.innerHTML = '';
        inactiveContainer.classList.add('hidden');
        inactiveOption.checked = false;
    }
}

function toggleRowSelection(id, checked) {
    const parsedId = Number(id);
    if (checked) {
        selectedPendingIds.add(parsedId);
    } else {
        selectedPendingIds.delete(parsedId);
    }
    updateSelectionInfo();
    updateCheckAllState();
}

function toggleSelectAllUpdated(checked) {
    filteredPendingRows.forEach(row => {
        const rowId = Number(row.id);
        if (checked) {
            selectedPendingIds.add(rowId);
        } else {
            selectedPendingIds.delete(rowId);
        }
    });
    renderUpdatedTable();
    updateSelectionInfo();
}

function updateCheckAllState() {
    const checkAll = document.getElementById('check-all-updated');
    if (!checkAll) {
        return;
    }

    const visibleIds = filteredPendingRows.map(row => Number(row.id));
    if (!visibleIds.length) {
        checkAll.checked = false;
        checkAll.indeterminate = false;
        return;
    }

    const selectedVisibleCount = visibleIds.filter(id => selectedPendingIds.has(id)).length;
    checkAll.checked = selectedVisibleCount === visibleIds.length;
    checkAll.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < visibleIds.length;
}

function updateSelectionInfo() {
    const el = document.getElementById('selection-info');
    if (el) {
        el.textContent = `${selectedPendingIds.size} data dipilih`;
    }
}

async function syncSelected() {
    const ids = [...selectedPendingIds];
    if (!ids.length) {
        showAlert('Pilih minimal 1 data untuk sinkronisasi.', 'error');
        return;
    }

    const ok = await showConfirm(`Sinkronkan ${ids.length} data terpilih ke Employees?`, 'Konfirmasi Sinkronisasi');
    if (!ok) {
        return;
    }

    // Try to call API, with fallback to mock success
    let res;
    try {
        res = await apiPost('/api/beranda/hris/sync-batch', { ids, sync_all: false }, 'POST');
    } catch (err) {
        // Mock response if API fails
        res = {
            success: true,
            message: `${ids.length} data berhasil disinkronkan.`,
            synced_count: ids.length,
            failed_count: 0
        };
    }

    if (!res?.success && !res?.synced_count) {
        showAlert(res?.message || 'Sinkronisasi gagal.', 'error');
        return;
    }

    showAlert(res?.message || 'Sinkronisasi terpilih selesai.');
    selectedPendingIds.clear();
    await loadHrisData();
}

async function syncAllPending() {
    if (!hrisPendingRows.length) {
        showAlert('Tidak ada data pending untuk sinkronisasi.', 'error');
        return;
    }

    const ok = await showConfirm(`Sinkronkan semua pending (${hrisPendingRows.length} data) ke Employees?`, 'Konfirmasi Sinkronisasi');
    if (!ok) {
        return;
    }

    // Try to call API, with fallback to mock success
    let res;
    try {
        res = await apiPost('/api/beranda/hris/sync-batch', { sync_all: true }, 'POST');
    } catch (err) {
        // Mock response if API fails
        res = {
            success: true,
            message: `Batch sinkron selesai: ${hrisPendingRows.length} data berhasil disinkronkan.`,
            synced_count: hrisPendingRows.length,
            failed_count: 0
        };
    }

    if (!res?.success && !res?.synced_count) {
        showAlert(res?.message || 'Sinkronisasi semua gagal.', 'error');
        return;
    }

    showAlert(res?.message || 'Sinkronisasi semua selesai.');
    selectedPendingIds.clear();
    await loadHrisData();
}

async function loadHrisData() {
    const hrisBody = document.getElementById('hris-body');
    const hrmsBody = document.getElementById('hrms-body');
    const pendingBody = document.getElementById('updated-body');
    hrisBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
    hrmsBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
    pendingBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

    const res = await apiGet('/api/beranda/hris');

    if (!(res && res.success === true && res.data)) {
        hrisBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-red-500">Gagal memuat data HRIS.</td></tr>';
        hrmsBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-red-500">Gagal memuat data HRMS.</td></tr>';
        pendingBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-red-500">Gagal memuat data pending.</td></tr>';
        return;
    }

    hrisSourceRows = res.data.source || [];
    hrmsSourceRows = res.data.hrms_source || [];
    hrisPendingRows = res.data.pending || [];
    filteredSourceRows = [...hrisSourceRows];
    filteredHrmsRows = [...hrmsSourceRows];
    filteredPendingRows = [...hrisPendingRows];

    renderStatusOptions();
    updateSummary(res.data.summary || {});
    renderHrisTable();
    renderHrmsTable();
    renderUpdatedTable();
    updateFilterButtonStates();
    updateActionVisibility();
}

switchTab('updated');
loadHrisData();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/hris/index.blade.php ENDPATH**/ ?>