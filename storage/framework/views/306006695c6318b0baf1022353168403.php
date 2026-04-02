
<?php $__env->startSection('title','Aktivitas VnB'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4">
  <!-- Plan Status Check Container -->
  <div id="plan-status-container"></div>

  <!-- Activity Content (hidden until plan is approved) -->
  <div id="activity-content" style="display: none;">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Aktivitas VnB</h1>

    <div id="deadline-banner" class="mb-5 rounded-lg px-4 py-3 text-sm hidden"></div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Behaviour</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Phase</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Integrasi Pengukuran</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Activity Description</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Activity Date</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Status</th>
            <th class="px-4 py-3 text-right text-xs uppercase text-gray-500">Aksi</th>
          </tr>
        </thead>
        <tbody id="activity-body" class="divide-y divide-gray-200 text-sm text-gray-700">
          <tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let activities = [];

function renderPlanStatusLock(planStatus) {
    const container = document.getElementById('plan-status-container');
    const activityContent = document.getElementById('activity-content');
    
    // Determine message based on plan status
    let message = '';
    let buttonText = '';
    let icon = '';
    let bgColor = 'bg-blue-50';
    let textColor = 'text-blue-900';
    
    if (!planStatus || planStatus === 'not_found') {
        // No plan created yet
        message = 'Rencana Aktivitas Belum Dibuat';
        icon = '📋';
        textColor = 'text-blue-900';
        buttonText = 'Buat Rencana VnB';
    } else if (planStatus === 'draft' || planStatus === 'revision_draft') {
        // Still in draft
        message = 'Rencana Aktivitas Masih Draft';
        icon = '✏️';
        bgColor = 'bg-yellow-50';
        textColor = 'text-yellow-900';
        buttonText = 'Lanjutkan Rencana';
    } else if (planStatus === 'waiting_manager_approval' || planStatus === 'submitted') {
        // Waiting for approval
        message = 'Menunggu Persetujuan Rencana';
        icon = '⏳';
        bgColor = 'bg-purple-50';
        textColor = 'text-purple-900';
        buttonText = 'Lihat Status';
    } else if (planStatus === 'revision_requested') {
        // Needs revision
        message = 'Rencana Aktivitas Perlu Perbaikan';
        icon = '🔄';
        bgColor = 'bg-red-50';
        textColor = 'text-red-900';
        buttonText = 'Perbaiki Rencana';
    } else if (planStatus === 'approved') {
        // Plan is approved, show activities
        container.innerHTML = '';
        activityContent.style.display = 'block';
        loadActivities();
        return;
    }
    
    // Show lock screen
    activityContent.style.display = 'none';
    container.innerHTML = `
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-5xl mb-4">${icon}</div>
            <h2 class="text-2xl font-bold mb-2 ${textColor}">${message}</h2>
            <p class="text-gray-600 mb-6 max-w-lg mx-auto">
                ${
                    planStatus === 'not_found' ? 'Anda perlu membuat rencana aktivitas VnB sebelum dapat mengakses fitur aktivitas.' :
                    planStatus === 'draft' || planStatus === 'revision_draft' ? 'Selesaikan dan ajukan rencana Anda untuk dapat mengakses fitur aktivitas.' :
                    planStatus === 'waiting_manager_approval' || planStatus === 'submitted' ? 'Rencana aktivitas Anda sedang dalam proses review oleh manager. Anda dapat mengakses fitur setelah rencana disetujui.' :
                    planStatus === 'revision_requested' ? 'Rencana Anda belum disetujui. Silakan lakukan revisi sesuai masukan yang diberikan.' :
                    'Selesaikan pengaturan rencana VnB Anda terlebih dahulu.'
                }
            </p>
            <a href="/vnb-plans" class="inline-block px-8 py-3 rounded-lg text-white font-semibold transition" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#0a2c00'" onmouseout="this.style.backgroundColor='#144600'">
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
        
        // Try multiple ways to extract status
        const planStatus = res?.data?.status || res?.status || (res?.success === false ? 'not_found' : res?.data) || 'not_found';
        console.log('✅ Extracted status:', planStatus);
        
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
    b.className = 'mb-5 rounded-lg px-4 py-3 text-sm' + (status === 'active' ? (' background-color: #D0EC98; color: #144600;' : ' background-color: #f3f4f6; color: #9ca3af;');
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
        <input id="date-${a.id}"type="date" class="border border-gray-300 rounded-lg px-2 py-1 text-sm" value="${a.activity_date || ''}">
        ${a.due_date ? `<p class="text-xs text-gray-400 mt-1">Due: ${a.due_date}</p>` : ''}
      </td>
      <td class="px-4 py-3">${statusBadge(a.submission_status)}</td>
      <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
        <button onclick="saveDraft(${a.id})" class="px-3 py-1.5 border border-gray-300 rounded text-xs hover:bg-gray-50">Save Draft</button>
        <button onclick="submitActivity(${a.id})" class="px-3 py-1.5 text-white rounded text-xs transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Submit</button>
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
  if (!confirm('Submit aktivitas ini untuk review manager?')) return;
  const res = await apiPost(`/api/vnb-activities/${id}/submit`, payloadFor(id));
  if (res.message || res.id || res.data) {
    showAlert('Aktivitas disubmit');
    loadActivities();
  } else showAlert(res.error || 'Gagal submit', 'error');
}

// Check plan status on page load
checkPlanStatus();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-activity/index.blade.php ENDPATH**/ ?>