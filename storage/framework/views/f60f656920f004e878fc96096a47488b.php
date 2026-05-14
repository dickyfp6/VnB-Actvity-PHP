
<?php $__env->startSection('title','Manager - Detail Employee'); ?>
<?php $__env->startSection('page_title','VnB Plan Review'); ?>
<?php $__env->startSection('page_subtitle','Review rencana pengembangan employee per fase dan lihat riwayatnya.'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <!-- Header Section -->
  <div class="card-glass rounded-xl p-6 md:p-8">
    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6">
      <div class="flex-1">
        <p class="text-gray-600 mb-4">Review rencana pengembangan karyawan per fase</p>
      </div>
      <div class="flex gap-2 flex-shrink-0">
        <a id="planning-history-link" href="#" class="btn-secondary flex items-center gap-2 hover:bg-gray-50 hidden">
          <i class="fas fa-history"></i> History
        </a>
        <a href="/manager/employees" class="btn-secondary flex items-center gap-2 hover:bg-gray-50">
          <i class="fas fa-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>

  <!-- Employee Profile Card -->
  <div class="card-glass rounded-xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm" id="profile-box">
      <div class="text-gray-500">Memuat profil...</div>
    </div>
  </div>

  <!-- Progress Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Progress Bar Card -->
    <div class="card-glass rounded-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-sm font-semibold text-gray-900">Progress VnB</h3>
          <p class="text-xs text-gray-500 mt-1">Kemajuan rencana aktivitas</p>
        </div>
        <span id="progress-label" class="text-2xl font-bold text-green-600">0%</span>
      </div>
      <div class="w-full bg-gray-200/50 h-3 rounded-full overflow-hidden">
        <div id="progress-bar" class="h-3 bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-500" style="width:0%;"></div>
      </div>
      <div id="phase-label" class="text-xs text-gray-600 mt-3">Fase: -</div>
    </div>

    <!-- Planning Status Card -->
    <div class="card-glass rounded-xl p-6">
      <h3 class="text-sm font-semibold text-gray-900 mb-4">Planning Status</h3>
      <div id="phase-status-list" class="space-y-2"></div>
    </div>
  </div>

  <!-- Planning Section -->
  <div id="planning-table-box" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-blue-500/10 to-blue-600/10 border-b border-gray-200/50">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-gray-900">PLANNING APPROVAL</h2>
          <p class="text-sm text-gray-600 mt-1">Persetujuan rencana pengembangan karyawan</p>
        </div>
        <div class="flex gap-2">
          <button id="approve-all-btn" onclick="submitApproveAll()" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="fas fa-check"></i> Setujui Semua
          </button>
          <button id="batch-submit-btn-header" onclick="submitBatchReview()" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition flex items-center gap-2" style="background-color: #9ca3af; opacity: 0.5; cursor: not-allowed;" disabled>
            <i class="fas fa-save"></i> Simpan
          </button>
        </div>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern w-full">
        <thead>
          <tr>
            <th class="w-1/6">Behaviour</th>
            <th class="w-1/4">Integrasi Pengukuran</th>
            <th class="w-1/3">Rencana Aktivitas</th>
            <th class="w-1/6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="planning-body">
          <tr><td colspan="4" class="text-center py-8 text-gray-400">Memuat planning...</td></tr>
        </tbody>
      </table>
    </div>
    <div id="batch-action-bar" class="hidden bg-gray-50 border-t border-gray-200 p-4 flex justify-between items-center">
      <div>
        <p class="text-base font-bold text-gray-900">Review Menunggu Konfirmasi</p>
        <p class="text-xs text-gray-600 mt-1">Pilihan Anda sudah dicatat sementara. Klik kirim untuk menyimpan semua.</p>
      </div>
    </div>
  </div>

  <!-- Fase 1 Section -->
  <div id="phase-1-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-blue-500/10 to-blue-600/10 border-b border-gray-200/50">
      <h2 class="text-lg font-semibold text-gray-900">FASE 1</h2>
      <p class="text-sm text-gray-600 mt-1">Bulan ke-1 hingga ke-3 | Orientasi & Onboarding</p>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern w-full">
        <thead>
          <tr>
            <th class="w-1/6">Behaviour</th>
            <th class="w-1/4">Integrasi Pengukuran</th>
            <th class="w-1/3">Rencana Aktivitas</th>
            <th class="w-1/6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="phase-1-body">
          <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Fase 2 Section -->
  <div id="phase-2-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-amber-500/10 to-amber-600/10 border-b border-gray-200/50">
      <h2 class="text-lg font-semibold text-gray-900">FASE 2</h2>
      <p class="text-sm text-gray-600 mt-1">Bulan ke-4 hingga ke-6 | Pengembangan & Adaptasi</p>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern w-full">
        <thead>
          <tr>
            <th class="w-1/6">Behaviour</th>
            <th class="w-1/4">Integrasi Pengukuran</th>
            <th class="w-1/3">Rencana Aktivitas</th>
            <th class="w-1/6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="phase-2-body">
          <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Fase 3 Section -->
  <div id="phase-3-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-green-500/10 to-green-600/10 border-b border-gray-200/50">
      <h2 class="text-lg font-semibold text-gray-900">FASE 3</h2>
      <p class="text-sm text-gray-600 mt-1">Bulan ke-7 hingga ke-12 | Konsolidasi & Kemandirian</p>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern w-full">
        <thead>
          <tr>
            <th class="w-1/6">Behaviour</th>
            <th class="w-1/4">Integrasi Pengukuran</th>
            <th class="w-1/3">Rencana Aktivitas</th>
            <th class="w-1/6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="phase-3-body">
          <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Approve All Confirmation Modal -->
  <div id="approve-all-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
      <div class="flex items-start gap-3 mb-4">
        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
          <i class="fas fa-check text-2xl text-blue-600"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900">Setujui Semua Rencana?</h3>
          <p class="text-sm text-gray-500 mt-1">Anda yakin akan menyetujui semua rencana aktivitas?</p>
        </div>
      </div>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
        <p class="text-sm text-blue-800">
          <i class="fas fa-info-circle text-blue-600 mr-2"></i>
          Setelah disetujui, tidak bisa diubah kecuali ada revisi baru.
        </p>
      </div>
      <div class="flex gap-3 justify-end">
        <button onclick="closeApproveAllModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium">Batal</button>
        <button onclick="confirmApproveAll()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 font-medium flex items-center gap-2">
          <i class="fas fa-check"></i> Setujui
        </button>
      </div>
    </div>
  </div>

  <!-- Revision Modal -->
  <div id="revision-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
      <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Catatan Revisi</h3>
      <div class="mb-4 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div id="modal-behaviour-val" class="font-bold text-base mb-3 text-gray-900"></div>
        <div class="font-semibold text-xs text-gray-500 uppercase mb-2">Integrasi Pengukuran</div>
        <div id="modal-integrasi-val" class="ml-2 mb-3 whitespace-pre-wrap">-</div>
        <div class="font-semibold text-xs text-gray-500 uppercase mb-2">Rencana Aktivitas</div>
        <div id="modal-rencana-val" class="ml-2 whitespace-pre-wrap">-</div>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi</label>
        <textarea id="modal-revision-notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Masukkan catatan revisi di sini..."></textarea>
      </div>
      <div class="flex justify-between items-center mt-6">
        <button id="modal-cancel-revision-btn" onclick="cancelRevisionFromModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium hidden">Batalkan</button>
        <div class="flex gap-2 flex-1 justify-end">
          <button onclick="closeRevisionModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium">Batal</button>
          <button onclick="submitRevisionModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 font-medium">Kirim Revisi</button>
        </div>
      </div>
    </div>
  </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
const employeeId = <?php echo json_encode($employeeId, 15, 512) ?>;
let detailData = null;
let selectedTab = null;
let pendingDecisions = {}; // Track local manager decisions { rowKey: { item_id, action: 'approve'/'revise', notes: '...' } }
let totalPlanningSubRows = 0;

function savePendingDecisionsLocal() {
  if (detailData && detailData.plan && detailData.plan.id) {
    localStorage.setItem(`vnb_batch_decisions_${detailData.plan.id}`, JSON.stringify(pendingDecisions));
  }
}

function clearPendingDecisionsLocal() {
  if (detailData && detailData.plan && detailData.plan.id) {
    localStorage.removeItem(`vnb_batch_decisions_${detailData.plan.id}`);
  }
}

function toggleBatchButtonBar() {
  const bar = document.getElementById('batch-action-bar');
  const submitBtn = document.getElementById('batch-submit-btn-header');
  if (!bar || !submitBtn) return;
  
  const hasPendingDecisions = Object.keys(pendingDecisions).length > 0;
  
  if (hasPendingDecisions) {
    bar.classList.remove('hidden');
    // Enable Simpan button
    submitBtn.disabled = false;
    submitBtn.style.backgroundColor = '#1e3a8a';
    submitBtn.style.opacity = '1';
    submitBtn.style.cursor = 'pointer';
  } else {
    bar.classList.add('hidden');
    // Disable Simpan button
    submitBtn.disabled = true;
    submitBtn.style.backgroundColor = '#9ca3af';
    submitBtn.style.opacity = '0.5';
    submitBtn.style.cursor = 'not-allowed';
  }
}

function cancelPendingDecision(itemId, subIdx) {
  const rowKey = itemId + '_' + subIdx;
  delete pendingDecisions[rowKey];
  savePendingDecisionsLocal();
  // Re-render only planning table if it's the active view to restore button state
  if (detailData && detailData.items) {
      renderPlanningTable(detailData.items);
  }
  toggleBatchButtonBar();
}

function toLabelStatus(status) {
  const map = {
    waiting_approval: 'Waiting Approval',
    revision_required: 'Perlu Revisi',
    completed: 'Completed',
    draft: 'Draft'
  };
  return map[status] || status || '-';
}

function resolvePhaseNumberFromItem(item) {
  const metrics = Array.isArray(item.behavior_metrics) ? item.behavior_metrics : [];
  const metricPhase = metrics.find(v => typeof v === 'string' && /^phase_[1-3]$/i.test(v));
  if (metricPhase) {
    return Number((metricPhase.match(/phase_(\d)/i) || [])[1] || 1);
  }

  const title = String(item.activity_title || '');
  const titleMatch = title.match(/phase\s*(\d)/i);
  if (titleMatch) {
    return Number(titleMatch[1]);
  }

  return 1;
}

function normalizeCurrentStage(detail) {
  const phaseLabel = String(detail.phase || '').toLowerCase();
  if (phaseLabel === 'planning') return 'planning';

  const num = Number(detail.plan?.phase_number || 1);
  if (num >= 1 && num <= 3) {
    return `phase_${num}`;
  }
  return 'planning';
}

function setSelectedTab(tabKey) {
  // Deprecated: Tab-based navigation removed. All phases now display simultaneously.
  // This function is kept for backward compatibility.
}

function updateSubmitButtonState(detail) {
  const headerBtn = document.getElementById('batch-submit-btn-header');
  const tooltip = document.getElementById('batch-submit-tooltip');
  if (!headerBtn) return;
  
  // Check if there are any items waiting for approval
  const items = detail.items || [];
  const hasWaitingItems = items.some(item => item.submission_status === 'waiting_approval');
  
  if (hasWaitingItems) {
    // Disable button
    headerBtn.disabled = true;
    headerBtn.classList.add('opacity-50', 'cursor-not-allowed');
    headerBtn.classList.remove('cursor-pointer', 'hover:shadow-lg');
    headerBtn.style.backgroundColor = '#9CA3AF';
    if (tooltip) tooltip.classList.remove('hidden');
  } else {
    // Enable button
    headerBtn.disabled = false;
    headerBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    headerBtn.classList.add('cursor-pointer', 'hover:shadow-lg');
    headerBtn.style.backgroundColor = '#144600';
    if (tooltip) tooltip.classList.add('hidden');
  }
}

function renderPhaseOverview(detail) {
  const currentStage = normalizeCurrentStage(detail);
  const planningWaiting = !!detail.approval_requests?.planning_waiting;
  const items = detail.items || [];

  const waitingByPhase = { phase_1: 0, phase_2: 0, phase_3: 0 };
  items.forEach(item => {
    if (item.submission_status !== 'waiting_approval') return;
    const p = resolvePhaseNumberFromItem(item);
    const key = `phase_${p}`;
    if (Object.prototype.hasOwnProperty.call(waitingByPhase, key)) {
      waitingByPhase[key] += 1;
    }
  });

  const setBadge = (id, count) => {
    const el = document.getElementById(id);
    if (!el) return;
    if (count > 0) {
      el.textContent = count > 99 ? '99+' : String(count);
      el.classList.remove('hidden');
    } else {
      el.classList.add('hidden');
    }
  };

  setBadge('badge-planning', planningWaiting ? 1 : 0);
  setBadge('badge-phase-1', waitingByPhase.phase_1 || 0);
  setBadge('badge-phase-2', waitingByPhase.phase_2 || 0);
  setBadge('badge-phase-3', waitingByPhase.phase_3 || 0);

  // Check if all items are reviewed
  updateSubmitButtonState(detail);

  const statusList = document.getElementById('phase-status-list');
  if (!statusList) return;

  const currentNum = currentStage === 'planning'
    ? 0
    : Number((currentStage.match(/phase_(\d)/) || [])[1] || 1);
  const activePhase = selectedTab || currentStage;

  if (activePhase === 'planning') {
    const planningStatusText = planningWaiting
      ? 'Planning menunggu approval manager.'
      : (currentStage === 'planning' ? 'Planning masih draft / belum diajukan.' : 'Planning sudah disetujui manager.');
    statusList.innerHTML = `<div class="border border-amber-200 bg-amber-50 rounded-lg px-3 py-2">${planningStatusText}</div>`;
    return;
  }

  const tabNum = Number((activePhase.match(/phase_(\d)/) || [])[1] || 1);
  let msg = '';
  if (currentStage === 'planning') {
    msg = `Planning belum disetujui. Fase ${tabNum} belum dimulai.`;
  } else if (tabNum < currentNum) {
    msg = `Fase ${tabNum} sudah selesai.`;
  } else if (tabNum === currentNum) {
    msg = `Fase ${tabNum} sedang berjalan.`;
  } else {
    msg = `Fase ${tabNum} belum dimulai.`;
  }
  statusList.innerHTML = `<div class="border border-gray-200 bg-white rounded-lg px-3 py-2">${msg}</div>`;
}

function renderPhaseContent(detail) {
  // Hide all sections first
  ['planning-table-box', 'phase-1-section', 'phase-2-section', 'phase-3-section'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
  });

  const currentStage = normalizeCurrentStage(detail);
  const planningWaiting = !!detail.approval_requests?.planning_waiting;
  const items = detail.items || [];

  // Show planning table if in planning stage and waiting approval
  if (currentStage === 'planning' && planningWaiting) {
    document.getElementById('planning-table-box').classList.remove('hidden');
    renderPlanningTable(items);
  }

  // Always show phase sections regardless of current stage
  ['phase_1', 'phase_2', 'phase_3'].forEach((phaseKey, idx) => {
    const phaseNum = idx + 1;
    const phaseItems = items.filter(item => resolvePhaseNumberFromItem(item) === phaseNum);
    
    const sectionId = `phase-${phaseNum}-section`;
    const bodyId = `phase-${phaseNum}-body`;
    
    if (phaseItems.length > 0) {
      document.getElementById(sectionId).classList.remove('hidden');
      renderPhaseActivityTable(bodyId, phaseItems);
    }
  });
}

