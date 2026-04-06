@extends('layouts.app')
@section('title','Manager - Detail New Hire')
@section('content')
<div class="px-4 space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Detail New Hire</h1>
    <div class="flex gap-2">
      <a id="planning-history-link" href="#" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 hidden">Planning Approved & History</a>
      <a href="/manager/new-hires" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Kembali</a>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm" id="profile-box">
      <div class="text-gray-500">Memuat profil...</div>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-4">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-sm font-semibold text-gray-700">Progress VnB</h2>
      <span id="progress-label" class="text-sm font-semibold text-gray-700">0%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
      <div id="progress-bar" class="h-3 rounded-full" style="width:0%; background-color:#144600;"></div>
    </div>
    <div id="phase-label" class="text-xs text-gray-500 mt-2">Fase: -</div>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-4">
    <div id="phase-tabs" class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
      <button id="tab-planning" type="button" onclick="setSelectedTab('planning')" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-left cursor-pointer">
        <div class="flex items-center gap-2">
          <span class="font-semibold">Planning</span>
          <span id="badge-planning" class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 hidden">0</span>
        </div>
      </button>
      <button id="tab-phase-1" type="button" onclick="setSelectedTab('phase_1')" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-left cursor-pointer">
        <div class="flex items-center gap-2">
          <span class="font-semibold">Fase 1</span>
          <span id="badge-phase-1" class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 hidden">0</span>
        </div>
      </button>
      <button id="tab-phase-2" type="button" onclick="setSelectedTab('phase_2')" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-left cursor-pointer">
        <div class="flex items-center gap-2">
          <span class="font-semibold">Fase 2</span>
          <span id="badge-phase-2" class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 hidden">0</span>
        </div>
      </button>
      <button id="tab-phase-3" type="button" onclick="setSelectedTab('phase_3')" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-left cursor-pointer">
        <div class="flex items-center gap-2">
          <span class="font-semibold">Fase 3</span>
          <span id="badge-phase-3" class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 hidden">0</span>
        </div>
      </button>
    </div>
    <div id="phase-status-list" class="grid grid-cols-1 gap-2 text-sm text-gray-700"></div>
  </div>

  <!-- Global planning approval box removed as per request for point-per-point revision -->
  <div id="planning-table-box" class="bg-white rounded-xl shadow-sm overflow-hidden hidden">
    <div class="p-4 border-b border-gray-100">
      <h2 class="text-base font-semibold text-gray-800">Planning New Hire (Fase Planning)</h2>
      <p class="text-xs text-gray-500 mt-1">Saat fase Planning, manager mereview planning secara keseluruhan di sini.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm h-full" style="min-width: 1000px; table-layout: fixed;">
        <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 12%;">Behaviour</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 6%;">Fase</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 35%;">Integrasi Pengukuran</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 35%;">Rencana Aktivitas</th>
            <th class="px-4 py-3 text-center text-xs uppercase text-gray-500" style="width: 12%;">Aksi</th>
          </tr>
        </thead>
        <tbody id="planning-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="5" class="text-center py-8 text-gray-400">Memuat planning...</td></tr>
        </tbody>
      </table>
    </div>

    <!-- Batch Action Bar (Sticky Bottom) -->
    <div id="batch-action-bar" class="hidden bg-white border-t border-gray-200 p-4 flex justify-between items-center sticky bottom-0 z-20 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] w-full">
      <div>
        <p class="text-base font-bold text-gray-900">Review Menunggu Konfirmasi</p>
        <p class="text-xs text-gray-600 mt-1">Pilihan Anda sudah dicatat sementara. Klik kirim untuk menyimpan semua.</p>
      </div>
      <button id="batch-submit-btn" onclick="submitBatchReview()" class="px-6 py-2.5 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center gap-2 cursor-pointer" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#0a2c00'" onmouseout="this.style.backgroundColor='#144600'">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
        Kirim Persetujuan & Revisi
      </button>
    </div>
  </div>

  <div id="activity-table-box" class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
      <h2 class="text-base font-semibold text-gray-800">VnB Activity</h2>
      <span id="activity-waiting" class="text-xs text-gray-500">Pending approval: 0</span>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm" style="min-width: 1200px;">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Behaviour</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Rencana Aktivitas</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Aktivitas Aktual</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Tanggal</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Status</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Progress</th>
            <th class="px-4 py-3 text-right text-xs uppercase text-gray-500">Aksi</th>
          </tr>
        </thead>
        <tbody id="items-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="7" class="text-center py-8 text-gray-400">Memuat aktivitas...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="phase-note-box" class="bg-white rounded-xl shadow-sm p-4 hidden">
    <div id="phase-note-text" class="text-sm text-gray-700">-</div>
  </div>

  <!-- Revision Modal -->
  <div id="revision-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
      <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Catatan Revisi</h3>
      <div class="mb-4 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
        <div id="modal-behaviour-val" class="font-bold text-base mb-2 text-gray-900"></div>
        <div class="font-semibold text-xs text-gray-500 uppercase mt-2">Integrasi Pengukuran</div>
        <div id="modal-integrasi-val" class="ml-2 mb-2 whitespace-pre-wrap">-</div>
        <div class="font-semibold text-xs text-gray-500 uppercase mt-2">Rencana Aktivitas</div>
        <div id="modal-rencana-val" class="ml-2 whitespace-pre-wrap">-</div>
      </div>
      <div class="mb-4">
        <textarea id="modal-revision-notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Masukkan catatan revisi di sini..."></textarea>
      </div>
      <div class="flex justify-between items-center mt-4">
        <button id="modal-cancel-revision-btn" onclick="cancelRevisionFromModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg text-sm hover:bg-gray-300 font-medium hidden">Batalkan Revisi</button>
        <div class="flex gap-2 w-full justify-end">
          <button onclick="closeRevisionModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium">Cancel</button>
          <button onclick="submitRevisionModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 font-medium">OK</button>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
