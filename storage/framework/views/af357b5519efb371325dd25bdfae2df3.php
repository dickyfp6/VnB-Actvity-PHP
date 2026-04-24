
<?php $__env->startSection('title','V&B Framework'); ?>
<?php $__env->startSection('content'); ?>
<div class="py-2 space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">V&B Framework</h1>
  </div>

  <section id="framework-empty" class="hidden bg-white border border-gray-200 rounded-xl p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Belum dibuat.</h2>
    <p class="text-sm text-gray-600 mb-4">Yuk siapkan VnB Framework kamu!</p>
    <button id="btn-open-setup" class="px-4 py-2 rounded-lg text-white text-sm" style="background-color:#144600;">Buat Framework</button>
  </section>

  <section id="framework-config" class="hidden bg-white border border-gray-200 rounded-xl p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-3">Pengaturan Detail Framework</h2>
    <p class="text-sm text-gray-600 mb-4">Atur fase (durasi bulan) dan batas maksimal integrasi aktivitas untuk tiap career stage.</p>
    <div id="stage-config-list" class="space-y-4"></div>
  </section>

  <section id="framework-preview" class="hidden bg-white border border-gray-200 rounded-xl p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-3">Preview Kerangka Framework</h2>
    <div class="overflow-x-auto">
      <table class="table-modern w-full">
        <thead>
          <tr>
            <th>Career Stage</th>
            <th>Behaviour</th>
            <th>Phase</th>
          </tr>
        </thead>
        <tbody id="framework-preview-body"></tbody>
      </table>
    </div>
  </section>

  <section id="framework-integrations" class="hidden bg-white border border-gray-200 rounded-xl p-6">
    <div class="flex items-center justify-between mb-3 gap-3 flex-wrap">
      <div>
        <h2 class="text-lg font-semibold text-gray-900">Integrasi Aktivitas</h2>
        <p class="text-sm text-gray-600">Isi detail integrasi per behaviour dan fase sesuai batas maksimum stage.</p>
      </div>
      <div class="flex items-center gap-2">
        <label class="text-sm text-gray-700">Career Stage</label>
        <select id="integrations-stage-select" class="border border-gray-300 rounded-lg px-3 py-2 text-sm"></select>
        <span id="integrations-dirty-badge" class="hidden text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">Perubahan belum disimpan</span>
        <button id="discard-local-draft-btn" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">Buang Draft Lokal</button>
        <button id="copy-prev-phase-btn" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">Copy dari Fase Sebelumnya</button>
        <button id="reset-template-btn" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">Reset ke Template</button>
        <button id="save-integrations-btn" class="px-4 py-2 rounded-lg text-white text-sm" style="background-color:#144600;">Simpan Integrasi</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern w-full">
        <thead id="integrations-head"></thead>
        <tbody id="integrations-body"></tbody>
      </table>
    </div>
  </section>
</div>

<div id="setup-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-900">Buat Kerangka VnB Framework</h3>
      <button type="button" id="btn-close-setup" class="text-gray-500"><i class="fas fa-times"></i></button>
    </div>

    <div class="space-y-6">
      <div>
        <div class="flex items-center justify-between mb-2 gap-3 flex-wrap">
          <div>
            <label class="block text-sm font-medium text-gray-700">1. Behaviour</label>
            <p class="text-xs text-gray-500">Satu behaviour per baris. Tambah baris jika ingin menambah behaviour.</p>
          </div>
          <button type="button" id="btn-add-behaviour" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">+ Tambah Behaviour</button>
        </div>
        <div id="behaviour-list" class="space-y-2"></div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-2 gap-3 flex-wrap">
          <div>
            <label class="block text-sm font-medium text-gray-700">2. Klasifikasi Career Stage</label>
            <p class="text-xs text-gray-500">Satu golongan hanya boleh berada di satu career stage. Semua golongan harus kebagian stage.</p>
          </div>
          <button type="button" id="btn-add-stage" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">+ Tambah Stage</button>
        </div>
        <div id="stage-editor-list" class="space-y-3"></div>
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" id="btn-cancel-setup" class="px-4 py-2 rounded-lg border border-gray-300 text-sm">Batal</button>
        <button type="button" id="btn-save-setup" class="px-4 py-2 rounded-lg text-white text-sm" style="background-color:#144600;">Simpan Kerangka</button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let setupPayload = null;