function renderPhaseActivityTable(bodyId, items) {
  const tbody = document.getElementById(bodyId);
  if (!items.length) {
    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>';
    return;
  }

  let html = '';
  
  items.forEach(item => {
    const behaviorMatch = (item.activity_title || '').match(/^([^-]+)/);
    const behavior = behaviorMatch ? behaviorMatch[1].trim() : (item.activity_title || '-');
    
    const integrations = (item.description || '-').split('|').map(s => s.trim()).filter(s => s);
    if (integrations.length === 0) {
      integrations.push('-');
    }
    
    const rencanaList = (item.deliverables || '').split('\n---\n').map(s => s.trim());
    
    // Create a row for each integration
    integrations.forEach((integration, idx) => {
      const waiting = item.submission_status === 'waiting_approval';
      let actionHtml = '';
      
      if (waiting) {
        actionHtml = `
          <button onclick="approveActivityRow(${item.id}, ${idx})" class="inline-flex items-center justify-center w-8 h-8 rounded bg-green-600 text-white hover:bg-green-700 transition mr-2 cursor-pointer shadow-sm" title="Approve">✓</button>
          <button onclick="reviseActivityRow(${item.id}, ${idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(integration).replace(/'/g, "%27")}', '${encodeURIComponent(rencanaList[idx] || '-').replace(/'/g, "%27")}')" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 text-white hover:bg-red-700 transition cursor-pointer shadow-sm" title="Revise">✕</button>
        `;
      } else {
        const statusLabel = toLabelStatus(item.submission_status);
        actionHtml = `<span class="text-xs text-gray-500">${statusLabel}</span>`;
      }
      
      html += `
        <tr class="hover:bg-gray-50 transition-colors">
          ${idx === 0 ? `<td class="px-4 py-3 font-medium">${behavior}</td>` : '<td class="px-4 py-3"></td>'}
          <td class="px-4 py-3"><span class="text-xs text-gray-700 whitespace-pre-wrap">${integration}</span></td>
          <td class="px-4 py-3 whitespace-pre-wrap">${rencanaList[idx] || '-'}</td>
          <td class="px-4 py-3 text-right">${actionHtml}</td>
        </tr>
      `;
    });
  });

  tbody.innerHTML = html;
}

