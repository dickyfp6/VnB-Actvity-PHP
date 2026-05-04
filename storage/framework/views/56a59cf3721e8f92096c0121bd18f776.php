
<?php $__env->startSection('title','VnB Framework'); ?>
<?php $__env->startSection('page_title','VnB Framework'); ?>
<?php $__env->startSection('page_subtitle','Bangun behaviour, career stage, dan peta golongan dalam satu ruang kerja yang ringkas.'); ?>
<?php $__env->startSection('content'); ?>
<style>
  .fw-card {
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(55,170,5,0.12);
    border-radius: 1rem;
    padding: 1.75rem;
    box-shadow: 0 4px 24px rgba(31,38,135,0.06);
    transition: box-shadow .25s ease, transform .25s ease;
  }
  .fw-card:hover { box-shadow: 0 8px 32px rgba(31,38,135,0.1); }
  .fw-card-title {
    font-size: 1.1rem; font-weight: 700; color: var(--color-neutral-900);
    display: flex; align-items: center; gap: .5rem;
  }
  .fw-card-title i { color: var(--color-primary); font-size: .95rem; }
  .fw-card-desc { font-size: .82rem; color: var(--color-neutral-600); margin-top: .25rem; }
  .fw-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1.1rem; border-radius: 999px; font-size: .82rem; font-weight: 600;
    border: none; cursor: pointer; transition: all .2s ease;
  }
  .fw-btn-primary {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
    color: #fff; box-shadow: 0 3px 12px rgba(55,170,5,0.25);
  }
  .fw-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(55,170,5,0.35); }
  .fw-btn-outline {
    background: transparent; color: var(--color-neutral-700);
    border: 1px solid var(--color-neutral-300);
  }
  .fw-btn-outline:hover { background: rgba(55,170,5,0.06); border-color: var(--color-primary); color: var(--color-primary-dark); }
  .fw-empty-icon {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    background: linear-gradient(135deg, rgba(55,170,5,0.12), rgba(95,196,46,0.08));
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
  }
  .fw-empty-icon i { font-size: 1.8rem; color: var(--color-primary); }
  .fw-select {
    border: 1.5px solid var(--color-neutral-300); border-radius: .6rem;
    padding: .45rem .75rem; font-size: .82rem; font-weight: 500;
    transition: border-color .2s ease, box-shadow .2s ease; background: #fff;
  }
  .fw-select:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(55,170,5,0.1); }
  .fw-badge-dirty {
    font-size: .72rem; padding: .25rem .6rem; border-radius: 999px;
    background: rgba(234,179,8,0.12); color: #a16207; font-weight: 600;
  }
  .fw-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
  .fw-stage-card {
    border: 1px solid rgba(55,170,5,0.15); border-radius: .75rem;
    padding: 1.25rem; background: rgba(255,255,255,0.6);
    transition: border-color .2s ease;
  }
  .fw-stage-card:hover { border-color: rgba(55,170,5,0.35); }
  .fw-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.45);
    backdrop-filter: blur(6px); z-index: 50;
    display: none; align-items: center; justify-content: center; padding: 1.5rem;
  }
  .fw-modal-overlay.open { display: flex; }
  /* Inline setup mode: render modal content inline in page instead of popup */
  body.inline-setup .fw-modal-overlay.open {
    position: static !important;
    display: block !important;
    background: transparent !important;
    inset: auto !important;
    padding: 0 !important;
  }
  body.inline-setup .fw-modal {
    box-shadow: none; max-width: none; width: 100%; padding: 1rem 1.25rem; border-radius: 0.75rem;
    margin-bottom: 1rem;
  }
  .fw-modal {
    background: #fff; border-radius: 1.25rem; width: 100%; max-width: 52rem;
    padding: 2rem; max-height: 88vh; overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    animation: fwSlideUp .3s ease-out;
  }
  @keyframes fwSlideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  .fw-input {
    width: 100%; border: 1.5px solid var(--color-neutral-300); border-radius: .5rem;
    padding: .5rem .75rem; font-size: .82rem; transition: border-color .2s ease;
  }
  .fw-input:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(55,170,5,0.08); }
  .fw-section-num {
    width: 1.6rem; height: 1.6rem; border-radius: 50%; font-size: .72rem; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light)); color: #fff;
  }
  .stage-card.drag-over,
  #editor-level-pool.drag-over {
    border-color: var(--color-primary);
    background: rgba(55,170,5,0.04);
    box-shadow: 0 0 0 3px rgba(55,170,5,0.08);
  }
  .fw-drag-handle {
    cursor: grab;
    padding: 0.5rem 0.25rem;
    color: var(--color-neutral-400);
    transition: color 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .fw-drag-handle:hover { color: var(--color-primary); }
  .fw-drag-handle:active { cursor: grabbing; }
  .dragging-item {
    opacity: 0.4;
    border: 2px dashed var(--color-primary) !important;
    background: rgba(55,170,5,0.02) !important;
  }
  .drag-over-sort {
    border-top: 2px solid var(--color-primary) !important;
  }
</style>

<div class="py-2 space-y-5 animate-fade-in">

  
  <section id="framework-empty" class="hidden fw-card text-center py-10">
    <div class="fw-empty-icon"><i class="fas fa-layer-group"></i></div>
    <h2 class="text-lg font-bold text-gray-900 mb-1">Framework Belum Dibuat</h2>
    <p class="text-sm text-gray-500 mb-5">Mulai dengan membuat kerangka VnB Framework untuk organisasi kamu.</p>
    <button id="btn-open-setup" class="fw-btn fw-btn-primary"><i class="fas fa-plus"></i> Buat Framework</button>
  </section>

  
  <section id="framework-config" class="hidden fw-card">
    <div class="fw-card-title"><i class="fas fa-sliders-h"></i> Pengaturan Detail Framework</div>
    <div id="stage-config-list" class="space-y-4"></div>
  </section>

  

  
  <section id="framework-integrations" class="hidden">
    <div class="flex items-end justify-between border-b border-gray-200 mb-6 px-1">
      <div id="integrations-stage-tabs" class="flex gap-6"></div>
      <div class="flex items-center gap-3 pb-2">
        <button id="btn-edit-career-stage" class="fw-btn fw-btn-outline text-xs"><i class="fas fa-pen"></i> Edit Career Stage</button>
      </div>
    </div>

    <div id="active-stage-config-container" class="border border-gray-200 rounded-xl p-4 bg-white mb-4">
      <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-bold text-gray-800">Konfigurasi Stage Aktif</h4>
        <div class="flex items-center gap-3">
          <span id="stage-autosave-indicator" class="text-xs text-gray-500 hidden">Auto-save aktif</span>
          <button id="btn-close-config" class="text-gray-400 hover:text-gray-600 hidden"><i class="fas fa-times"></i></button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="stage-max-integrations" class="text-xs font-semibold text-gray-500 block mb-1.5">Jumlah Integrasi (minimal 1)</label>
          <input id="stage-max-integrations" type="number" min="1" max="20" value="1" class="w-28 fw-input text-center">
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-500 block mb-1.5">Jumlah Fase & Durasi (bulan)</label>
          <div id="stage-phase-list" class="space-y-2"></div>
          <button id="btn-add-phase" type="button" class="fw-btn fw-btn-outline text-xs mt-2"><i class="fas fa-plus"></i> Tambah Fase</button>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
        <button id="btn-save-stage-config" class="fw-btn fw-btn-primary"><i class="fas fa-save"></i> Simpan & Lanjutkan</button>
      </div>
    </div>

    <div id="integrations-table-container" class="hidden">
      <div class="flex items-center justify-between mb-3 gap-2 flex-wrap">
        <div class="text-sm font-semibold text-gray-800">Integrasi Pengukuran (Activity VnB)</div>
        <div class="fw-toolbar">
          <button id="btn-open-config" class="fw-btn fw-btn-outline text-xs"><i class="fas fa-cog"></i> Konfigurasi Stage</button>
          <button id="btn-clone-stage" class="fw-btn fw-btn-outline"><i class="fas fa-clone"></i> Clone</button>
          <button id="btn-edit-vnb-activity" class="fw-btn fw-btn-primary"><i class="fas fa-pen"></i> Edit Integrasi</button>
          <span id="integrations-dirty-badge" class="hidden fw-badge-dirty">⚠ Belum disimpan</span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="table-modern w-full">
          <thead id="integrations-head"></thead>
          <tbody id="integrations-body"></tbody>
        </table>
      </div>
    </div>
  </section>

  
  <section id="framework-incomplete" class="hidden fw-card text-center py-12">
    <div class="fw-empty-icon" style="background: linear-gradient(135deg, rgba(239,68,68,0.12), rgba(239,68,68,0.08));">
      <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
    </div>
    <h2 class="text-lg font-bold text-gray-900 mb-2">Framework Perlu Diperbarui</h2>
    <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
      Terdapat golongan karyawan yang belum terpetakan ke dalam Career Stage. 
      Anda harus melengkapi pemetaan ini sebelum dapat mengelola integrasi aktivitas.
    </p>
    <button id="btn-fix-framework" class="fw-btn fw-btn-primary">
      <i class="fas fa-tools"></i> Perbarui Pemetaan Sekarang
    </button>
  </section>
</div>


<div id="framework-setup-editor" class="fw-card hidden">
  <div class="flex items-center justify-between mb-4">
    <div class="fw-card-title"><i class="fas fa-tools"></i> Buat Kerangka VnB Framework</div>
    <div class="flex items-center gap-2">
      <button id="btn-create-framework" class="fw-btn fw-btn-primary">Buat Framework</button>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Behaviours -->
    <div class="md:col-span-1">
      <div class="flex items-center gap-2 mb-3">
        <span class="fw-section-num">1</span>
        <h4 class="text-sm font-bold text-gray-800">Behaviour</h4>
      </div>
      <div id="editor-behaviour-list" class="space-y-2 mb-3"></div>
      <button type="button" id="editor-add-behaviour" class="fw-btn fw-btn-outline text-xs"><i class="fas fa-plus"></i> Tambah Behaviour</button>
    </div>

    <!-- Career Stages -->
    <div class="md:col-span-2">
      <div class="flex items-center gap-2 mb-3">
        <span class="fw-section-num">2</span>
        <h4 class="text-sm font-bold text-gray-800">Career Stage</h4>
      </div>

      <div class="flex gap-4">
        <div class="flex-1">
          <div id="editor-stage-list" class="space-y-3 mb-4"></div>
          <button type="button" id="editor-add-stage" class="fw-btn fw-btn-outline text-xs mb-3"><i class="fas fa-plus"></i> Tambah Career Stage</button>
        </div>
        <div class="w-1/3">
          <div class="border border-gray-200 rounded-lg bg-white p-3">
            <h5 class="text-sm font-semibold text-gray-700 mb-2">Pool Golongan</h5>
            <div id="editor-level-pool" class="flex flex-wrap gap-2 min-h-[56px]"></div>
            <p class="mt-2 text-xs text-gray-500 leading-relaxed">
              Seret golongan ke career stage yang sesuai untuk menerapkan pembagian.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="clone-stage-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeCloneModal()"></div>
  <div class="max-w-md mx-auto mt-24 bg-white rounded-lg shadow-lg relative p-5">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold">Clone ke career stage mana?</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeCloneModal()"><i class="fas fa-times"></i></button>
    </div>
    <div id="clone-stage-options" class="space-y-2">Loading…</div>
    <div class="mt-4 flex justify-end">
      <button type="button" class="fw-btn fw-btn-outline" onclick="closeCloneModal()">Cancel</button>
    </div>
  </div>
</div>


<div id="confirm-dialog-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="window.closeConfirmDialog && window.closeConfirmDialog(false)"></div>
  <div class="max-w-md mx-auto mt-24 bg-white rounded-lg shadow-lg relative p-6">
    <div class="flex items-start gap-3 mb-4">
      <div id="confirm-dialog-icon" class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div>
        <h3 id="confirm-dialog-title" class="text-lg font-semibold text-gray-900">Konfirmasi</h3>
        <p id="confirm-dialog-subtitle" class="text-sm text-gray-500 mt-0.5 hidden"></p>
      </div>
    </div>
    <p id="confirm-dialog-message" class="text-gray-700 mb-6 text-sm leading-relaxed"></p>
    <div class="flex justify-end gap-3">
      <button id="confirm-dialog-cancel" type="button" class="fw-btn fw-btn-outline">Batal</button>
      <button id="confirm-dialog-confirm" type="button" class="fw-btn fw-btn-primary">Lanjutkan</button>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let setupPayload = null;
let behaviourDraft = [];
let stageDraft = [];
let stageConfigs = [];
let currentStage = 'manage_self_non_staff';
let integrationRows = [];
let integrationDraft = {};
let integrationDirty = false;
const integrationDraftStoragePrefix = 'vnb_framework_integration_draft_v1';
let stageLabelMap = {};
let draggedLevelId = null;
let draggedLevelOrigin = null;
let stageConfigDraft = { max_integrations: 1, phases: [{ duration_months: 1 }] };
let stageConfigDraftStage = null;
let stageConfigAutosaveTimer = null;
let integrationEditEnabled = true;
let frameworkEditorMode = 'create';

// ===== DIRTY STATE TRACKING =====
let behaviourDraftOriginal = [];
let behaviourDirty = false;
let stageDraftOriginal = [];
let stageDirty = false;
let stageConfigDraftOriginal = { max_integrations: 1, phases: [{ duration_months: 1 }] };
let stageConfigDirty = false;

// Snapshot original state when opening editor
function snapshotOriginalState() {
  behaviourDraftOriginal = JSON.parse(JSON.stringify(behaviourDraft));
  stageDraftOriginal = JSON.parse(JSON.stringify(stageDraft));
  stageConfigDraftOriginal = JSON.parse(JSON.stringify(stageConfigDraft));
  behaviourDirty = false;
  stageDirty = false;
  stageConfigDirty = false;
}

// Check if behaviour list changed
function checkBehaviourDirty() {
  behaviourDirty = JSON.stringify(behaviourDraft) !== JSON.stringify(behaviourDraftOriginal);
  return behaviourDirty;
}

// Check if stage setup changed (labels or level assignments)
function checkStageDirty() {
  stageDirty = JSON.stringify(stageDraft) !== JSON.stringify(stageDraftOriginal);
  return stageDirty;
}

// Check if stage config changed (phases or max_integrations)
function checkStageConfigDirty() {
  stageConfigDirty = JSON.stringify(stageConfigDraft) !== JSON.stringify(stageConfigDraftOriginal);
  return stageConfigDirty;
}

// Check if ANY unsaved changes exist
function hasUnsavedChanges() {
  return checkBehaviourDirty() || checkStageDirty() || checkStageConfigDirty();
}

// Show confirmation dialog
let confirmDialogState = {
  onConfirm: null,
  onCancel: null,
};

function closeConfirmDialog(triggerCancel = true) {
  const modal = document.getElementById('confirm-dialog-modal');
  const cancelHandler = confirmDialogState.onCancel;
  confirmDialogState.onConfirm = null;
  confirmDialogState.onCancel = null;

  if (modal) {
    modal.classList.add('hidden');
  }

  if (triggerCancel && cancelHandler) {
    cancelHandler();
  }
}

window.closeConfirmDialog = closeConfirmDialog;

function showConfirmDialog(message, onConfirm, onCancel, options = {}) {
  const modal = document.getElementById('confirm-dialog-modal');
  const msgEl = document.getElementById('confirm-dialog-message');
  const titleEl = document.getElementById('confirm-dialog-title');
  const subtitleEl = document.getElementById('confirm-dialog-subtitle');
  const confirmBtn = document.getElementById('confirm-dialog-confirm');
  const cancelBtn = document.getElementById('confirm-dialog-cancel');

  confirmDialogState.onConfirm = onConfirm || null;
  confirmDialogState.onCancel = onCancel || null;
  msgEl.textContent = message;
  titleEl.textContent = options.title || 'Konfirmasi';

  if (subtitleEl) {
    if (options.subtitle) {
      subtitleEl.textContent = options.subtitle;
      subtitleEl.classList.remove('hidden');
    } else {
      subtitleEl.textContent = '';
      subtitleEl.classList.add('hidden');
    }
  }

  confirmBtn.textContent = options.confirmText || 'Lanjutkan';
  confirmBtn.className = options.confirmButtonClass || 'fw-btn fw-btn-primary';
  confirmBtn.style.cssText = options.confirmButtonStyle || '';
  cancelBtn.textContent = options.cancelText || 'Batal';

  confirmBtn.onclick = () => {
    const confirmHandler = confirmDialogState.onConfirm;
    closeConfirmDialog(false);
    if (confirmHandler) {
      confirmHandler();
    }
  };

  cancelBtn.onclick = () => {
    closeConfirmDialog(true);
  };

  modal.classList.remove('hidden');
}

// Show destructive operation warning
function showDestructiveWarning(itemName, childCount, onConfirm, onCancel) {
  const message = childCount > 0
    ? `Hapus "${itemName}"? ${childCount} data integrasi yang terkait akan direset ke nilai awal.`
    : `Hapus "${itemName}"?`;
  showConfirmDialog(message, onConfirm, onCancel, {
    title: 'Hapus Data',
    confirmText: 'Ya, Hapus',
    cancelText: 'Batal',
    confirmButtonClass: 'fw-btn fw-btn-primary',
    confirmButtonStyle: 'background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff;',
  });
}

// ===== STEP 1: BEHAVIOUR MANAGEMENT =====
function renderBehaviourList() {
  const container = document.getElementById('editor-behaviour-list');
  container.innerHTML = behaviourDraft.map((value, index) => `
    <div class="flex items-center gap-2 behaviour-row" draggable="true" data-index="${index}">
      <div class="fw-drag-handle"><i class="fas fa-grip-vertical"></i></div>
      <input type="text" class="behaviour-input flex-1 fw-input" data-index="${index}" value="${String(value || '').replace(/"/g, '&quot;')}" placeholder="Nama behaviour">
      <button type="button" class="remove-behaviour-btn fw-btn fw-btn-outline" style="color:#dc2626;border-color:rgba(239,68,68,0.3)"><i class="fas fa-trash-alt"></i></button>
    </div>
  `).join('');

  container.querySelectorAll('.behaviour-row').forEach(row => {
    row.addEventListener('dragstart', (e) => {
      e.dataTransfer.setData('text/plain', row.dataset.index);
      row.classList.add('dragging-item');
    });
    row.addEventListener('dragend', () => row.classList.remove('dragging-item'));
    row.addEventListener('dragover', (e) => {
      e.preventDefault();
      row.classList.add('drag-over-sort');
    });
    row.addEventListener('dragleave', () => row.classList.remove('drag-over-sort'));
    row.addEventListener('drop', (e) => {
      e.preventDefault();
      row.classList.remove('drag-over-sort');
      const fromIdx = parseInt(e.dataTransfer.getData('text/plain'));
      const toIdx = parseInt(row.dataset.index);
      if (fromIdx === toIdx) return;
      
      const item = behaviourDraft.splice(fromIdx, 1)[0];
      behaviourDraft.splice(toIdx, 0, item);
      renderBehaviourList();
    });
  });

  document.querySelectorAll('.behaviour-input').forEach((input) => {
    input.addEventListener('input', (event) => {
      const index = Number(event.target.dataset.index);
      behaviourDraft[index] = event.target.value;
    });
  });

  document.querySelectorAll('.remove-behaviour-btn').forEach((button) => {
    button.addEventListener('click', () => {
      const row = button.parentElement;
      const index = Number(row.querySelector('.behaviour-input').dataset.index);
      const behaviourName = behaviourDraft[index] || `Behaviour ${index + 1}`;
      
      showConfirmDialog(
        `Hapus "${behaviourName}"?`,
        () => {
          behaviourDraft.splice(index, 1);
          if (!behaviourDraft.length) {
            behaviourDraft.push('');
          }
          renderBehaviourList();
        },
        () => {
          // User cancelled
        }
      );
    });
  });
}

function showFrameworkSetupEditor(useCurrentDraft = false) {
  frameworkEditorMode = useCurrentDraft ? 'edit' : 'create';

  if (!useCurrentDraft) {
    // initialize drafts from server payload or local draft
    behaviourDraft = Array.isArray(setupPayload?.behaviours) ? setupPayload.behaviours.map((v) => String(v)) : [];
    if (!behaviourDraft.length) behaviourDraft = [''];

    // stages draft - try to load saved draft first
    const local = loadEditorDraft();
    if (local) {
      behaviourDraft = Array.isArray(local.behaviours) && local.behaviours.length ? local.behaviours : behaviourDraft;
      stageDraft = Array.isArray(local.stages) && local.stages.length ? local.stages : [{ label: '', level_ids: [] }];
    } else {
      stageDraft = [{ label: '', level_ids: [] }];
    }
  }

  // Snapshot original state for dirty-state tracking
  snapshotOriginalState();

  renderBehaviourList();
  renderStageList();
  syncFrameworkEditorActionButton();
  document.getElementById('framework-setup-editor').classList.remove('hidden');
  document.getElementById('framework-empty').classList.add('hidden');
  document.getElementById('framework-config').classList.add('hidden');
  const previewEl = document.getElementById('framework-preview');
  if (previewEl) previewEl.classList.add('hidden');
  document.getElementById('framework-integrations').classList.add('hidden');
}

function syncFrameworkEditorActionButton() {
  const button = document.getElementById('btn-create-framework');
  if (!button) return;

  if (frameworkEditorMode === 'edit') {
    button.innerHTML = '<i class="fas fa-save"></i> Simpan Framework';
  } else {
    button.innerHTML = '<i class="fas fa-plus"></i> Buat Framework';
  }
}

function hideFrameworkSetupEditor() {
  document.getElementById('framework-setup-editor').classList.add('hidden');
}

// ===== STEP 2: CAREER STAGE MANAGEMENT =====
function renderStageList() {
  const container = document.getElementById('editor-stage-list');
  const levels = setupPayload?.levels || [];

  container.innerHTML = stageDraft.map((stage, stageIndex) => {
    const assignedIds = new Set((stage.level_ids || []).map((id) => String(id)));
    const assignedPills = (stage.level_ids || []).map((id) => {
      const lvl = (levels || []).find((l) => String(l.id) === String(id));
      const name = lvl ? lvl.name : id;
      return `<span class="level-pill assigned inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 border border-green-100 text-sm cursor-grab" draggable="true" data-level-id="${id}" data-stage-index="${stageIndex}">${name}</span>`;
    }).join('');

    return `
      <div class="border border-gray-200 rounded-lg p-3 bg-gray-50 stage-card" draggable="true" data-stage-index="${stageIndex}">
        <div class="flex items-center gap-2 mb-3">
          <div class="fw-drag-handle"><i class="fas fa-grip-vertical"></i></div>
          <input type="text" class="stage-label fw-input text-sm" style="max-width:420px" data-index="${stageIndex}" value="${String(stage.label || '').replace(/"/g, '&quot;')}" placeholder="Nama career stage">
          <button type="button" class="remove-stage-btn fw-btn fw-btn-outline" style="color:#dc2626;border-color:rgba(239,68,68,0.3);padding:0.4rem 0.8rem"><i class="fas fa-trash-alt text-xs"></i></button>
        </div>
        <div class="mb-2">
          <div class="stage-pills flex flex-wrap gap-2">${assignedPills}</div>
        </div>
      </div>
    `;
  }).join('');

  document.querySelectorAll('.stage-card').forEach((card) => {
    card.addEventListener('dragstart', (e) => {
      if (draggedLevelId) return; // ignore if dragging level pill
      e.dataTransfer.setData('text/stage-index', card.dataset.stageIndex);
      card.classList.add('dragging-item');
    });

    card.addEventListener('dragend', () => card.classList.remove('dragging-item'));

    card.addEventListener('dragover', (event) => {
      event.preventDefault();
      if (draggedLevelId) {
        card.classList.add('drag-over');
      } else {
        card.classList.add('drag-over-sort');
      }
    });

    card.addEventListener('dragleave', () => {
      card.classList.remove('drag-over', 'drag-over-sort');
    });

    card.addEventListener('drop', (event) => {
      event.preventDefault();
      card.classList.remove('drag-over', 'drag-over-sort');
      
      const stageFromIdx = event.dataTransfer.getData('text/stage-index');
      if (stageFromIdx !== "") {
        const fromIdx = parseInt(stageFromIdx);
        const toIdx = parseInt(card.dataset.stageIndex);
        if (fromIdx === toIdx) return;
        const item = stageDraft.splice(fromIdx, 1)[0];
        stageDraft.splice(toIdx, 0, item);
        renderStageList();
        return;
      }

      const levelId = String(draggedLevelId || event.dataTransfer.getData('text/plain') || '');
      if (!levelId) return;

      stageDraft.forEach((stage) => {
        stage.level_ids = (stage.level_ids || []).filter((id) => String(id) !== levelId);
      });
      const targetIndex = Number(card.dataset.stageIndex);
      if (!stageDraft[targetIndex].level_ids) stageDraft[targetIndex].level_ids = [];
      if (!stageDraft[targetIndex].level_ids.includes(levelId)) {
        stageDraft[targetIndex].level_ids.push(levelId);
      }
      draggedLevelId = null;
      draggedLevelOrigin = null;
      renderStageList();
    });
  });

  document.querySelectorAll('.stage-label').forEach((input) => {
    input.addEventListener('input', (event) => {
      const index = Number(event.target.dataset.index);
      stageDraft[index].label = event.target.value;
    });
  });

  document.querySelectorAll('.remove-stage-btn').forEach((button) => {
    button.addEventListener('click', (ev) => {
      ev.stopPropagation();
      const card = button.closest('.stage-card');
      const index = Number(card.dataset.stageIndex);
      const stageName = stageDraft[index]?.label || `Career Stage ${index + 1}`;
      
      showConfirmDialog(
        `Hapus "${stageName}"?`,
        () => {
          stageDraft.splice(index, 1);
          if (!stageDraft.length) stageDraft.push({ label: '', level_ids: [] });
          renderStageList();
        },
        () => {
          // User cancelled
        }
      );
    });
  });

  document.querySelectorAll('.level-pill.assigned').forEach((pill) => {
    pill.addEventListener('dragstart', (event) => {
      draggedLevelId = String(pill.dataset.levelId);
      draggedLevelOrigin = 'stage';
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', draggedLevelId);
    });

    pill.addEventListener('dragend', () => {
      draggedLevelId = null;
      draggedLevelOrigin = null;
      document.querySelectorAll('.stage-card, #editor-level-pool').forEach((el) => el.classList.remove('drag-over'));
    });

    pill.addEventListener('click', (ev) => {
      ev.stopPropagation();
    });
  });

  renderLevelPool();
}

function renderLevelPool() {
  const pool = document.getElementById('editor-level-pool');
  const levels = setupPayload?.levels || [];
  const assigned = new Set(stageDraft.flatMap((s) => (s.level_ids || []).map((id) => String(id))));

  pool.innerHTML = levels.map((lvl) => {
    const id = String(lvl.id);
    if (assigned.has(id)) return '';
    if (!lvl.is_active) return '';
    return `<span class="level-pill pool inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border text-sm cursor-grab" draggable="true" data-level-id="${id}">${lvl.name}</span>`;
  }).join('');

  pool.addEventListener('dragover', (event) => {
    event.preventDefault();
    pool.classList.add('drag-over');
  });

  pool.addEventListener('dragleave', () => {
    pool.classList.remove('drag-over');
  });

  pool.addEventListener('drop', (event) => {
    event.preventDefault();
    pool.classList.remove('drag-over');
    const levelId = String(draggedLevelId || event.dataTransfer.getData('text/plain') || '');
    if (!levelId) return;
    stageDraft.forEach((s) => {
      s.level_ids = (s.level_ids || []).filter((id) => String(id) !== levelId);
    });
    draggedLevelId = null;
    draggedLevelOrigin = null;
    renderStageList();
  });

  document.querySelectorAll('#editor-level-pool .level-pill.pool').forEach((pill) => {
    pill.addEventListener('dragstart', (event) => {
      draggedLevelId = String(pill.dataset.levelId);
      draggedLevelOrigin = 'pool';
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', draggedLevelId);
    });

    pill.addEventListener('dragend', () => {
      draggedLevelId = null;
      draggedLevelOrigin = null;
      document.querySelectorAll('.stage-card, #editor-level-pool').forEach((el) => el.classList.remove('drag-over'));
    });
  });
}

// editor uses renderStageList() and is shown via showFrameworkSetupEditor()

function renderStageDetailCards(stages) {
  const container = document.getElementById('stage-config-list');
  container.innerHTML = stages.map((stage) => {
    // Ensure phases array exists; default to a single phase
    const phaseRows = Array.isArray(stage.phases) && stage.phases.length ? stage.phases : [{ phase_order: 1, duration_months: 3 }];

    const phaseInputs = phaseRows.map((phase, idx) => `
      <div class="flex items-center gap-2 mb-2">
        <span class="text-xs font-semibold text-gray-500 w-16">Fase ${idx + 1}</span>
        <input type="number" min="1" max="60" value="${phase.duration_months}" data-stage="${stage.career_stage}" data-phase-index="${idx}" class="phase-duration w-24 fw-input text-center">
        <span class="text-xs text-gray-400">bulan</span>
      </div>
    `).join('');

    return `
      <div class="fw-stage-card">
        <div class="font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="fas fa-bookmark text-green-500 text-xs"></i> ${stage.label}</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-500 block mb-1.5">Maks. Integrasi Aktivitas</label>
            <input type="number" min="1" max="20" value="${stage.max_integrations || 2}" data-stage="${stage.career_stage}" class="max-integrations w-24 fw-input text-center">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-500 block mb-1.5">Durasi per Fase</label>
            ${phaseInputs}
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100">
          <button type="button" class="save-stage fw-btn fw-btn-primary text-xs" data-stage="${stage.career_stage}"><i class="fas fa-save"></i> Simpan Stage</button>
        </div>
      </div>
    `;
  }).join('');

  bindStageCardEvents();
}

function bindStageCardEvents() {
  document.querySelectorAll('.add-phase').forEach((button) => {
    // phase add button removed — UI restricted to single phase only
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

// Preview removed — function deleted per user request

function getStageConfigByCode(stageCode) {
  return (stageConfigs || []).find((s) => s.career_stage === stageCode) || null;
}

function normalizeStagePhases(phases) {
  const rows = (phases || [])
    .map((row) => Number(row?.duration_months || 0))
    .filter((value) => value > 0)
    .map((duration_months) => ({ duration_months }));

  return rows.length ? rows : [{ duration_months: 1 }];
}

function setStageAutosaveIndicator(message, tone = 'muted') {
  const el = document.getElementById('stage-autosave-indicator');
  if (!el) return;
  el.textContent = message;
  el.classList.remove('text-gray-500', 'text-green-600', 'text-red-600');
  if (tone === 'success') el.classList.add('text-green-600');
  else if (tone === 'error') el.classList.add('text-red-600');
  else el.classList.add('text-gray-500');
}

function renderStageTabs(stages) {
  const container = document.getElementById('integrations-stage-tabs');
  container.innerHTML = (stages || []).map((stage) => {
    const isActive = stage.career_stage === currentStage;
    if (isActive) {
      return `
        <button type="button" 
          class="px-1 py-3 font-bold transition-colors border-b-2 -mb-[2px] text-sm" 
          style="color: var(--color-primary-dark); border-color: var(--color-primary-dark);" 
          data-stage-tab="${stage.career_stage}">
          ${stage.label}
        </button>`;
    }
    return `
      <button type="button" 
        class="px-1 py-3 font-medium transition-colors text-gray-500 hover:text-gray-700 text-sm border-b-2 border-transparent -mb-[2px]" 
        data-stage-tab="${stage.career_stage}">
        ${stage.label}
      </button>`;
  }).join('');

  container.querySelectorAll('[data-stage-tab]').forEach((button) => {
    button.addEventListener('click', async () => {
      const stageCode = button.dataset.stageTab;
      if (!stageCode || stageCode === currentStage) return;

      if (integrationDirty) {
        showConfirmDialog(
          'Ada perubahan integrasi yang belum disimpan. Tetap pindah career stage?',
          async () => {
            currentStage = stageCode;
            renderStageTabs(stageConfigs);
            renderActiveStageConfig();
            await loadStageItems(currentStage);
          },
          () => {},
          {
            title: 'Pindah Career Stage',
            subtitle: 'Perubahan yang belum disimpan tetap ada di draft lokal sampai kamu simpan atau batalkan.',
            confirmText: 'Tetap Pindah',
            cancelText: 'Batal',
          }
        );
        return;
      }

      currentStage = stageCode;
      renderStageTabs(stageConfigs);
      renderActiveStageConfig();
      await loadStageItems(currentStage);
    });
  });
}

function renderActiveStageConfig() {
  const cfg = getStageConfigByCode(currentStage) || {};
  if (stageConfigDraftStage !== currentStage) {
    stageConfigDraft.max_integrations = Math.max(1, Number(cfg.max_integrations || 1));
    stageConfigDraft.phases = normalizeStagePhases(cfg.phases);
    stageConfigDraftStage = currentStage;
    stageConfigDraftOriginal = JSON.parse(JSON.stringify(stageConfigDraft));
  } else {
    stageConfigDraft.max_integrations = Math.max(1, Number(stageConfigDraft.max_integrations || cfg.max_integrations || 1));
    stageConfigDraft.phases = Array.isArray(stageConfigDraft.phases) && stageConfigDraft.phases.length
      ? stageConfigDraft.phases
      : normalizeStagePhases(cfg.phases);
  }

  const maxInput = document.getElementById('stage-max-integrations');
  if (maxInput) {
    maxInput.value = stageConfigDraft.max_integrations;
  }

  const phaseList = document.getElementById('stage-phase-list');
  if (phaseList) {
    phaseList.innerHTML = stageConfigDraft.phases.map((phase, index) => `
      <div class="flex items-center gap-2" data-phase-row="${index}">
        <span class="text-xs font-semibold text-gray-500 w-14">Fase ${index + 1}</span>
        <input type="number" min="1" max="60" class="fw-input w-24 text-center stage-phase-input" data-phase-index="${index}" value="${phase.duration_months}">
        <span class="text-xs text-gray-400">bulan</span>
        <button type="button" class="remove-phase-btn fw-btn fw-btn-outline text-xs ${stageConfigDraft.phases.length > 1 ? '' : 'hidden'}" data-phase-index="${index}"><i class="fas fa-trash-alt"></i></button>
      </div>
    `).join('');

    phaseList.querySelectorAll('.stage-phase-input').forEach((input) => {
      input.addEventListener('input', () => {
        const idx = Number(input.dataset.phaseIndex);
        const value = Math.max(1, Number(input.value || 1));
        stageConfigDraft.phases[idx].duration_months = value;
      });
    });

    phaseList.querySelectorAll('.remove-phase-btn').forEach((button) => {
      button.addEventListener('click', () => {
        const idx = Number(button.dataset.phaseIndex);
        if (stageConfigDraft.phases.length <= 1) return;

        const phaseDuration = Number(stageConfigDraft.phases[idx]?.duration_months || 1);
        const phaseName = `Fase ${idx + 1} (${phaseDuration} Bulan)`;
        const relatedItems = integrationRows.filter((row) => String(row.phase || '') === phaseName).length;

        showDestructiveWarning(
          phaseName,
          relatedItems,
          () => {
            // User confirmed deletion
            stageConfigDraft.phases.splice(idx, 1);
            renderActiveStageConfig();
          },
          () => {
            // User cancelled
          }
        );
      });
    });
  }

  setStageAutosaveIndicator('');
}

function getCurrentStageConfigPayload() {
  const maxIntegrations = Math.max(1, Number(document.getElementById('stage-max-integrations')?.value || 1));
  const durations = Array.from(document.querySelectorAll('.stage-phase-input'))
    .map((el) => Math.max(1, Number(el.value || 1)))
    .map((duration_months) => ({ duration_months }));

  return {
    career_stage: currentStage,
    max_integrations: maxIntegrations,
    phases: durations.length ? durations : [{ duration_months: 1 }],
  };
}

async function saveStageConfig(options = { silent: false }) {
  const payload = getCurrentStageConfigPayload();
  const res = await apiPost('/api/vnb-framework/stage-details', payload);

  if (!res.success) {
    setStageAutosaveIndicator('Auto-save gagal', 'error');
    if (!options.silent) {
      showAlert(res.message || 'Gagal menyimpan konfigurasi stage.', 'error');
    }
    return false;
  }

  // update local stageConfigs snapshot
  const idx = stageConfigs.findIndex((s) => s.career_stage === currentStage);
  if (idx >= 0) {
    stageConfigs[idx].max_integrations = payload.max_integrations;
    stageConfigs[idx].phases = payload.phases.map((phase, i) => ({ phase_order: i + 1, duration_months: phase.duration_months }));
  }

  stageConfigDraftStage = currentStage;
  stageConfigDraft.max_integrations = payload.max_integrations;
  stageConfigDraft.phases = payload.phases.map((phase) => ({ duration_months: phase.duration_months }));
  stageConfigDraftOriginal = JSON.parse(JSON.stringify(stageConfigDraft));
  stageConfigDirty = false;

  setStageAutosaveIndicator('Tersimpan', 'success');
  
  if (!options.silent) {
    toggleStageConfig(false);
  }
  
  return true;
}

function toggleStageConfig(showConfig) {
  const configContainer = document.getElementById('active-stage-config-container');
  const tableContainer = document.getElementById('integrations-table-container');
  const closeBtn = document.getElementById('btn-close-config');

  if (showConfig) {
    configContainer.classList.remove('hidden');
    tableContainer.classList.add('hidden');
    
    // show close button only if it's already configured (meaning user can go back to table)
    const cfg = getStageConfigByCode(currentStage);
    const hasConfig = cfg && Number(cfg.max_integrations || 0) > 0;
    closeBtn.classList.toggle('hidden', !hasConfig);
  } else {
    configContainer.classList.add('hidden');
    tableContainer.classList.remove('hidden');
  }
}


function setIntegrationEditEnabled(enabled) {
  integrationEditEnabled = !!enabled;
  const button = document.getElementById('btn-edit-vnb-activity');
  if (button) {
    button.innerHTML = integrationEditEnabled
      ? '<i class="fas fa-save"></i> Simpan Integrasi'
      : '<i class="fas fa-pen"></i> Edit Integrasi';
  }

  document.querySelectorAll('.integration-input').forEach((input) => {
    input.disabled = !integrationEditEnabled;
    input.classList.toggle('bg-gray-100', !integrationEditEnabled);
    input.classList.toggle('cursor-not-allowed', !integrationEditEnabled);
  });
}

function setIntegrationDirty(isDirty) {
  integrationDirty = !!isDirty;
  const badge = document.getElementById('integrations-dirty-badge');

  if (integrationDirty) {
    badge.classList.remove('hidden');
  } else {
    badge.classList.add('hidden');
  }
}

async function saveCurrentIntegrations() {
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
    return false;
  }

  // Frontend validation rules:
  // - Integrasi 1 (index 0) is mandatory for every row
  // - Integrasi 2 (index 1) is optional per row, but at least one row in this stage must have Integrasi 2 filled
  const missingIntegrasi1 = items.find((it) => !(String(it.integrations[0] || '').trim()));
  if (missingIntegrasi1) {
    showAlert('Integrasi 1 wajib diisi untuk semua baris sebelum menyimpan.', 'error');
    // highlight the first offending textarea if present
    const el = document.querySelector(`textarea.integration-input[data-item-id="${missingIntegrasi1.id}"][data-idx="0"]`);
    if (el) {
      el.classList.add('border-red-500', 'ring-1', 'ring-red-300');
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => el.classList.remove('border-red-500', 'ring-1', 'ring-red-300'), 4000);
    }
    return false;
  }

  const hasAnyIntegrasi2 = items.some((it) => String(it.integrations[1] || '').trim());
  if (!hasAnyIntegrasi2 && maxIntegrations >= 2) {
    showAlert('Setidaknya satu Integrasi 2 harus diisi untuk stage ini.', 'error');
    return false;
  }

  const res = await apiPost('/api/vnb-framework/integrations', { items });
  if (!res.success) {
    showAlert(res.message || 'Gagal menyimpan integrasi.', 'error');
    return false;
  }

  integrationDraft = {};
  clearDraftFromLocalStorage(currentStage);
  setIntegrationDirty(false);
  showAlert(res.message || 'Integrasi aktivitas berhasil disimpan.');
  await loadStageItems(currentStage);
  return true;
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
  const match = /^F(?:ase)?\s*(\d+)/i.exec((phaseLabel || '').toString().trim());
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
      return `<td><textarea data-item-id="${row.id}" data-idx="${idx}" rows="2" class="integration-input w-full border border-gray-300 rounded px-2 py-1 text-sm${dirtyClass}" ${integrationEditEnabled ? '' : 'disabled'}>${value}</textarea></td>`;
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

  setIntegrationEditEnabled(integrationEditEnabled);
}

async function loadStageItems(stageCode) {
  const res = await apiGet(`/api/vnb-framework?career_stage=${stageCode}`);
  if (!res.success || res.setup_required) {
    showAlert('Gagal memuat data stage.', 'error');
    return;
  }

  stageLabelMap = Object.fromEntries((res.stages || []).map((stage) => [stage.career_stage, stage.label]));

  currentStage = stageCode;
  const behaviorOrder = setupPayload?.behaviours || [];
  integrationRows = (res.data || []).sort((a, b) => {
    const idxA = behaviorOrder.indexOf(a.behaviour);
    const idxB = behaviorOrder.indexOf(b.behaviour);
    if (idxA !== idxB) return idxA - idxB;
    return extractPhaseOrder(a.phase) - extractPhaseOrder(b.phase);
  });
  const maxIntegrations = Number(((res.stages || []).find((s) => s.career_stage === stageCode)?.max_integrations) || 2);
  loadDraftFromLocalStorage(stageCode, maxIntegrations);
  refreshIntegrationDirtyState(stageCode, maxIntegrations);
  const cfg = (res.stages || []).find((s) => s.career_stage === stageCode) || null;

  // Only allow editing/filling integrations after stage configuration (max_integrations & phases) exists.
  const hasStageConfig = cfg && Number(cfg.max_integrations || 0) > 0 && Array.isArray(cfg.phases) && cfg.phases.length > 0;

  // start in read mode; the button switches to edit mode on demand
  setIntegrationEditEnabled(false);
  const cloneBtn = document.getElementById('btn-clone-stage');
  const editActivityBtn = document.getElementById('btn-edit-vnb-activity');
  if (cloneBtn) cloneBtn.disabled = !hasStageConfig;
  if (editActivityBtn) editActivityBtn.disabled = !hasStageConfig;

  toggleStageConfig(!hasStageConfig);
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
    // show inline setup editor so user can fill behaviours + stages on the page
    showFrameworkSetupEditor();
    return;
  }

  stageConfigs = res.stages || [];
  currentStage = res.career_stage || (stageConfigs[0]?.career_stage || 'manage_self_non_staff');
  hideFrameworkSetupEditor();
  
  document.getElementById('framework-empty').classList.add('hidden');
  document.getElementById('framework-config').classList.add('hidden');
  document.getElementById('framework-integrations').classList.add('hidden');
  document.getElementById('framework-incomplete').classList.add('hidden');

  if (res.framework_incomplete) {
    document.getElementById('framework-incomplete').classList.remove('hidden');
    return;
  }

  document.getElementById('framework-integrations').classList.remove('hidden');

  renderStageTabs(stageConfigs);
  renderActiveStageConfig();
  setIntegrationEditEnabled(false);
  await loadStageItems(currentStage);
}

// Setup editor event bindings
document.getElementById('btn-open-setup').addEventListener('click', () => {
  showFrameworkSetupEditor(false);
});

// add behaviour in editor
document.getElementById('editor-add-behaviour').addEventListener('click', () => {
  behaviourDraft.push('');
  renderBehaviourList();
});

// add stage in editor
document.getElementById('editor-add-stage').addEventListener('click', () => {
  stageDraft.push({ label: '', level_ids: [] });
  renderStageList();
});

function getEditorDraftStorageKey() { return 'vnb_framework_editor_draft_v1'; }

function loadEditorDraft() {
  try {
    const raw = window.localStorage.getItem(getEditorDraftStorageKey());
    if (!raw) return null;
    const parsed = JSON.parse(raw);
    return parsed;
  } catch (e) { return null; }
}

// create framework (finalize)
document.getElementById('btn-create-framework').addEventListener('click', async () => {
  // First, check if user has unsaved stage config changes
  if (checkStageConfigDirty()) {
    showConfirmDialog(
      'Ada perubahan konfigurasi stage yang belum disimpan. Lanjutkan tanpa menyimpan?',
      () => {
        // User confirmed, proceed with framework creation (but without stage config changes)
        performFrameworkCreation();
      },
      () => {
        // User cancelled
      }
    );
  } else {
    performFrameworkCreation();
  }

  async function performFrameworkCreation() {
    const stages = stageDraft
      .map((stage) => ({ label: String(stage.label || '').trim(), level_ids: Array.isArray(stage.level_ids) ? stage.level_ids : [] }))
      .filter((stage) => stage.label);

    if (!stages.length) {
      showAlert('Tambahkan minimal satu career stage.', 'error');
      return;
    }

    const allLevels = (setupPayload?.levels || []).map((lvl) => String(lvl.id));
    const selectedLevelIds = stages.flatMap((stage) => stage.level_ids).map((id) => String(id));
    const uniqueSelected = Array.from(new Set(selectedLevelIds));

    if (uniqueSelected.length !== selectedLevelIds.length) {
      showAlert('Satu golongan tidak boleh berada di beberapa career stage.', 'error');
      return;
    }

    const missingLevelIds = allLevels.filter((id) => !uniqueSelected.includes(id));
    if (missingLevelIds.length) {
      const missingNames = (setupPayload?.levels || []).filter((lvl) => missingLevelIds.includes(String(lvl.id))).map((lvl) => lvl.name);
      showAlert(`Semua golongan harus kebagian stage. Yang belum dipilih: ${missingNames.join(', ')}`, 'error');
      return;
    }

    const payload = {
      behaviours: behaviourDraft.map((v) => String(v).trim()).filter(Boolean),
      stages: stages.map(s => ({
        label: s.label,
        level_ids: s.level_ids.map(id => parseInt(id))
      })),
    };

    const res = await apiPost('/api/vnb-framework/setup-initialize', payload);
    if (!res.success) {
      showAlert(res.message || 'Gagal menyimpan kerangka awal.', 'error');
      return;
    }

    showAlert(res.message || 'Framework berhasil dibuat!');
    try { window.localStorage.removeItem('vnb_framework_editor_draft_v1'); } catch (e) {}
    await loadFrameworkPage();
  }
});

document.getElementById('btn-edit-career-stage').addEventListener('click', () => {
  // Check for unsaved stage config changes
  if (hasUnsavedChanges()) {
    showConfirmDialog(
      'Ada perubahan konfigurasi stage yang belum disimpan. Lanjutkan tanpa menyimpan?',
      () => {
        // User confirmed, proceed to edit
        behaviourDraft = Array.isArray(setupPayload?.behaviours)
          ? setupPayload.behaviours.map((v) => String(v || ''))
          : [''];

        stageDraft = (stageConfigs || []).map((stage) => ({
          label: String(stage.label || ''),
          level_ids: Array.isArray(stage.level_ids) ? stage.level_ids.map((id) => String(id)) : [],
        }));

        if (!stageDraft.length) {
          stageDraft = [{ label: '', level_ids: [] }];
        }

        showFrameworkSetupEditor(true);
      },
      () => {
        // User cancelled, stay on current page
      }
    );
  } else {
    // No unsaved changes, proceed directly
    behaviourDraft = Array.isArray(setupPayload?.behaviours)
      ? setupPayload.behaviours.map((v) => String(v || ''))
      : [''];

    stageDraft = (stageConfigs || []).map((stage) => ({
      label: String(stage.label || ''),
      level_ids: Array.isArray(stage.level_ids) ? stage.level_ids.map((id) => String(id)) : [],
    }));

    if (!stageDraft.length) {
      stageDraft = [{ label: '', level_ids: [] }];
    }

    showFrameworkSetupEditor(true);
  }
});

document.getElementById('stage-max-integrations').addEventListener('input', () => {
  const input = document.getElementById('stage-max-integrations');
  input.value = String(Math.max(1, Number(input.value || 1)));
});

// Allow appending more phase rows from the active stage config panel
document.getElementById('btn-add-phase').addEventListener('click', () => {
  stageConfigDraft.phases.push({ duration_months: 1 });
  renderActiveStageConfig();
});

// Preview button and handler removed per user request

document.getElementById('btn-save-stage-config').addEventListener('click', async () => {
  const ok = await saveStageConfig({ silent: false });
  if (!ok) return;
  showAlert('Konfigurasi stage berhasil disimpan.', 'success');
  await loadStageItems(currentStage);
});

document.getElementById('btn-edit-vnb-activity').addEventListener('click', () => {
  if (!integrationEditEnabled) {
    setIntegrationEditEnabled(true);
    return;
  }

  saveCurrentIntegrations().then((saved) => {
    if (saved) {
      setIntegrationEditEnabled(false);
    }
  });
});

document.getElementById('btn-clone-stage').addEventListener('click', async () => {
  openCloneModal();
});

// --- Clone modal helpers ---
function openCloneModal() {
  const modal = document.getElementById('clone-stage-modal');
  if (!modal) return;
  renderCloneModalOptions();
  modal.classList.remove('hidden');
}

function closeCloneModal() {
  const modal = document.getElementById('clone-stage-modal');
  if (!modal) return;
  modal.classList.add('hidden');
}

function renderCloneModalOptions() {
  const container = document.getElementById('clone-stage-options');
  if (!container) return;
  const options = (stageConfigs || []).filter((s) => s.career_stage !== currentStage);
  container.innerHTML = options.map((opt) => `
    <button type="button" class="clone-target-btn fw-btn fw-btn-outline w-full text-left" data-target-stage="${opt.career_stage}">
      ${escapeHtml(opt.label)}
    </button>
  `).join('');

  // bind
  container.querySelectorAll('.clone-target-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const targetCode = btn.dataset.targetStage;
      if (!targetCode) return;
      await performCloneToTarget(targetCode);
      closeCloneModal();
    });
  });

  // if no options
  if (!options.length) {
    container.innerHTML = '<div class="text-sm text-gray-500">Tidak ada career stage lain untuk clone.</div>';
  }
}

