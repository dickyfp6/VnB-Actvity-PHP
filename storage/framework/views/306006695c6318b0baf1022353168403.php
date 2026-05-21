
<?php $__env->startSection('title','Aktivitas VnB'); ?> 
<?php $__env->startSection('page_title','Aktivitas VnB'); ?>
<?php $__env->startSection('page_subtitle','Pantau dan kerjakan aktivitas pengembangan setelah rencana disetujui.'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <!-- Plan Status Check Container -->
  <div id="plan-status-container"></div>

  <!-- Activity Content (hidden until plan is approved) -->
  <div id="activity-content" style="display: none;">


    <!-- Phase Tabs -->
    <div id="phase-tabs" class="flex flex-wrap gap-2 mb-4">
      <!-- Tabs generated dynamically -->
    </div>

    <!-- Real-time Phase Deadline Widget -->
    <div id="countdown-widget" class="hidden mb-5">
      <div id="countdown-widget-container" class="card-glass rounded-xl py-3 px-5 border border-emerald-100 bg-gradient-to-r from-emerald-50/40 to-teal-50/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-3">
          <span id="countdown-widget-label" class="text-[10px] font-bold text-[#144600] tracking-wider uppercase bg-[#144600]/10 px-2 py-0.5 rounded-full whitespace-nowrap">Masa Berakhir Fase</span>
          <h3 id="widget-due-date" class="text-sm font-bold text-gray-700">Rabu, 20 Mei 2026</h3>
        </div>
        <div class="flex items-center gap-3 justify-between sm:justify-end">
          <!-- Countdown Blocks -->
          <div class="flex items-center gap-1">
            <!-- Days block -->
            <div class="text-center">
              <div id="countdown-days" class="w-9 h-9 bg-white border border-gray-150 rounded-lg flex items-center justify-center text-sm font-extrabold text-gray-800 shadow-xs">00</div>
              <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 block">Hari</span>
            </div>
            <div class="text-sm font-bold text-gray-400 self-start mt-2">:</div>
            <!-- Hours block -->
            <div class="text-center">
              <div id="countdown-hours" class="w-9 h-9 bg-white border border-gray-150 rounded-lg flex items-center justify-center text-sm font-extrabold text-gray-800 shadow-xs">00</div>
              <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 block">Jam</span>
            </div>
            <div class="text-sm font-bold text-gray-400 self-start mt-2">:</div>
            <!-- Minutes block -->
            <div class="text-center">
              <div id="countdown-minutes" class="w-9 h-9 bg-white border border-gray-150 rounded-lg flex items-center justify-center text-sm font-extrabold text-gray-800 shadow-xs">00</div>
              <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 block">Menit</span>
            </div>
            <div class="text-sm font-bold text-gray-400 self-start mt-2">:</div>
            <!-- Seconds block -->
            <div class="text-center">
              <div id="countdown-seconds" class="w-9 h-9 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center justify-center text-sm font-extrabold text-emerald-700 shadow-xs">00</div>
              <span id="countdown-seconds-label" class="text-[8px] font-bold text-emerald-600 uppercase tracking-wider mt-0.5 block">Detik</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activities Table Container -->
    <div class="table-container overflow-hidden hover:shadow-lg transition-all duration-300 bg-white rounded-xl">
      <div class="overflow-x-auto" id="activities-container">
        <!-- Tables generated dynamically -->
        <table class="table-modern w-full text-left">
          <tbody id="activity-body">
            <tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let activities = [];
let activePhase = '';
const pendingEvidenceFiles = new Map();
let evidencePreviewModal = null;

function evidenceRowKey(id, integIdx) {
  return `${id}-${integIdx}`;
}

function getPendingEvidenceList(id, integIdx) {
  return pendingEvidenceFiles.get(evidenceRowKey(id, integIdx)) || [];
}

function getExistingEvidenceList(id, integIdx) {
  const activity = activities.find(x => x.id === id);
  if (!activity) return [];
  return (activity.evidences || []).filter(ev => ev.description === `Integration ${integIdx}`);
}

function getFileExt(name, fallbackType = '') {
  const fromName = (name || '').split('.').pop()?.toLowerCase();
  if (fromName) return fromName;
  return (fallbackType || '').toLowerCase();
}

function isImageExtension(ext) {
  return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic', 'heif', 'svg'].includes((ext || '').toLowerCase());
}

function isVideoExtension(ext) {
  return ['mp4', 'mov', 'avi', 'mkv', 'webm', 'm4v', '3gp'].includes((ext || '').toLowerCase());
}

function fileIconClass(ext) {
  const e = (ext || '').toLowerCase();
  if (isImageExtension(e)) return 'fas fa-image';
  if (isVideoExtension(e)) return 'fas fa-video';
  if (['pdf'].includes(e)) return 'fas fa-file-pdf';
  if (['doc', 'docx'].includes(e)) return 'fas fa-file-word';
  if (['xls', 'xlsx', 'csv'].includes(e)) return 'fas fa-file-excel';
  if (['ppt', 'pptx'].includes(e)) return 'fas fa-file-powerpoint';
  if (['zip', 'rar', '7z'].includes(e)) return 'fas fa-file-archive';
  return 'fas fa-file';
}

function escapeJsString(value) {
  return String(value || '')
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/\r?\n/g, ' ');
}