async function loadDetail() {
  const res = await apiGet(`/api/manager/employees/${employeeId}`);
  if (!(res && res.success === true && res.data)) {
    showAlert(res?.message || 'Gagal memuat detail Employee', 'error');
    return;
  }

  detailData = res.data;
  renderDetail();
}

function renderDetail() {
  const e = detailData.employee || {};
  const profile = document.getElementById('profile-box');
  profile.innerHTML = `
    <div><div class="text-xs text-gray-500">NIP</div><div class="font-medium">${e.employee_number || '-'}</div></div>
    <div><div class="text-xs text-gray-500">Nama</div><div class="font-medium">${e.name || '-'}</div></div>
    <div><div class="text-xs text-gray-500">Email</div><div class="font-medium">${e.email || '-'}</div></div>
    <div><div class="text-xs text-gray-500">Perusahaan</div><div class="font-medium">${e.company || '-'}</div></div>
    <div><div class="text-xs text-gray-500">Divisi</div><div class="font-medium">${e.division || '-'}</div></div>
    <div><div class="text-xs text-gray-500">Career Stage</div><div class="font-medium">${e.level || '-'}</div></div>
  `;

  const progress = Number(detailData.progress || 0);
  document.getElementById('progress-label').textContent = `${progress}%`;
  document.getElementById('progress-bar').style.width = `${Math.max(0, Math.min(progress, 100))}%`;
  document.getElementById('phase-label').textContent = `Fase Saat Ini: ${detailData.phase || '-'}`;
  
  if (detailData?.plan?.id) {
    const saved = localStorage.getItem(`vnb_batch_decisions_${detailData.plan.id}`);
    if (saved) {
      try {
        pendingDecisions = JSON.parse(saved);
      } catch (e) {
        pendingDecisions = {};
      }
    } else {
      pendingDecisions = {};
    }
  }

  // Restore UI states from pending decisions (button colors, labels, etc.)
  toggleBatchButtonBar();

  renderPhaseOverview(detailData);
  renderPhaseContent(detailData);

  const planningHistoryLink = document.getElementById('planning-history-link');
  const showPlanningHistory = normalizeCurrentStage(detailData) !== 'planning';
  planningHistoryLink.classList.toggle('hidden', !showPlanningHistory);
  if (showPlanningHistory) {
    planningHistoryLink.href = `/manager/employees/${employeeId}/planning-history`;
  }

  const waitingCount = detailData.approval_requests?.activity_waiting_count || 0;
  document.getElementById('activity-waiting').textContent = `Pending approval: ${waitingCount}`;

  // NEW: Check manager authorization for Approve All button
  updateApproveAllButtonState();
}