let setupDraft = { behaviours: [], stages: [] };
let stageConfigs = [];
let currentStage = 'manage_self_non_staff';
let integrationRows = [];
let integrationDraft = {};
let integrationDirty = false;
const integrationDraftStoragePrefix = 'vnb_framework_integration_draft_v1';
let stageLabelMap = {};

function ensureSetupDraft(payload) {
  const behaviours = Array.isArray(payload?.behaviours) ? payload.behaviours : [];
  const stages = Array.isArray(payload?.stages) ? payload.stages : [];

  setupDraft = {
    behaviours: behaviours.length ? behaviours.map((value) => (typeof value === 'string' ? value : (value?.name || ''))) : [''],
    stages: stages.length
      ? stages.map((stage) => ({
          label: stage.label || '',
          position_ids: Array.isArray(stage.position_ids) ? stage.position_ids.map((id) => Number(id)) : [],
        }))
      : [{ label: '', position_ids: [] }],
  };
}

function renderSetupEditor() {
  const behaviourList = document.getElementById('behaviour-list');
  const stageList = document.getElementById('stage-editor-list');
  const positions = setupPayload?.positions || [];

  behaviourList.innerHTML = setupDraft.behaviours.map((value, index) => `
    <div class="flex items-center gap-2">
      <input type="text" class="setup-behaviour-input flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" data-index="${index}" value="${String(value || '').replace(/"/g, '&quot;')}" placeholder="Tulis satu behaviour">
      <button type="button" class="remove-behaviour-btn px-3 py-2 rounded-lg border border-gray-300 text-sm" data-index="${index}">Hapus</button>
    </div>
  `).join('');

  stageList.innerHTML = setupDraft.stages.map((stage, index) => {
    const selectedIds = new Set((stage.position_ids || []).map((id) => Number(id)));
    const selectedNames = positions.filter((pos) => selectedIds.has(Number(pos.id))).map((pos) => pos.name);
    const checkboxList = positions.map((pos) => `
      <label class="flex items-center gap-2 text-sm text-gray-700 py-1">
        <input type="checkbox" class="setup-stage-position" data-stage-index="${index}" value="${pos.id}" ${selectedIds.has(Number(pos.id)) ? 'checked' : ''}>
        <span>${pos.name}</span>
      </label>
    `).join('');

    return `
      <div class="border border-gray-200 rounded-lg p-4 stage-card" data-stage-index="${index}">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Career Stage</label>
            <input type="text" class="setup-stage-label w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" data-index="${index}" value="${String(stage.label || '').replace(/"/g, '&quot;')}" placeholder="Contoh: Manage Self (Non-Staff)">
          </div>
          <button type="button" class="remove-stage-btn px-3 py-2 rounded-lg border border-gray-300 text-sm" data-index="${index}">Hapus Stage</button>
        </div>
        <div class="mt-3">
          <button type="button" class="toggle-stage-positions px-3 py-2 rounded-lg border border-gray-300 text-sm" data-index="${index}">
            Pilih Golongan (${selectedIds.size})
          </button>
          <div class="stage-position-panel hidden mt-3 border border-gray-200 rounded-lg p-3 bg-gray-50">
            <div class="max-h-48 overflow-y-auto">${checkboxList}</div>
          </div>
          <div class="text-xs text-gray-500 mt-2">Dipilih: ${selectedNames.length ? selectedNames.join(', ') : 'Belum ada golongan'}</div>
        </div>
      </div>
    `;
  }).join('');

  document.querySelectorAll('.setup-behaviour-input').forEach((input) => {
    input.addEventListener('input', (event) => {
      const index = Number(event.target.dataset.index);
      setupDraft.behaviours[index] = event.target.value;
    });
  });

  document.querySelectorAll('.remove-behaviour-btn').forEach((button) => {
    button.addEventListener('click', () => {
      const index = Number(button.dataset.index);
      setupDraft.behaviours.splice(index, 1);
      if (!setupDraft.behaviours.length) {
        setupDraft.behaviours.push('');
      }
      renderSetupEditor();
    });
  });

  document.querySelectorAll('.setup-stage-label').forEach((input) => {
    input.addEventListener('input', (event) => {
      const index = Number(event.target.dataset.index);
      setupDraft.stages[index].label = event.target.value;
    });
  });

  document.querySelectorAll('.remove-stage-btn').forEach((button) => {
    button.addEventListener('click', () => {
      const index = Number(button.dataset.index);
      setupDraft.stages.splice(index, 1);
      if (!setupDraft.stages.length) {
        setupDraft.stages.push({ label: '', position_ids: [] });
      }
      renderSetupEditor();
    });
  });

  document.querySelectorAll('.toggle-stage-positions').forEach((button) => {
    button.addEventListener('click', () => {
      const card = button.closest('.stage-card');
      const panel = card.querySelector('.stage-position-panel');
      panel.classList.toggle('hidden');
    });
  });

  document.querySelectorAll('.setup-stage-position').forEach((checkbox) => {
    checkbox.addEventListener('change', (event) => {
      const stageIndex = Number(event.target.dataset.stageIndex);
      const positionId = Number(event.target.value);

      setupDraft.stages.forEach((stage, index) => {
        if (!Array.isArray(stage.position_ids)) {
          stage.position_ids = [];
        }

        if (index !== stageIndex) {
          stage.position_ids = stage.position_ids.filter((id) => Number(id) !== positionId);
          return;
        }

        if (event.target.checked) {
          if (!stage.position_ids.includes(positionId)) {
            stage.position_ids.push(positionId);
          }
        } else {
          stage.position_ids = stage.position_ids.filter((id) => Number(id) !== positionId);
        }
      });

      renderSetupEditor();
    });
  });
}

