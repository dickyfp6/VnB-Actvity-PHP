@extends('layouts.app')
@section('title','Manager - Planning History')
@section('content')
<div class="px-4 space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Planning Approved & History</h1>
    <a id="back-detail-link" href="#" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Kembali ke Detail New Hire</a>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-4" id="employee-summary">
    <div class="text-sm text-gray-500">Memuat data...</div>
  </div>

  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
      <h2 class="text-base font-semibold text-gray-800">Planning Saat Ini (Approved/Current)</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm" style="min-width: 900px;">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Behaviour</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Integrasi</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Rencana Aktivitas</th>
            <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Tanggal Implementasi</th>
          </tr>
        </thead>
        <tbody id="approved-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="4" class="text-center py-8 text-gray-400">Memuat planning...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
      <h2 class="text-base font-semibold text-gray-800">Riwayat Versi Planning</h2>
      <p class="text-xs text-gray-500 mt-1">Setiap submit ulang planning akan terekam sebagai versi baru.</p>
    </div>
    <div id="revision-list" class="divide-y divide-gray-100"></div>
  </div>
</div>

@push('scripts')
<script>
const employeeId = @json($employeeId);

function badgeClass(status) {
  if (status === 'approved') return 'bg-emerald-100 text-emerald-700';
  if (status === 'rejected') return 'bg-red-100 text-red-700';
  return 'bg-amber-100 text-amber-700';
}

async function loadPlanningHistory() {
  const res = await apiGet(`/api/manager/new-hires/${employeeId}/planning-history`);
  if (!(res && res.success === true && res.data)) {
    showAlert(res?.message || 'Gagal memuat planning history', 'error');
    return;
  }

  const data = res.data;
  const employee = data.employee || {};
  document.getElementById('employee-summary').innerHTML = `
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
      <div><div class="text-xs text-gray-500">NIP</div><div class="font-medium">${employee.employee_number || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Nama</div><div class="font-medium">${employee.name || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Status Plan</div><div class="font-medium">${data.plan?.status || '-'}</div></div>
    </div>
  `;

  const backDetail = document.getElementById('back-detail-link');
  backDetail.href = `/manager/new-hires/${employee.id}`;

  const approvedBody = document.getElementById('approved-body');
  const approvedItems = data.approved_items || [];
  if (!approvedItems.length) {
    approvedBody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Belum ada data planning</td></tr>';
  } else {
    approvedBody.innerHTML = approvedItems.map(item => `
      <tr>
        <td class="px-4 py-3">${item.activity_title || '-'}</td>
        <td class="px-4 py-3">${item.description || '-'}</td>
        <td class="px-4 py-3">${item.deliverables || '-'}</td>
        <td class="px-4 py-3">${item.implementation_date || '-'}</td>
      </tr>
    `).join('');
  }

  const revisionList = document.getElementById('revision-list');
  const revisions = data.revisions || [];
  if (!revisions.length) {
    revisionList.innerHTML = '<div class="p-4 text-sm text-gray-500">Belum ada riwayat versi.</div>';
    return;
  }

  revisionList.innerHTML = revisions.map(rev => {
    const items = rev.snapshot_items || [];
    const preview = items.slice(0, 3).map(it => `<li>${it.activity_title || '-'} (${it.implementation_date || '-'})</li>`).join('');

    return `
      <div class="p-4">
        <div class="flex flex-wrap items-center gap-2 mb-2">
          <span class="font-semibold text-sm text-gray-800">Versi ${rev.version_number}</span>
          <span class="px-2 py-0.5 rounded-full text-xs font-medium ${badgeClass(rev.status)}">${rev.status}</span>
          <span class="text-xs text-gray-500">Submit: ${rev.submitted_at || '-'}</span>
          <span class="text-xs text-gray-500">Review: ${rev.reviewed_at || '-'}</span>
        </div>
        <div class="text-xs text-gray-600 mb-2">Catatan Manager: ${rev.review_notes || '-'}</div>
        <details class="text-sm">
          <summary class="cursor-pointer text-gray-700">Lihat snapshot item (${items.length})</summary>
          <ul class="list-disc pl-5 mt-2 text-xs text-gray-600">${preview || '<li>-</li>'}</ul>
        </details>
      </div>
    `;
  }).join('');
}

loadPlanningHistory();
</script>
@endpush
@endsection