// NEW: Update Approve All button state based on manager role
function updateApproveAllButtonState() {
  const approveAllBtn = document.getElementById('approve-all-btn');
  const submitBtn = document.getElementById('batch-submit-btn-header');
  if (!approveAllBtn || !submitBtn) return;

  const managerRole = detailData?.current_manager_role; // 'functional' | 'operational' | 'both' | null

  if (managerRole === 'operational') {
    // Disable buttons for operational manager
    approveAllBtn.disabled = true;
    approveAllBtn.style.opacity = '0.5';
    approveAllBtn.style.backgroundColor = '#9ca3af';
    approveAllBtn.style.cursor = 'not-allowed';
    approveAllBtn.style.pointerEvents = 'none';
    approveAllBtn.title = 'Hanya Manager Fungsional yang bisa menyetujui planning. Anda adalah Manager Operasional.';
    
    submitBtn.disabled = true;
    submitBtn.style.backgroundColor = '#9ca3af';
    submitBtn.style.opacity = '0.5';
    submitBtn.style.cursor = 'not-allowed';
    submitBtn.title = 'Hanya Manager Fungsional yang bisa menyetujui planning. Anda adalah Manager Operasional.';
  } else {
    // Enable buttons for functional or dual manager
    approveAllBtn.disabled = false;
    approveAllBtn.style.opacity = '1';
    approveAllBtn.style.backgroundColor = '#16a34a';
    approveAllBtn.style.cursor = 'pointer';
    approveAllBtn.style.pointerEvents = 'auto';
    approveAllBtn.title = '';
    
    // Simpan button state will be managed by toggleBatchButtonBar()
    submitBtn.style.cursor = 'pointer';
    submitBtn.title = '';
  }
}

