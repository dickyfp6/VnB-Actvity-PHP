
<?php $__env->startSection('title','Manager - Approval Request'); ?>
<?php $__env->startSection('page_title','Manager Approval Request'); ?>
<?php $__env->startSection('page_subtitle','Review dan setujui permintaan yang masuk dari employee dalam satu dashboard.'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
  <!-- Tab Navigation -->
  <div class="flex gap-2 border-b border-gray-200">
    <button id="tab-my-approvals" onclick="switchTab('my_approvals')" class="px-4 py-2 font-medium transition-colors" style="color: #144600; border-bottom: 2px solid #144600;">
      <i class="fas fa-check-circle mr-1"></i> Perlu Approval Saya
      <span id="manager-approval-page-badge" class="inline-flex items-center justify-center ml-1 w-4 h-4 text-xs font-bold text-white rounded-full" style="background-color: white; display: none; font-size: 9px;">0</span>
    </button>
    <button id="tab-monitoring" onclick="switchTab('monitoring')" class="px-4 py-2 font-medium transition-colors text-gray-500 hover:text-gray-700">
      <i class="fas fa-eye mr-1"></i> Pantau (Monitoring)
      <span id="manager-monitoring-badge" class="inline-flex items-center justify-center ml-1 w-4 h-4 text-xs font-bold text-white rounded-full" style="background-color: white; display: none; font-size: 9px;">0</span>
    </button>
  </div>

  <!-- Summary Box -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="summary-box">
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Pending Planning</div>
      <div id="planning-count" class="text-2xl font-bold text-gray-800">0</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Pending Activity</div>
      <div id="activity-count" class="text-2xl font-bold text-gray-800">0</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Total Approval Request</div>
      <div id="total-count" class="text-2xl font-bold text-gray-800">0</div>
    </div>
  </div>

  <!-- My Approvals Section -->
  <div id="section-my-approvals" class="table-container">
    <div class="overflow-x-auto">
      <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-sort-key="type">Jenis</th>
            <th data-sort-key="employee_name">Employee</th>
            <th data-sort-key="employee_number">NIP</th>
            <th data-sort-key="company">Perusahaan</th>
            <th data-sort-key="title">Judul</th>
            <th data-sort-key="phase">Tahap</th>
            <th data-sort-key="submitted_at">Waktu Submit</th>
            <th class="text-right" data-sortable="false">Aksi</th>
          </tr>
        </thead>
        <tbody id="my-approvals-body" style="white-space: nowrap;">
          <tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Monitoring Section (hidden by default) -->
  <div id="section-monitoring" class="table-container hidden">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
      <p class="text-sm text-blue-800"><i class="fas fa-info-circle mr-1"></i> Bagian ini menampilkan approval yang perlu manager lain. Anda bisa memantau progress namun tidak bisa mengambil aksi.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-sort-key="type">Jenis</th>
            <th data-sort-key="employee_name">Employee</th>
            <th data-sort-key="employee_number">NIP</th>
            <th data-sort-key="company">Perusahaan</th>
            <th data-sort-key="title">Judul</th>
            <th data-sort-key="phase">Tahap</th>
            <th data-sort-key="submitted_at">Waktu Submit</th>
            <th class="text-right" data-sortable="false">Aksi</th>
          </tr>
        </thead>
        <tbody id="monitoring-body" style="white-space: nowrap;">
          <tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let myApprovalsRows = [];
let monitoringRows = [];
let currentTab = 'my_approvals';

function switchTab(tab) {
  currentTab = tab;
  
  // Update tab buttons
  document.getElementById('tab-my-approvals').style.color = tab === 'my_approvals' ? '#144600' : '#999999';
  document.getElementById('tab-my-approvals').style.borderBottom = tab === 'my_approvals' ? '2px solid #144600' : 'none';
  document.getElementById('tab-monitoring').style.color = tab === 'monitoring' ? '#144600' : '#999999';
  document.getElementById('tab-monitoring').style.borderBottom = tab === 'monitoring' ? '2px solid #144600' : 'none';
  
  // Update visibility
  document.getElementById('section-my-approvals').classList.toggle('hidden', tab !== 'my_approvals');
  document.getElementById('section-monitoring').classList.toggle('hidden', tab !== 'monitoring');
}

async function loadRequests() {
  const myApprovalsBody = document.getElementById('my-approvals-body');
  const monitoringBody = document.getElementById('monitoring-body');
  myApprovalsBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
  monitoringBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

  const res = await apiGet('/api/manager/approval-requests');
  if (!(res && res.success === true)) {
    myApprovalsBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Gagal memuat approval request</td></tr>';
    monitoringBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Gagal memuat approval request</td></tr>';
    return;
  }

  myApprovalsRows = res.data?.my_approvals || [];
  monitoringRows = res.data?.monitoring || [];
  
  // Calculate summary counts from my_approvals only (badge should count only owned)
  const planningCount = myApprovalsRows.filter(r => r.type === 'planning').length;
  const activityCount = myApprovalsRows.filter(r => r.type === 'activity').length;
  const totalCount = myApprovalsRows.length;
  
  document.getElementById('planning-count').textContent = planningCount;
  document.getElementById('activity-count').textContent = activityCount;
  document.getElementById('total-count').textContent = totalCount;

  // Update badges with dynamic color logic
  const badge = document.getElementById('manager-approval-page-badge');
  if (badge) {
    badge.textContent = totalCount;
    badge.style.display = totalCount > 0 || monitoringRows.length > 0 ? 'inline-flex' : 'none';
    // Color: white badge if 0, red badge if >0
    badge.style.backgroundColor = totalCount > 0 ? '#dc2626' : 'white';
    badge.style.color = 'white';
  }
  
  const monitoringBadge = document.getElementById('manager-monitoring-badge');
  if (monitoringBadge) {
    monitoringBadge.textContent = monitoringRows.length;
    monitoringBadge.style.display = monitoringRows.length > 0 ? 'inline-flex' : 'none';
    // Color: white badge if 0, red badge if >0
    monitoringBadge.style.backgroundColor = monitoringRows.length > 0 ? '#dc2626' : 'white';
    monitoringBadge.style.color = 'white';
  }

  renderMyApprovals();
  renderMonitoring();
}

function renderMyApprovals() {
  const tbody = document.getElementById('my-approvals-body');
  if (!myApprovalsRows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Tidak ada approval request</td></tr>';
    return;
  }

  tbody.innerHTML = myApprovalsRows.map(row => `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3" data-column-key="type">
        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${row.type === 'planning' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'}">
          ${row.type === 'planning' ? 'Planning' : 'Activity'}
        </span>
      </td>
      <td class="px-4 py-3" data-column-key="employee_name">
        <a href="/manager/employees/${row.employee_id}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">
          ${row.employee_name || '-'}
        </a>
      </td>
      <td class="px-4 py-3" data-column-key="employee_number">${row.employee_number || '-'}</td>
      <td class="px-4 py-3" data-column-key="company">${row.company || '-'}</td>
      <td class="px-4 py-3" data-column-key="title">${row.title || '-'}</td>
      <td class="px-4 py-3" data-column-key="phase">${row.phase || '-'}</td>
      <td class="px-4 py-3" data-column-key="submitted_at">${row.submitted_at || '-'}</td>
      <td class="px-4 py-3 text-right">
        <a href="/manager/employees/${row.employee_id}" class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700">
          <i class="fas fa-arrow-right"></i> Lihat Detail
        </a>
      </td>
    </tr>
  `).join('');
}

function renderMonitoring() {
  const tbody = document.getElementById('monitoring-body');
  if (!monitoringRows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Tidak ada approval yang sedang dipantau</td></tr>';
    return;
  }

  tbody.innerHTML = monitoringRows.map(row => `
    <tr class="hover:bg-gray-50 opacity-75">
      <td class="px-4 py-3" data-column-key="type">
        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${row.type === 'planning' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'}">
          ${row.type === 'planning' ? 'Planning' : 'Activity'}
        </span>
      </td>
      <td class="px-4 py-3" data-column-key="employee_name">
        <a href="/manager/employees/${row.employee_id}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">
          ${row.employee_name || '-'}
        </a>
      </td>
      <td class="px-4 py-3" data-column-key="employee_number">${row.employee_number || '-'}</td>
      <td class="px-4 py-3" data-column-key="company">${row.company || '-'}</td>
      <td class="px-4 py-3" data-column-key="title">${row.title || '-'}</td>
      <td class="px-4 py-3" data-column-key="phase">${row.phase || '-'}</td>
      <td class="px-4 py-3" data-column-key="submitted_at">${row.submitted_at || '-'}</td>
      <td class="px-4 py-3 text-right">
        <a href="/manager/employees/${row.employee_id}" class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700">
          <i class="fas fa-eye"></i> Pantau
        </a>
      </td>
    </tr>
  `).join('');
}

loadRequests();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views\manager-approval\index.blade.php ENDPATH**/ ?>