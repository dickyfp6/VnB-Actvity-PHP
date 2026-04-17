
<?php $__env->startSection('title','Manajemen Intercomm'); ?>
<?php $__env->startSection('content'); ?>
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
          <th>Nama</th>
          <th>Email</th>
          <th>Status</th>
          <th>Terdaftar</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tr><td colspan="5" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>
</div>


<div id="modal-add" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Intercomm</h2>
    <form id="form-add" class="space-y-3">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input id="add-name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input id="add-email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'" required>
      </div>
      <p class="text-xs text-gray-400">Password akan dibuat otomatis dan ditampilkan setelah menyimpan.</p>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Simpan</button>
      </div>
    </form>
  </div>
</div>


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

<?php $__env->startPush('scripts'); ?>
<script>
let users = [];

function openAddModal() {
  document.getElementById('add-name').value = '';
  document.getElementById('add-email').value = '';
  document.getElementById('modal-add').classList.remove('hidden');
}

async function loadData() {
  document.getElementById('table-body').innerHTML = '<tr><td colspan="5" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  const res = await apiGet('/api/intercomm');
  users = res.data || res || [];
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!users.length) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-10 text-gray-400">Belum ada data intercomm</td></tr>';
    return;
  }
  tbody.innerHTML = users.map(u => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-3 font-medium">${u.name}</td>
      <td class="px-6 py-3">${u.email}</td>
      <td class="px-6 py-3">
        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${u.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}">
          ${u.status === 'active' ? 'Aktif' : 'Nonaktif'}
        </span>
      </td>
      <td class="px-6 py-3 text-gray-500">${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
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
  const res = await apiPost('/api/intercomm', {
    name: document.getElementById('add-name').value,
    email: document.getElementById('add-email').value,
  });
  if (res.temp_password) {
    document.getElementById('modal-add').classList.add('hidden');
    document.getElementById('pw-display').textContent = res.temp_password;
    document.getElementById('modal-pw').classList.remove('hidden');
    loadData();
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/intercomm/index.blade.php ENDPATH**/ ?>