function renderPlanningTable(items) {
  const tbody = document.getElementById('planning-body');
  if (!items.length) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada data planning</td></tr>';
    return;
  }

  // Convert month range to phase number
  function convertMonthRangeToPhase(monthRange) {
    // e.g., "1-3" -> 1, "4-6" -> 2, "7-12" -> 3
    if (!monthRange || monthRange === '-') return '-';
    
    // Handle ranges like "1-3", "4-6", "7-12"
    if (monthRange.includes('1-3') || monthRange.includes('1 - 3')) return '1';
    if (monthRange.includes('4-6') || monthRange.includes('4 - 6')) return '2';
    if (monthRange.includes('7-12') || monthRange.includes('7 - 12')) return '3';
    
    // Handle single month numbers
    const monthNum = parseInt(monthRange);
    if (!isNaN(monthNum)) {
      if (monthNum >= 1 && monthNum <= 3) return '1';
      if (monthNum >= 4 && monthNum <= 6) return '2';
      if (monthNum >= 7 && monthNum <= 12) return '3';
    }
    
    return monthRange || '-';
  }

  // Group items by behavior and create rows for each integration
  const groupedByBehavior = {};
  totalPlanningSubRows = 0;
  
  items.forEach(item => {
    // Extract behavior name from activity_title (e.g., "Empathy - Phase 1-3" -> "Empathy")
    const behaviorMatch = (item.activity_title || '').match(/^([^-]+)/);
    const behavior = behaviorMatch ? behaviorMatch[1].trim() : (item.activity_title || '-');
    
    // Extract phase from activity_title (e.g., "Phase 1-3" -> convert to 1)
    const phaseMatch = (item.activity_title || '').match(/phase\s+([\d\-]+)/i);
    const phaseRaw = phaseMatch ? phaseMatch[1] : '-';
    const phase = convertMonthRangeToPhase(phaseRaw);
    
    // Split integrations by pipe | delimiter
    const integrations = (item.description || '-').split('|').map(s => s.trim()).filter(s => s);
    if (integrations.length === 0) {
      integrations.push('-');
    }
    
    if (!groupedByBehavior[behavior]) {
      groupedByBehavior[behavior] = [];
    }
    
    const rencanaList = (item.deliverables || '').split('\n---\n').map(s => s.trim());
    
    // Create a row for each integration
    integrations.forEach((integration, idx) => {
      totalPlanningSubRows++;
      groupedByBehavior[behavior].push({
        ...item,
        extracted_phase: phase,
        integration_text: integration,
        rencana_text: rencanaList[idx] || '-',
        sub_idx: idx
      });
    });
  });

  let html = '';
  
  Object.entries(groupedByBehavior).forEach(([behavior, itemsInGroup]) => {
    itemsInGroup.forEach((item, idx) => {
      const showBehavior = idx === 0;
      
      const rowKey = item.id + '_' + item.sub_idx;
      const decision = pendingDecisions[rowKey];
      let actionHtml = '';
      let rowBgClass = '';
      
      if (decision) {
        if (decision.action === 'approve') {
          rowBgClass = 'bg-green-50';
          actionHtml = `
            <span class="text-xs font-semibold text-green-700 block mb-1">✓ Disetujui</span>
            <button onclick="cancelPendingDecision(${item.id}, ${item.sub_idx})" class="px-2 py-1 text-xs text-gray-600 bg-gray-200 hover:bg-gray-300 rounded transition cursor-pointer w-full text-center">Batalkan</button>
          `;
        } else if (decision.action === 'revise') {
          rowBgClass = 'bg-red-50';
          actionHtml = `
            <span class="text-xs font-semibold text-red-700 block mb-1">✕ Revisi</span>
            <div class="flex flex-col gap-1 items-center justify-center auto">
              <button onclick="editPendingDecision(${item.id}, ${item.sub_idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(item.integration_text).replace(/'/g, "%27")}', '${encodeURIComponent(item.rencana_text || '-').replace(/'/g, "%27")}')" class="px-2 py-1 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded transition cursor-pointer w-full text-center">Edit</button>
              <button onclick="cancelPendingDecision(${item.id}, ${item.sub_idx})" class="px-2 py-1 text-xs text-gray-600 bg-gray-200 hover:bg-gray-300 rounded transition cursor-pointer w-full text-center">Batalkan</button>
            </div>
          `;
        }
      } else {
        actionHtml = `
          <button onclick="approvePlanningRow(${item.id}, ${item.sub_idx})" class="inline-flex items-center justify-center w-8 h-8 rounded bg-green-600 text-white hover:bg-green-700 transition mr-2 cursor-pointer shadow-sm" title="Approve" style="font-size: 14px; font-weight: bold;">✓</button>
          <button onclick="revisePlanningRow(${item.id}, ${item.sub_idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(item.integration_text).replace(/'/g, "%27")}', '${encodeURIComponent(item.rencana_text || '-').replace(/'/g, "%27")}')" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 text-white hover:bg-red-700 transition cursor-pointer shadow-sm" title="Revise" style="font-size: 14px; font-weight: bold;">✕</button>
        `;
      }

      html += `
    <tr class="${rowBgClass} hover:bg-gray-50 transition-colors">
      ${showBehavior ? `<td class="px-4 py-3 align-top font-medium" style="vertical-align: top;">${behavior}</td>` : '<td class="px-4 py-3"></td>'}
      <td class="px-4 py-3 align-top"><span class="text-xs text-gray-700 whitespace-pre-wrap">${item.integration_text}</span></td>
      <td class="px-4 py-3 align-top whitespace-pre-wrap">${item.rencana_text || '-'}</td>
      <td class="px-4 py-3 align-top text-center">
        ${actionHtml}
      </td>
    </tr>
      `;
    });
  });

  tbody.innerHTML = html;
  toggleBatchButtonBar();
}

