@extends('layouts.app')

@section('title', 'HRIS - VnB Platform')

@section('content')
<div class="px-4 space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">HRIS Data Sync</h1>
            <p class="text-sm text-gray-500 mt-1">Database Employee dari HRIS vs Updated Data yang perlu sinkron ke Employees.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex flex-wrap gap-3 border-b border-gray-200 pb-3 items-center">
            <button id="tab-updated" onclick="switchTab('updated')" class="px-4 py-2 font-medium transition-colors" style="color: #144600; border-bottom: 2px solid #144600;">
                Updated Data
                <span id="pending-badge" class="inline-flex items-center justify-center ml-2 min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold text-white" style="background-color: #dc2626; display: none;">
                    <i class="fas fa-sync-alt mr-1"></i>
                    <span id="pending-badge-count">0</span>
                </span>
            </button>
            <button id="tab-source" onclick="switchTab('source')" class="px-4 py-2 font-medium transition-colors text-gray-500 hover:text-gray-700">
                Database Employee dari HRIS
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-3">
            <input id="f-search" type="text" placeholder="Cari NIP/Nama/Email" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onkeyup="applyFilters()">
            <select id="f-status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="applyFilters()">
                <option value="">Semua Status Pegawai</option>
            </select>
            <div id="selection-info" class="text-sm text-gray-600 flex items-center px-1">0 data dipilih</div>
            <div class="flex gap-2 justify-start md:justify-end">
                <button onclick="syncSelected()" class="px-4 py-2 rounded-lg text-sm border border-amber-500 text-amber-700 hover:bg-amber-50">
                    <i class="fas fa-check-square mr-1"></i> Sinkronkan Terpilih
                </button>
                <button onclick="syncAllPending()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
                    <i class="fas fa-sync-alt mr-1"></i> Sinkronkan Semua
                </button>
            </div>
        </div>
    </div>

    <div id="summary-box" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Total Data HRIS</div>
            <div id="sum-total" class="text-2xl font-bold text-gray-800">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Pending Sinkron</div>
            <div id="sum-pending" class="text-2xl font-bold text-red-600">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Data Baru</div>
            <div id="sum-new" class="text-2xl font-bold text-green-600">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Data Update</div>
            <div id="sum-updated" class="text-2xl font-bold text-yellow-600">0</div>
        </div>
    </div>

    <div id="section-updated" class="table-container">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th><input id="check-all-updated" type="checkbox" onchange="toggleSelectAllUpdated(this.checked)"></th>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama Lengkap</th>
                        <th>Tanggal Masuk</th>
                        <th>Email</th>
                        <th>Whatsapp</th>
                        <th>Perusahaan</th>
                        <th>Divisi</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Penempatan</th>
                        <th>Golongan</th>
                        <th>Status Pegawai</th>
                        <th>Status Data</th>
                    </tr>
                </thead>
                <tbody id="updated-body">
                    <tr><td colspan="15" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="section-source" class="table-container hidden">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama Lengkap</th>
                        <th>Tanggal Masuk</th>
                        <th>Email</th>
                        <th>Whatsapp</th>
                        <th>Perusahaan</th>
                        <th>Divisi</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Penempatan</th>
                        <th>Golongan</th>
                        <th>Status Pegawai</th>
                        <th>Status Sinkron</th>
                    </tr>
                </thead>
                <tbody id="source-body">
                    <tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentTab = 'updated';
let hrisSourceRows = [];
let hrisPendingRows = [];
let filteredSourceRows = [];
let filteredPendingRows = [];
let selectedPendingIds = new Set();

function escapeHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function switchTab(tab) {
    currentTab = tab === 'source' ? 'source' : 'updated';

    const updatedTab = document.getElementById('tab-updated');
    const sourceTab = document.getElementById('tab-source');
    const sectionUpdated = document.getElementById('section-updated');
    const sectionSource = document.getElementById('section-source');

    if (currentTab === 'updated') {
        updatedTab.style.color = '#144600';
        updatedTab.style.borderBottom = '2px solid #144600';
        sourceTab.style.color = '#999999';
        sourceTab.style.borderBottom = 'none';
        sectionUpdated.classList.remove('hidden');
        sectionSource.classList.add('hidden');
    } else {
        sourceTab.style.color = '#144600';
        sourceTab.style.borderBottom = '2px solid #144600';
        updatedTab.style.color = '#999999';
        updatedTab.style.borderBottom = 'none';
        sectionSource.classList.remove('hidden');
        sectionUpdated.classList.add('hidden');
    }

    updateActionVisibility();
}

function updateActionVisibility() {
    const selectionInfo = document.getElementById('selection-info');
    const syncButtons = selectionInfo ? selectionInfo.parentElement?.nextElementSibling : null;
    if (!selectionInfo || !syncButtons) {
        return;
    }

    const showActions = currentTab === 'updated';
    selectionInfo.style.visibility = showActions ? 'visible' : 'hidden';
    syncButtons.style.visibility = showActions ? 'visible' : 'hidden';
}

