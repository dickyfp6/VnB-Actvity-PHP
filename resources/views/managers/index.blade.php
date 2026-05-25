@extends('layouts.app')
@section('title','Manage Manager')
@section('page_title','Manage Manager')
@section('page_subtitle','Pantau daftar manager yang diambil langsung dari data employee.')
@section('content')
<div class="px-4">
  <div class="table-container">
    <div class="overflow-x-auto">
      <table class="table-modern" style="width: max-content; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-column-key="employee_number">NIP</th>
            <th data-column-key="name">Nama Lengkap</th>
            <th data-column-key="division">Divisi</th>
            <th data-column-key="department">Departemen</th>
            <th data-column-key="position">Jabatan</th>
            <th data-column-key="total_employee_count" class="text-center">Jumlah Employee</th>
            <th data-column-key="vnb_employee_count" class="text-center">VnB's Employee</th>
            <th data-column-key="star_submissions_count" class="text-center">Ajuan STAR</th>
          </tr>
        </thead>
        <tbody id="table-body" style="white-space: nowrap;">
          <tr><td colspan="8" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

    @push('styles')
    <style>
      .manager-circle {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: none;
        cursor: pointer;
        box-shadow: none;
      }
      .manager-circle:focus { outline: 2px solid rgba(20,70,0,0.15); outline-offset: 2px; }
      .manager-circle-grey { background: #f3f4f6; color: #374151; }
      .manager-circle-green { background: rgba(55,170,5,0.12); color: #166534; }
      .manager-circle-red { background: rgba(220,38,38,0.08); color: #b45309; }
      .manager-circle + .manager-circle { margin-left: 6px; }
    </style>
    @endpush

    @push('scripts')
<script>
let managers = [];

function renderEmployeeNameLink(row) {
  const employeeId = row?.employee_id ?? row?.id;
  const name = row?.name || '-';
  if (!employeeId) {
    return name;
  }
  return `<a href="/employees/${employeeId}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">${name}</a>`;
}

async function loadData() {
  const tbody = document.getElementById('table-body');
  tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-gray-400">Memuat...</td></tr>';

  try {
    const res = await apiGet('/api/managers-list');
    managers = res?.data || res || [];
    renderTable();
  } catch (error) {
    console.error('Error loading managers:', error);
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-red-500">Gagal memuat data</td></tr>';
  }
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!managers.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-gray-400">Tidak ada manager yang ditemukan</td></tr>';
    return;
  }

  tbody.innerHTML = managers.map((manager) => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-3 font-mono text-sm" data-column-key="employee_number">${manager.employee_number ?? '-'}</td>
      <td class="px-6 py-3 font-medium" data-column-key="name">${renderEmployeeNameLink(manager)}</td>
      <td class="px-6 py-3" data-column-key="division">${manager.division || '-'}</td>
      <td class="px-6 py-3" data-column-key="department">${manager.department || '-'}</td>
      <td class="px-6 py-3" data-column-key="position">${manager.position || '-'}</td>
      <td class="px-6 py-3 text-center" data-column-key="total_employee_count">
        <button type="button" class="manager-circle manager-circle-grey" title="Lihat semua employee" onclick="goToEmployeesFiltered('${manager.name}')">
          ${manager.total_employee_count ?? 0}
        </button>
      </td>
      <td class="px-6 py-3 text-center" data-column-key="vnb_employee_count">
        <button type="button" class="manager-circle manager-circle-green" title="Lihat participants VnB" onclick="goToParticipantsFiltered('${manager.name}')">
          ${manager.vnb_employee_count ?? 0}
        </button>
      </td>
      <td class="px-6 py-3 text-center" data-column-key="star_submissions_count">
        <button type="button" class="manager-circle manager-circle-red" title="Ajuan STAR (belum tersedia)" onclick="openStarPlaceholder()">
          ${manager.star_submissions_count ?? 0}
        </button>
      </td>
    </tr>
  `).join('');
}

function goToEmployeesFiltered(managerName) {
  if (!managerName || managerName === 'null' || managerName === 'undefined') {
    alert('Nama Manager tidak valid.');
    return;
  }
  window.location.href = `/employees?manager_name=${encodeURIComponent(managerName)}`;
}

function goToParticipantsFiltered(managerName) {
  if (!managerName || managerName === 'null' || managerName === 'undefined') {
    alert('Nama Manager tidak valid.');
    return;
  }
  window.location.href = `/vnb/participants?manager_name=${encodeURIComponent(managerName)}`;
}

function openStarPlaceholder() {
  alert('Fitur Ajuan STAR belum tersedia.');
}

loadData();
</script>
@endpush
@endsection
