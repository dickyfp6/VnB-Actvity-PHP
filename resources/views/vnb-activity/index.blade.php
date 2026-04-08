@extends('layouts.app')
@section('title','Aktivitas VnB') 
@section('content')
<div class="space-y-6">
  <!-- Plan Status Check Container -->
  <div id="plan-status-container"></div>

  <!-- Activity Content (hidden until plan is approved) -->
  <div id="activity-content" style="display: none;">
    <!-- Header -->
    <div class="card-glass rounded-xl p-6 md:p-8 mb-6">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Aktivitas VnB</h1>
      <p class="text-gray-600">Pelacakan pelaksanaan aktivitas pengembangan nilai dan perilaku</p>
    </div>

    <!-- Deadline Banner -->
    <div id="deadline-banner" class="card-glass rounded-xl px-6 py-4 hidden border-l-4 border-amber-500">
      <div class="flex items-center gap-3">
        <i class="fas fa-calendar-alt text-amber-600 flex-shrink-0"></i>
        <div>
          <p class="text-sm font-semibold text-amber-900" id="deadline-text">Deadline mendekati</p>
          <p class="text-xs text-amber-700" id="deadline-detail">Pastikan semua aktivitas selesai sebelum tenggat waktu</p>
        </div>
      </div>
    </div>

    <!-- Activities Table -->
    <div class="table-container overflow-hidden hover:shadow-lg transition-all duration-300">
      <div class="overflow-x-auto">
        <table class="table-modern">
          <thead>
            <tr>
              <th>Behaviour</th>
              <th>Phase</th>
              <th>Integrasi Pengukuran</th>
              <th>Deskripsi Aktivitas</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody id="activity-body">
            <tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let activities = [];

function renderPlanStatusLock(planStatus) {
    const container = document.getElementById('plan-status-container');
    const activityContent = document.getElementById('activity-content');
    
    const statusConfig = {
        'not_found': {
            message: 'Rencana Aktivitas Belum Dibuat',
            icon: '📋',
            textColor: 'text-blue-700',
            bgColor: 'bg-blue-50',
            buttonText: 'Buat Rencana VnB',
            description: 'Anda perlu membuat rencana aktivitas VnB sebelum dapat mencatat pelaksanaan aktivitas.'
        },
        'draft': {
            message: 'Rencana Aktivitas Masih Draft',
            icon: '✏️',
            textColor: 'text-amber-700',
            bgColor: 'bg-amber-50',
            buttonText: 'Lanjutkan Rencana',
            description: 'Selesaikan dan ajukan rencana Anda untuk dapat mencatat pelaksanaan aktivitas.'
        },
        'revision_draft': {
            message: 'Rencana Aktivitas Masih Draft',
            icon: '✏️',
            textColor: 'text-amber-700',
            bgColor: 'bg-amber-50',
            buttonText: 'Lanjutkan Rencana',
            description: 'Selesaikan dan ajukan rencana Anda untuk dapat mencatat pelaksanaan aktivitas.'
        },
        'waiting_manager_approval': {
            message: 'Menunggu Persetujuan Rencana',
            icon: '⏳',
            textColor: 'text-purple-700',
            bgColor: 'bg-purple-50',
            buttonText: 'Lihat Status',
            description: 'Rencana Anda sedang dalam review. Anda dapat mencatat aktivitas setelah rencana disetujui.'
        },
        'submitted': {
            message: 'Menunggu Persetujuan Rencana',
            icon: '⏳',
            textColor: 'text-purple-700',
            bgColor: 'bg-purple-50',
            buttonText: 'Lihat Status',
            description: 'Rencana Anda sedang dalam review. Anda dapat mencatat aktivitas setelah rencana disetujui.'
        },
        'revision_requested': {
            message: 'Rencana Aktivitas Perlu Perbaikan',
            icon: '🔄',
            textColor: 'text-red-700',
            bgColor: 'bg-red-50',
            buttonText: 'Perbaiki Rencana',
            description: 'Rencana Anda memerlukan revisi. Silakan selesaikan revisi untuk melanjutkan.'
        }
    };

    const config = statusConfig[planStatus] || statusConfig['not_found'];

    if (planStatus === 'approved') {
        container.innerHTML = '';
        activityContent.style.display = 'block';
        loadActivities();
        return;
    }

    activityContent.style.display = 'none';
    container.innerHTML = `
        <div class="card-glass rounded-xl p-8 md:p-12 text-center">
            <div class="text-6xl mb-4 animate-fade-in">${config.icon}</div>
            <h2 class="text-3xl font-bold mb-3 ${config.textColor}">${config.message}</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-base leading-relaxed">
                ${config.description}
                    planStatus === 'not_found' ? 'Anda perlu membuat rencana aktivitas VnB sebelum dapat mengakses fitur aktivitas.' :
                    planStatus === 'draft' || planStatus === 'revision_draft' ? 'Selesaikan dan ajukan rencana Anda untuk dapat mengakses fitur aktivitas.' :
                    planStatus === 'waiting_manager_approval' || planStatus === 'submitted' ? 'Rencana aktivitas Anda sedang dalam proses review oleh manager. Anda dapat mengakses fitur setelah rencana disetujui.' :
                    planStatus === 'revision_requested' ? 'Rencana Anda belum disetujui. Silakan lakukan revisi sesuai masukan yang diberikan.' :
                    'Selesaikan pengaturan rencana VnB Anda terlebih dahulu.'
                }
            </p>
            <a href="/vnb-plans" class="inline-block px-8 py-3 rounded-lg text-white font-semibold transition" id="lock-screen-btn" style="background-color: #144600;">
                ${buttonText}
            </a>
        </div>
    `;
}