function collectSetupPayload() {
  const behaviours = setupDraft.behaviours.map((value) => String(value || '').trim()).filter(Boolean);
  const stages = setupDraft.stages
    .map((stage) => ({
      label: String(stage.label || '').trim(),
      position_ids: Array.isArray(stage.position_ids) ? stage.position_ids.map((id) => Number(id)).filter((id) => Number.isInteger(id)) : [],
    }))
    .filter((stage) => stage.label);

  return { behaviours, stages };
}

function openSetupModal() {
  document.getElementById('setup-modal').classList.remove('hidden');
  document.getElementById('setup-modal').classList.add('flex');
}

function closeSetupModal() {
  document.getElementById('setup-modal').classList.remove('flex');
  document.getElementById('setup-modal').classList.add('hidden');
}

function renderStageDetailCards(stages) {
  const container = document.getElementById('stage-config-list');
  container.innerHTML = stages.map((stage) => {
    const phaseRows = stage.phases && stage.phases.length
      ? stage.phases
      : [{ phase_order: 1, duration_months: 3 }, { phase_order: 2, duration_months: 3 }, { phase_order: 3, duration_months: 6 }];

    const phaseInputs = phaseRows.map((phase, idx) => `
      <div class="flex items-center gap-2 mb-2">
        <span class="text-sm text-gray-600 w-20">Fase ${idx + 1}</span>
        <input type="number" min="1" max="60" value="${phase.duration_months}" data-stage="${stage.career_stage}" data-phase-index="${idx}" class="phase-duration w-28 border border-gray-300 rounded px-2 py-1 text-sm">
        <span class="text-sm text-gray-500">bulan</span>
      </div>
    `).join('');

    return `
      <div class="border border-gray-200 rounded-lg p-4">
        <div class="font-semibold text-gray-900 mb-3">${stage.label}</div>
        <div class="mb-3">
          <label class="text-sm text-gray-700 block mb-1">Maksimal Integrasi Aktivitas</label>
          <input type="number" min="1" max="20" value="${stage.max_integrations || 2}" data-stage="${stage.career_stage}" class="max-integrations w-28 border border-gray-300 rounded px-2 py-1 text-sm">
        </div>
        <div class="mb-3">
          <label class="text-sm text-gray-700 block mb-1">Fase Activity (durasi per fase)</label>
          ${phaseInputs}
          <button type="button" class="add-phase text-xs text-blue-600" data-stage="${stage.career_stage}">+ Tambah fase</button>
        </div>
        <button type="button" class="save-stage px-3 py-2 rounded-lg text-white text-sm" style="background-color:#144600;" data-stage="${stage.career_stage}">Simpan Stage</button>
      </div>
    `;
  }).join('');

  bindStageCardEvents();
}

