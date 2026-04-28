@extends('layouts.app')

@section('title', 'Feedback Plan VnB - VnB Platform')
@section('page_title', 'Feedback Plan VnB')
@section('page_subtitle', 'Review hasil approval dan revisi dari manager atas rencana VnB Anda.')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="card-glass rounded-xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6">
            <div class="flex-1">
                <p class="text-gray-600 mb-4">Review hasil approval dan revisi dari manager atas rencana VnB Anda</p>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    <div id="status-alert" class="card-glass rounded-xl p-4" style="display: none;">
        <div class="flex items-start gap-3">
            <i id="status-icon" class="fas fa-check-circle text-2xl text-green-600 mt-1"></i>
            <div>
                <h3 id="status-title" class="font-bold text-gray-800">Rencana Disetujui</h3>
                <p id="status-message" class="text-sm text-gray-600 mt-1">Rencana VnB Anda telah mendapat persetujuan dari manager</p>
            </div>
        </div>
    </div>

    <!-- Plan Summary -->
    <div class="card-glass rounded-xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Status Approval</div>
                <div id="approval-status" class="text-lg font-bold text-gray-800 mt-2">-</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Approved</div>
                <div id="approved-date" class="text-lg font-bold text-gray-800 mt-2">-</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Disetujui Oleh</div>
                <div id="approved-by" class="text-lg font-bold text-gray-800 mt-2">-</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Total Items</div>
                <div id="total-items" class="text-lg font-bold text-gray-800 mt-2">0</div>
            </div>
        </div>
    </div>

    <!-- Plan Description -->
    <div class="card-glass rounded-xl p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Deskripsi Planning</h3>
        <p id="plan-description" class="text-gray-600 leading-relaxed">-</p>
    </div>

    <!-- Approved Items (Non-Editable) -->
    <div class="card-glass rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-500/10 to-green-600/10 border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list-check text-green-600"></i>
                Approved Planning Items (Final Version - Non-Editable)
            </h3>
        </div>
        <div id="items-container" class="divide-y divide-gray-200">
            <div class="text-center py-8 text-gray-400 p-6">Memuat items...</div>
        </div>
    </div>

    <!-- Revision Notes Section (if any) -->
    <div id="revision-section" class="card-glass rounded-xl overflow-hidden" style="display: none;">
        <div class="bg-gradient-to-r from-orange-500/10 to-orange-600/10 border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-pencil-alt text-orange-600"></i>
                Catatan Revisi dari Manager
            </h3>
        </div>
        <div id="revision-notes-container" class="p-6">
            <div class="text-center text-gray-400">Memuat catatan...</div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3">
        <a href="/vnb-plans" class="flex-1 px-4 py-2 rounded-lg text-gray-700 border border-gray-300 font-medium hover:bg-gray-50 text-center transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Planning
        </a>
        <button onclick="printFeedback()" class="flex-1 px-4 py-2 rounded-lg text-white font-medium transition" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
            <i class="fas fa-print mr-2"></i>Cetak Feedback
        </button>
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

  await loadPlanFeedback();
}

// Load plan feedback
async function loadPlanFeedback() {
  try {
    const res = await apiGet(`/api/vnb-plans/${planId}/feedback`);
    if (!(res && res.success)) {
      showError('Gagal memuat plan feedback');
      return;
    }

    planData = res.data;
    renderPlanFeedback();
  } catch (err) {
    console.error(err);
    showError('Error loading plan feedback');
  }
}

// Render plan feedback
function renderPlanFeedback() {
  if (!planData) return;

  const statusLabels = {
    'approved': 'Disetujui',
    'approved_with_revision': 'Disetujui dengan Revisi',
    'waiting_manager_approval': 'Sedang Diproses Manager',
    'rejected': 'Ditolak',
  };

  const statusColors = {
    'approved': { bg: 'bg-green-100', text: 'text-green-700', icon: 'text-green-600' },
    'approved_with_revision': { bg: 'bg-orange-100', text: 'text-orange-700', icon: 'text-orange-600' },
    'waiting_manager_approval': { bg: 'bg-blue-100', text: 'text-blue-700', icon: 'text-blue-600' },
    'rejected': { bg: 'bg-red-100', text: 'text-red-700', icon: 'text-red-600' },
  };

  // Update status alert
  const alertColors = statusColors[planData.status] || statusColors.waiting_manager_approval;
  const alertIcon = planData.status === 'approved' ? 'fas fa-check-circle' : 
                    planData.status === 'approved_with_revision' ? 'fas fa-pencil-alt' : 
                    planData.status === 'rejected' ? 'fas fa-times-circle' : 'fas fa-hourglass-half';
  
  document.getElementById('status-alert').style.display = 'block';
  document.getElementById('status-icon').className = `${alertIcon} text-2xl ${alertColors.icon} mt-1`;
  document.getElementById('status-title').textContent = statusLabels[planData.status] || planData.status;
  document.getElementById('status-message').textContent = 
    planData.status === 'approved' ? 'Rencana VnB Anda telah mendapat persetujuan dari manager tanpa revisi.' :
    planData.status === 'approved_with_revision' ? 'Manager telah melakukan persetujuan dengan beberapa revisi. Silakan review perubahan di bawah.' :
    planData.status === 'rejected' ? 'Rencana Anda perlu dilakukan perbaikan. Silakan diskusikan dengan manager.' :
    'Rencana Anda sedang dalam proses review oleh manager.';

  // Update summary
  document.getElementById('approval-status').textContent = statusLabels[planData.status] || planData.status;
  document.getElementById('approved-date').textContent = planData.approved_at ? new Date(planData.approved_at).toLocaleDateString('id-ID') : '-';
  document.getElementById('approved-by').textContent = planData.approved_by_name || '-';
  document.getElementById('total-items').textContent = (planData.items?.length || 0);

  // Update description
  document.getElementById('plan-description').textContent = planData.description || '-';

  // Render items (final version - non-editable)
  renderItems();

  // Show revision notes if status is approved_with_revision
  if (planData.status === 'approved_with_revision' && planData.revision_details) {
    document.getElementById('revision-section').style.display = 'block';
    renderRevisionNotes();
  }
}

