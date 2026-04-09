@extends('layouts.app')
@section('title','V&B Framework')
@section('content')
<div class="py-2">
  <!-- Header -->
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">V&B Framework</h1>
    <div class="flex gap-2">
      <button id="edit-btn" onclick="toggleEditMode()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-white transition-all hover:shadow-md" style="background-color: #144600;">
        <i class="fas fa-edit"></i> <span>Edit</span>
      </button>
      <button id="clone-btn" onclick="openCloneModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-white transition-all hover:shadow-md" style="background-color: #144600;">
        <i class="fas fa-copy"></i> <span>Clone Career Stage</span>
      </button>
    </div>
  </div>

  <!-- Career Stage Tabs -->
  <div class="flex gap-4 mb-6 overflow-x-auto pb-2 border-b border-gray-200">
    <button id="tab-manage_self_non_staff" class="stage-btn px-2 py-3 font-medium transition-all whitespace-nowrap text-sm md:text-base" data-stage="manage_self_non_staff" style="color: #999999; border-bottom: none;">Manage Self (Non-Staff)</button>
    <button id="tab-manage_self_staff" class="stage-btn px-2 py-3 font-medium transition-all whitespace-nowrap text-sm md:text-base" data-stage="manage_self_staff" style="color: #999999; border-bottom: none;">Manage Self (Staff)</button>
    <button id="tab-manage_others" class="stage-btn px-2 py-3 font-medium transition-all whitespace-nowrap text-sm md:text-base" data-stage="manage_others" style="color: #999999; border-bottom: none;">Manage Others</button>
    <button id="tab-manage_managers" class="stage-btn px-2 py-3 font-medium transition-all whitespace-nowrap text-sm md:text-base" data-stage="manage_managers" style="color: #999999; border-bottom: none;">Manage Managers</button>
    <button id="tab-manage_function" class="stage-btn px-2 py-3 font-medium transition-all whitespace-nowrap text-sm md:text-base" data-stage="manage_function" style="color: #999999; border-bottom: none;">Manage Function</button>
  </div>

  <!-- Framework Table -->
  <div class="table-container overflow-x-auto">
    <table class="table-modern">
      <thead>
        <tr>
          <th>Behaviour</th>
          <th>Phase</th>
          <th>Integration 1</th>
          <th>Integration 2</th>
        </tr>
      </thead>
      <tbody id="framework-body">
        <tr>
          <td colspan="4" class="text-center py-8 text-gray-400">
            Memuat data...
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

<!-- Clone Modal -->
<div id="clone-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Clone Career Stage</h2>
    <form id="clone-form" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
        <select id="clone-source" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required></select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ke</label>
        <select id="clone-target" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required></select>
      </div>
      <div class="flex justify-end gap-2 pt-4">
        <button type="button" onclick="closeCloneModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm font-medium" style="background-color: #144600;">Clone</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
let currentStage = 'manage_self_non_staff';
let frameworkRows = [];
let editMode = false;
let editDraft = {};
let editBehaviours = ['Empathy', 'Be A Wismilak Ambassador', 'Effective & Efficient', 'Speak with Data', 'Collaborative', 'Decisive', 'Open Mind'];
let editPhases = ['1-3', '4-6', '6+'];
let editPhaseLabels = ['1-3 Bulan', '4-6 Bulan', '>6 Bulan'];

const stageLabels = {
  manage_self_non_staff: 'Manage Self (Non-Staff)',
  manage_self_staff: 'Manage Self (Staff)',
  manage_others: 'Manage Others',
  manage_managers: 'Manage Managers',
  manage_function: 'Manage Function'
};

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function draftKey(behaviour, phaseStorage) {
  return `${behaviour}||${phaseStorage}`;
}

function captureIntegrationDraft(behaviour, phaseStorage, field, value) {
  const key = draftKey(behaviour, phaseStorage);
  if (!editDraft[key]) {
    const existing = findRow(behaviour, phaseStorage) || {};
    editDraft[key] = {
      integration_1: existing.integration_1 || '',
      integration_2: existing.integration_2 || ''
    };
  }
  editDraft[key][field] = value;
}