async function submitBatchReview() {
  const planId = detailData?.plan?.id;
  if (!planId) return;

  const pendingCount = Object.keys(pendingDecisions).length;
  if (pendingCount < totalPlanningSubRows) {
    showAlert('Harap berikan keputusan (Setujui atau Revisi) untuk seluruh rencana aktivitas terlebih dahulu.', 'error');
    return;
  }

  const groupedReviews = {};
  let validActionCount = 0;
  
  Object.values(pendingDecisions).forEach(data => {
    validActionCount++;
    if (!groupedReviews[data.item_id]) {
      groupedReviews[data.item_id] = { id: data.item_id, action: data.action, notes: [] };
    }
    // If ANY integration marks it as revise, the whole DB item is revised.
    if (data.action === 'revise') {
      groupedReviews[data.item_id].action = 'revise';
      if (data.notes) groupedReviews[data.item_id].notes.push(data.notes);
    }
  });

  if (validActionCount === 0) return;

  const reviews = Object.values(groupedReviews).map(g => ({
    id: g.id,
    action: g.action,
    notes: g.notes.join('\\n\\n') || null
  }));

  const btn = document.getElementById('batch-submit-btn-header');
  const orgHtml = btn.innerHTML;
  btn.innerHTML = 'Sedang Memproses...';
  btn.disabled = true;
  btn.classList.add('opacity-50', 'cursor-not-allowed');

  const res = await apiPost(`/api/manager/plans/${planId}/batch-review`, { reviews });
  
  btn.innerHTML = orgHtml;
  btn.disabled = false;
  btn.classList.remove('opacity-50', 'cursor-not-allowed');
  btn.classList.add('cursor-pointer');

  if (res && res.success) {
    pendingDecisions = {};
    clearPendingDecisionsLocal();
    toggleBatchButtonBar();
    showAlert(res.message || 'Review berhasil disimpan', 'success');
    loadDetail();
  } else {
    showAlert(res?.message || res?.error || 'Gagal menyimpan review', 'error');
  }
}