function ensureEvidencePreviewModal() {
  if (evidencePreviewModal) {
    return evidencePreviewModal;
  }

  const modal = document.createElement('div');
  modal.id = 'evidence-preview-modal';
  modal.className = 'fixed inset-0 bg-black/50 hidden items-center justify-center z-[120] p-4';
  modal.innerHTML = `
    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden">
      <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <h3 id="evidence-preview-title" class="text-sm font-semibold text-gray-800 truncate pr-3">Preview Bukti</h3>
        <button type="button" onclick="closeEvidencePreview()" class="w-9 h-9 rounded-lg border border-gray-300 text-gray-500 hover:text-gray-800" aria-label="Tutup preview">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div id="evidence-preview-content" class="p-4 bg-gray-50 min-h-[280px] max-h-[70vh] overflow-auto flex items-center justify-center"></div>
    </div>
  `;

  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeEvidencePreview();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeEvidencePreview();
    }
  });

  document.body.appendChild(modal);
  evidencePreviewModal = modal;
  return modal;
}

function openEvidencePreview(url, fileName, fileType = '') {
  const modal = ensureEvidencePreviewModal();
  const title = document.getElementById('evidence-preview-title');
  const content = document.getElementById('evidence-preview-content');
  if (!modal || !title || !content) return;

  const safeName = fileName || 'Bukti Implementasi';
  title.textContent = safeName;

  const ext = getFileExt(fileName, fileType);
  let html = '';

  if (!url) {
    html = `
      <div class="text-center text-gray-600">
        <i class="${fileIconClass(ext)} text-4xl mb-3"></i>
        <p class="text-sm font-medium">Preview belum tersedia untuk file ini.</p>
      </div>
    `;
  } else if (isImageExtension(ext)) {
    html = `<img src="${url}" alt="${escapeHtml(safeName)}" class="max-w-full max-h-[62vh] rounded-lg shadow">`;
  } else if (isVideoExtension(ext)) {
    html = `<video src="${url}" controls class="max-w-full max-h-[62vh] rounded-lg shadow bg-black"></video>`;
  } else if (ext === 'pdf') {
    html = `<iframe src="${url}" class="w-full h-[62vh] rounded-lg border border-gray-200 bg-white"></iframe>`;
  } else {
    html = `
      <div class="text-center text-gray-700">
        <i class="${fileIconClass(ext)} text-4xl mb-3"></i>
        <p class="text-sm mb-2">Preview langsung tidak tersedia untuk tipe file ini.</p>
        <a href="${url}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-semibold">
          <i class="fas fa-external-link-alt"></i>
          Buka File
        </a>
      </div>
    `;
  }

  content.innerHTML = html;
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeEvidencePreview() {
  if (!evidencePreviewModal) return;
  evidencePreviewModal.classList.remove('flex');
  evidencePreviewModal.classList.add('hidden');
}

function renderEvidencePreviewBlock(fileName, fileType, previewUrl = '') {
  const ext = getFileExt(fileName, fileType);
  const onClick = `openEvidencePreview('${escapeJsString(previewUrl)}','${escapeJsString(fileName)}','${escapeJsString(fileType)}')`;

  if (previewUrl && isImageExtension(ext)) {
    return `<button type="button" onclick="${onClick}" class="w-14 h-12 overflow-hidden block hover:opacity-90" title="Lihat preview"><img src="${previewUrl}" alt="preview" class="w-14 h-12 object-cover"></button>`;
  }

  if (previewUrl && isVideoExtension(ext)) {
    return `<button type="button" onclick="${onClick}" class="w-14 h-12 overflow-hidden block hover:opacity-90" title="Lihat preview"><video src="${previewUrl}" class="w-14 h-12 object-cover" muted></video></button>`;
  }

  return `<button type="button" onclick="${onClick}" class="w-14 h-12 inline-flex items-center justify-center text-gray-500 bg-gray-50 hover:bg-gray-100" title="Lihat preview"><i class="${fileIconClass(ext)} text-base"></i></button>`;
}

function renderEvidenceItems(id, integIdx) {
  const existing = getExistingEvidenceList(id, integIdx);
  const pending = getPendingEvidenceList(id, integIdx);
  let html = '';

  existing.forEach((ev) => {
    html += `
      <div class="w-full border border-gray-200 rounded-xl bg-white overflow-hidden flex items-stretch justify-between gap-0">
        <div class="flex items-center min-w-0 flex-1">
          <div class="overflow-hidden">${renderEvidencePreviewBlock(ev.file_name, ev.file_type || '', ev.preview_url || ev.s3_url || '')}</div>
          <div class="w-px self-stretch bg-gray-200"></div>
          <span class="px-3 text-sm font-semibold text-[#144600] truncate">${escapeHtml(ev.file_name || 'file')}</span>
        </div>
        <button type="button" onclick="removeExistingEvidence(${id}, ${integIdx}, ${ev.id})" class="w-9 h-9 m-1.5 rounded-lg border border-gray-300 text-gray-500 hover:text-red-600 hover:border-red-300" title="Hapus bukti" aria-label="Hapus bukti">
          <i class="fas fa-times text-base"></i>
        </button>
      </div>
    `;
  });

  pending.forEach((ev) => {
    html += `
      <div class="w-full border border-gray-200 rounded-xl bg-white overflow-hidden flex items-stretch justify-between gap-0">
        <div class="flex items-center min-w-0 flex-1">
          <div class="overflow-hidden">${renderEvidencePreviewBlock(ev.name, ev.type || '', ev.previewUrl || '')}</div>
          <div class="w-px self-stretch bg-gray-200"></div>
          <span class="px-3 text-sm font-semibold text-[#144600] truncate">${escapeHtml(ev.name || 'file')}</span>
        </div>
        <button type="button" onclick="removePendingEvidence(${id}, ${integIdx}, '${ev.tempId}')" class="w-9 h-9 m-1.5 rounded-lg border border-gray-300 text-gray-500 hover:text-red-600 hover:border-red-300" title="Hapus bukti" aria-label="Hapus bukti">
          <i class="fas fa-times text-base"></i>
        </button>
      </div>
    `;
  });

  return html;
}

function renderEvidenceUploader(id, integIdx) {
  const hasAnyEvidence = getExistingEvidenceList(id, integIdx).length > 0 || getPendingEvidenceList(id, integIdx).length > 0;

  if (!hasAnyEvidence) {
    return `
      <label for="file-${id}-${integIdx}" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 border border-dashed border-gray-300 rounded-xl bg-white hover:bg-gray-50 cursor-pointer text-sm text-gray-600">
        <i class="fas fa-cloud-upload-alt text-sm"></i>
        <span class="font-semibold">Unggah Bukti</span>
      </label>
    `;
  }

  return `
    <label for="file-${id}-${integIdx}" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 border border-dashed border-gray-300 rounded-xl bg-white hover:bg-gray-50 cursor-pointer text-sm text-gray-600">
      <i class="fas fa-plus text-sm"></i>
      <span class="font-semibold">Tambah Bukti lainnya</span>
    </label>
  `;
}

function renderEvidenceCellContent(id, integIdx) {
  const itemsHtml = renderEvidenceItems(id, integIdx);
  const hasItems = itemsHtml.trim().length > 0;

  return `
    <div class="flex flex-col">
      ${hasItems ? `<div class="space-y-2">${itemsHtml}</div><div class="mt-2">${renderEvidenceUploader(id, integIdx)}</div>` : renderEvidenceUploader(id, integIdx)}
      <input type="file" id="file-${id}-${integIdx}" onchange="addEvidenceFiles(${id}, ${integIdx}, this)" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z" multiple>
      <p class="text-[10px] text-red-500 hidden mt-1" id="file-error-${id}-${integIdx}"></p>
    </div>
  `;
}

function rerenderEvidenceRow(id, integIdx) {
  const cellContainer = document.getElementById(`evidence-cell-${id}-${integIdx}`);
  if (cellContainer) {
    cellContainer.innerHTML = renderEvidenceCellContent(id, integIdx);
  }
}

function expandImplementationField(el) {
  if (!el) return;
  el.classList.remove('h-10');
  el.classList.add('min-h-[110px]');
}

function collapseImplementationField(el) {
  if (!el) return;
  if ((el.value || '').trim().length > 0) return;
  el.classList.remove('min-h-[110px]');
  el.classList.add('h-10');
}

function addEvidenceFiles(id, integIdx, input) {
  const maxFileSize = 10 * 1024 * 1024;
  const files = Array.from(input.files || []);
  const key = evidenceRowKey(id, integIdx);
  const current = getPendingEvidenceList(id, integIdx);
  const errorEl = document.getElementById(`file-error-${id}-${integIdx}`);

  if (errorEl) {
    errorEl.textContent = '';
    errorEl.classList.add('hidden');
  }

  files.forEach((file) => {
    if (file.size > maxFileSize) {
      if (errorEl) {
        errorEl.textContent = 'File > 10MB, tidak bisa diupload.';
        errorEl.classList.remove('hidden');
      }
      return;
    }

    current.push({
      tempId: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
      name: file.name,
      type: file.type,
      file,
      previewUrl: URL.createObjectURL(file),
    });
  });

  pendingEvidenceFiles.set(key, current);
  input.value = '';
  rerenderEvidenceRow(id, integIdx);
}

function removePendingEvidence(id, integIdx, tempId) {
  const key = evidenceRowKey(id, integIdx);
  const current = getPendingEvidenceList(id, integIdx);
  const next = current.filter(item => {
    if (item.tempId === tempId) {
      if (item.previewUrl) {
        URL.revokeObjectURL(item.previewUrl);
      }
      return false;
    }
    return true;
  });
  pendingEvidenceFiles.set(key, next);
  rerenderEvidenceRow(id, integIdx);
}

async function removeExistingEvidence(id, integIdx, evidenceId) {
  const proceed = await showConfirm('Hapus bukti ini?', 'Konfirmasi Hapus Bukti');
  if (!proceed) return;

  try {
    const response = await fetch(`/api/evidence/${evidenceId}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': window.csrfToken,
      }
    });
    const data = await response.json();
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Gagal menghapus bukti.');
    }

    const activity = activities.find(x => x.id === id);
    if (activity && Array.isArray(activity.evidences)) {
      activity.evidences = activity.evidences.filter(ev => ev.id !== evidenceId);
    }
    rerenderEvidenceRow(id, integIdx);
    showAlert('Bukti berhasil dihapus.');
  } catch (e) {
    showAlert(e.message || 'Gagal menghapus bukti.', 'error');
  }
}

function switchPhase(phase) {
  activePhase = phase;
  renderActivities();
}

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
            </p>
            <a href="/vnb-plans" class="inline-block px-8 py-3 rounded-lg text-white font-semibold transition" id="lock-screen-btn" style="background-color: #144600;">
          ${config.buttonText}
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
        
        const res = await apiGet('/api/vnb-plans/employee');
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
    overdue: 'bg-red-100 text-red-700'
  };
  if (status === 'completed') return '';
  const label = (status || 'draft').replace(/_/g, ' ');
  return `<span class="px-2 py-0.5 rounded-full text-xs font-medium ${map[status] || map.draft}">${label}</span>`;
}

function formatIndonesianDate(dateStr) {
  if (!dateStr) return '-';
  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  
  const d = new Date(dateStr);
  if (isNaN(d.getTime())) return dateStr;
  
  const dayName = days[d.getDay()];
  const dateNum = d.getDate();
  const monthName = months[d.getMonth()];
  const year = d.getFullYear();
  
  return `${dayName}, ${dateNum} ${monthName} ${year}`;
}

function formatDateDisplay(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  if (isNaN(date.getTime())) return '';
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
}

function applyDateFieldState(input) {
  if (!input) return;

  const isoValue = input.dataset.isoValue || '';
  input.type = 'text';
  input.value = '';
  input.dataset.hasValue = isoValue ? 'true' : 'false';
  input.classList.add('text-gray-400');
  input.classList.remove('text-gray-700');
}

function focusDateField(input) {
  if (!input) return;
  if (input.dataset.isoValue) {
    input.value = input.dataset.isoValue;
  } else {
    input.value = '';
  }
  input.type = 'date';
  input.classList.remove('text-gray-400');
  input.classList.add('text-gray-700');
}

function blurDateField(input) {
  if (!input) return;
  if (input.type === 'date' && input.value) {
    input.dataset.isoValue = input.value;
  }
  applyDateFieldState(input);
}

function getDateFieldValue(input) {
  if (!input) return '';
  return input.dataset.isoValue || input.value.trim();
}

let countdownInterval = null;

function startCountdown(period) {
  if (countdownInterval) clearInterval(countdownInterval);
  
  const widget = document.getElementById('countdown-widget');
  const widgetContainer = document.getElementById('countdown-widget-container');
  const widgetLabel = document.getElementById('countdown-widget-label');
  const widgetDueDate = document.getElementById('widget-due-date');
  const secondsBox = document.getElementById('countdown-seconds');
  const secondsLabel = document.getElementById('countdown-seconds-label');
  
  if (!period) {
    widget.classList.add('hidden');
    return;
  }
  
  const now = new Date();
  const startDate = period.start_date ? new Date(period.start_date) : new Date();
  if (period.start_date) startDate.setHours(0, 0, 0, 0);
  
  const endDate = new Date(period.end_date || period.due_date);
  endDate.setHours(23, 59, 59, 999);
  
  let targetDate;
  
  if (period.start_date && (now < startDate || period.status === 'not_started')) {
    targetDate = startDate;
    widgetLabel.textContent = 'Masa Fase Dimulai';
    widgetContainer.className = 'card-glass rounded-xl py-3 px-5 border border-red-200 bg-gradient-to-r from-red-50/40 to-orange-50/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm';
    widgetLabel.className = 'text-[10px] font-bold text-red-700 tracking-wider uppercase bg-red-100/60 px-2 py-0.5 rounded-full whitespace-nowrap';
    secondsBox.className = 'w-9 h-9 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center text-sm font-extrabold text-red-700 shadow-xs';
    secondsLabel.className = 'text-[8px] font-bold text-red-600 uppercase tracking-wider mt-0.5 block';
  } else {
    targetDate = endDate;
    widgetLabel.textContent = 'Masa Berakhir Fase';
    
    const diffToDeadline = endDate.getTime() - now.getTime();
    const daysToDeadline = diffToDeadline / (1000 * 60 * 60 * 24);
    
    if (daysToDeadline <= 3 && daysToDeadline > 0) {
      widgetContainer.className = 'card-glass rounded-xl py-3 px-5 border border-amber-300 bg-gradient-to-r from-amber-50/40 to-yellow-50/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm';
      widgetLabel.className = 'text-[10px] font-bold text-amber-700 tracking-wider uppercase bg-amber-100/60 px-2 py-0.5 rounded-full whitespace-nowrap';
      secondsBox.className = 'w-9 h-9 bg-amber-50 border border-amber-300 rounded-lg flex items-center justify-center text-sm font-extrabold text-amber-700 shadow-xs';
      secondsLabel.className = 'text-[8px] font-bold text-amber-600 uppercase tracking-wider mt-0.5 block';
    } else {
      widgetContainer.className = 'card-glass rounded-xl py-3 px-5 border border-emerald-100 bg-gradient-to-r from-emerald-50/40 to-teal-50/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm';
      widgetLabel.className = 'text-[10px] font-bold text-[#144600] tracking-wider uppercase bg-[#144600]/10 px-2 py-0.5 rounded-full whitespace-nowrap';
      secondsBox.className = 'w-9 h-9 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center justify-center text-sm font-extrabold text-emerald-700 shadow-xs';
      secondsLabel.className = 'text-[8px] font-bold text-emerald-600 uppercase tracking-wider mt-0.5 block';
    }
  }
  
  widget.classList.remove('hidden');
  widgetDueDate.textContent = formatIndonesianDate(targetDate);
  
  function update() {
    const now = new Date();
    const diff = targetDate.getTime() - now.getTime();
    
    if (diff <= 0) {
      document.getElementById('countdown-days').textContent = '00';
      document.getElementById('countdown-hours').textContent = '00';
      document.getElementById('countdown-minutes').textContent = '00';
      document.getElementById('countdown-seconds').textContent = '00';
      clearInterval(countdownInterval);
      return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    document.getElementById('countdown-days').textContent = String(days).padStart(2, '0');
    document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('countdown-seconds').textContent = String(seconds).padStart(2, '0');
  }
  
  update();
  countdownInterval = setInterval(update, 1000);
}

async function loadActivities() {
  const activityBody = document.getElementById('activity-body');
  if (activityBody) {
    activityBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  }
  const res = await apiGet('/api/vnb-activities');
  if (!res || res.success === false) {
    showAlert(res?.message || 'Gagal memuat aktivitas', 'error');
    activities = [];
    window.employeePeriods = [];
    renderActivities();
    return;
  }

  activities = res.data || [];
  window.employeePeriods = res.periods || [];
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
  if (!activityTitle) return '-';
  const parts = activityTitle.split(/\s+-\s+(?:Phase|Fase)\s+/i);
  if (parts.length > 1) {
    return 'Fase ' + parts[1].replace(/^Fase\s+/i, ''); // Avoid 'Fase Fase'
  }
  return '-';
}

function parseIntegrations(description) {
  if (!description) return '-';
  const parts = description.split('|').map(s => s.trim()).filter(s => s.length > 0);
  return parts.length === 0 ? '-' : parts.join('\\n');
}

function renderActivities() {
  const container = document.getElementById('activities-container');
  const tabsContainer = document.getElementById('phase-tabs');
  
  if (!activities.length) {
    container.innerHTML = '<div class="text-center py-10 text-gray-400">Belum ada aktivitas</div>';
    tabsContainer.innerHTML = '';
    return;
  }

  // Extract unique phases
  const phases = [...new Set(activities.map(a => extractPhase(a.activity_title)))].sort();
  if (!activePhase && phases.length > 0) activePhase = phases[0];

  // Render Tabs
  let tabsHtml = '';
  phases.forEach(p => {
    const isActive = p === activePhase;
    tabsHtml += `
      <button onclick="switchPhase('${p}')" class="px-4 py-2 text-sm font-semibold rounded-t-lg transition-all border-b-2 ${isActive ? 'border-[#144600] text-[#144600] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'}">
        ${escapeHtml(p)}
      </button>
    `;
  });
  tabsContainer.innerHTML = `<div class="flex border-b border-gray-200 w-full mb-4 px-2">${tabsHtml}</div>`;

  // Render Table for Active Phase
  const filteredActivities = activities.filter(a => extractPhase(a.activity_title) === activePhase);
  
  // Trigger Countdown Widget dynamically
  const phaseNumMatch = activePhase.match(/\d+/);
  const phaseNumber = phaseNumMatch ? parseInt(phaseNumMatch[0]) : 1;
  const period = window.employeePeriods ? window.employeePeriods.find(p => p.phase_number === phaseNumber) : null;
  
  if (period) {
    startCountdown(period);
  } else {
    const activePlanItemWithDueDate = filteredActivities.find(a => a.due_date);
    const dueDateStr = activePlanItemWithDueDate ? activePlanItemWithDueDate.due_date : null;
    startCountdown(dueDateStr ? { end_date: dueDateStr, status: 'in_progress' } : null);
  }

  let html = `
    <table class="table-modern w-full text-left">
      <thead>
        <tr>
          <th>Value</th>
          <th>Integrasi Pengukuran</th>
          <th>Rencana Aktivitas</th>
          <th>Implementasi</th>
          <th>Tanggal Implementasi</th>
          <th>Bukti Implementasi</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
  `;
  
  if (filteredActivities.length === 0) {
    html += `<tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada aktivitas untuk fase ini.</td></tr>`;
  } else {
    filteredActivities.forEach((a) => {
      const behaviour = extractBehaviour(a.activity_title);
      const integrations = parseIntegrations(a.description);
      const integrationList = integrations === '-' ? ['-'] : integrations.split('\\n').filter(s => s);
      const deliverableList = (a.deliverables || '-').split(/\r?\n---\r?\n/).map(s => s.trim());
      
      const rowCount = integrationList.length;
      
      for (let integIdx = 0; integIdx < rowCount; integIdx++) {
        const integration = integrationList[integIdx];
        const deliverable = deliverableList[integIdx] || deliverableList[0] || '-';
        
        // Split existing descriptions and dates by newline dash separator
        const descList = (a.activity_description || '').split('\n---\n').map(s => s.trim());
        const thisDesc = descList[integIdx] === '-' ? '' : (descList[integIdx] || '');
        
        const dateList = (a.activity_date || '').split('\n---\n').map(s => s.trim());
        const thisDate = dateList[integIdx] === '-' ? '' : (dateList[integIdx] || '');

        const existingEvidenceList = (a.evidences || []).filter(ev => ev.description === 'Integration ' + integIdx);

        html += `
          <tr class="hover:bg-gray-50 align-top">
            ${integIdx === 0 ? `
              <td class="px-4 py-4 font-semibold text-gray-800 border-b border-gray-100 w-40" rowspan="${rowCount}">${escapeHtml(behaviour)}</td>
            ` : ''}
            <td class="px-4 py-4 text-xs border-b border-gray-100 w-64 text-gray-700">${escapeHtml(integration).replace(/\\n/g, '<br>')}</td>
            <td class="px-4 py-4 text-xs border-b border-gray-100 text-gray-600 min-w-[180px]">${escapeHtml(deliverable).replace(/\\n/g, '<br>')}</td>
            <td class="px-4 py-4 border-b border-gray-100 min-w-[220px]">
              <textarea id="desc-${a.id}-${integIdx}" rows="1" onfocus="expandImplementationField(this)" onblur="collapseImplementationField(this)" class="w-full h-10 border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none overflow-hidden focus:ring-2 focus:ring-[#144600] focus:border-[#144600] transition-all bg-white" placeholder="Jelaskan implementasi...">${escapeHtml(thisDesc)}</textarea>
              ${a.revision_notes ? `<div class="text-xs text-red-600 mt-2 bg-red-50 p-2 rounded border border-red-100"><i class="fas fa-exclamation-circle mr-1"></i><strong>Revisi:</strong> ${escapeHtml(a.revision_notes)}</div>` : ''}
            </td>
            <td class="px-4 py-4 border-b border-gray-100 w-44">
              <div class="relative">
                <input id="date-${a.id}-${integIdx}" type="text" inputmode="numeric" autocomplete="off" placeholder="Masukkan tanggal" data-iso-value="${thisDate}" onfocus="focusDateField(this)" onblur="blurDateField(this)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#144600] focus:border-[#144600] transition-all bg-white placeholder:text-gray-400 text-gray-400" value="">
              </div>
            </td>
            <td class="px-4 py-4 border-b border-gray-100 w-64">
              <div id="evidence-cell-${a.id}-${integIdx}">${renderEvidenceCellContent(a.id, integIdx)}</div>
            </td>
            <td class="px-3 py-3 text-right whitespace-nowrap border-b border-gray-100 align-top w-24">
              <div class="flex items-start justify-end gap-1">
                <button onclick="saveDraft(${a.id}, ${integIdx})" class="inline-flex items-center justify-center w-10 h-10 border border-gray-300 bg-white rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm" title="Simpan draft" aria-label="Simpan draft">
                  <i class="far fa-save text-lg"></i>
                </button>
                <button onclick="submitActivity(${a.id}, ${integIdx})" class="inline-flex items-center justify-center w-10 h-10 text-white rounded-lg transition-all shadow-sm hover:shadow-md submit-btn bg-gradient-to-r from-[#144600] to-[#1a5c00] hover:from-[#0f3600] hover:to-[#144600]" title="Ajukan" aria-label="Ajukan">
                  <i class="fas fa-paper-plane text-sm"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      }
    });
  }
  
  html += `
      </tbody>
    </table>
  `;
  container.innerHTML = html;
}

