@extends('layouts.app')
@section('title','Manajemen Intercomm')
@section('content')
<div class="px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manajemen Intercomm</h1>
    <button onclick="openAddModal()" class="text-white px-4 py-2 rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
      <i class="fas fa-plus mr-1"></i> Tambah Intercomm
    </button>
  </div>

  <div class="table-container">
    <table class="table-modern">
      <thead>
        <tr>
          <th>No</th>
          <th>NIP</th>
          <th>Nama Lengkap</th>
          <th>Tanggal Masuk</th>
          <th>Email</th>
          <th>Golongan</th>
          <th>Status Pegawai</th>
          <th>Tanggal Assign</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tr><td colspan="9" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Detail Employee --}}
<div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800">Detail Employee</h2>
      <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
    </div>
    <div id="detail-body" class="max-h-[70vh] overflow-y-auto space-y-1"></div>
  </div>
</div>

{{-- Modal Tambah --}}
<div id="modal-add" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Assign Intercomm</h2>
    <form id="form-add" class="space-y-3">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Employee</label>
        <select id="add-employee-id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
          <option value="">Memuat opsi employee...</option>
        </select>
      </div>
      <div id="eligible-info" class="text-xs text-gray-500">
        Hanya employee dari Divisi Human Resource dan Departemen People, Culture, and Experience yang bisa di-assign sebagai Intercomm.
      </div>
      <p class="text-xs text-gray-400">Jika employee belum punya akun user, sistem akan membuat akun otomatis.</p>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Assign</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Edit Intercomm</h2>
    <form id="form-edit" class="space-y-3">
      <input type="hidden" id="edit-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input id="edit-name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input id="edit-email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'" required>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Password Result --}}
<div id="modal-pw" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-xl text-center">
    <div class="text-4xl mb-3" style="color: #37AA05;"><i class="fas fa-check-circle"></i></div>
    <h2 class="text-lg font-bold text-gray-800 mb-1">Intercomm Ditambahkan</h2>
    <p class="text-sm text-gray-600 mb-3">Password sementara untuk akun baru:</p>
    <div id="pw-display" class="rounded-lg px-4 py-3 text-2xl font-mono font-bold tracking-widest mb-4" style="background-color: #f3f4f6; color: #144600;"></div>
    <p class="text-xs text-gray-400 mb-4">Sampaikan password ini kepada pengguna. Mereka dapat menggantinya nanti.</p>
    <button onclick="document.getElementById('modal-pw').classList.add('hidden')" class="w-full text-white py-2 rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Tutup</button>
  </div>
</div>

@push('scripts')
<script>
let users = [];
let employeeOptions = [];

