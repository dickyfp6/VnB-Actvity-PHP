@extends('layouts.app')
@section('title','Manager - Approval Request')
@section('content')
<div class="px-4 space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Approval Request</h1>
    <button onclick="loadRequests()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
      <i class="fas fa-sync-alt mr-1"></i> Refresh
    </button>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="summary-box">
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Pending Planning</div>
      <div id="planning-count" class="text-2xl font-bold text-gray-800">0</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Pending Activity</div>
      <div id="activity-count" class="text-2xl font-bold text-gray-800">0</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="text-xs text-gray-500">Total Approval Request</div>
      <div id="total-count" class="text-2xl font-bold text-gray-800">0</div>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm" style="min-width: 980px;">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Jenis</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">New Hire</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">NIP</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Perusahaan</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Judul</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Phase</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Waktu Submit</th>
            <th class="px-4 py-3 text-right text-xs uppercase text-gray-500">Aksi</th>
          </tr>
        </thead>
        <tbody id="request-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script>
let requestRows = [];

async function loadRequests() {
  const tbody = document.getElementById('request-body');
  tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';

  const res = await apiGet('/api/manager/approval-requests');
  if (!(res && res.success === true)) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Gagal memuat approval request</td></tr>';
    return;
  }

  requestRows = res.data || [];
  const summary = res.summary || {};
  document.getElementById('planning-count').textContent = summary.planning_count || 0;
  document.getElementById('activity-count').textContent = summary.activity_count || 0;
  document.getElementById('total-count').textContent = summary.total_count || 0;

  renderRows();
}

function renderRows() {
  const tbody = document.getElementById('request-body');
  if (!requestRows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-400">Tidak ada approval request</td></tr>';
    return;
  }

  tbody.innerHTML = requestRows.map(row => `
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-3">
        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${row.type === 'planning' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'}">
          ${row.type === 'planning' ? 'Planning' : 'Activity'}
        </span>
      </td>
      <td class="px-4 py-3">${row.employee_name || '-'}</td>
      <td class="px-4 py-3">${row.employee_number || '-'}</td>
      <td class="px-4 py-3">${row.company || '-'}</td>
      <td class="px-4 py-3">${row.title || '-'}</td>
      <td class="px-4 py-3">${row.phase || '-'}</td>
      <td class="px-4 py-3">${row.submitted_at || '-'}</td>
      <td class="px-4 py-3 text-right">
        <a href="/manager/new-hires/${row.employee_id}" class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700">
          <i class="fas fa-arrow-right"></i> Lihat Detail
        </a>
      </td>
    </tr>
  `).join('');
}

loadRequests();
</script>
@endpush
@endsection
