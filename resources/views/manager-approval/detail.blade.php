@extends('layouts.app')
@section('title','Manager - Plan Approval Detail')
@section('content')
<div class="px-4 space-y-4">
  <!-- Header dengan Breadcrumb -->
  <div class="flex items-center justify-between">
    <div>
      <a href="/manager/employees" class="text-sm text-blue-600 hover:underline mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
      </a>
      <h1 class="text-2xl font-bold text-gray-800" id="plan-title">Plan Details</h1>
      <p class="text-sm text-gray-500 mt-1" id="employee-name"></p>
    </div>
    <div id="plan-status-badge" class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
      Draft
    </div>
  </div>

  <!-- Info Card -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Fase</div>
      <div id="plan-phase" class="text-lg font-bold text-gray-800">-</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Status</div>
      <div id="plan-status-label" class="text-lg font-bold text-gray-800">-</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Total Aktivitas</div>
      <div id="plans-item-count" class="text-lg font-bold text-gray-800">0</div>
    </div>
  </div>

  <!-- Plan Description -->
  <div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-3">Deskripsi Planning</h3>
    <p id="plan-description" class="text-gray-600 leading-relaxed">-</p>
  </div>

  <!-- Planning Items / Aktivitas -->
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-200 p-6">
      <h3 class="text-lg font-bold text-gray-800">Aktivitas Planning</h3>
    </div>
    <div id="activities-container" class="divide-y divide-gray-200">
      <div class="text-center py-8 text-gray-400">Memuat aktivitas...</div>
    </div>
  </div>

  <!-- Revision History Section -->
  <div id="revision-section" class="bg-white rounded-xl shadow-sm overflow-hidden" style="display: none;">
    <div class="border-b border-gray-200 p-6">
      <h3 class="text-lg font-bold text-gray-800">
        <i class="fas fa-history mr-2 text-orange-500"></i>Riwayat Revisi
      </h3>
    </div>
    <div id="revision-history-container" class="divide-y divide-gray-200">
      <div class="text-center py-8 text-gray-400">Memuat history...</div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div id="action-buttons" class="flex gap-3 pb-4">
    <button onclick="openApproveModal()" id="btn-approve" class="flex-1 px-4 py-2 rounded-lg text-white font-medium transition" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
      <i class="fas fa-check mr-2"></i>Approve Planning
    </button>
    <button onclick="submitApproveAll()" id="btn-approve-all" class="flex-1 px-4 py-2 rounded-lg text-white font-medium transition" style="background-color: #0891b2;" onmouseover="this.style.backgroundColor='#06b6d4'" onmouseout="this.style.backgroundColor='#0891b2'">
      <i class="fas fa-check-double mr-2"></i>Approve All
    </button>
    <button onclick="toggleEditMode()" id="btn-revise" class="flex-1 px-4 py-2 rounded-lg text-white font-medium transition" style="background-color: #FF6B35;" onmouseover="this.style.backgroundColor='#FF8555'" onmouseout="this.style.backgroundColor='#FF6B35'">
      <i class="fas fa-edit mr-2"></i>Revise Items
    </button>
  </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Approval</h3>
    <p class="text-gray-600 mb-6">Anda yakin akan approve planning ini? Setelah disetujui, planning tidak akan bisa diubah kecuali ada revisi baru dari manager.</p>
    <div class="flex gap-3">
      <button onclick="closeApproveModal()" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-medium hover:bg-gray-50">
        Batal
      </button>
      <button onclick="submitApproval()" class="flex-1 px-4 py-2 rounded-lg text-white font-medium" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
        Ya, Approve
      </button>
    </div>
  </div>
</div>

<!-- Revision Modal - REPLACED WITH Edit Items Modal -->
<div id="revision-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full p-6 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-800">Manager - Edit & Revise Plan Items</h3>
      <button onclick="closeRevisionModal()" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    
    <p class="text-sm text-gray-600 mb-4">
      <i class="fas fa-info-circle mr-2 text-blue-500"></i>
      Edit item details di bawah sesuai kebutuhan. Revisi ini akan langsung disimpan dan dikirim ke Employee untuk diketahui.
    </p>

    <div id="edit-items-container" class="space-y-4 mb-6">
      <div class="text-center py-8 text-gray-400">Memuat items...</div>
    </div>

    <div class="flex gap-3">
      <button onclick="closeRevisionModal()" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-medium hover:bg-gray-50">
        Batal (Jangan Revise)
      </button>
      <button onclick="submitManagerRevisions()" class="flex-1 px-4 py-2 rounded-lg text-white font-medium" style="background-color: #FF6B35;" onmouseover="this.style.backgroundColor='#FF8555'" onmouseout="this.style.backgroundColor='#FF6B35'">
        <i class="fas fa-save mr-2"></i>Simpan & Kirim Revisi ke Employee
      </button>
    </div>
  </div>