function bindStageCardEvents() {
  document.querySelectorAll('.add-phase').forEach((button) => {
    button.addEventListener('click', () => {
      const stage = button.dataset.stage;
      const card = button.closest('div.border');
      const anchor = button;
      const currentCount = card.querySelectorAll(`.phase-duration[data-stage="${stage}"]`).length;
      const wrapper = document.createElement('div');
      wrapper.className = 'flex items-center gap-2 mb-2';
      wrapper.innerHTML = `
        <span class="text-sm text-gray-600 w-20">Fase ${currentCount + 1}</span>
        <input type="number" min="1" max="60" value="1" data-stage="${stage}" data-phase-index="${currentCount}" class="phase-duration w-28 border border-gray-300 rounded px-2 py-1 text-sm">
        <span class="text-sm text-gray-500">bulan</span>
      `;
      anchor.parentNode.insertBefore(wrapper, anchor);
    });
  });

  document.querySelectorAll('.save-stage').forEach((button) => {
    button.addEventListener('click', async () => {
      const stage = button.dataset.stage;
      const maxIntegrationsEl = document.querySelector(`.max-integrations[data-stage="${stage}"]`);
      const durations = Array.from(document.querySelectorAll(`.phase-duration[data-stage="${stage}"]`))
        .map((el) => Number(el.value || 0))
        .filter((value) => value > 0)
        .map((duration_months) => ({ duration_months }));

      if (!durations.length) {
        showAlert('Durasi fase wajib diisi minimal 1.', 'error');
        return;
      }

      const res = await apiPost('/api/vnb-framework/stage-details', {
        career_stage: stage,
        max_integrations: Number(maxIntegrationsEl.value || 2),
        phases: durations,
      });

      if (!res.success) {
        showAlert(res.message || 'Gagal menyimpan stage.', 'error');
        return;
      }

      showAlert(res.message || 'Stage berhasil disimpan.');
      await loadFrameworkPage();
    });
  });
}

function renderPreview(rows) {
  const body = document.getElementById('framework-preview-body');
  if (!rows.length) {
    body.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-gray-400">Belum ada item framework.</td></tr>';
    return;
  }

  body.innerHTML = rows.map((row) => `
    <tr>
      <td>${stageLabelMap[row.career_stage] || row.career_stage}</td>
      <td>${row.behaviour}</td>
      <td>${row.phase}</td>
    </tr>
  `).join('');
}

function initIntegrationsSelect(stages) {
  const select = document.getElementById('integrations-stage-select');
  select.innerHTML = stages.map((stage) => {
    return `<option value="${stage.career_stage}">${stage.label}</option>`;
  }).join('');
  select.value = currentStage;
}

function setIntegrationDirty(isDirty) {
  integrationDirty = !!isDirty;
  const badge = document.getElementById('integrations-dirty-badge');
  const saveBtn = document.getElementById('save-integrations-btn');

  if (integrationDirty) {
    badge.classList.remove('hidden');
    saveBtn.textContent = 'Simpan Integrasi*';
  } else {
    badge.classList.add('hidden');
    saveBtn.textContent = 'Simpan Integrasi';
  }
}

function normalizeIntegrationValue(value) {
  return (value ?? '').toString();
}

function getIntegrationDraftStorageKey(stageCode) {
  return `${integrationDraftStoragePrefix}:${stageCode}`;
}

function pruneUnchangedDraftEntries(maxIntegrations) {
  const nextDraft = {};

  integrationRows.forEach((row) => {
    const existing = integrationDraft[row.id];
    if (!Array.isArray(existing)) {
      return;
    }

    const trimmed = Array.from({ length: maxIntegrations }, (_, idx) => normalizeIntegrationValue(existing[idx]));
    const base = Array.from({ length: maxIntegrations }, (_, idx) => normalizeIntegrationValue((row.integrations || [])[idx]));
    const hasDiff = trimmed.some((value, idx) => value !== base[idx]);

    if (hasDiff) {
      nextDraft[row.id] = trimmed;
    }
  });

  integrationDraft = nextDraft;
}

function saveDraftToLocalStorage(stageCode, maxIntegrations) {
  if (!window.localStorage) return;
  const key = getIntegrationDraftStorageKey(stageCode);
  pruneUnchangedDraftEntries(maxIntegrations);

  if (!Object.keys(integrationDraft).length) {
    window.localStorage.removeItem(key);
    return;
  }

  window.localStorage.setItem(key, JSON.stringify(integrationDraft));
}

function clearDraftFromLocalStorage(stageCode) {
  if (!window.localStorage) return;
  const key = getIntegrationDraftStorageKey(stageCode);
  window.localStorage.removeItem(key);
}