async function saveAllDraftChanges() {
  const entries = Object.entries(editDraft);
  if (entries.length === 0) return true;

  let failed = 0;
  for (const [key, payloadDraft] of entries) {
    const [behaviour, phaseStorage] = key.split('||');
    const payload = {
      career_stage: currentStage,
      behaviour: behaviour.trim(),
      phase: phaseStorage.trim(),
      integration_1: (payloadDraft.integration_1 || '').trim(),
      integration_2: (payloadDraft.integration_2 || '').trim(),
    };

    try {
      const res = await apiPost('/api/vnb-framework/upsert', payload);
      if (!(res.message || res.id)) {
        failed++;
      }
    } catch (error) {
      failed++;
    }
  }

  if (failed > 0) {
    showAlert(`${failed} perubahan gagal disimpan`, 'error');
    return false;
  }

  showAlert('Semua perubahan berhasil disimpan');
  editDraft = {};
  return true;
}

async function toggleEditMode() {
  const btn = document.getElementById('edit-btn');
  const cloneBtn = document.getElementById('clone-btn');

  if (!editMode) {
    editMode = true;
    editDraft = {};
    btn.innerHTML = '<i class="fas fa-save"></i> <span>Simpan</span>';
    btn.style.backgroundColor = '#2563eb';
    cloneBtn.style.display = 'none';
    await loadFramework();
    return;
  }

  btn.disabled = true;
  const success = await saveAllDraftChanges();
  btn.disabled = false;
  if (!success) return;

  editMode = false;
  btn.innerHTML = '<i class="fas fa-edit"></i> <span>Edit</span>';
  btn.style.backgroundColor = '#144600';
  cloneBtn.style.display = 'inline-flex';
  await loadFramework();
}

function activateStageButtons() {
  const stages = ['manage_self_non_staff', 'manage_self_staff', 'manage_others', 'manage_managers', 'manage_function'];
  stages.forEach(stage => {
    const btn = document.getElementById(`tab-${stage}`);
    if (btn) {
      if (stage === currentStage) {
        btn.style.color = '#144600';
        btn.style.borderBottom = '2px solid #144600';
      } else {
        btn.style.color = '#999999';
        btn.style.borderBottom = 'none';
      }
    }
  });
}

async function loadFramework() {
  activateStageButtons();
  
  const tbody = document.getElementById('framework-body');
  
  tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Memuat data...</td></tr>';
  
  try {
    const res = await apiGet(`/api/vnb-framework?career_stage=${currentStage}`);
    
    if (!res.success || !res.data) {
      console.error('Framework load error:', res);
      tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-red-500">Error: Data tidak ditemukan</td></tr>';
      return;
    }
    
    frameworkRows = res.data || [];
    
    if (editMode) {
      renderEditMode();
    } else {
      renderViewMode();
    }
  } catch (error) {
    console.error('Load framework error:', error);
    tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-red-500">Error loading framework</td></tr>';
  }
}

function findRow(behaviour, phaseStorage) {
  return frameworkRows.find(r => r.behaviour === behaviour && r.phase === phaseStorage);
}

function renderViewMode() {
  const tbody = document.getElementById('framework-body');
  
  if (frameworkRows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada data untuk career stage ini</td></tr>';
    return;
  }
  
  let html = '';
  editBehaviours.forEach((b, bIdx) => {
    editPhases.forEach((p, pIdx) => {
      const row = findRow(b, p);
      if (!row) return;
      
      html += `<tr>
        <td class="px-6 py-4 align-top font-medium text-gray-900">${b}</td>
        <td class="px-6 py-4 align-top text-gray-700 whitespace-nowrap">${editPhaseLabels[pIdx]}</td>
        <td class="px-6 py-4 align-top text-gray-600 whitespace-pre-wrap break-words">${row.integration_1 || '-'}</td>
        <td class="px-6 py-4 align-top text-gray-600 whitespace-pre-wrap break-words">${row.integration_2 || '-'}</td>
      </tr>`;
    });
  });
  
  tbody.innerHTML = html || '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada data</td></tr>';
}