async function approvePlanningRow(itemId, subIdx) {
  const rowKey = itemId + '_' + subIdx;
  pendingDecisions[rowKey] = { item_id: itemId, action: 'approve' };
  savePendingDecisionsLocal();
  if (detailData && detailData.items) {
      renderPlanningTable(detailData.items);
  }
  toggleBatchButtonBar();
}

async function approveActivityRow(itemId, subIdx) {
  const rowKey = itemId + '_' + subIdx;
  pendingDecisions[rowKey] = { item_id: itemId, action: 'approve' };
  savePendingDecisionsLocal();
  
  // Re-render appropriate phase section
  if (detailData && detailData.items) {
    const item = detailData.items.find(i => i.id === itemId);
    if (item) {
      const phaseNum = resolvePhaseNumberFromItem(item);
      const phaseItems = detailData.items.filter(i => resolvePhaseNumberFromItem(i) === phaseNum);
      const bodyId = `phase-${phaseNum}-body`;
      renderPhaseActivityTable(bodyId, phaseItems);
    }
  }
  toggleBatchButtonBar();
}

async function reviseActivityRow(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
  const planId = detailData?.plan?.id;
  if (!planId) {
    showAlert('ID planning tidak ditemukan', 'error');
    return;
  }
  
  currentRevisionItemId = itemId;
  currentRevisionSubIdx = subIdx;
  
  document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
  document.getElementById('modal-integrasi-val').textContent = '- ' + decodeURIComponent(integrasiEnc);
  document.getElementById('modal-rencana-val').textContent = '- ' + decodeURIComponent(rencanaEnc);
  
  document.getElementById('modal-revision-notes').value = '';
  document.getElementById('modal-cancel-revision-btn').classList.add('hidden');
  document.getElementById('revision-modal').classList.remove('hidden');
}

let currentRevisionItemId = null;
let currentRevisionSubIdx = null;