function loadDraftFromLocalStorage(stageCode, maxIntegrations) {
  integrationDraft = {};
  if (!window.localStorage) return;

  const key = getIntegrationDraftStorageKey(stageCode);
  const raw = window.localStorage.getItem(key);
  if (!raw) return;

  try {
    const parsed = JSON.parse(raw);
    if (!parsed || typeof parsed !== 'object') return;

    const validIds = new Set((integrationRows || []).map((row) => Number(row.id)));
    Object.entries(parsed).forEach(([itemId, values]) => {
      const numericId = Number(itemId);
      if (!validIds.has(numericId) || !Array.isArray(values)) return;
      integrationDraft[numericId] = Array.from({ length: maxIntegrations }, (_, idx) => normalizeIntegrationValue(values[idx]));
    });
  } catch (error) {
    clearDraftFromLocalStorage(stageCode);
  }
}

function refreshIntegrationDirtyState(stageCode, maxIntegrations) {
  pruneUnchangedDraftEntries(maxIntegrations);
  const hasDirty = Object.keys(integrationDraft).length > 0;
  setIntegrationDirty(hasDirty);

  if (hasDirty) {
    saveDraftToLocalStorage(stageCode, maxIntegrations);
  } else {
    clearDraftFromLocalStorage(stageCode);
  }
}

function warnBeforeUnload(event) {
  if (!integrationDirty) {
    return;
  }

  event.preventDefault();
  event.returnValue = '';
}

function extractPhaseOrder(phaseLabel) {
  const match = /^F(\d+)/i.exec((phaseLabel || '').toString().trim());
  if (!match) return 9999;
  return Number(match[1] || 9999);
}

function renderIntegrationsTable(stageConfig, rows) {
  const head = document.getElementById('integrations-head');
  const body = document.getElementById('integrations-body');
  const max = Number(stageConfig?.max_integrations || 2);

  const integrationHeaders = Array.from({ length: max }, (_, idx) => `<th>Integrasi ${idx + 1}</th>`).join('');
  head.innerHTML = `<tr><th>Behaviour</th><th>Phase</th>${integrationHeaders}</tr>`;

  if (!rows.length) {
    body.innerHTML = `<tr><td colspan="${max + 2}" class="text-center py-4 text-gray-400">Belum ada kerangka untuk stage ini. Simpan detail stage dulu.</td></tr>`;
    setIntegrationDirty(false);
    return;
  }

  body.innerHTML = rows.map((row) => {
    const draft = integrationDraft[row.id] || row.integrations || [];
    const integrationCells = Array.from({ length: max }, (_, idx) => {
      const value = (draft[idx] ?? '').toString();
      const baseValue = normalizeIntegrationValue((row.integrations || [])[idx]);
      const isDirtyCell = normalizeIntegrationValue(value) !== baseValue;
      const dirtyClass = isDirtyCell ? ' border-yellow-500 bg-yellow-50 ring-1 ring-yellow-300' : '';
      return `<td><textarea data-item-id="${row.id}" data-idx="${idx}" rows="2" class="integration-input w-full border border-gray-300 rounded px-2 py-1 text-sm${dirtyClass}">${value}</textarea></td>`;
    }).join('');

    return `<tr><td>${row.behaviour}</td><td>${row.phase}</td>${integrationCells}</tr>`;
  }).join('');

  document.querySelectorAll('.integration-input').forEach((input) => {
    input.addEventListener('input', (event) => {
      const itemId = Number(event.target.dataset.itemId);
      const idx = Number(event.target.dataset.idx);
      if (!integrationDraft[itemId]) {
        const base = integrationRows.find((r) => r.id === itemId);
        integrationDraft[itemId] = Array.isArray(base?.integrations) ? [...base.integrations] : [];
      }
      integrationDraft[itemId][idx] = event.target.value;

      const baseRow = integrationRows.find((r) => Number(r.id) === itemId);
      const baseValue = normalizeIntegrationValue((baseRow?.integrations || [])[idx]);
      const isDirtyCell = normalizeIntegrationValue(event.target.value) !== baseValue;
      event.target.classList.toggle('border-yellow-500', isDirtyCell);
      event.target.classList.toggle('bg-yellow-50', isDirtyCell);
      event.target.classList.toggle('ring-1', isDirtyCell);
      event.target.classList.toggle('ring-yellow-300', isDirtyCell);

      refreshIntegrationDirtyState(currentStage, max);
    });
  });
}