// Render items
function renderItems() {
  const container = document.getElementById('items-container');
  
  if (!planData.items || planData.items.length === 0) {
    container.innerHTML = '<div class="text-center py-8 text-gray-400 p-6">Tidak ada item planning</div>';
    return;
  }

  container.innerHTML = planData.items.map((item, idx) => {
    const wasRevised = item.was_revised ? 'border-l-4 border-l-orange-500 bg-orange-50' : 'border-l-4 border-l-green-500';
    
    return `
      <div class="p-6 ${wasRevised}">
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <h4 class="font-bold text-gray-800">${idx + 1}. ${item.activity_title}</h4>
            <p class="text-xs text-gray-500 mt-1">
              Status: <span class="font-medium">${item.status || 'Belum Dimulai'}</span>
              ${item.was_revised ? '<span class="ml-2 inline-block px-2 py-0.5 bg-orange-200 text-orange-800 rounded text-xs font-medium">Direvisi Manager</span>' : ''}
            </p>
          </div>
        </div>
        <p class="text-sm text-gray-700 mb-4 bg-white rounded p-3 border border-gray-200">${item.description || '-'}</p>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
          <div class="bg-white rounded p-3 border border-gray-200">
            <span class="text-gray-600">Tanggal Implementasi:</span>
            <p class="font-medium text-gray-800 mt-1">${item.implementation_date || '-'}</p>
          </div>
          <div class="bg-white rounded p-3 border border-gray-200">
            <span class="text-gray-600">Tanggal Aktivitas:</span>
            <p class="font-medium text-gray-800 mt-1">${item.activity_date || '-'}</p>
          </div>
          <div class="bg-white rounded p-3 border border-gray-200">
            <span class="text-gray-600">Deliverables:</span>
            <p class="font-medium text-gray-800 mt-1">${item.deliverables || '-'}</p>
          </div>
        </div>

        ${item.was_revised && item.revision_changes ? `
          <div class="mt-4 bg-blue-50 border border-blue-200 rounded p-3">
            <p class="text-xs font-bold text-blue-800 mb-2"><i class="fas fa-info-circle mr-1"></i>Perubahan oleh Manager:</p>
            <ul class="text-xs text-blue-700 space-y-1">
              ${Object.entries(item.revision_changes).map(([field, changes]) => `
                <li><strong>${field}:</strong> "${changes.old}" → "${changes.new}"</li>
              `).join('')}
            </ul>
          </div>
        ` : ''}
      </div>
    `;
  }).join('');
}

// Render revision notes
function renderRevisionNotes() {
  const container = document.getElementById('revision-notes-container');
  
  if (!planData.revision_details || planData.revision_details.length === 0) {
    container.innerHTML = '<p class="text-gray-600">Tidak ada catatan revisi</p>';
    return;
  }

  container.innerHTML = planData.revision_details.map(rev => `
    <div class="bg-white rounded-lg border border-orange-200 p-4">
      <div class="flex items-start gap-3">
        <i class="fas fa-pen text-orange-600 mt-1"></i>
        <div class="flex-1">
          <h5 class="font-bold text-gray-800">Revisi Item: ${rev.activity_title}</h5>
          <p class="text-sm text-gray-600 mt-2">${rev.notes || 'Tidak ada catatan tambahan'}</p>
          
          ${rev.changes ? `
            <div class="mt-3 space-y-2 text-sm bg-gray-50 rounded p-3">
              ${Object.entries(rev.changes).map(([field, change]) => `
                <div>
                  <span class="font-medium text-gray-700">${capitalizeField(field)}:</span>
                  <div class="text-xs text-gray-600 mt-1">
                    <p><strong class="text-red-600">Sebelum:</strong> ${change.old || '-'}</p>
                    <p><strong class="text-green-600">Sesudah:</strong> ${change.new || '-'}</p>
                  </div>
                </div>
              `).join('')}
            </div>
          ` : ''}

          <p class="text-xs text-gray-500 mt-3">
            <i class="fas fa-user mr-1"></i>${rev.manager_name || 'Manager'} | 
            <i class="fas fa-calendar mr-1"></i>${rev.revised_at ? new Date(rev.revised_at).toLocaleDateString('id-ID') : '-'}
          </p>
        </div>
      </div>
    </div>
  `).join('');
}

function capitalizeField(field) {
  const labels = {
    'description': 'Deskripsi',
    'implementation_date': 'Tanggal Implementasi',
    'activity_date': 'Tanggal Aktivitas',
    'deliverables': 'Deliverables',
  };
  return labels[field] || field;
}

function printFeedback() {
  window.print();
}

// Init
init();
</script>
@endpush

@endsection