async function performCloneToTarget(targetStageCode) {
  const targetRes = await apiGet(`/api/vnb-framework?career_stage=${targetStageCode}`);
  if (!targetRes.success || targetRes.setup_required) {
    showAlert('Gagal memuat data target career stage.', 'error');
    return;
  }

  const sourceCfg = getStageConfigByCode(currentStage);
  const targetCfg = (targetRes.stages || []).find((s) => s.career_stage === targetStageCode) || null;
  const maxIntegrations = Number(targetCfg?.max_integrations || sourceCfg?.max_integrations || 1);

  const sourceMap = {};
  (integrationRows || []).forEach((row) => {
    const key = `${String(row.behaviour || '').trim()}||${String(row.phase || '').trim()}`;
    const sourceValues = integrationDraft[row.id] || row.integrations || [];
    sourceMap[key] = Array.from({ length: maxIntegrations }, (_, idx) => String(sourceValues[idx] || ''));
  });

  const items = (targetRes.data || []).map((row) => {
    const key = `${String(row.behaviour || '').trim()}||${String(row.phase || '').trim()}`;
    const values = sourceMap[key] || [];
    return {
      id: row.id,
      integrations: Array.from({ length: maxIntegrations }, (_, idx) => String(values[idx] || '')),
    };
  });

  const saveRes = await apiPost('/api/vnb-framework/integrations', { items });
  if (!saveRes.success) {
    showAlert(saveRes.message || 'Gagal clone integrasi.', 'error');
    return;
  }

  const targetLabel = (stageConfigs || []).find(s => s.career_stage === targetStageCode)?.label || targetStageCode;
  showAlert(`Integrasi berhasil di-clone ke ${targetLabel}.`, 'success');
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

document.getElementById('btn-open-config').addEventListener('click', () => {
  toggleStageConfig(true);
});

document.getElementById('btn-close-config').addEventListener('click', () => {
  toggleStageConfig(false);
});

document.getElementById('btn-fix-framework').addEventListener('click', () => {
  document.getElementById('btn-edit-career-stage').click();
});

window.addEventListener('beforeunload', warnBeforeUnload);

loadFrameworkPage();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views\vnb-framework\index.blade.php ENDPATH**/ ?>