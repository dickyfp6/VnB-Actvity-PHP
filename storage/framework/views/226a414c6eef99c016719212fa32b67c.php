
<?php $__env->startSection('title','Manager - Approval Request'); ?>
<?php $__env->startSection('page_title','Manager Approval Request'); ?>
<?php $__env->startSection('page_subtitle','Review dan setujui permintaan yang masuk dari employee dalam satu dashboard.'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
  <!-- Tab Navigation -->
  <div class="flex gap-2 border-b border-gray-200">
    <button id="tab-vnb-plans" onclick="switchTab('vnb_plans')" class="inline-flex items-center gap-2 border-b-2 border-green-800 px-4 py-2 font-medium text-[#144600] transition-colors rounded-none">
      <i class="fas fa-file-contract mr-1"></i> VnB Plan
      <span id="vnb-plan-badge" class="inline-flex items-center justify-center ml-1 w-4 h-4 text-xs font-bold text-white rounded-full" style="background-color: white; display: none; font-size: 9px;">0</span>
    </button>
    <button id="tab-vnb-activities" onclick="switchTab('vnb_activities')" class="inline-flex items-center gap-2 border-b-2 border-transparent px-4 py-2 font-medium text-gray-500 transition-colors hover:border-gray-300 hover:text-gray-700 rounded-none">
      <i class="fas fa-tasks mr-1"></i> VnB Activity
      <span id="vnb-activity-badge" class="inline-flex items-center justify-center ml-1 w-4 h-4 text-xs font-bold text-white rounded-full" style="background-color: white; display: none; font-size: 9px;">0</span>
    </button>
  </div>

  <!-- VnB Plans Section -->
  <div id="section-vnb-plans" class="table-container">
    <div class="overflow-x-auto">
      <table class="table-modern" style="width: max-content; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-sort-key="employee_name">Employee</th>
            <th data-sort-key="employee_number">NIP</th>
            <th data-sort-key="company">Perusahaan</th>
            <th data-sort-key="title">Judul</th>
            <th data-sort-key="submitted_at_date">Tanggal Ajuan</th>
            <th data-sort-key="submitted_at_time">Jam Ajuan</th>
            <th class="text-center" data-sortable="false">Aksi</th>
          </tr>
        </thead>
        <tbody id="vnb-plans-body" style="white-space: nowrap;">
          <tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- VnB Activities Section (hidden by default) -->
  <div id="section-vnb-activities" class="table-container hidden">
    <div class="overflow-x-auto">
      <table class="table-modern" style="width: max-content; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-sort-key="employee_name">Employee</th>
            <th data-sort-key="employee_number">NIP</th>
            <th data-sort-key="company">Perusahaan</th>
            <th data-sort-key="title">Judul Aktivitas</th>
            <th data-sort-key="phase">Fase</th>
            <th data-sort-key="submitted_at_date">Tanggal Ajuan</th>
            <th data-sort-key="submitted_at_time">Jam Ajuan</th>
            <th class="text-center" data-sortable="false">Aksi</th>
          </tr>
        </thead>
        <tbody id="vnb-activities-body" style="white-space: nowrap;">
          <tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let planRows = [];
let activityRows = [];
let currentTab = 'vnb_plans';

function switchTab(tab) {
  currentTab = tab;

  const planTab = document.getElementById('tab-vnb-plans');
  const activityTab = document.getElementById('tab-vnb-activities');

  if (planTab) {
    planTab.classList.toggle('border-green-800', tab === 'vnb_plans');
    planTab.classList.toggle('border-transparent', tab !== 'vnb_plans');
    planTab.classList.toggle('text-[#144600]', tab === 'vnb_plans');
    planTab.classList.toggle('text-gray-500', tab !== 'vnb_plans');
  }

  if (activityTab) {
    activityTab.classList.toggle('border-green-800', tab === 'vnb_activities');
    activityTab.classList.toggle('border-transparent', tab !== 'vnb_activities');
    activityTab.classList.toggle('text-[#144600]', tab === 'vnb_activities');
    activityTab.classList.toggle('text-gray-500', tab !== 'vnb_activities');
  }
  
  // Update visibility
  document.getElementById('section-vnb-plans').classList.toggle('hidden', tab !== 'vnb_plans');
  document.getElementById('section-vnb-activities').classList.toggle('hidden', tab !== 'vnb_activities');
}

async function loadRequests() {
  const vnbPlansBody = document.getElementById('vnb-plans-body');
  const vnbActivitiesBody = document.getElementById('vnb-activities-body');
  vnbPlansBody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
  vnbActivitiesBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

  const res = await apiGet('/api/manager/approval-requests');
  if (!(res && res.success === true)) {
    vnbPlansBody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500">Gagal memuat approval request</td></tr>';
    vnbActivitiesBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Gagal memuat approval request</td></tr>';
    return;
  }

  // Backend currently returns `my_approvals` and `monitoring` arrays.
  // `my_approvals` contains mixed types with `type` === 'planning' or 'activity'.
  const myApprovals = res.data?.my_approvals || [];
  const monitoring = res.data?.monitoring || [];

  // Split into planning vs activity lists for the UI
  planRows = myApprovals.filter(r => r.type === 'planning');
  activityRows = myApprovals.filter(r => r.type === 'activity');

  // Calculate summary counts
  const planCount = planRows.length;
  const activityCount = activityRows.length;

  // Update badges
  const planBadge = document.getElementById('vnb-plan-badge');
  if (planBadge) {
    planBadge.textContent = planCount;
    planBadge.style.display = planCount > 0 ? 'inline-flex' : 'none';
    planBadge.style.backgroundColor = planCount > 0 ? '#dc2626' : 'white';
  }
  
  const activityBadge = document.getElementById('vnb-activity-badge');
  if (activityBadge) {
    activityBadge.textContent = activityCount;
    activityBadge.style.display = activityCount > 0 ? 'inline-flex' : 'none';
    activityBadge.style.backgroundColor = activityCount > 0 ? '#dc2626' : 'white';
  }

  renderVnbPlans();
  renderVnbActivities();
}

function renderVnbPlans() {
  const tbody = document.getElementById('vnb-plans-body');
  if (!planRows.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Tidak ada approval request</td></tr>';
    return;
  }

  const formatDateTime = (dateTime) => {
    if (!dateTime) return { date: '-', time: '-' };
    const dt = new Date(dateTime);
    if (isNaN(dt.getTime())) return { date: '-', time: '-' };
    const date = dt.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' });
    const hours = String(dt.getHours()).padStart(2, '0');
    const minutes = String(dt.getMinutes()).padStart(2, '0');
    const seconds = String(dt.getSeconds()).padStart(2, '0');
    const time = `${hours}:${minutes}:${seconds}`;
    return { date, time };
  };

  tbody.innerHTML = planRows.map(row => {
    const { date, time } = formatDateTime(row.submitted_at);
    return `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3" data-column-key="employee_name">
        <a href="/employees/${row.employee_id}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">
          ${row.employee_name || '-'}
        </a>
      </td>
      <td class="px-4 py-3" data-column-key="employee_number">${row.employee_number || '-'}</td>
      <td class="px-4 py-3" data-column-key="company">${row.company || '-'}</td>
      <td class="px-4 py-3" data-column-key="title">${row.title || '-'}</td>
      <td class="px-4 py-3" data-column-key="submitted_at_date">${date}</td>
      <td class="px-4 py-3" data-column-key="submitted_at_time">${time}</td>
      <td class="px-4 py-3 flex justify-center">
        <a href="/employees/${row.employee_id}" class="inline-flex items-center gap-2 text-xs px-4 py-2 rounded-lg font-semibold transition-all duration-200" style="background-color: #144600; color: white; border: none;" onmouseover="this.style.backgroundColor='#0d2f00'; this.style.boxShadow='0 4px 12px rgba(20, 70, 0, 0.3)';" onmouseout="this.style.backgroundColor='#144600'; this.style.boxShadow='none';" title="Lihat detail">
          <i class="fas fa-eye"></i> Review
        </a>
      </td>
    </tr>
  `;
  }).join('');
}

function renderVnbActivities() {
  const tbody = document.getElementById('vnb-activities-body');
  if (!activityRows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Tidak ada approval request</td></tr>';
    return;
  }

  const formatDateTime = (dateTime) => {
    if (!dateTime) return { date: '-', time: '-' };
    const dt = new Date(dateTime);
    if (isNaN(dt.getTime())) return { date: '-', time: '-' };
    const date = dt.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' });
    const hours = String(dt.getHours()).padStart(2, '0');
    const minutes = String(dt.getMinutes()).padStart(2, '0');
    const seconds = String(dt.getSeconds()).padStart(2, '0');
    const time = `${hours}:${minutes}:${seconds}`;
    return { date, time };
  };

  tbody.innerHTML = activityRows.map(row => {
    const { date, time } = formatDateTime(row.submitted_at);
    return `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3" data-column-key="employee_name">
        <a href="/employees/${row.employee_id}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">
          ${row.employee_name || '-'}
        </a>
      </td>
      <td class="px-4 py-3" data-column-key="employee_number">${row.employee_number || '-'}</td>
      <td class="px-4 py-3" data-column-key="company">${row.company || '-'}</td>
      <td class="px-4 py-3" data-column-key="title">${row.title || '-'}</td>
      <td class="px-4 py-3" data-column-key="phase">${row.phase || '-'}</td>
      <td class="px-4 py-3" data-column-key="submitted_at_date">${date}</td>
      <td class="px-4 py-3" data-column-key="submitted_at_time">${time}</td>
      <td class="px-4 py-3 flex justify-center">
        <a href="/employees/${row.employee_id}" class="inline-flex items-center gap-2 text-xs px-4 py-2 rounded-lg font-semibold transition-all duration-200" style="background-color: #144600; color: white; border: none;" onmouseover="this.style.backgroundColor='#0d2f00'; this.style.boxShadow='0 4px 12px rgba(20, 70, 0, 0.3)';" onmouseout="this.style.backgroundColor='#144600'; this.style.boxShadow='none';" title="Lihat detail">
          <i class="fas fa-eye"></i> Review
        </a>
      </td>
    </tr>
  `;
  }).join('');
}

// Ensure requests load after DOM is ready. If already interactive/complete, call immediately.
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  loadRequests();
} else {
  document.addEventListener('DOMContentLoaded', loadRequests);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/manager-approval/index.blade.php ENDPATH**/ ?>