@extends('layouts.app')
@section('title','Review Aktivitas')
@section('page_title','Review Aktivitas')
@section('page_subtitle','Tinjau aktivitas employee sebelum memberi approval atau revisi.')
@section('content')
<div class="px-4">
  <div class="table-container">
    <table class="table-modern">
      <thead>
        <tr>
          <th data-sort-key="employee_name">Employee</th>
          <th data-sort-key="behaviour">Behaviour</th>
          <th data-sort-key="phase">Phase</th>
          <th data-sort-key="activity_description">Activity</th>
          <th data-sort-key="activity_date">Tanggal</th>
          <th class="text-right" data-sortable="false">Aksi</th>
        </tr>
      </thead>
      <tbody id="review-body">
        <tr><td colspan="6" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Detail Review --}}
<div id="review-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800">Detail Review Aktivitas</h2>
      <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
    </div>
    <div id="detail-box" class="space-y-2 text-sm text-gray-700 mb-4"></div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Revisi (opsional)</label>
      <textarea id="revision-notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Isi catatan jika meminta revisi..."></textarea>
    </div>
    <div class="flex justify-end gap-2 mt-4">
      <button onclick="requestRevision()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Request Revision</button>
      <button onclick="approveActivity()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Approve</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
let pending = [];
let selected = null;

function escapeHtml(value) {
  return (value || '').toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function extractBehaviour(activityTitle) {
  if (!activityTitle) return '-';
  const parts = activityTitle.split(' - ');
  return parts[0].trim() || '-';
}

function extractPhase(activityTitle) {
  if (!activityTitle) return '-';
  const parts = activityTitle.split(/\s+-\s+(?:Phase|Fase)\s+/i);
  if (parts.length > 1) {
    return 'Fase ' + parts[1].replace(/^Fase\s+/i, '');
  }
  return '-';
}

function parseIntegrations(description) {
  if (!description) return '-';
  const parts = description.split('|').map(s => s.trim()).filter(s => s.length > 0);
  return parts.length === 0 ? '-' : parts.join('\n');
}

function renderEmployeeNameLink(row) {
  const employeeId = row?.employee?.id ?? row?.employee_id ?? row?.id;
  const name = escapeHtml(row?.employee?.name || row?.employee_name || '-');
  if (!employeeId) {
    return name;
  }

  return `<a href="/employees/${employeeId}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">${name}</a>`;
}

async function loadPending() {
  document.getElementById('review-body').innerHTML = '<tr><td colspan="6" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  const res = await apiGet('/api/vnb-activities/pending-review');
  pending = res.data || res || [];
  renderPending();
}

function renderPending() {
  const tbody = document.getElementById('review-body');
  if (!pending.length) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-gray-400">Tidak ada aktivitas menunggu review</td></tr>';
    return;
  }
  tbody.innerHTML = pending.map(p => {
    const behaviour = extractBehaviour(p.activity_title);
    const phase = extractPhase(p.activity_title);
    
    // Split descriptions and dates by newline-dash
    const descParts = (p.activity_description || '').split('\n---\n').map(s => s.trim()).filter(s => s && s !== '-');
    const dateParts = (p.activity_date || '').split('\n---\n').map(s => s.trim()).filter(s => s && s !== '-');
    
    const descFormatted = descParts.map((d, i) => `[Int ${i+1}] ${escapeHtml(d)}`).join('<br>');
    const dateFormatted = dateParts.map((d, i) => `[Int ${i+1}] ${escapeHtml(d)}`).join('<br>');

    return `
      <tr class="hover:bg-gray-50 align-top">
        <td class="px-4 py-3" data-column-key="employee_name">${renderEmployeeNameLink(p)}</td>
        <td class="px-4 py-3" data-column-key="behaviour">${escapeHtml(behaviour)}</td>
        <td class="px-4 py-3" data-column-key="phase">${escapeHtml(phase)}</td>
        <td class="px-4 py-3 text-xs leading-relaxed" data-column-key="activity_description">${descFormatted || '-'}</td>
        <td class="px-4 py-3 text-xs leading-relaxed" data-column-key="activity_date">${dateFormatted || '-'}</td>
        <td class="px-4 py-3 text-right">
          <button onclick="openReviewModal(${p.id})" class="px-3 py-1.5 text-white rounded text-xs transition animate-pulse-subtle" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Review</button>
        </td>
      </tr>
    `;
  }).join('');
}

