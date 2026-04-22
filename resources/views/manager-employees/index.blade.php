@extends('layouts.app')
@section('title','Manager - Employee')
@section('content')
<div class="px-4 space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Employee</h1>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-4">
    <div class="flex gap-4 border-b border-gray-200 pb-3">
      <button id="btn-lifecycle-active" onclick="setLifecycleTab('active')" class="px-4 py-2 font-medium transition-colors" style="color: #144600; border-bottom: 2px solid #144600;">Employee Active</button>
      <button id="btn-lifecycle-history" onclick="setLifecycleTab('history')" class="px-4 py-2 font-medium transition-colors text-gray-500 hover:text-gray-700">History Employee</button>
    </div>
    <div id="filters-bar" class="pt-3">
      <!-- Filters in column headers -->
    </div>
  </div>

  <div class="table-container">
    <div class="overflow-x-auto">
      <table class="table-modern">
        <thead>
          <tr>
            <th>No</th>
            <th>NIP</th>
            <th>Nama Employee</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Induction</th>
            <th>Email</th>
            <th>Whatsapp</th>
            <th>Periode Awal</th>
            <th>Periode Akhir</th>
            <th>Career Stage</th>
            <th class="relative">
              <div class="flex items-center justify-between gap-2">
                <span>Fase</span>
                <div class="relative group cursor-pointer">
                  <span class="text-gray-500 group-hover:text-gray-700">▼</span>
                  <div class="hidden group-hover:block absolute right-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-max">
                    <div class="p-1">
                      <button onclick="setColumnFilter('phase', 'Planning')" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100">Planning</button>
                      <button onclick="setColumnFilter('phase', 'Fase 1')" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100">Fase 1</button>
                      <button onclick="setColumnFilter('phase', 'Fase 2')" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100">Fase 2</button>
                      <button onclick="setColumnFilter('phase', 'Fase 3')" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100">Fase 3</button>
                      <button onclick="setColumnFilter('phase', 'Selesai')" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100">Selesai</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>
            <th>Progress</th>
            <th>Manager Fungsional</th>
            <th>Manager Operasional</th>
            <th>Perusahaan</th>
            <th>Divisi</th>
            <th>Departemen</th>
            <th>Jabatan</th>
            <th>Penempatan</th>
            <th>Golongan</th>
            <th>Status Pegawai</th>
            <th>Status Lifecycle</th>
          </tr>
        </thead>
        <tbody id="rows-body">
          <tr><td colspan="22" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script>
let allRows = [];
let filteredRows = [];
let currentLifecycle = 'active';
let columnFilters = {
  phase: null
};

function setLifecycleTab(lifecycle) {
  currentLifecycle = lifecycle;
  const activeBtn = document.getElementById('btn-lifecycle-active');
  const historyBtn = document.getElementById('btn-lifecycle-history');

  if (lifecycle === 'active') {
    activeBtn.style.color = '#144600';
    activeBtn.style.borderBottom = '2px solid #144600';
    historyBtn.style.color = '#999999';
    historyBtn.style.borderBottom = 'none';
  } else {
    historyBtn.style.color = '#144600';
    historyBtn.style.borderBottom = '2px solid #144600';
    activeBtn.style.color = '#999999';
    activeBtn.style.borderBottom = 'none';
  }

  loadRows();
}

async function loadRows() {
  const tbody = document.getElementById('rows-body');
  tbody.innerHTML = '<tr><td colspan="22" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

  const url = `/api/manager/employees?lifecycle=${encodeURIComponent(currentLifecycle)}`;
  const res = await apiGet(url);

  if (!(res && res.success === true)) {
    tbody.innerHTML = '<tr><td colspan="22" class="text-center py-8 text-red-500">Gagal memuat data</td></tr>';
    return;
  }

  allRows = res.data || [];
  applyFilters();
}

function applyFilters() {
  filteredRows = allRows.filter(row => {
    const phaseMatch = columnFilters.phase === null || row.phase === columnFilters.phase;
    return phaseMatch;
  });

  renderRows();
}

function setColumnFilter(column, value) {
  columnFilters[column] = value || null;
  applyFilters();
}

function resetColumnFilter(column) {
  columnFilters[column] = null;
  applyFilters();
}

function renderRows() {
  const tbody = document.getElementById('rows-body');
  if (!filteredRows.length) {
    tbody.innerHTML = '<tr><td colspan="22" class="text-center py-8 text-gray-400">Tidak ada data Employee</td></tr>';
    return;
  }

  tbody.innerHTML = filteredRows.map((row, idx) => `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3">${idx + 1}</td>
      <td class="px-4 py-3">${row.employee_number || '-'}</td>
      <td class="px-4 py-3 font-medium">
        <div class="flex items-center gap-2">
          <span>${row.name || '-'}</span>
          <a href="/manager/employees/${row.id}" class="inline-flex items-center justify-center w-6 h-6 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700" title="Lihat Detail">
            <i class="fas fa-external-link-alt text-xs"></i>
          </a>
        </div>
      </td>
      <td class="px-4 py-3">${row.date_joined || '-'}</td>
      <td class="px-4 py-3">${row.induction_date || '-'}</td>
      <td class="px-4 py-3">${row.email || '-'}</td>
      <td class="px-4 py-3">${row.whatsapp || '-'}</td>
      <td class="px-4 py-3">${row.vnb_period_start || '-'}</td>
      <td class="px-4 py-3">${row.vnb_period_end || '-'}</td>
      <td class="px-4 py-3">${row.career_stage || '-'}</td>
      <td class="px-4 py-3">${row.phase || '-'}</td>
      <td class="px-4 py-3 font-semibold">${row.progress || 0}%</td>
      <td class="px-4 py-3">${row.manager_functional || '-'}</td>
      <td class="px-4 py-3">${row.manager_operational || '-'}</td>
      <td class="px-4 py-3">${row.company || '-'}</td>
      <td class="px-4 py-3">${row.division || '-'}</td>
      <td class="px-4 py-3">${row.department || '-'}</td>
      <td class="px-4 py-3">${row.position || '-'}</td>
      <td class="px-4 py-3">${row.placement || '-'}</td>
      <td class="px-4 py-3">${row.level || '-'}</td>
      <td class="px-4 py-3">${row.employee_status || '-'}</td>
      <td class="px-4 py-3">
        <span class="inline-block px-2 py-1 rounded text-xs font-medium ${
          row.employment_state === 'active' ? 'bg-green-100 text-green-700' :
          row.employment_state === 'resigned' ? 'bg-gray-100 text-gray-700' :
          row.employment_state === 'terminated' ? 'bg-red-100 text-red-700' :
          'bg-blue-100 text-blue-700'
        }">
          ${row.employment_state || '-'}
        </span>
      </td>
    </tr>
  `).join('');
}

loadRows();
</script>
@endpush
@endsection
