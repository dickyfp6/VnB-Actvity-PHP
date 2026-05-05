
<?php $__env->startSection('title','Manage Manager'); ?>
<?php $__env->startSection('page_title','Manage Manager'); ?>
<?php $__env->startSection('page_subtitle','Pantau daftar manager yang diambil langsung dari data employee.'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4">
  <div class="table-container">
    <div class="overflow-x-auto">
      <table class="table-modern" style="width: max-content; min-width: 100%; table-layout: auto;">
        <thead style="white-space: nowrap;">
          <tr>
            <th data-column-key="employee_number">NIP</th>
            <th data-column-key="name">Nama Lengkap</th>
            <th data-column-key="division">Divisi</th>
            <th data-column-key="department">Departemen</th>
            <th data-column-key="position">Jabatan</th>
            <th data-column-key="vnb_employee_count" class="text-center">VnB's Employee</th>
            <th data-column-key="star_submissions_count" class="text-center">Ajuan STAR</th>
          </tr>
        </thead>
        <tbody id="table-body" style="white-space: nowrap;">
          <tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
  tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat...</td></tr>';

  try {
    const res = await apiGet('/api/managers-list');
    managers = res?.data || res || [];
    renderTable();
  } catch (error) {
    console.error('Error loading managers:', error);
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-red-500">Gagal memuat data</td></tr>';
  }
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!managers.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">Tidak ada manager yang ditemukan</td></tr>';
    return;
  }

  tbody.innerHTML = managers.map((manager) => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-3 font-mono text-sm" data-column-key="employee_number">${manager.employee_number ?? '-'}</td>
      <td class="px-6 py-3 font-medium" data-column-key="name">${renderEmployeeNameLink(manager)}</td>
      <td class="px-6 py-3" data-column-key="division">${manager.division || '-'}</td>
      <td class="px-6 py-3" data-column-key="department">${manager.department || '-'}</td>
      <td class="px-6 py-3" data-column-key="position">${manager.position || '-'}</td>
      <td class="px-6 py-3 text-center" data-column-key="vnb_employee_count">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full" style="background: rgba(55,170,5,0.1); color: #37AA05; font-weight: bold;">
          ${manager.vnb_employee_count ?? 0}
        </span>
      </td>
      <td class="px-6 py-3 text-center" data-column-key="star_submissions_count">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full" style="background: rgba(180,83,9,0.1); color: #b45309; font-weight: bold;">
          ${manager.star_submissions_count ?? 0}
        </span>
      </td>
    </tr>
  `).join('');
}

loadData();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/managers/index.blade.php ENDPATH**/ ?>