
<?php $__env->startSection('title','Manager - New Hire'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">New Hire</h1>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-4">
    <div class="flex items-center gap-2 mb-3">
      <button id="btn-lifecycle-active" onclick="setLifecycleTab('active')" class="px-3 py-2 rounded-lg text-sm text-white" style="background-color:#144600;">New Hire Active</button>
      <button id="btn-lifecycle-history" onclick="setLifecycleTab('history')" class="px-3 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">History New Hire</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <input id="f-search" type="text" placeholder="Cari NIP/Nama/Email" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onkeyup="applyFilters()">
      <select id="f-phase" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="applyFilters()">
        <option value="">Semua Fase</option>
        <option value="Planning">Planning</option>
        <option value="Fase 1">Fase 1</option>
        <option value="Fase 2">Fase 2</option>
        <option value="Fase 3">Fase 3</option>
        <option value="Selesai">Selesai</option>
      </select>
      <button onclick="loadRows()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
        <i class="fas fa-sync-alt mr-1"></i> Refresh
      </button>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm" style="min-width: 1100px;">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">No</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">NIP</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Nama New Hire</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Email</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Perusahaan</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Divisi</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Career Stage</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Fase</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Progress</th>
            <th class="px-4 py-3 text-right text-xs uppercase text-gray-500">Aksi</th>
          </tr>
        </thead>
        <tbody id="rows-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="10" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
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

function setLifecycleTab(lifecycle) {
  currentLifecycle = lifecycle;
  const activeBtn = document.getElementById('btn-lifecycle-active');
  const historyBtn = document.getElementById('btn-lifecycle-history');

  if (lifecycle === 'active') {
    activeBtn.className = 'px-3 py-2 rounded-lg text-sm text-white';
    activeBtn.style.backgroundColor = '#144600';
    historyBtn.className = 'px-3 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50';
    historyBtn.style.backgroundColor = '#ffffff';
  } else {
    historyBtn.className = 'px-3 py-2 rounded-lg text-sm text-white';
    historyBtn.style.backgroundColor = '#144600';
    activeBtn.className = 'px-3 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50';
    activeBtn.style.backgroundColor = '#ffffff';
  }

  loadRows();
}

async function loadRows() {
  const tbody = document.getElementById('rows-body');
  tbody.innerHTML = '<tr><td colspan="10" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

  const search = encodeURIComponent(document.getElementById('f-search').value || '');
  const url = `/api/manager/new-hires?lifecycle=${encodeURIComponent(currentLifecycle)}&search=${search}`;
  const res = await apiGet(url);

  if (!(res && res.success === true)) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-8 text-red-500">Gagal memuat data</td></tr>';
    return;
  }

  allRows = res.data || [];
  applyFilters();
}

function applyFilters() {
  const phase = (document.getElementById('f-phase').value || '').toLowerCase();
  const search = (document.getElementById('f-search').value || '').toLowerCase();

  filteredRows = allRows.filter(row => {
    if (phase && (row.phase || '').toLowerCase() !== phase) return false;
    if (search) {
      const haystack = `${row.employee_number || ''} ${row.name || ''} ${row.email || ''}`.toLowerCase();
      if (!haystack.includes(search)) return false;
    }
    return true;
  });

  renderRows();
}

function renderRows() {
  const tbody = document.getElementById('rows-body');
  if (!filteredRows.length) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-8 text-gray-400">Tidak ada data New Hire</td></tr>';
    return;
  }

  tbody.innerHTML = filteredRows.map((row, idx) => `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3">${idx + 1}</td>
      <td class="px-4 py-3">${row.employee_number || '-'}</td>
      <td class="px-4 py-3 font-medium">${row.name || '-'}</td>
      <td class="px-4 py-3">${row.email || '-'}</td>
      <td class="px-4 py-3">${row.company || '-'}</td>
      <td class="px-4 py-3">${row.division || '-'}</td>
      <td class="px-4 py-3">${row.career_stage || '-'}</td>
      <td class="px-4 py-3">${row.phase || '-'}</td>
      <td class="px-4 py-3 font-semibold">${row.progress || 0}%</td>
      <td class="px-4 py-3 text-right">
        <a href="/manager/new-hires/${row.id}" class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700">
          <i class="fas fa-eye"></i> Detail
        </a>
      </td>
    </tr>
  `).join('');
}

loadRows();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/manager-new-hires/index.blade.php ENDPATH**/ ?>