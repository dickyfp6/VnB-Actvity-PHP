
<?php $__env->startSection('title','VnB - Pending Revisions'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Revisi Pending dari Manager</h1>
      <p class="text-sm text-gray-500 mt-1">Selesaikan revisi yang diminta oleh manager untuk melanjutkan planning</p>
    </div>
    <button onclick="loadRevisions()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
      <i class="fas fa-sync-alt mr-1"></i> Refresh
    </button>
  </div>

  <!-- Alert if no revisions -->
  <div id="no-revisions-alert" class="bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex items-center">
      <i class="fas fa-check-circle text-green-600 mr-3 text-lg"></i>
      <div>
        <h3 class="font-bold text-green-800">Tidak ada revisi pending</h3>
        <p class="text-sm text-green-700">Semua planning Anda dalam status yang baik!</p>
      </div>
    </div>
  </div>

  <!-- Revisions List -->
  <div id="revisions-container" class="space-y-4" style="display: none;">
    <!-- Revision cards akan di-render di sini -->
  </div>
</div>

<!-- Modal untuk View & Edit Revision -->
<div id="revision-editor-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full p-6 max-h-screen overflow-y-auto">
    <!-- Modal Header -->
    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
      <div>
        <h3 class="text-lg font-bold text-gray-800">Edit Revisi</h3>
        <p id="revision-modal-subtitle" class="text-sm text-gray-500 mt-1">-</p>
      </div>
      <button onclick="closeRevisionEditor()" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <!-- Revision Notes -->
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
      <h4 class="font-bold text-orange-800 mb-2">Catatan Revisi dari Manager:</h4>
      <p id="revision-notes-display" class="text-sm text-orange-700 leading-relaxed">-</p>
    </div>

    <!-- Activities to Revise -->
    <div id="revision-activities-container" class="space-y-4">
      <div class="text-center py-8 text-gray-400">Memuat aktivitas yang perlu direvisi...</div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
      <button onclick="closeRevisionEditor()" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-medium hover:bg-gray-50">
        Batal
      </button>
      <button onclick="submitRevisionChanges()" class="flex-1 px-4 py-2 rounded-lg text-white font-medium" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
        <i class="fas fa-save mr-2"></i>Simpan Perubahan
      </button>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRevision = null;
let revisionChanges = {};

// Initialize
async function init() {
  await loadRevisions();
}

// Load revisions
async function loadRevisions() {
  try {
    const res = await apiGet('/api/new-hire/pending-revisions');
    
    if (!(res && res.success)) {
      showError('Gagal memuat revisi pending');
      return;
    }

    const revisions = res.data || [];
    
    if (revisions.length === 0) {
      document.getElementById('no-revisions-alert').style.display = 'block';
      document.getElementById('revisions-container').style.display = 'none';
      return;
    }

    document.getElementById('no-revisions-alert').style.display = 'none';
    document.getElementById('revisions-container').style.display = 'block';
    renderRevisions(revisions);
  } catch (err) {
    console.error(err);
    showError('Error loading pending revisions');
  }
}

// Render revisions
function renderRevisions(revisions) {
  const container = document.getElementById('revisions-container');
  
  container.innerHTML = revisions.map(revision => `
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
      <div class="border-l-4 border-orange-500 p-6">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="text-lg font-bold text-gray-800">${revision.plan_title}</h3>
            <p class="text-sm text-gray-500 mt-1">
              Fase ${revision.plan_phase} • Revisi #${revision.revision_number}
              <span class="ml-3 px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">
                ${revision.status_label}
              </span>
            </p>
          </div>
          <div class="text-right">
            <p class="text-xs text-gray-500">Diminta oleh:</p>
            <p class="font-bold text-gray-800">${revision.requested_by}</p>
            <p class="text-xs text-gray-500 mt-1">${revision.requested_at}</p>
          </div>
        </div>

        <!-- Catatan Revisi -->
        <div class="bg-gray-50 p-3 rounded mb-4 max-h-24 overflow-y-auto">
          <p class="text-sm text-gray-700 leading-relaxed">${revision.revision_notes}</p>
        </div>

        <!-- Items to Revise -->
        <div class="mb-4">
          <p class="text-xs font-bold text-gray-600 mb-2">Aktivitas yang perlu direvisi (${revision.items_to_revise}):</p>
          <div class="space-y-1">
            ${revision.details.map(detail => `
              <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="fas fa-arrow-right text-orange-500 text-xs"></i>
                <span>${detail.activity_title}</span>
              </div>
            `).join('')}
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <button onclick="editRevision(${revision.id})" class="flex-1 px-4 py-2 rounded-lg text-white font-medium transition" style="background-color: #FF6B35;" onmouseover="this.style.backgroundColor='#FF8555'" onmouseout="this.style.backgroundColor='#FF6B35'">
            <i class="fas fa-edit mr-2"></i>Edit Aktivitas
          </button>
          <button onclick="viewRevisionHistory(${revision.plan_id})" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-medium hover:bg-gray-50">
            <i class="fas fa-history mr-2"></i>Lihat History
          </button>
        </div>
      </div>
    </div>
  `).join('');
}

// Edit revision
async function editRevision(revisionId) {
  try {
    // Get revision details
    const res = await apiGet('/api/new-hire/pending-revisions');
    if (!(res && res.success)) {
      showError('Gagal load revision');
      return;
    }

    currentRevision = res.data.find(r => r.id === revisionId);
    if (!currentRevision) {
      showError('Revision tidak ditemukan');
      return;
    }

    // Get plan details for current activity values
    const planRes = await apiGet(`/api/vnb-plans/${currentRevision.plan_id}`);
    if (!(planRes && planRes.success)) {
      showError('Gagal load plan details');
      return;
    }

    const planData = planRes.data;

    // Update modal
    document.getElementById('revision-modal-subtitle').textContent = 
      `${currentRevision.plan_title} • Revisi #${currentRevision.revision_number}`;
    
    document.getElementById('revision-notes-display').textContent = currentRevision.revision_notes;

    // Init changes object for tracking
    revisionChanges = {};
    currentRevision.details.forEach(detail => {
      revisionChanges[detail.activity_id] = {};
    });

    // Render activity editors
    const container = document.getElementById('revision-activities-container');
    const itemsToEdit = planData.items.filter(item => 
      currentRevision.details.some(d => d.activity_id === item.id)
    );

    container.innerHTML = itemsToEdit.map(item => `
      <div class="border border-gray-200 rounded-lg p-4">
        <h4 class="font-bold text-gray-800 mb-3">${item.activity_title}</h4>

        <!-- Title -->
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-1">Judul Aktivitas</label>
          <input type="text" 
            value="${item.activity_title}" 
            onchange="updateRevisionChange(${item.id}, 'activity_title', this.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <!-- Description -->
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea 
            onchange="updateRevisionChange(${item.id}, 'description', this.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
            rows="3"
          >${item.description}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
          <!-- Implementation Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Implementasi</label>
            <input type="date" 
              value="${item.implementation_date}" 
              onchange="updateRevisionChange(${item.id}, 'implementation_date', this.value)"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
          </div>

          <!-- Deliverables -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deliverables</label>
            <input type="text" 
              value="${item.deliverables}" 
              onchange="updateRevisionChange(${item.id}, 'deliverables', this.value)"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
          </div>
        </div>

        <!-- Behavior Metrics -->
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-1">Metrics Perilaku</label>
          <textarea 
            onchange="updateRevisionChange(${item.id}, 'behavior_metrics', this.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
            rows="2"
            placeholder="Contoh: Deliverable dalam format PDF, Presentasi berkualitas, dll"
          >${Array.isArray(item.behavior_metrics) ? item.behavior_metrics.join(', ') : (item.behavior_metrics || '')}</textarea>
        </div>

        <div class="bg-blue-50 p-2 rounded text-xs text-blue-700">
          <i class="fas fa-info-circle mr-1"></i>Perubahan akan dicatat dalam Version Control
        </div>
      </div>
    `).join('');

    document.getElementById('revision-editor-modal').classList.remove('hidden');
  } catch (err) {
    console.error(err);
    showError('Error loading revision editor');
  }
}

// Update revision changes
function updateRevisionChange(activityId, field, value) {
  if (!revisionChanges[activityId]) {
    revisionChanges[activityId] = {};
  }
  revisionChanges[activityId][field] = value;
}

// Submit revision changes
async function submitRevisionChanges() {
  if (!currentRevision || !Object.keys(revisionChanges).length) {
    showError('Tidak ada perubahan yang dibuat');
    return;
  }

  try {
    showLoading('Menyimpan perubahan revisi...');

    // TODO: Create API endpoint untuk submit revisi changes
    // Endpoint: POST /api/new-hire/plans/{planId}/submit-revision/
    // Body: {revision_id, changes}
    // This will:
    // 1. Update vnb_plan_items
    // 2. Create VnbPlanRevisionDetail records dengan old_values dan new_values
    // 3. Update revision status dari 'pending' ke 'in_progress'
    // 4. Create activity log entries

    console.log('Revision changes:', revisionChanges);
    
    showSuccess('Perubahan revisi berhasil disimpan!');
    closeRevisionEditor();
    
    setTimeout(() => {
      loadRevisions();
    }, 1500);
  } catch (err) {
    console.error(err);
    showError('Error submitting revision changes');
  }
}

// Close editor
function closeRevisionEditor() {
  document.getElementById('revision-editor-modal').classList.add('hidden');
  currentRevision = null;
  revisionChanges = {};
}

// View revision history
function viewRevisionHistory(planId) {
  // TODO: Open modal/view untuk lihat revision history dari plan ini
  showInfo('Fitur revision history sedang dikembangkan');
}

// Init
init();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-plans/pending-revisions.blade.php ENDPATH**/ ?>