async function checkPlanStatus() {
    try {
        console.log('🔍 Checking plan status...');
        
        // Check if apiGet exists
        if (typeof apiGet !== 'function') {
            console.error('❌ apiGet function not found');
            renderPlanStatusLock('not_found');
            return;
        }
        
        const res = await apiGet('/api/vnb-plans/new-hire');
        console.log('📊 Plan status response:', res);
        
        // Handle response
        let planStatus = 'not_found';
        
        if (!res.success) {
            // API returned error
            console.log('⚠️ API returned success: false');
            planStatus = 'not_found';
        } else if (res.data && res.data.status) {
            // Successfully got plan with status
            planStatus = res.data.status;
            console.log('✅ Found plan with status:', planStatus);
        } else {
            console.log('⚠️ Response has success but no data.status');
            planStatus = 'not_found';
        }
        
        renderPlanStatusLock(planStatus);
    } catch (e) {
        console.error('❌ Error checking plan status:', {
            message: e.message,
            error: e,
            stack: e.stack
        });
        
        // Show error on page temporarily
        const container = document.getElementById('plan-status-container');
        container.innerHTML = `
            <div class="bg-red-50 rounded-lg shadow p-8 text-center text-red-900">
                <p class="text-sm mb-4">Error loading plan status. Please try refreshing the page.</p>
                <p class="text-xs text-red-600">${e.message}</p>
            </div>
        `;
    }
}

function statusBadge(status) {
  const map = {
    draft: 'bg-gray-100 text-gray-600',
    waiting_approval: 'bg-yellow-100 text-yellow-700',
    revision_required: 'bg-red-100 text-red-700',
    completed: 'bg-green-100 text-green-700',
    overdue: 'bg-red-100 text-red-700'
  };
  const label = (status || 'draft').replace(/_/g, ' ');
  return `<span class="px-2 py-0.5 rounded-full text-xs font-medium ${map[status] || map.draft}">${label}</span>`;
}

