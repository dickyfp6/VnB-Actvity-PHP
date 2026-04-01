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

  <div id="planning-approval-box" class="bg-white rounded-xl shadow-sm p-4 hidden">
    <h2 class="text-base font-semibold text-gray-800 mb-2">Approval Planning</h2>
    <p class="text-sm text-gray-600 mb-2">New Hire mengajukan planning dan menunggu approval manager.</p>
    <textarea id="planning-notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3" placeholder="Catatan (wajib jika revisi, opsional jika approve)"></textarea>
    <div class="flex gap-2">
      <button onclick="approvePlanning()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Accept Planning</button>
      <button onclick="revisePlanning()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Revisi Planning</button>
    </div>
  </div>

  <div id="planning-table-box" class="bg-white rounded-xl shadow-sm overflow-hidden hidden">
    <div class="p-4 border-b border-gray-100">
      <h2 class="text-base font-semibold text-gray-800">Planning New Hire (Fase Planning)</h2>
      <p class="text-xs text-gray-500 mt-1">Saat fase Planning, manager mereview planning secara keseluruhan di sini.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm" style="min-width: 1000px; table-layout: fixed;">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 12%;">Behaviour</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 6%;">Fase</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 35%;">Integrasi Pengukuran</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 35%;">Rencana Aktivitas</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500" style="width: 12%;">Aksi</th>
          </tr>
        </thead>
        <tbody id="planning-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="5" class="text-center py-8 text-gray-400">Memuat planning...</td></tr>
        </tbody>
      </table>
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
</div>

@push('scripts')
<script>
const employeeId = @json($employeeId);
let detailData = null;
let selectedTab = null;

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

  const planningApprovalBox = document.getElementById('planning-approval-box');
  const planningTableBox = document.getElementById('planning-table-box');
  const activityTableBox = document.getElementById('activity-table-box');
  const phaseNoteBox = document.getElementById('phase-note-box');
  const phaseNoteText = document.getElementById('phase-note-text');

  planningApprovalBox.classList.add('hidden');
  planningTableBox.classList.add('hidden');
  activityTableBox.classList.add('hidden');
  phaseNoteBox.classList.add('hidden');

  if (activePhase === 'planning') {
    planningTableBox.classList.remove('hidden');
    if (planningWaiting) {
      planningApprovalBox.classList.remove('hidden');
    }
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
    if (monthRange.includes('1-3')) return '1';
    if (monthRange.includes('4-6')) return '2';
    if (monthRange.includes('7-12')) return '3';
    return monthRange || '-';
  }

  // Group items by behavior and create rows for each integration
  const groupedByBehavior = {};
  
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
    
    // Create a row for each integration
    integrations.forEach(integration => {
      groupedByBehavior[behavior].push({
        ...item,
        extracted_phase: phase,
        integration_text: integration
      });
    });
  });

  let html = '';
  
  Object.entries(groupedByBehavior).forEach(([behavior, itemsInGroup]) => {
    let lastPhase = null;
    
    itemsInGroup.forEach((item, idx) => {
      const showBehavior = idx === 0;  // Only show behavior name on first row
      const showPhase = lastPhase !== item.extracted_phase;  // Only show fase if different from previous
      lastPhase = item.extracted_phase;
      
      html += `
    <tr>
      ${showBehavior ? `<td class="px-4 py-3 align-top font-medium" style="vertical-align: top;">${behavior}</td>` : '<td class="px-4 py-3"></td>'}
      ${showPhase ? `<td class="px-4 py-3 align-top text-center" style="vertical-align: top;">${item.extracted_phase}</td>` : '<td class="px-4 py-3"></td>'}
      <td class="px-4 py-3 align-top"><span class="text-xs text-gray-700">${item.integration_text}</span></td>
      <td class="px-4 py-3 align-top">${item.deliverables || '-'}</td>
      <td class="px-4 py-3 align-top text-center">
        <button onclick="approvePlanningRow(${item.id})" class="inline-flex items-center justify-center w-8 h-8 rounded bg-green-600 text-white hover:bg-green-700 transition mr-2" title="Approve" style="font-size: 14px; font-weight: bold;">✓</button>
        <button onclick="revisePlanningRow(${item.id})" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 text-white hover:bg-red-700 transition" title="Revise" style="font-size: 14px; font-weight: bold;">✕</button>
      </td>
    </tr>
      `;
    });
  });

  tbody.innerHTML = html;
}

async function approvePlanning() {
  const planId = detailData?.plan?.id;
  if (!planId) return;
  const notes = document.getElementById('planning-notes').value || '';
  const res = await apiPost(`/api/vnb-plans/${planId}/manager-review`, { action: 'approve', notes });
  if (res && res.success) {
    showAlert('Planning berhasil di-approve');
    loadDetail();
  } else {
    showAlert(res?.message || res?.error || 'Gagal approve planning', 'error');
  }
}

async function revisePlanning() {
  const planId = detailData?.plan?.id;
  if (!planId) return;
  const notes = (document.getElementById('planning-notes').value || '').trim();
  if (!notes) {
    showAlert('Isi catatan revisi planning terlebih dahulu', 'error');
    return;
  }
  const res = await apiPost(`/api/vnb-plans/${planId}/manager-review`, { action: 'reject', notes });
  if (res && res.success) {
    showAlert('Revisi planning berhasil dikirim');
    loadDetail();
  } else {
    showAlert(res?.message || res?.error || 'Gagal kirim revisi planning', 'error');
  }
}

function approvePlanningRow(itemId) {
  showAlert('Fitur approval per-row akan segera diaktifkan', 'info');
  // TODO: Implement row-level approval
}

function revisePlanningRow(itemId) {
  showAlert('Fitur revise per-row akan segera diaktifkan', 'info');
  // TODO: Implement row-level revision
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