function renderEditMode() {
  const tbody = document.getElementById('framework-body');
  let html = '';
  
  // Behaviour Management Section
  html += `<tr><td colspan="4" class="px-6 py-4" style="background-color: #f5f5f5;">
    <div class="space-y-3">
      <div class="font-bold text-gray-900 text-base">Manage Behaviours</div>
      <div class="space-y-2 bg-white rounded p-3 border border-gray-200">`;
  
  editBehaviours.forEach((b, idx) => {
    html += `<div class="flex gap-2 items-center">
      <input type="text" id="behav-${idx}" value="${b.replace(/"/g, '&quot;')}" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
      <button onclick="removeBehaviour(${idx})" type="button" class="px-4 py-2 text-red-700 text-sm border border-red-300 rounded hover:bg-red-50 transition-colors">Hapus</button>
    </div>`;
  });
  
  html += `</div>
      <button onclick="addBehaviour()" type="button" class="px-4 py-2 text-sm font-medium text-green-700 border border-green-500 rounded hover:bg-green-50 transition-colors">+ Behaviour</button>
    </div>
  </td></tr>`;
  
  // Phase Management Section
  html += `<tr><td colspan="4" class="px-6 py-4" style="background-color: #f5f5f5;">
    <div class="space-y-3">
      <div class="font-bold text-gray-900 text-base">Manage Phases</div>
      <div class="space-y-2 bg-white rounded p-3 border border-gray-200">`;
  
  editPhases.forEach((p, idx) => {
    html += `<div class="flex gap-2 items-center">
      <label class="text-sm text-gray-600 w-20">Storage:</label>
      <input type="text" id="phase-storage-${idx}" value="${p}" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
      <label class="text-sm text-gray-600 w-20">Display:</label>
      <input type="text" id="phase-label-${idx}" value="${editPhaseLabels[idx]}" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
      <button onclick="removePhase(${idx})" type="button" class="px-4 py-2 text-red-700 text-sm border border-red-300 rounded hover:bg-red-50 transition-colors">Hapus</button>
    </div>`;
  });
  
  html += `</div>
      <button onclick="addPhase()" type="button" class="px-4 py-2 text-sm font-medium text-green-700 border border-green-500 rounded hover:bg-green-50 transition-colors">+ Phase</button>
    </div>
  </td></tr>`;
  
  // Framework Data Edit Section
  html += `<tr><td colspan="4" class="px-6 py-4" style="background-color: #f5f5f5;">
    <div class="space-y-3">
      <div class="flex items-center justify-between gap-3">
        <div class="font-bold text-gray-900 text-base">Edit Data Framework</div>
        <button onclick="clearAllIntegrationFields()" type="button" class="px-3 py-2 text-xs font-medium text-red-700 border border-red-300 rounded hover:bg-red-50 transition-colors">Clear All Integrasi Pengukuran</button>
      </div>
      <div class="overflow-x-auto border border-gray-300 rounded bg-white">
        <table class="w-full text-sm">
          <thead style="background-color: #f0f0f0;">
            <tr>
              <th class="px-4 py-2 text-left font-medium text-gray-700 border-b border-r">Behaviour</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700 border-b border-r">Phase</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700 border-b border-r">Integration 1</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700 border-b">Integration 2</th>
            </tr>
          </thead>
          <tbody>`;
  
  editBehaviours.forEach((b, bIdx) => {
    editPhases.forEach((p, pIdx) => {
      const row = findRow(b, p) || {};
      const i1Val = escapeHtml(row.integration_1 || '');
      const i2Val = escapeHtml(row.integration_2 || '');
      const bAttr = escapeHtml(b);
      const pAttr = escapeHtml(p);
      
      html += `<tr>
        <td class="px-4 py-2 align-top border-b border-r"><span class="text-gray-900">${b}</span></td>
        <td class="px-4 py-2 align-top border-b border-r whitespace-nowrap"><span class="text-gray-700">${editPhaseLabels[pIdx]}</span></td>
        <td class="px-4 py-2 align-top border-b border-r">
          <textarea id="int1-${bIdx}-${pIdx}" data-behaviour="${bAttr}" data-phase="${pAttr}" data-field="integration_1" rows="3" class="integration-input w-full min-w-[280px] border border-gray-300 rounded px-3 py-2 text-xs leading-5 resize-y focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis integration 1 di sini">${i1Val}</textarea>
        </td>
        <td class="px-4 py-2 align-top border-b border-r">
          <textarea id="int2-${bIdx}-${pIdx}" data-behaviour="${bAttr}" data-phase="${pAttr}" data-field="integration_2" rows="3" class="integration-input w-full min-w-[280px] border border-gray-300 rounded px-3 py-2 text-xs leading-5 resize-y focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis integration 2 di sini">${i2Val}</textarea>
        </td>
      </tr>`;
    });
  });
  
  html += `</tbody>
        </table>
      </div>
    </div>
  </td></tr>`;
  
  tbody.innerHTML = html;
  document.querySelectorAll('.integration-input').forEach(el => {
    el.addEventListener('input', (event) => {
      const target = event.target;
      captureIntegrationDraft(target.dataset.behaviour, target.dataset.phase, target.dataset.field, target.value || '');
    });
  });
}