const employeeId = @json($employeeId);
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
  if (!bar) return;
  if (Object.keys(pendingDecisions).length > 0) {
    bar.classList.remove('hidden');
  } else {
    bar.classList.add('hidden');
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
  selectedTab = tabKey;
  if (detailData) {
    renderPhaseOverview(detailData);
    renderPhaseContent(detailData);
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

  const tabIds = ['tab-planning', 'tab-phase-1', 'tab-phase-2', 'tab-phase-3'];
  tabIds.forEach(id => {
    const tab = document.getElementById(id);
    if (!tab) return;
    tab.classList.remove('border-green-800', 'text-white');
    tab.style.backgroundColor = '';
    tab.classList.add('border-gray-200', 'text-gray-700');
  });

  const activeKey = selectedTab || currentStage;
  const activeTab = document.getElementById(`tab-${activeKey.replace('_', '-')}`);
  if (activeTab) {
    activeTab.classList.remove('border-gray-200', 'text-gray-700');
    activeTab.classList.add('border-green-800', 'text-white');
    activeTab.style.backgroundColor = '#144600';
  }

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
  const currentStage = normalizeCurrentStage(detail);
  const activePhase = selectedTab || currentStage;
  const planningWaiting = !!detail.approval_requests?.planning_waiting;
  const items = detail.items || [];

  const planningTableBox = document.getElementById('planning-table-box');
  const activityTableBox = document.getElementById('activity-table-box');
  const phaseNoteBox = document.getElementById('phase-note-box');
  const phaseNoteText = document.getElementById('phase-note-text');

  planningTableBox.classList.add('hidden');
  activityTableBox.classList.add('hidden');
  phaseNoteBox.classList.add('hidden');

  if (activePhase === 'planning') {
    planningTableBox.classList.remove('hidden');
    renderPlanningTable(items);
    return;
  }

  const tabNum = Number((activePhase.match(/phase_(\d)/) || [])[1] || 1);
  const currentNum = currentStage === 'planning'
    ? 0
    : Number((currentStage.match(/phase_(\d)/) || [])[1] || 1);

  if (currentStage === 'planning') {
    phaseNoteBox.classList.remove('hidden');
    phaseNoteText.textContent = `Planning belum disetujui manager. Fase ${tabNum} belum dimulai.`;
    return;
  }

  const filteredItems = items.filter(item => resolvePhaseNumberFromItem(item) === tabNum);

  if (tabNum > currentNum) {
    phaseNoteBox.classList.remove('hidden');
    phaseNoteText.textContent = `Fase ${tabNum} belum dimulai.`;
    return;
  }

  if (tabNum < currentNum) {
    phaseNoteBox.classList.remove('hidden');
    phaseNoteText.textContent = `Fase ${tabNum} sudah selesai. Anda tetap bisa melihat histori planning melalui tombol "Planning Approved & History".`;
  }

  activityTableBox.classList.remove('hidden');
  renderActivityTable(filteredItems);
}

function renderActivityTable(items) {
  const tbody = document.getElementById('items-body');
  if (!items.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Belum ada aktivitas untuk fase ini</td></tr>';
    return;
  }

  tbody.innerHTML = items.map(item => {
    const waiting = item.submission_status === 'waiting_approval';
    return `
      <tr>
        <td class="px-4 py-3">${item.activity_title || '-'}</td>
        <td class="px-4 py-3">${item.deliverables || '-'}</td>
        <td class="px-4 py-3">${item.activity_description || '-'}</td>
        <td class="px-4 py-3">${item.activity_date || '-'}</td>
        <td class="px-4 py-3">${toLabelStatus(item.submission_status)}</td>
        <td class="px-4 py-3">${item.completion_percentage || 0}%</td>
        <td class="px-4 py-3 text-right">
          ${waiting ? `
            <button onclick="approveActivity(${item.id})" class="px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 mr-1">Accept</button>
            <button onclick="reviseActivity(${item.id})" class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700">Revisi</button>
          ` : '<span class="text-xs text-gray-400">-</span>'}
        </td>
      </tr>
    `;
  }).join('');
}

async function loadDetail() {
  const res = await apiGet(`/api/manager/new-hires/${employeeId}`);
  if (!(res && res.success === true && res.data)) {
    showAlert(res?.message || 'Gagal memuat detail New Hire', 'error');
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

  if (!selectedTab) {
    selectedTab = normalizeCurrentStage(detailData);
  }
  renderPhaseOverview(detailData);
  renderPhaseContent(detailData);

  const planningHistoryLink = document.getElementById('planning-history-link');
  const showPlanningHistory = normalizeCurrentStage(detailData) !== 'planning';
  planningHistoryLink.classList.toggle('hidden', !showPlanningHistory);
  if (showPlanningHistory) {
    planningHistoryLink.href = `/manager/new-hires/${employeeId}/planning-history`;
  }

  const waitingCount = detailData.approval_requests?.activity_waiting_count || 0;
  document.getElementById('activity-waiting').textContent = `Pending approval: ${waitingCount}`;
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
    let lastPhase = null;
    
    itemsInGroup.forEach((item, idx) => {
      const showBehavior = idx === 0;
      const showPhase = lastPhase !== item.extracted_phase;
      lastPhase = item.extracted_phase;
      
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
      ${showPhase ? `<td class="px-4 py-3 align-top text-center" style="vertical-align: top;">${item.extracted_phase}</td>` : '<td class="px-4 py-3"></td>'}
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

  const btn = document.getElementById('batch-submit-btn');
  const orgHtml = btn.innerHTML;
  btn.innerHTML = 'Sedang Memproses...';
  btn.disabled = true;

  const res = await apiPost(`/api/manager/plans/${planId}/batch-review`, { reviews });
  
  btn.innerHTML = orgHtml;
  btn.disabled = false;

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

loadDetail();
</script>
@endpush
@endsection