</div>

<!-- Revision History Detail Modal -->
<div id="revision-detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full p-6 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-800">Revisi Detail & Version Control</h3>
      <button onclick="closeRevisionDetailModal()" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <div id="revision-detail-content" class="space-y-4">
      <div class="text-center py-8 text-gray-400">Memuat detail...</div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let planId = null;
let planData = null;

// Get plan ID from URL
function getPlanIdFromUrl() {
  const segments = window.location.pathname.split('/');
  return segments[segments.length - 1];
}

// Initialize
async function init() {
  planId = getPlanIdFromUrl();
  console.log('Plan ID:', planId);
  
  if (!planId || isNaN(planId)) {
    showError('Plan ID tidak valid');
    return;
  }

  await loadPlanDetails();
}

// Load plan details
async function loadPlanDetails() {
  try {
    const res = await apiGet(`/api/vnb-plans/${planId}`);
    if (!(res && res.success)) {
      showError('Gagal memuat plan details');
      return;
    }

    planData = res.data;
    renderPlanDetails();
    await loadRevisionHistory();
  } catch (err) {
    console.error(err);
    showError('Error loading plan details');
  }
}

// Render plan details
function renderPlanDetails() {
  if (!planData) return;

  const statusLabels = {
    'draft': 'Draft',
    'waiting_manager_approval': 'Menunggu Approval Manager',
    'approved': 'Disetujui',
    'rejected': 'Ditolak',
    'revision_requested': 'Revisi Diminta',
    'in_progress': 'Sedang Berjalan',
    'submitted': 'Disubmit',
  };

  const statusColors = {
    'draft': 'bg-gray-100 text-gray-700',
    'waiting_manager_approval': 'bg-blue-100 text-blue-700',
    'approved': 'bg-green-100 text-green-700',
    'rejected': 'bg-red-100 text-red-700',
    'revision_requested': 'bg-orange-100 text-orange-700',
    'in_progress': 'bg-purple-100 text-purple-700',
    'submitted': 'bg-indigo-100 text-indigo-700',
  };

  // Update header
  document.getElementById('plan-title').textContent = planData.title || 'Planning';
  document.getElementById('employee-name').textContent = `${planData.employee?.name || '-'} (${planData.employee?.employee_number || '-'})`;
  document.getElementById('plan-phase').textContent = `Fase ${planData.phase_number}`;
  document.getElementById('plan-status-label').textContent = statusLabels[planData.status] || planData.status;
  
  const badgeClass = statusColors[planData.status] || 'bg-gray-100 text-gray-700';
  const badge = document.getElementById('plan-status-badge');
  badge.className = `px-3 py-1 rounded-full text-sm font-medium ${badgeClass}`;
  badge.textContent = statusLabels[planData.status] || planData.status;

  document.getElementById('plan-description').textContent = planData.description || '-';
  document.getElementById('plans-item-count').textContent = (planData.items?.length || 0);

  // Render activities
  renderActivities();

  // Show revision section if has revisions
  if (planData.revision_count > 0) {
    document.getElementById('revision-section').style.display = 'block';
  }

  // Update action buttons visibility
  updateActionButtons();
}

// Render activities
function renderActivities() {
  const container = document.getElementById('activities-container');
  
  if (!planData.items || planData.items.length === 0) {
    container.innerHTML = '<div class="text-center py-8 text-gray-400 p-6">Tidak ada aktivitas</div>';
    return;
  }

  container.innerHTML = planData.items.map((item, idx) => `
    <div class="p-6">
      <div class="flex items-start justify-between mb-3">
        <div>
          <h4 class="font-bold text-gray-800">${idx + 1}. ${item.activity_title}</h4>
          <p class="text-xs text-gray-500 mt-1">Status: <span class="font-medium">${item.status || 'Belum Dimulai'}</span></p>
        </div>
        <span class="px-2 py-1 rounded text-xs font-medium ${getActivityStatusBadge(item.status)}">
          ${getActivityStatusLabel(item.status)}
        </span>
      </div>
      <p class="text-sm text-gray-600 mb-3">${item.description}</p>
      <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
          <span class="text-gray-500">Tanggal Implementasi:</span>
          <p class="font-medium text-gray-800">${item.implementation_date || '-'}</p>
        </div>
        <div>
          <span class="text-gray-500">Deliverables:</span>
          <p class="font-medium text-gray-800">${item.deliverables || '-'}</p>
        </div>
      </div>
    </div>
  `).join('');
}

function getActivityStatusLabel(status) {
  const labels = {
    'not_started': 'Belum Dimulai',
    'in_progress': 'Sedang Berjalan',
    'completed': 'Selesai',
    'not_achieved': 'Tidak Tercapai',
  };
  return labels[status] || status;
}

