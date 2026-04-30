
<?php $__env->startSection('title','Manager - Employee'); ?>
<?php $__env->startSection('page_title','Manager - Employee'); ?>
<?php $__env->startSection('page_subtitle','Lihat employee yang berada di bawah pengawasan manager beserta lifecycle mereka.'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
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
      <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-sort-key="row_index">No</th>
            <th data-sort-key="employee_number">NIP</th>
            <th data-sort-key="name">Nama Employee</th>
            <th data-sort-key="date_joined">Tanggal Masuk</th>
            <th data-sort-key="induction_date">Tanggal Induction</th>
            <th data-sort-key="email">Email</th>
            <th data-sort-key="whatsapp">Whatsapp</th>
            <th data-sort-key="vnb_period_start">Periode Awal</th>
            <th data-sort-key="vnb_period_end">Periode Akhir</th>
            <th data-sort-key="career_stage">Career Stage</th>
            <th class="relative" data-sort-key="phase">
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
            <th data-sort-key="progress">Progress</th>
            <th data-sort-key="manager_functional">Manager Fungsional</th>
            <th data-sort-key="manager_operational">Manager Operasional</th>
            <th data-sort-key="company">Perusahaan</th>
            <th data-sort-key="division">Divisi</th>
            <th data-sort-key="department">Departemen</th>
            <th data-sort-key="position">Jabatan</th>
            <th data-sort-key="placement">Penempatan</th>
            <th data-sort-key="level">Golongan</th>
            <th data-sort-key="employee_status">Status Pegawai</th>
            <th data-sort-key="status">Status Lifecycle</th>
          </tr>
        </thead>
        <tbody id="rows-body" style="white-space: nowrap;">
          <tr><td colspan="22" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
      <td class="px-4 py-3" data-column-key="row_index">${idx + 1}</td>
      <td class="px-4 py-3" data-column-key="employee_number">${row.employee_number || '-'}</td>
      <td class="px-4 py-3 font-medium" data-column-key="name">
        <div class="flex items-center gap-2">
          <span>${row.name || '-'}</span>
          <a href="/manager/employees/${row.id}" class="inline-flex items-center justify-center w-6 h-6 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700" title="Lihat Detail">
            <i class="fas fa-external-link-alt text-xs"></i>
          </a>
        </div>
      </td>
      <td class="px-4 py-3" data-column-key="date_joined">${row.date_joined || '-'}</td>
      <td class="px-4 py-3" data-column-key="induction_date">${row.induction_date || '-'}</td>
      <td class="px-4 py-3" data-column-key="email">${row.email || '-'}</td>
      <td class="px-4 py-3" data-column-key="whatsapp">${row.whatsapp || '-'}</td>
      <td class="px-4 py-3" data-column-key="vnb_period_start">${row.vnb_period_start || '-'}</td>
      <td class="px-4 py-3" data-column-key="vnb_period_end">${row.vnb_period_end || '-'}</td>
      <td class="px-4 py-3" data-column-key="career_stage">${row.career_stage || '-'}</td>
      <td class="px-4 py-3" data-column-key="phase">${row.phase || '-'}</td>
      <td class="px-4 py-3 font-semibold" data-column-key="progress">${row.progress || 0}%</td>
      <td class="px-4 py-3" data-column-key="manager_functional">${row.manager_functional || '-'}</td>
      <td class="px-4 py-3" data-column-key="manager_operational">${row.manager_operational || '-'}</td>
      <td class="px-4 py-3" data-column-key="company">${row.company || '-'}</td>
      <td class="px-4 py-3" data-column-key="division">${row.division || '-'}</td>
      <td class="px-4 py-3" data-column-key="department">${row.department || '-'}</td>
      <td class="px-4 py-3" data-column-key="position">${row.position || '-'}</td>
      <td class="px-4 py-3" data-column-key="placement">${row.placement || '-'}</td>
      <td class="px-4 py-3" data-column-key="level">${row.level || '-'}</td>
      <td class="px-4 py-3" data-column-key="employee_status">${row.employee_status || '-'}</td>
      <td class="px-4 py-3" data-column-key="status">
        <span class="inline-block px-2 py-1 rounded text-xs font-medium ${
          row.status === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
        }">
          ${row.status || '-'}
        </span>
      </td>
    </tr>
  `).join('');
}

loadRows();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views\manager-employees\index.blade.php ENDPATH**/ ?>