function normalize(value) {
    return (value || '').toString().trim().toLowerCase();
}

function applyFilters() {
    const q = normalize(document.getElementById('f-search')?.value);
    const status = normalize(document.getElementById('f-status')?.value);

    const apply = (rows) => rows.filter(row => {
        const matchQ = !q || [row.employee_number, row.name, row.email].some(v => normalize(v).includes(q));
        const matchStatus = !status || normalize(row.employee_status) === status;
        return matchQ && matchStatus;
    });

    filteredSourceRows = apply(hrisSourceRows);
    filteredPendingRows = apply(hrisPendingRows);

    selectedPendingIds = new Set([...selectedPendingIds].filter(id => filteredPendingRows.some(row => Number(row.id) === Number(id))));

    renderSourceTable();
    renderUpdatedTable();
    updateSelectionInfo();
}

function renderStatusOptions() {
    const select = document.getElementById('f-status');
    if (!select) return;

    const statuses = [...new Set(hrisSourceRows.map(row => row.employee_status).filter(Boolean))].sort();
    const current = select.value;
    select.innerHTML = '<option value="">Semua Status Pegawai</option>' + statuses.map(s => `<option value="${escapeHtml(s)}">${escapeHtml(s)}</option>`).join('');
    if (statuses.includes(current)) {
        select.value = current;
    }
}

function updateSummary(summary) {
    document.getElementById('sum-total').textContent = summary.total_source || 0;
    document.getElementById('sum-pending').textContent = summary.pending_total || 0;
    document.getElementById('sum-new').textContent = summary.new_total || 0;
    document.getElementById('sum-updated').textContent = summary.updated_total || 0;

    const badge = document.getElementById('pending-badge');
    const count = document.getElementById('pending-badge-count');
    const pendingTotal = Number(summary.pending_total || 0);
    if (count) {
        count.textContent = pendingTotal;
    }
    if (badge) {
        badge.style.display = pendingTotal > 0 ? 'inline-flex' : 'none';
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

function getPendingRowClass(syncType) {
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
        tbody.innerHTML = '<tr><td colspan="15" class="text-center py-8 text-gray-400">Tidak ada data pending sinkronisasi.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row, idx) => `
        <tr class="${getPendingRowClass(row.sync_type)} hover:bg-gray-50">
            <td class="px-3 py-2">
                <input type="checkbox" ${selectedPendingIds.has(Number(row.id)) ? 'checked' : ''} onchange="toggleRowSelection(${Number(row.id)}, this.checked)">
            </td>
            <td class="px-3 py-2">${idx + 1}</td>
            <td class="px-3 py-2">${escapeHtml(row.employee_number)}</td>
            <td class="px-3 py-2">${escapeHtml(row.name)}</td>
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
            <td class="px-3 py-2">${formatSyncType(row.sync_type)}</td>
        </tr>
    `).join('');

    updateCheckAllState();
}

function renderSourceTable() {
    const tbody = document.getElementById('source-body');
    const rows = filteredSourceRows;

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Tidak ada data HRIS.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row, idx) => `
        <tr class="hover:bg-gray-50">
            <td class="px-3 py-2">${idx + 1}</td>
            <td class="px-3 py-2">${escapeHtml(row.employee_number)}</td>
            <td class="px-3 py-2">${escapeHtml(row.name)}</td>
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
            <td class="px-3 py-2">${formatSyncType(row.sync_type)}</td>
        </tr>
    `).join('');
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

    const res = await apiPost('/api/beranda/hris/sync-batch', { ids, sync_all: false }, 'POST');
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

    const res = await apiPost('/api/beranda/hris/sync-batch', { sync_all: true }, 'POST');
    if (!res?.success && !res?.synced_count) {
        showAlert(res?.message || 'Sinkronisasi semua gagal.', 'error');
        return;
    }

    showAlert(res?.message || 'Sinkronisasi semua selesai.');
    selectedPendingIds.clear();
    await loadHrisData();
}

async function loadHrisData() {
    const sourceBody = document.getElementById('source-body');
    const pendingBody = document.getElementById('updated-body');
    sourceBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
    pendingBody.innerHTML = '<tr><td colspan="15" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

    const res = await apiGet('/api/beranda/hris');
    if (!(res && res.success === true && res.data)) {
        sourceBody.innerHTML = '<tr><td colspan="14" class="text-center py-8 text-red-500">Gagal memuat data HRIS.</td></tr>';
        pendingBody.innerHTML = '<tr><td colspan="15" class="text-center py-8 text-red-500">Gagal memuat data pending.</td></tr>';
        return;
    }

    hrisSourceRows = res.data.source || [];
    hrisPendingRows = res.data.pending || [];
    filteredSourceRows = [...hrisSourceRows];
    filteredPendingRows = [...hrisPendingRows];

    renderStatusOptions();
    updateSummary(res.data.summary || {});
    renderSourceTable();
    renderUpdatedTable();
    updateSelectionInfo();
    updateActionVisibility();
}

switchTab('updated');
loadHrisData();
</script>
@endpush