function getActivityStatusBadge(status) {
  const badges = {
    'not_started': 'bg-gray-100 text-gray-700',
    'in_progress': 'bg-blue-100 text-blue-700',
    'completed': 'bg-green-100 text-green-700',
    'not_achieved': 'bg-red-100 text-red-700',
  };
  return badges[status] || 'bg-gray-100 text-gray-700';
}

// Load revision history
async function loadRevisionHistory() {
  if (planData.revision_count === 0) return;

  try {
    const res = await apiGet(`/api/manager/plans/${planId}/revisions/history`);
    if (!(res && res.success)) {
      console.log('Gagal load revision history');
      return;
    }

    renderRevisionHistory(res.data);
  } catch (err) {
    console.error('Error loading revision history:', err);
  }
}

// Render revision history summary
function renderRevisionHistory(data) {
  const container = document.getElementById('revision-history-container');
  
  if (!data.revisions || data.revisions.length === 0) {
    container.innerHTML = '<div class="text-center py-8 text-gray-400 p-6">Belum ada revisi</div>';
    return;
  }

  container.innerHTML = data.revisions.map(rev => `
    <div class="p-6 hover:bg-gray-50 cursor-pointer" onclick="viewRevisionDetail(${rev.id}, ${rev.revision_number})">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <h4 class="font-bold text-gray-800">Revisi #${rev.revision_number}</h4>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-${getRevisionStatusColor(rev.status)}-100 text-${getRevisionStatusColor(rev.status)}-700">
              ${rev.status_label}
            </span>
          </div>
          <p class="text-sm text-gray-600 mt-2">${rev.revision_notes}</p>
          <p class="text-xs text-gray-500 mt-2">
            Diminta oleh: <span class="font-medium">${rev.requested_by}</span> pada ${rev.requested_at}
            <br/>
            <span class="text-gray-400">${rev.activities_changed} aktivitas diubah</span>
          </p>
        </div>
        <i class="fas fa-chevron-right text-gray-400 mt-2"></i>
      </div>
    </div>
  `).join('');
}

function getRevisionStatusColor(status) {
  const colors = {
    'pending': 'orange',
    'in_progress': 'blue',
    'submitted': 'purple',
    'applied': 'green',
  };
  return colors[status] || 'gray';
}

// Update action buttons
function updateActionButtons() {
  const container = document.getElementById('action-buttons');
  
  // Hide action buttons jika sudah approved
  if (planData.status === 'approved') {
    container.style.display = 'none';
    return;
  }

  // Show buttons untuk status yang masih bisa di-action
  if (['waiting_manager_approval', 'revision_requested'].includes(planData.status)) {
    container.style.display = 'flex';
  } else {
    container.style.display = 'none';
  }
}

// Modals & Edit Mode
let editMode = false;
let itemsBackup = null;

function openApproveModal() {
  document.getElementById('approve-modal').classList.remove('hidden');
}

function closeApproveModal() {
  document.getElementById('approve-modal').classList.add('hidden');
}

async function submitApproval() {
  try {
    showLoading('Memproses approval...');
    
    const res = await apiPost(`/api/manager/plans/${planId}/approve`, {});
    
    if (res && res.success) {
      showSuccess('Planning berhasil diapprove!');
      closeApproveModal();
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      showError(res?.message || 'Gagal approve planning');
    }
  } catch (err) {
    console.error(err);
    showError('Error processing approval');
  }
}

function toggleEditMode() {
  editMode = !editMode;
  if (editMode) {
    openEditModal();
  } else {
    closeRevisionModal();
  }
}

function openEditModal() {
  // Backup items sebelum edit
  itemsBackup = JSON.parse(JSON.stringify(planData.items));
  
  // Render editable items
  const container = document.getElementById('edit-items-container');
  container.innerHTML = planData.items.map((item, idx) => `
    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
      <h4 class="font-bold text-gray-800 mb-3">#${idx + 1} ${item.activity_title}</h4>
      
      <div class="space-y-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea 
            id="item-desc-${item.id}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="3"
            placeholder="Edit deskripsi aktivitas...">${item.description || ''}</textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Implementasi</label>
            <input 
              type="date" 
              id="item-date-${item.id}"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              value="${item.implementation_date || ''}" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Aktivitas</label>
            <input 
              type="date" 
              id="item-activity-date-${item.id}"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              value="${item.activity_date || ''}" />
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Deliverables</label>
          <textarea 
            id="item-deliverables-${item.id}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="2"
            placeholder="Edit deliverables...">${item.deliverables || ''}</textarea>
        </div>
      </div>
    </div>
  `).join('');
  
  document.getElementById('revision-modal').classList.remove('hidden');
}