async function clearAllIntegrationFields() {
  const confirmed = await showConfirm('Apakah Anda yakin ingin menghapus seluruh integrasi pengukuran pada section ini?', 'Hapus Integrasi');
  if (!confirmed) return;

  document.querySelectorAll('.integration-input').forEach((el) => {
    el.value = '';
    captureIntegrationDraft(el.dataset.behaviour, el.dataset.phase, el.dataset.field, '');
  });

  showAlert('Semua integrasi pengukuran pada section ini sudah dikosongkan. Klik Simpan untuk menyimpan perubahan.');
}

function addBehaviour() {
  const name = prompt('Nama behaviour baru:');
  if (name && name.trim()) {
    editBehaviours.push(name.trim());
    renderEditMode();
  }
}

async function removeBehaviour(idx) {
  if (await showConfirm('Hapus behaviour ini?', 'Hapus Behaviour')) {
    editBehaviours.splice(idx, 1);
    renderEditMode();
  }
}

function addPhase() {
  const storage = prompt('Fase storage (contoh: 1-2, 3-4):');
  if (!storage?.trim()) return;
  const display = prompt('Label display (contoh: 1-2 Bulan):');
  if (!display?.trim()) return;
  editPhases.push(storage.trim());
  editPhaseLabels.push(display.trim());
  renderEditMode();
}

async function removePhase(idx) {
  if (await showConfirm('Hapus phase ini?', 'Hapus Phase')) {
    editPhases.splice(idx, 1);
    editPhaseLabels.splice(idx, 1);
    renderEditMode();
  }
}

function openCloneModal() {
  const source = document.getElementById('clone-source');
  const target = document.getElementById('clone-target');
  source.innerHTML = Object.entries(stageLabels).map(([k,v]) => `<option value="${k}">${v}</option>`).join('');
  target.innerHTML = Object.entries(stageLabels).map(([k,v]) => `<option value="${k}">${v}</option>`).join('');
  source.value = currentStage;
  document.getElementById('clone-modal').classList.remove('hidden');
}

function closeCloneModal() {
  document.getElementById('clone-modal').classList.add('hidden');
}

document.getElementById('clone-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const source = document.getElementById('clone-source').value;
  const target = document.getElementById('clone-target').value;
  
  if (source === target) {
    showAlert('Source dan target harus berbeda', 'error');
    return;
  }
  
  if (!(await showConfirm('Clone data dari ' + stageLabels[source] + ' ke ' + stageLabels[target] + '?', 'Konfirmasi Clone'))) {
    return;
  }
  
  try {
    const res = await apiPost('/api/vnb-framework/clone', { 
      source_career_stage: source, 
      target_career_stage: target 
    });
    
    if (res.message) {
      showAlert(res.message);
      closeCloneModal();
      if (currentStage === target) {
        await loadFramework();
      }
    } else {
      showAlert(res.error || 'Gagal clone data', 'error');
    }
  } catch (error) {
    console.error('Clone error:', error);
    showAlert('Error: ' + (error.message || 'Gagal clone'), 'error');
  }
});

// Initialize stage buttons
document.querySelectorAll('.stage-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    currentStage = btn.dataset.stage;
    loadFramework();
  });
});

// Load initial data
loadFramework();
</script>
@endpush