function cancelRevisionFromModal() {
  if (currentRevisionItemId && currentRevisionSubIdx !== null) {
      cancelPendingDecision(currentRevisionItemId, currentRevisionSubIdx);
  }
  closeRevisionModal();
}

function editPendingDecision(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
  const rowKey = itemId + '_' + subIdx;
  const decision = pendingDecisions[rowKey];
  currentRevisionItemId = itemId;
  currentRevisionSubIdx = subIdx;
  
  document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
  document.getElementById('modal-integrasi-val').textContent = '- ' + decodeURIComponent(integrasiEnc);
  document.getElementById('modal-rencana-val').textContent = '- ' + decodeURIComponent(rencanaEnc);
  
  document.getElementById('modal-revision-notes').value = decision.notes || '';
  document.getElementById('modal-cancel-revision-btn').classList.remove('hidden');
  document.getElementById('revision-modal').classList.remove('hidden');
}

function revisePlanningRow(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
  const planId = detailData?.plan?.id;
  if (!planId) {
    showAlert('ID planning tidak ditemukan', 'error');
    return;
  }
  
  currentRevisionItemId = itemId;
  currentRevisionSubIdx = subIdx;
  
  document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
  document.getElementById('modal-integrasi-val').textContent = '- ' + decodeURIComponent(integrasiEnc);
  document.getElementById('modal-rencana-val').textContent = '- ' + decodeURIComponent(rencanaEnc);
  
  document.getElementById('modal-revision-notes').value = '';
  document.getElementById('modal-cancel-revision-btn').classList.add('hidden');
  document.getElementById('revision-modal').classList.remove('hidden');
}

function closeRevisionModal() {
  document.getElementById('revision-modal').classList.add('hidden');
  currentRevisionItemId = null;
  currentRevisionSubIdx = null;
}

async function submitRevisionModal() {
  if (!currentRevisionItemId || currentRevisionSubIdx === null) return;
  
  const revisionNotes = document.getElementById('modal-revision-notes').value.trim();
  if (!revisionNotes) {
    showAlert('Harap isi catatan revisi', 'error');
    return;
  }

  // Update local pending state
  const rowKey = currentRevisionItemId + '_' + currentRevisionSubIdx;
  pendingDecisions[rowKey] = { item_id: currentRevisionItemId, action: 'revise', notes: revisionNotes };
  savePendingDecisionsLocal();
  closeRevisionModal();
  
  // Re-render
  if (detailData && detailData.items) {
      renderPlanningTable(detailData.items);
  }
  toggleBatchButtonBar();
}

async function approveActivity(planItemId) {
  const res = await apiPost(`/api/vnb-activities/${planItemId}/approve`, {});
  if (res && res.success) {
    showAlert(res.message || 'Aktivitas di-approve');
    loadDetail();
  } else {
    showAlert(res?.message || res?.error || 'Gagal approve aktivitas', 'error');
  }
}

async function reviseActivity(planItemId) {
  const notes = (window.prompt('Masukkan catatan revisi untuk aktivitas ini:') || '').trim();
  if (!notes) return;
  const res = await apiPost(`/api/vnb-activities/${planItemId}/request-revision`, { revision_notes: notes });
  if (res && res.success) {
    showAlert(res.message || 'Revisi aktivitas dikirim');
    loadDetail();
  } else {
    showAlert(res?.message || res?.error || 'Gagal kirim revisi aktivitas', 'error');
  }
}

// NEW: Approve All Planning Items Immediately
function submitApproveAll() {
  const planId = detailData?.plan?.id;
  if (!planId) {
    showAlert('Plan ID tidak ditemukan', 'error');
    return;
  }
  document.getElementById('approve-all-modal').classList.remove('hidden');
}

function closeApproveAllModal() {
  document.getElementById('approve-all-modal').classList.add('hidden');
}

async function confirmApproveAll() {
  const planId = detailData?.plan?.id;
  if (!planId) {
    showAlert('Plan ID tidak ditemukan', 'error');
    return;
  }

  closeApproveAllModal();

  try {
    const res = await apiPost(`/api/vnb-plans/${planId}/approve-all`, {});
    
    if (res && res.success) {
      showAlert('Semua rencana aktivitas berhasil disetujui!', 'success');
      setTimeout(() => {
        loadDetail();
      }, 1000);
    } else {
      showAlert(res?.message || 'Gagal menyetujui semua rencana aktivitas', 'error');
    }
  } catch (err) {
    console.error('Error submitting approve all:', err);
    showAlert('Error: ' + err.message, 'error');
  }
}

loadDetail();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/manager-employees/detail.blade.php ENDPATH**/ ?>