async function loadStageItems(stageCode) {
  const res = await apiGet(`/api/vnb-framework?career_stage=${stageCode}`);
  if (!res.success || res.setup_required) {
    showAlert('Gagal memuat data stage.', 'error');
    return;
  }

  stageLabelMap = Object.fromEntries((res.stages || []).map((stage) => [stage.career_stage, stage.label]));

  currentStage = stageCode;
  integrationRows = res.data || [];
  const maxIntegrations = Number(((res.stages || []).find((s) => s.career_stage === stageCode)?.max_integrations) || 2);
  loadDraftFromLocalStorage(stageCode, maxIntegrations);
  refreshIntegrationDirtyState(stageCode, maxIntegrations);
  renderPreview(integrationRows);
  const cfg = (res.stages || []).find((s) => s.career_stage === stageCode) || null;
  renderIntegrationsTable(cfg, integrationRows);
}

async function loadFrameworkPage() {
  const res = await apiGet('/api/vnb-framework');
  if (!res.success) {
    showAlert('Gagal memuat VnB Framework.', 'error');
    return;
  }

  setupPayload = res;
  stageLabelMap = Object.fromEntries((res.stages || []).map((stage) => [stage.career_stage, stage.label]));

  if (res.setup_required) {
    document.getElementById('framework-empty').classList.remove('hidden');
    document.getElementById('framework-config').classList.add('hidden');
    document.getElementById('framework-preview').classList.add('hidden');
    document.getElementById('framework-integrations').classList.add('hidden');
    return;
  }

  stageConfigs = res.stages || [];
  currentStage = res.career_stage || (stageConfigs[0]?.career_stage || 'manage_self_non_staff');
  document.getElementById('framework-empty').classList.add('hidden');
  document.getElementById('framework-config').classList.remove('hidden');
  document.getElementById('framework-preview').classList.remove('hidden');
  document.getElementById('framework-integrations').classList.remove('hidden');

  renderStageDetailCards(stageConfigs);
  initIntegrationsSelect(stageConfigs);
  await loadStageItems(currentStage);
}

document.getElementById('btn-open-setup').addEventListener('click', () => {
  ensureSetupDraft(setupPayload || { behaviours: [], stages: [] });
  renderSetupEditor();
  openSetupModal();
});

document.getElementById('btn-close-setup').addEventListener('click', closeSetupModal);
document.getElementById('btn-cancel-setup').addEventListener('click', closeSetupModal);

document.getElementById('btn-save-setup').addEventListener('click', async () => {
  const payload = collectSetupPayload();
  if (!payload.behaviours.length) {
    showAlert('Behaviour wajib diisi minimal 1.', 'error');
    return;
  }
  if (!payload.stages.length) {
    showAlert('Tambahkan minimal satu career stage.', 'error');
    return;
  }

  const allPositions = (setupPayload?.positions || []).map((position) => Number(position.id));
  const selectedPositionIds = payload.stages.flatMap((stage) => stage.position_ids);
  const uniqueSelected = Array.from(new Set(selectedPositionIds));

  if (uniqueSelected.length !== selectedPositionIds.length) {
    showAlert('Satu golongan tidak boleh berada di beberapa career stage.', 'error');
    return;
  }

  const missingPositionIds = allPositions.filter((id) => !uniqueSelected.includes(Number(id)));
  if (missingPositionIds.length) {
    const missingNames = (setupPayload?.positions || [])
      .filter((position) => missingPositionIds.includes(Number(position.id)))
      .map((position) => position.name);
    showAlert(`Semua golongan harus kebagian stage. Yang belum dipilih: ${missingNames.join(', ')}`, 'error');
    return;
  }

  const res = await apiPost('/api/vnb-framework/setup-initialize', payload);
  if (!res.success) {
    showAlert(res.message || 'Gagal menyimpan kerangka awal.', 'error');
    return;
  }

  showAlert(res.message || 'Kerangka awal berhasil dibuat.');
  closeSetupModal();
  await loadFrameworkPage();
});

document.getElementById('integrations-stage-select').addEventListener('change', async (event) => {
  const stageCode = event.target.value;
  if (stageCode !== currentStage && integrationDirty) {
    const confirmed = window.confirm('Ada perubahan integrasi yang belum disimpan. Tetap pindah stage?');
    if (!confirmed) {
      event.target.value = currentStage;
      return;
    }
  }
  await loadStageItems(stageCode);
});

