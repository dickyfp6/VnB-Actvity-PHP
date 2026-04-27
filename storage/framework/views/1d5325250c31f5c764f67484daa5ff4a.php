
<?php $__env->startSection('title','Manajemen Intercomm'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manajemen Intercomm</h1>
  </div>

  <div class="table-container">
    <table class="table-modern">
      <thead>
        <tr>
          <th data-column-key="row_index">No</th>
          <th data-column-key="employee_number">NIP</th>
          <th data-column-key="name_display">Nama Lengkap</th>
          <th data-column-key="position">Jabatan</th>
          <th data-column-key="employee_status">Status Pegawai</th>
          <th data-column-key="level">Golongan</th>
          <th class="text-center" data-sortable="false">Aksi</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let users = [];
function formatDate(value) {
  if (!value) return '-';
  const dt = new Date(value);
  if (Number.isNaN(dt.getTime())) return value;
  return dt.toLocaleDateString('id-ID');
}

function renderEmployeeNameLink(row) {
  const employeeId = row?.employee_id ?? row?.id;
  const name = row?.name || '-';
  if (!employeeId) {
    return name;
  }
  return `<a href="/employees/${employeeId}" class="font-bold hover:underline" style="color:#144600;" title="Lihat detail">${name}</a>`;
}

async function loadData() {
  document.getElementById('table-body').innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  const res = await apiGet('/api/intercomm');
  users = res.data || res || [];
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!users.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada employee eligible</td></tr>';
    return;
  }
  tbody.innerHTML = users.map((u, idx) => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-3" data-column-key="row_index">${idx + 1}</td>
      <td class="px-6 py-3" data-column-key="employee_number">${u.employee_number ?? '-'}</td>
      <td class="px-6 py-3 font-medium" data-column-key="name_display">${renderEmployeeNameLink(u)}</td>
      <td class="px-6 py-3" data-column-key="position">${u.position || '-'}</td>
      <td class="px-6 py-3" data-column-key="employee_status">${u.employee_status ?? '-'}</td>
      <td class="px-6 py-3" data-column-key="level">${u.level ?? '-'}</td>
      <td class="px-6 py-3 text-center">
        <label class="inline-flex items-center cursor-pointer select-none">
          <input type="checkbox" class="sr-only peer" ${u.is_intercomm ? 'checked' : ''} onchange="toggleIntercomm(${u.employee_id}, this.checked, this)">
          <span class="relative w-12 h-6 bg-gray-200 rounded-full peer-checked:bg-green-600 transition-colors duration-200 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-transform after:duration-200 peer-checked:after:translate-x-6"></span>
        </label>
      </td>
    </tr>
  `).join('');
}

async function toggleIntercomm(employeeId, enabled, inputEl) {
  const label = enabled ? 'mengaktifkan' : 'mencabut';
  if (!(await showConfirm(`Yakin ingin ${label} akun ini?`, 'Konfirmasi'))) return;
  const endpoint = enabled ? 'activate' : 'deactivate';
  const res = await apiPost(`/api/intercomm/${employeeId}/${endpoint}`, {});

  if (res && res.success) {
    showAlert(res.temp_password ? `${res.message} Password sementara: ${res.temp_password}` : (res.message || 'Perubahan berhasil'));
    await loadData();
    return;
  }

  if (inputEl) {
    inputEl.checked = !enabled;
  }
  showAlert(res?.message || res?.error || 'Gagal mengubah status Intercomm', 'error');
}

loadData();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/intercomm/index.blade.php ENDPATH**/ ?>