function openReviewModal(id) {
  selected = pending.find(x => x.id == id);
  if (!selected) return;
  document.getElementById('revision-notes').value = '';
  
  const behaviour = extractBehaviour(selected.activity_title);
  const phase = extractPhase(selected.activity_title);
  
  const integrations = parseIntegrations(selected.description);
  const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
  
  const descList = (selected.activity_description || '').split('\n---\n').map(s => s.trim());
  const dateList = (selected.activity_date || '').split('\n---\n').map(s => s.trim());
  
  let detailsHtml = `
    <div class="space-y-1.5 pb-3 border-b mb-4">
      <p><span class="font-semibold text-gray-700 text-sm">Employee:</span> ${renderEmployeeNameLink(selected)}</p>
      <p><span class="font-semibold text-gray-700 text-sm">Behaviour:</span> ${escapeHtml(behaviour)}</p>
      <p><span class="font-semibold text-gray-700 text-sm">Phase:</span> ${escapeHtml(phase)}</p>
    </div>
    <div class="space-y-3.5">
      <h3 class="font-bold text-gray-900 text-sm">Detail Pelaksanaan per Integrasi:</h3>
  `;
  
  integrationList.forEach((integ, idx) => {
    const desc = descList[idx] || '-';
    const date = dateList[idx] || '-';
    const matchingEvidence = (selected.evidences || []).find(ev => ev.description === 'Integration ' + idx);
    const hasEvidence = !!matchingEvidence;
    const fileHtml = hasEvidence 
      ? `<a href="/storage/${matchingEvidence.file_path}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-[#144600] font-bold hover:underline bg-[#144600]/10 px-2.5 py-1 rounded mt-1.5 border border-[#144600]/20"><i class="fas fa-file-download"></i> ${escapeHtml(matchingEvidence.file_name)}</a>`
      : `<span class="text-xs text-gray-400 italic">Belum ada file bukti</span>`;

    detailsHtml += `
      <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-200/60 shadow-sm space-y-1.5 hover:shadow-md transition-all">
        <p class="font-bold text-xs text-[#144600]">Integrasi ${idx + 1}: ${escapeHtml(integ)}</p>
        <p class="text-xs text-gray-700 leading-relaxed"><span class="font-semibold text-gray-800">Implementasi:</span> ${escapeHtml(desc)}</p>
        <p class="text-xs text-gray-700"><span class="font-semibold text-gray-800">Tanggal:</span> ${escapeHtml(date)}</p>
        <div class="pt-1 flex items-center gap-2">
          <span class="text-xs font-semibold text-gray-800">Bukti:</span> ${fileHtml}
        </div>
      </div>
    `;
  });
  detailsHtml += '</div>';
  
  document.getElementById('detail-box').innerHTML = detailsHtml;
  document.getElementById('review-modal').classList.remove('hidden');
}

function closeReviewModal() {
  document.getElementById('review-modal').classList.add('hidden');
}

async function approveActivity() {
  if (!selected) return;
  const res = await apiPost(`/api/vnb-activities/${selected.id}/approve`, {});
  if (res.message || res.id || res.data) {
    showAlert(res.message || 'Aktivitas disetujui');
    closeReviewModal();
    loadPending();
  } else showAlert(res.error || 'Gagal approve', 'error');
}

async function requestRevision() {
  if (!selected) return;
  const notes = document.getElementById('revision-notes').value;
  if (!notes.trim()) {
    showAlert('Isi catatan revisi terlebih dahulu', 'error');
    return;
  }
  const res = await apiPost(`/api/vnb-activities/${selected.id}/request-revision`, { revision_notes: notes });
  if (res.message || res.id || res.data) {
    showAlert('Revisi dikirim ke Employee');
    closeReviewModal();
    loadPending();
  } else showAlert(res.error || 'Gagal request revision', 'error');
}

loadPending();
</script>
@endpush
@endsection