document.getElementById('save-integrations-btn').addEventListener('click', async () => {
  const stageConfig = stageConfigs.find((s) => s.career_stage === currentStage);
  const maxIntegrations = Number(stageConfig?.max_integrations || 2);

  const items = integrationRows.map((row) => {
    const base = Array.isArray(row.integrations) ? [...row.integrations] : [];
    const draft = integrationDraft[row.id] || base;
    const normalized = Array.from({ length: maxIntegrations }, (_, idx) => (draft[idx] || '').toString());
    return {
      id: row.id,
      integrations: normalized,
    };
  });

  if (!items.length) {
    showAlert('Belum ada item yang bisa disimpan.', 'warning');
    return;
  }

  const res = await apiPost('/api/vnb-framework/integrations', { items });
  if (!res.success) {
    showAlert(res.message || 'Gagal menyimpan integrasi.', 'error');
    return;
  }

  integrationDraft = {};
  clearDraftFromLocalStorage(currentStage);
  setIntegrationDirty(false);
  showAlert(res.message || 'Integrasi aktivitas berhasil disimpan.');
  await loadStageItems(currentStage);
});

document.getElementById('copy-prev-phase-btn').addEventListener('click', () => {
  if (!integrationRows.length) {
    showAlert('Belum ada data untuk di-copy.', 'warning');
    return;
  }

  const stageConfig = stageConfigs.find((s) => s.career_stage === currentStage);
  const maxIntegrations = Number(stageConfig?.max_integrations || 2);

  const grouped = {};
  integrationRows.forEach((row) => {
    if (!grouped[row.behaviour]) grouped[row.behaviour] = [];
    grouped[row.behaviour].push(row);
  });

  Object.values(grouped).forEach((rows) => {
    rows.sort((a, b) => extractPhaseOrder(a.phase) - extractPhaseOrder(b.phase));

    for (let i = 1; i < rows.length; i++) {
      const prev = rows[i - 1];
      const current = rows[i];
      const prevDraft = integrationDraft[prev.id] || (Array.isArray(prev.integrations) ? [...prev.integrations] : []);

      integrationDraft[current.id] = Array.from({ length: maxIntegrations }, (_, idx) => {
        return (prevDraft[idx] || '').toString();
      });
    }
  });

  refreshIntegrationDirtyState(currentStage, maxIntegrations);
  const cfg = stageConfigs.find((s) => s.career_stage === currentStage) || null;
  renderIntegrationsTable(cfg, integrationRows);
  showAlert('Integrasi berhasil di-copy dari fase sebelumnya. Klik Simpan Integrasi untuk menyimpan.', 'success');
});

document.getElementById('discard-local-draft-btn').addEventListener('click', () => {
  if (!currentStage) {
    showAlert('Career stage tidak valid.', 'error');
    return;
  }

  const hasDraftInMemory = Object.keys(integrationDraft).length > 0;
  const hasDraftInStorage = !!(window.localStorage && window.localStorage.getItem(getIntegrationDraftStorageKey(currentStage)));
  if (!hasDraftInMemory && !hasDraftInStorage) {
    showAlert('Tidak ada draft lokal untuk dibuang.', 'warning');
    return;
  }

  const confirmed = window.confirm('Draft lokal stage ini akan dihapus. Lanjutkan?');
  if (!confirmed) return;

  integrationDraft = {};
  clearDraftFromLocalStorage(currentStage);
  setIntegrationDirty(false);

  const cfg = stageConfigs.find((s) => s.career_stage === currentStage) || null;
  renderIntegrationsTable(cfg, integrationRows);
  showAlert('Draft lokal berhasil dibuang.', 'success');
});

document.getElementById('reset-template-btn').addEventListener('click', async () => {
  if (!currentStage) {
    showAlert('Career stage tidak valid.', 'error');
    return;
  }

  const confirmed = window.confirm('Reset akan mengembalikan semua integrasi stage ini ke template default. Lanjutkan?');
  if (!confirmed) return;

  const res = await apiPost('/api/vnb-framework/reset-stage-template', {
    career_stage: currentStage,
  });

  if (!res.success) {
    showAlert(res.message || 'Gagal reset template.', 'error');
    return;
  }

  integrationDraft = {};
  clearDraftFromLocalStorage(currentStage);
  setIntegrationDirty(false);
  showAlert(res.message || 'Template stage berhasil di-reset.');
  await loadStageItems(currentStage);
});

window.addEventListener('beforeunload', warnBeforeUnload);

loadFrameworkPage();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-framework/index.blade.php ENDPATH**/ ?>