function payloadFor(id) {
  // Find the activity
  const activity = activities.find(x => x.id === id);
  if (!activity) return { activity_description: '', activity_date: '' };

  const integrations = parseIntegrations(activity.description);
  const integrationList = integrations === '-' ? ['-'] : integrations.split('\\n').filter(s => s);
  const rowCount = integrationList.length;

  let descList = [];
  let dateList = [];

  for (let idx = 0; idx < rowCount; idx++) {
    const descEl = document.getElementById(`desc-${id}-${idx}`);
    const dateEl = document.getElementById(`date-${id}-${idx}`);

    const descVal = descEl ? descEl.value.trim() : '';
    const dateVal = getDateFieldValue(dateEl);

    descList.push(descVal || '-');
    dateList.push(dateVal || '-');
  }

  return {
    activity_description: descList.join('\n---\n'),
    activity_date: dateList.join('\n---\n'),
  };
}

function validateIntegrationRowsBeforeSubmit(id) {
  const activity = activities.find(x => x.id === id);
  if (!activity) {
    return { valid: false, message: 'Aktivitas tidak ditemukan.' };
  }

  const integrations = parseIntegrations(activity.description);
  const integrationList = integrations === '-' ? ['-'] : integrations.split('\\n').filter(s => s);

  for (let idx = 0; idx < integrationList.length; idx++) {
    const descEl = document.getElementById(`desc-${id}-${idx}`);
    const dateEl = document.getElementById(`date-${id}-${idx}`);

    const descVal = descEl ? descEl.value.trim() : '';
    const dateVal = getDateFieldValue(dateEl);
    const hasNewFile = getPendingEvidenceList(id, idx).length > 0;
    const hasExistingFile = getExistingEvidenceList(id, idx).length > 0;

    if (!descVal || descVal === '-') {
      return { valid: false, message: `Implementasi baris ${idx + 1} belum diisi.` };
    }

    if (!dateVal || dateVal === '-') {
      return { valid: false, message: `Tanggal implementasi baris ${idx + 1} belum diisi.` };
    }

    if (!hasNewFile && !hasExistingFile) {
      return { valid: false, message: `Bukti implementasi baris ${idx + 1} belum diupload.` };
    }
  }

  return { valid: true, message: '' };
}

