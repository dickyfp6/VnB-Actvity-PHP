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

function renderEmployeeNameLink(row) {
  const employeeId = row?.employee_id ?? row?.id;
  const name = escapeHtml(row?.employee_name || '-');
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
  tbody.innerHTML = pending.map(p => `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3" data-column-key="employee_name">${renderEmployeeNameLink(p)}</td>
      <td class="px-4 py-3" data-column-key="behaviour">${p.behaviour || '-'}</td>
      <td class="px-4 py-3" data-column-key="phase">${p.phase || '-'}</td>
      <td class="px-4 py-3" data-column-key="activity_description">${p.activity_description || '-'}</td>
      <td class="px-4 py-3" data-column-key="activity_date">${p.activity_date || '-'}</td>
      <td class="px-4 py-3 text-right">
        <button onclick="openReviewModal(${p.id})" class="px-3 py-1.5 text-white rounded text-xs transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Review</button>
      </td>
    </tr>
  `).join('');
}

function openReviewModal(id) {
  selected = pending.find(x => x.id == id);
  if (!selected) return;
  document.getElementById('revision-notes').value = '';
  document.getElementById('detail-box').innerHTML = `
    <p><span class="font-semibold">Employee:</span> ${renderEmployeeNameLink(selected)}</p>
    <p><span class="font-semibold">Behaviour:</span> ${selected.behaviour || '-'}</p>
    <p><span class="font-semibold">Phase:</span> ${selected.phase || '-'}</p>
    <p><span class="font-semibold">Rencana:</span> ${selected.plan_description || '-'}</p>
    <p><span class="font-semibold">Aktivitas:</span> ${selected.activity_description || '-'}</p>
    <p><span class="font-semibold">Tanggal Aktivitas:</span> ${selected.activity_date || '-'}</p>
  `;
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