async function submitApproveAll() {
  try {
    showLoading('Approving all items...');
    
    const res = await apiPost(`/api/manager/plans/${planId}/approve-all`, {});
    
    if (res && res.success) {
      showSuccess('Semua items berhasil diapprove!');
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      showError(res?.message || 'Gagal approve all items');
    }
  } catch (err) {
    console.error(err);
    showError('Error approving all items');
  }
}

async function submitManagerRevisions() {
  // Collect edited values
  const changes = [];
  let hasChanges = false;
  
  for (const item of planData.items) {
    const newDesc = document.getElementById(`item-desc-${item.id}`).value;
    const newImplDate = document.getElementById(`item-date-${item.id}`).value;
    const newActivityDate = document.getElementById(`item-activity-date-${item.id}`).value;
    const newDeliverables = document.getElementById(`item-deliverables-${item.id}`).value;
    
    // Track if this item changed
    if (newDesc !== item.description || 
        newImplDate !== item.implementation_date || 
        newActivityDate !== item.activity_date || 
        newDeliverables !== item.deliverables) {
      
      changes.push({
        item_id: item.id,
        old_values: {
          description: item.description,
          implementation_date: item.implementation_date,
          activity_date: item.activity_date,
          deliverables: item.deliverables,
        },
        new_values: {
          description: newDesc,
          implementation_date: newImplDate,
          activity_date: newActivityDate,
          deliverables: newDeliverables,
        }
      });
      
      hasChanges = true;
    }
  }
  
  if (!hasChanges) {
    showError('Tidak ada perubahan pada items');
    return;
  }
  
  try {
    showLoading('Menyimpan revisi...');
    
    const res = await apiPost(`/api/manager/plans/${planId}/save-revisions`, {
      changes: changes
    });
    
    if (res && res.success) {
      showSuccess('Revisi berhasil disimpan dan dikirim ke Employee!');
      closeRevisionModal();
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      showError(res?.message || 'Gagal menyimpan revisi');
    }
  } catch (err) {
    console.error(err);
    showError('Error saving revisions');
  }
}

function closeRevisionModal() {
  document.getElementById('revision-modal').classList.add('hidden');
  editMode = false;
}

function viewRevisionDetail(revisionId, revisionNumber) {
  document.getElementById('revision-detail-modal').classList.remove('hidden');
  document.getElementById('revision-detail-content').innerHTML = '<div class="text-center py-8 text-gray-400">Memuat detail...</div>';
  
  loadRevisionDetail(revisionId, revisionNumber);
}

async function loadRevisionDetail(revisionId, revisionNumber) {
  try {
    const res = await apiGet(`/api/manager/plans/${planId}/revisions/history`);
    
    if (res && res.success) {
      const revision = res.data.revisions.find(r => r.id === revisionId);
      if (revision) {
        renderRevisionDetailContent(revision);
      }
    }
  } catch (err) {
    console.error(err);
    showError('Gagal load revision detail');
  }
}

function renderRevisionDetailContent(revision) {
  const container = document.getElementById('revision-detail-content');
  
  let detailsHTML = revision.details.map(detail => `
    <div class="border border-gray-200 rounded-lg p-4">
      <h4 class="font-bold text-gray-800 mb-3">${detail.activity_title}</h4>
      
      ${Object.keys(detail.changed_fields).length > 0 ? `
        <div class="space-y-2 text-sm">
          ${Object.entries(detail.changed_fields).map(([field, values]) => `
            <div class="bg-gray-50 p-2 rounded">
              <p class="font-medium text-gray-700 mb-1">${formatFieldName(field)}</p>
              <p class="text-red-600"><strong>Sebelum:</strong> ${values.old || '-'}</p>
              <p class="text-green-600"><strong>Sesudah:</strong> ${values.new || '-'}</p>
            </div>
          `).join('')}
        </div>
      ` : `
        <p class="text-gray-500 text-sm">Tidak ada perubahan terdeteksi</p>
      `}
      
      <p class="text-xs text-gray-500 mt-3">
        Diubah oleh: <span class="font-medium">${detail.changed_by}</span> pada ${detail.changed_at}
      </p>
    </div>
  `).join('');

  container.innerHTML = `
    <div class="space-y-4">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
          <strong>Revisi #${revision.revision_number}:</strong> ${revision.revision_notes}
        </p>
        <p class="text-xs text-blue-600 mt-2">Status: ${revision.status_label}</p>
      </div>
      ${detailsHTML}
    </div>
  `;
}

function formatFieldName(field) {
  const labels = {
    'activity_title': 'Judul Aktivitas',
    'description': 'Deskripsi',
    'implementation_date': 'Tanggal Implementasi',
    'deliverables': 'Deliverables',
    'behavior_metrics': 'Metrics Perilaku',
  };
  return labels[field] || field;
}

function closeRevisionDetailModal() {
  document.getElementById('revision-detail-modal').classList.add('hidden');
}

// Init
init();
</script>
@endpush

@endsection