async function uploadPendingEvidenceFiles(id, integIdx) {
  const pendingList = getPendingEvidenceList(id, integIdx);
  if (!pendingList.length) {
    return true;
  }

  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const bearer = localStorage.getItem('token');
  const headers = {
    'Accept': 'application/json'
  };
  if (token) headers['X-CSRF-TOKEN'] = token;
  if (bearer) headers['Authorization'] = `Bearer ${bearer}`;

  for (const pendingFile of pendingList) {
    const formData = new FormData();
    formData.append('plan_item_id', id);
    formData.append('file', pendingFile.file);
    formData.append('description', 'Integration ' + integIdx);

    try {
      const uploadRes = await fetch('/api/evidence/upload', {
        method: 'POST',
        headers,
        body: formData
      });
      const data = await uploadRes.json();
      if (!uploadRes.ok || !data.success) {
        throw new Error(data.message || 'Gagal upload file bukti');
      }
    } catch (e) {
      console.error('Error uploading evidence:', e);
      showAlert(e.message, 'error');
      return false;
    }
  }

  pendingEvidenceFiles.delete(evidenceRowKey(id, integIdx));
  return true;
}

async function saveDraft(id, integIdx) {
  const btn = document.querySelector(`button[onclick="saveDraft(${id}, ${integIdx})"]`);
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
  btn.disabled = true;

  const uploadSuccess = await uploadPendingEvidenceFiles(id, integIdx);
  if (!uploadSuccess) {
    btn.innerHTML = originalText;
    btn.disabled = false;
    return;
  }

  const res = await apiPost(`/api/vnb-activities/${id}/draft`, payloadFor(id));
  if (res && res.success) {
    showAlert('Draft tersimpan');
    loadActivities();
  } else {
    showAlert(res?.message || res?.error || 'Gagal simpan draft', 'error');
  }
  
  btn.innerHTML = originalText;
  btn.disabled = false;
}

async function submitActivity(id, integIdx) {
  const validation = validateIntegrationRowsBeforeSubmit(id);
  if (!validation.valid) {
    showAlert(validation.message, 'warning');
    return;
  }

  if (!(await showConfirm('Submit aktivitas ini untuk direview oleh manager?', 'Konfirmasi Submit'))) return;
  
  const btn = document.querySelector(`button[onclick="submitActivity(${id}, ${integIdx})"]`);
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
  btn.disabled = true;

  const uploadSuccess = await uploadPendingEvidenceFiles(id, integIdx);
  if (!uploadSuccess) {
    btn.innerHTML = originalText;
    btn.disabled = false;
    return;
  }

  const res = await apiPost(`/api/vnb-activities/${id}/submit`, payloadFor(id));
  if (res && res.success) {
    showAlert('Aktivitas berhasil disubmit');
    loadActivities();
  } else {
    showAlert(res?.message || res?.error || 'Gagal submit', 'error');
  }
  
  btn.innerHTML = originalText;
  btn.disabled = false;
}

// Check plan status on page load
checkPlanStatus();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-activity/index.blade.php ENDPATH**/ ?>