function escapeHtml(value) {
  return (value || '').toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function formatDate(value) {
  if (!value) return '-';
  const dt = new Date(value);
  if (Number.isNaN(dt.getTime())) return value;
  return dt.toLocaleDateString('id-ID');
}

function formatDateTime(value) {
  if (!value) return '-';
  const dt = new Date(value);
  if (Number.isNaN(dt.getTime())) return value;
  return `${dt.toLocaleDateString('id-ID')} ${dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
}

function openAddModal() {
  loadEmployeeOptions().then(() => {
    document.getElementById('modal-add').classList.remove('hidden');
  });
}

async function loadEmployeeOptions() {
  const select = document.getElementById('add-employee-id');
  select.innerHTML = '<option value="">Memuat opsi employee...</option>';

  const res = await apiGet('/api/intercomm/employee-options');
  employeeOptions = res?.data || [];

  if (!employeeOptions.length) {
    select.innerHTML = '<option value="">Tidak ada employee yang eligible</option>';
    return;
  }

  select.innerHTML = '<option value="">Pilih employee</option>' + employeeOptions.map(emp => {
    const label = `${emp.employee_number || '-'} - ${emp.name || '-'} (${emp.email || '-'})`;
    return `<option value="${emp.id}">${escapeHtml(label)}</option>`;
  }).join('');
}

async function loadData() {
  document.getElementById('table-body').innerHTML = '<tr><td colspan="9" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  const res = await apiGet('/api/intercomm');
  users = res.data || res || [];
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!users.length) {
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-10 text-gray-400">Belum ada data intercomm</td></tr>';
    return;
  }
  tbody.innerHTML = users.map((u, idx) => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-3">${idx + 1}</td>
      <td class="px-6 py-3">${u.employee_number ?? '-'}</td>
      <td class="px-6 py-3 font-medium">
        ${u.employee_id
          ? `<button type="button" onclick="openDetailModal(${u.employee_id})" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">${escapeHtml(u.name || '-')}</button>`
          : `${escapeHtml(u.name || '-')}`}
      </td>
      <td class="px-6 py-3">${formatDate(u.date_joined)}</td>
      <td class="px-6 py-3">${u.email}</td>
      <td class="px-6 py-3">${u.level ?? '-'}</td>
      <td class="px-6 py-3">${u.employee_status ?? '-'}</td>
      <td class="px-6 py-3">
        ${formatDateTime(u.assigned_at)}
      </td>
      <td class="px-6 py-3 text-right space-x-2">
        <button onclick="openEditModal(${u.id})" class="text-sm" title="Edit" style="cursor: pointer; color: #144600;" onmouseover="this.style.color='#37AA05'" onmouseout="this.style.color='#144600'"><i class="fas fa-edit"></i></button>
        ${u.status === 'active'
          ? `<button onclick="toggleStatus(${u.id},'deactivate')" class="text-red-500 hover:text-red-700 text-sm" title="Nonaktifkan"><i class="fas fa-ban"></i></button>`
          : `<button onclick="toggleStatus(${u.id},'activate')" class="text-green-600 hover:text-green-800 text-sm" title="Aktifkan"><i class="fas fa-check-circle"></i></button>`
        }
      </td>
    </tr>
  `).join('');
}

async function openDetailModal(employeeId) {
  if (!employeeId) {
    showAlert('Data employee belum terhubung.', 'error');
    return;
  }

  const body = document.getElementById('detail-body');
  body.innerHTML = '<div class="text-sm text-gray-500 py-2">Memuat detail...</div>';

  let row = users.find(item => item.employee_id === employeeId) || {};
  const detailRes = await apiGet(`/api/employees/${employeeId}`);
  if (detailRes && detailRes.success === true && detailRes.data) {
    row = detailRes.data;
  }

  const fields = [
    ['NIP', row.employee_number],
    ['Nama Lengkap', row.name_display || row.name],
    ['Tanggal Masuk', row.date_joined],
    ['Email', row.email],
    ['Whatsapp', row.whatsapp],
    ['Perusahaan', row.company],
    ['Divisi', row.division?.name || row.division],
    ['Departemen', row.department?.name || row.department],
    ['Jabatan', row.position?.name || row.position],
    ['Penempatan', row.placement],
    ['Golongan', row.level],
    ['Status Pegawai', row.employee_status],
    ['Status Employee', row.status],
  ];

  body.innerHTML = fields.map(([k, v]) => `
    <div class="grid grid-cols-3 gap-2 text-sm py-1">
      <div class="text-gray-500">${escapeHtml(k)}</div>
      <div class="col-span-2 font-medium text-gray-800">${escapeHtml(v || '-')}</div>
    </div>
  `).join('');

  document.getElementById('modal-detail').classList.remove('hidden');
}

function closeDetailModal() {
  document.getElementById('modal-detail').classList.add('hidden');
}

function openEditModal(id) {
  const u = users.find(x => x.id == id);
  if (!u) return;
  document.getElementById('edit-id').value = u.id;
  document.getElementById('edit-name').value = u.name;
  document.getElementById('edit-email').value = u.email;
  document.getElementById('modal-edit').classList.remove('hidden');
}

document.getElementById('form-add').addEventListener('submit', async function(e) {
  e.preventDefault();
  const employeeId = Number(document.getElementById('add-employee-id').value);
  if (!employeeId) {
    showAlert('Pilih employee terlebih dahulu.', 'error');
    return;
  }

  const res = await apiPost('/api/intercomm', {
    employee_id: employeeId,
  });
  if (res.success && res.temp_password) {
    document.getElementById('modal-add').classList.add('hidden');
    document.getElementById('pw-display').textContent = res.temp_password;
    document.getElementById('modal-pw').classList.remove('hidden');
    await loadData();
  } else if (res.success) {
    showAlert(res.message || 'Intercomm berhasil di-assign');
    document.getElementById('modal-add').classList.add('hidden');
    await loadData();
  } else {
    showAlert(res.message || res.error || 'Gagal menambahkan', 'error');
  }
});

document.getElementById('form-edit').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('edit-id').value;
  const res = await apiPost(`/api/intercomm/${id}`, {
    name: document.getElementById('edit-name').value,
    email: document.getElementById('edit-email').value,
  }, 'PUT');
  if (res.message || res.id) {
    showAlert('Data diperbarui');
    document.getElementById('modal-edit').classList.add('hidden');
    loadData();
  } else {
    showAlert(res.error || 'Gagal memperbarui', 'error');
  }
});

async function toggleStatus(id, action) {
  const label = action === 'deactivate' ? 'menonaktifkan' : 'mengaktifkan';
  if (!(await showConfirm(`Yakin ingin ${label} akun ini?`, 'Konfirmasi'))) return;
  const res = await apiPost(`/api/intercomm/${id}/${action}`, {});
  if (res.message) { showAlert(res.message); loadData(); }
  else showAlert(res.error || 'Gagal', 'error');
}

loadData();
</script>
@endpush
@endsection