function renderDeadlineBanner() {
  const b = document.getElementById('deadline-banner');
  const countdown = activities.map(x => x.countdown_days).filter(v => typeof v === 'number');
  if (!countdown.length) { b.classList.add('hidden'); return; }
  const min = Math.min(...countdown);
  if (min < 0) {
    b.className = 'mb-5 rounded-lg px-4 py-3 text-sm bg-red-100 text-red-700';
    b.textContent = `Ada aktivitas melewati due date (${Math.abs(min)} hari). Mohon segera update / submit.`;
  } else {
    b.className = 'mb-5 rounded-lg px-4 py-3 text-sm ' + (typeof status !== 'undefined' && status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600');
    b.textContent = `Deadline terdekat: ${min} hari lagi.`;
  }
  b.classList.remove('hidden');
}

async function loadActivities() {
  document.getElementById('activity-body').innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  const res = await apiGet('/api/vnb-activities');
  activities = res.data || res || [];
  renderDeadlineBanner();
  renderActivities();
}

function escapeHtml(text) {
  if (!text) return '';
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}

function extractBehaviour(activityTitle) {
  // Extract behaviour name from activity_title format: "Behaviour Name - Phase X-Y"
  if (!activityTitle) return '-';
  const parts = activityTitle.split(' - ');
  return parts[0].trim() || '-';
}

function extractPhase(activityTitle) {
  // Extract phase from activity_title format: "Behaviour Name - Phase X-Y"
  if (!activityTitle) return '-';
  const match = activityTitle.match(/Phase\s+([\d\-]+)/i);
  return match ? `Phase ${match[1]}` : '-';
}

function parseIntegrations(description) {
  // Extract integration items from description (format: "int1 | int2")
  if (!description) return '-';
  
  // Split by pipe, trim whitespace, remove empty strings
  const parts = description
    .split('|')
    .map(s => s.trim())
    .filter(s => s.length > 0);
  
  // Return '-' if no integrations found, otherwise join with newline
  return parts.length === 0 ? '-' : parts.join('\n');
}

function renderActivities() {
  const tbody = document.getElementById('activity-body');
  if (!activities.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada aktivitas</td></tr>';
    return;
  }

  let html = '';
  
  activities.forEach((a, idx) => {
    const behaviour = extractBehaviour(a.activity_title);
    const phase = extractPhase(a.activity_title);
    const integrations = parseIntegrations(a.description);
    const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
    
    for (let integIdx = 0; integIdx < integrationList.length; integIdx++) {
      const integration = integrationList[integIdx];
      
      if (integIdx === 0) {
        // First row: include behaviour, phase, and action buttons
        html += `
    <tr class="hover:bg-gray-50 align-top">
      <td class="px-4 py-3 font-medium">${escapeHtml(behaviour)}</td>
      <td class="px-4 py-3">${escapeHtml(phase)}</td>
      <td class="px-4 py-3 text-xs bg-gray-50 rounded px-2 py-2">${escapeHtml(integration).replace(/\n/g, '<br>')}</td>
      <td class="px-4 py-3">
        <textarea id="desc-${a.id}" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm" rows="2">${escapeHtml(a.activity_description || '')}</textarea>
        ${a.revision_notes ? `<p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>Revisi: ${escapeHtml(a.revision_notes)}</p>` : ''}
      </td>
      <td class="px-4 py-3">
        <input id="date-${a.id}" type="date" class="border border-gray-300 rounded-lg px-2 py-1 text-sm" value="${a.activity_date || ''}">
        ${a.due_date ? `<p class="text-xs text-gray-400 mt-1">Due: ${a.due_date}</p>` : ''}
      </td>
      <td class="px-4 py-3">${statusBadge(a.submission_status)}</td>
      <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
        <button onclick="saveDraft(${a.id})" class="px-3 py-1.5 border border-gray-300 rounded text-xs hover:bg-gray-50">Save Draft</button>
        <button onclick="submitActivity(${a.id})" class="px-3 py-1.5 text-white rounded text-xs transition submit-btn" style="background-color: #144600; cursor: pointer;">Submit</button>
      </td>
    </tr>
        `;
      } else {
        // Subsequent rows: skip behaviour, phase columns
        html += `
    <tr class="hover:bg-gray-50 align-top">
      <td class="px-4 py-3"></td>
      <td class="px-4 py-3"></td>
      <td class="px-4 py-3 text-xs bg-gray-50 rounded px-2 py-2">${escapeHtml(integration).replace(/\n/g, '<br>')}</td>
      <td class="px-4 py-3"></td>
      <td class="px-4 py-3"></td>
      <td class="px-4 py-3"></td>
      <td class="px-4 py-3"></td>
    </tr>
        `;
      }
    }
  });

  tbody.innerHTML = html;
}

function payloadFor(id) {
  return {
    activity_description: document.getElementById(`desc-${id}`).value,
    activity_date: document.getElementById(`date-${id}`).value,
  };
}

async function saveDraft(id) {
  const res = await apiPost(`/api/vnb-activities/${id}/draft`, payloadFor(id));
  if (res.message || res.id || res.data) {
    showAlert('Draft tersimpan');
    loadActivities();
  } else showAlert(res.error || 'Gagal simpan draft', 'error');
}

async function submitActivity(id) {
  if (!(await showConfirm('Submit aktivitas ini untuk review manager?', 'Konfirmasi Submit'))) return;
  const res = await apiPost(`/api/vnb-activities/${id}/submit`, payloadFor(id));
  if (res.message || res.id || res.data) {
    showAlert('Aktivitas disubmit');
    loadActivities();
  } else showAlert(res.error || 'Gagal submit', 'error');
}

// Check plan status on page load
checkPlanStatus();
</script>
@endpush
@endsection
