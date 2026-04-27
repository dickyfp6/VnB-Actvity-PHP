
<?php $__env->startSection('title','Manage Manager'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manage Manager</h1>
    <button onclick="openAddModal()" class="text-white px-4 py-2 rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
      <i class="fas fa-plus mr-1"></i> Tambah Manager
    </button>
  </div>

  <div class="table-container">
    <div class="overflow-x-auto">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Nama Manager</th>
            <th>Email</th>
            <th>NIP</th>
            <th>Perusahaan</th>
            <th>Divisi</th>
            <th>Jumlah Employee</th>
            <th>Progress Employee</th>
            <th>Status</th>
            <th>Status Akun</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr><td colspan="10" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


<div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800">Detail Manager</h2>
      <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
    </div>
    <div id="detail-body" class="space-y-4 text-sm text-gray-700">
      <div class="text-sm text-gray-500 py-2">Memuat detail...</div>
    </div>
    <div class="flex justify-end mt-4">
      <button onclick="closeDetailModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Tutup</button>
    </div>
  </div>
</div>


<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
    <h2 id="modal-title" class="text-lg font-bold text-gray-800 mb-4">Tambah Manager</h2>
    <form id="modal-form" class="space-y-3">
      <input type="hidden" id="f-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Manager</label>
        <input id="f-name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input id="f-email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">NIP Manager</label>
        <input id="f-employee-number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Contoh: 23004567" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
        <input id="f-company" type="text" list="manager-company-options" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Ketik perusahaan" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
        <input id="f-division" type="text" list="manager-division-options" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Ketik divisi" required>
      </div>
      <datalist id="manager-company-options"></datalist>
      <datalist id="manager-division-options"></datalist>
      <p class="text-xs text-gray-500">Password default: Nama Depan + 2 digit akhir NIP. Contoh: Dicky67</p>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Simpan</button>
      </div>
    </form>
  </div>
</div>


<div id="modal-credential" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-xl text-center">
    <div class="text-4xl mb-3" style="color: #37AA05;"><i class="fas fa-check-circle"></i></div>
    <h2 class="text-lg font-bold text-gray-800 mb-1">Akun Manager Berhasil Dibuat</h2>
    <p class="text-sm text-gray-600 mb-3">Password sementara untuk akun manager:</p>
    <div id="credential-username" class="rounded-lg px-3 py-2 text-xs text-left mb-3" style="background-color: #f9fafb; color: #374151;"></div>
    <div id="credential-password" class="rounded-lg px-4 py-3 text-2xl font-mono font-bold tracking-widest mb-3" style="background-color: #f3f4f6; color: #144600;"></div>
    <p class="text-xs text-gray-500 mb-4">Silakan sampaikan credential ini ke manager dan minta ubah password setelah login pertama.</p>
    <button onclick="closeCredentialModal()" class="w-full text-white py-2 rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Tutup</button>
  </div>
</div>


<div id="modal-hires" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-3xl shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800">Daftar Employee</h2>
      <button onclick="closeHiresModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Employee</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Perusahaan</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Divisi</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Manager Fungsional</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Manager Operasional</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Career Stage</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Fase</th>
            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Progress</th>
          </tr>
        </thead>
        <tbody id="hires-body" class="divide-y divide-gray-200 text-gray-700">
          <tr><td colspan="8" class="text-center py-6 text-gray-400">Memuat...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let managers = [];
let companyOptions = [];
let divisionOptions = [];
let currentDetailManagerId = null;

function renderMasterOptions(listId, options) {
  const datalist = document.getElementById(listId);
  if (!datalist) return;
  datalist.innerHTML = '';
  options.forEach(item => {
    const option = document.createElement('option');
    option.value = item.name;
    datalist.appendChild(option);
  });
}

async function loadMasterOptions() {
  const [companiesRes, divisionsRes] = await Promise.all([
    apiGet('/api/master/companies'),
    apiGet('/api/master/divisions')
  ]);

  companyOptions = companiesRes.data || [];
  divisionOptions = divisionsRes.data || [];

  renderMasterOptions('manager-company-options', companyOptions);
  renderMasterOptions('manager-division-options', divisionOptions);
}

async function loadManagers() {
  const tbody = document.getElementById('table-body');
  tbody.innerHTML = '<tr><td colspan="10" class="text-center py-10 text-gray-400">Memuat...</td></tr>';

  try {
    const res = await apiGet('/api/managers');
    managers = res?.data || res || [];
    renderManagers();
  } catch (error) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-10 text-red-500">Gagal memuat data manager</td></tr>';
    showAlert('Gagal memuat data manager. Silakan refresh halaman.', 'error');
  }
}

function renderManagers() {
  const tbody = document.getElementById('table-body');
  if (!managers.length) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-10 text-gray-400">Belum ada manager</td></tr>';
    return;
  }

  tbody.innerHTML = managers.map(m => {
    const total = m.employee_count || 0;
    const progress = m.progress_employee || 0;
    const hasAccount = m.has_account === true;

    return `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-3 font-medium whitespace-nowrap">${m.name || '-'}</td>
      <td class="px-6 py-3 whitespace-nowrap">${m.email || '-'}</td>
      <td class="px-6 py-3 whitespace-nowrap">${m.employee_number || '-'}</td>
      <td class="px-6 py-3 whitespace-nowrap">${m.company || '-'}</td>
      <td class="px-6 py-3 whitespace-nowrap">${m.division || '-'}</td>
      <td class="px-6 py-3 whitespace-nowrap">
        <a href="/employees?manager_id=${m.id}" class="text-sm underline transition" style="color: #144600; cursor: pointer;" onmouseover="this.style.color='#37AA05'" onmouseout="this.style.color='#144600'" title="Lihat employee manager ini">
          ${total}
        </a>
      </td>
      <td class="px-6 py-3 font-medium whitespace-nowrap">${progress}%</td>
      <td class="px-6 py-3 whitespace-nowrap">
        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${m.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">
          ${m.status === 'active' ? 'Active' : 'Inactive'}
        </span>
      </td>
      <td class="px-6 py-3 whitespace-nowrap">
        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${hasAccount ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">
          ${hasAccount ? 'Sudah Ada' : 'Belum Ada'}
        </span>
      </td>
      <td class="px-6 py-3 text-right whitespace-nowrap">
        <button onclick="openDetailModal(${m.id})" class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:text-white hover:bg-gray-700 hover:border-gray-700 mr-1" title="Lihat detail manager">
          <i class="fas fa-eye"></i>
          Detail
        </button>
        <button onclick="openEditModal(${m.id})" class="transition" style="color: #144600; cursor: pointer;" onmouseover="this.style.color='#37AA05'" onmouseout="this.style.color='#144600'" title="Edit manager"><i class="fas fa-edit"></i></button>
        <button onclick="deleteManager(${m.id})" class="text-red-500 hover:text-red-700" title="Hapus manager"><i class="fas fa-trash"></i></button>
      </td>
    </tr>`;
  }).join('');
}

function openAddModal() {
  document.getElementById('modal-title').textContent = 'Tambah Manager';
  document.getElementById('f-id').value = '';
  document.getElementById('f-name').value = '';
  document.getElementById('f-email').value = '';
  document.getElementById('f-employee-number').value = '';
  loadMasterOptions();
  document.getElementById('f-company').value = '';
  document.getElementById('f-division').value = '';
  document.getElementById('modal').classList.remove('hidden');
}

function openEditModal(id) {
  const m = managers.find(x => x.id == id);
  if (!m) return;
  document.getElementById('modal-title').textContent = 'Edit Manager';
  document.getElementById('f-id').value = m.id;
  document.getElementById('f-name').value = m.name || '';
  document.getElementById('f-email').value = m.email || '';
  document.getElementById('f-employee-number').value = m.employee_number || '';
  loadMasterOptions().then(() => {
    document.getElementById('f-company').value = m.company || '';
    document.getElementById('f-division').value = m.division || '';
  });
  document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
  document.getElementById('modal').classList.add('hidden');
}

document.getElementById('modal-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('f-id').value;
  const payload = {
    name: document.getElementById('f-name').value,
    email: document.getElementById('f-email').value,
    employee_number: document.getElementById('f-employee-number').value,
    company: document.getElementById('f-company').value,
    division: document.getElementById('f-division').value,
  };
  const url = id ? `/api/managers/${id}` : '/api/managers';
  const method = id ? 'PUT' : 'POST';
  const res = await apiPost(url, payload, method);

  if (res && res.success === true) {
    if (!id && res?.data?.account_credential?.temporary_password) {
      const emailUsername = res?.data?.account_credential?.username_email || res?.data?.account_credential?.email || '-';
      const nipUsername = res?.data?.account_credential?.username_nip || '-';
      document.getElementById('credential-username').innerHTML = `
        <div><strong>Username Email:</strong> ${emailUsername}</div>
        <div><strong>Username NIP:</strong> ${nipUsername}</div>
      `;
      document.getElementById('credential-password').textContent = res.data.account_credential.temporary_password;
      document.getElementById('modal-credential').classList.remove('hidden');
    }
    showAlert(id ? 'Manager diperbarui' : 'Manager ditambahkan');
    closeModal();
    loadManagers();
  } else {
    showAlert(res?.message || res?.error || 'Gagal menyimpan', 'error');
  }
});

async function deleteManager(id) {
  const name = managers.find(x => String(x.id) === String(id))?.name || '';
  if (!(await showConfirm(`Hapus manager ${name}?`, 'Konfirmasi Hapus'))) return;
  const res = await apiPost(`/api/managers/${id}`, {}, 'DELETE');
  if (res && res.success === true) {
    showAlert(res.message || 'Data Manager berhasil dihapus');
    loadManagers();
  } else {
    showAlert(res?.message || res?.error || 'Gagal menghapus', 'error');
  }
}

async function showEmployees(id) {
  window.location.href = `/employees?manager_id=${encodeURIComponent(id)}`;
}

async function openDetailModal(id) {
  currentDetailManagerId = id;
  document.getElementById('modal-detail').classList.remove('hidden');
  const body = document.getElementById('detail-body');
  body.innerHTML = '<div class="text-sm text-gray-500 py-2">Memuat detail...</div>';

  const res = await apiGet(`/api/managers/${id}`);
  if (!(res && res.success === true && res.data)) {
    body.innerHTML = '<div class="text-sm text-red-600 py-2">Gagal memuat detail manager.</div>';
    return;
  }

  const m = res.data;
  const c = m.account_credential_preview || {};
  const temporaryPassword = c.temporary_password || '-';
  const generatedAt = c.temporary_password_generated_at || '-';

  body.innerHTML = `
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div><div class="text-xs text-gray-500">Nama Manager</div><div class="font-medium">${m.name || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Status</div><div class="font-medium">${m.status || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Email</div><div class="font-medium">${m.email || '-'}</div></div>
      <div><div class="text-xs text-gray-500">NIP</div><div class="font-medium">${m.employee_number || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Perusahaan</div><div class="font-medium">${m.company || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Divisi</div><div class="font-medium">${m.division || '-'}</div></div>
      <div><div class="text-xs text-gray-500">Total Employee</div><div class="font-medium">${m.employee_count || 0}</div></div>
      <div><div class="text-xs text-gray-500">Progress Employee</div><div class="font-medium">${m.progress_employee || 0}%</div></div>
    </div>

    <div class="border border-gray-200 rounded-lg p-3 mt-2">
      <div class="font-semibold text-gray-800 mb-2">Credential Akun Manager</div>
      <div class="grid grid-cols-1 gap-2 text-sm">
        <div><span class="text-gray-500">Username (Email):</span> <span class="font-medium">${c.username_email || m.email || '-'}</span></div>
        <div><span class="text-gray-500">Username (NIP):</span> <span class="font-medium">${c.username_nip || m.employee_number || '-'}</span></div>
        <div><span class="text-gray-500">Password Default:</span> <span class="font-medium">${temporaryPassword}</span></div>
        <div><span class="text-gray-500">Terakhir Generate Password:</span> <span class="font-medium">${generatedAt}</span></div>
      </div>
    </div>

    <div class="pt-1 flex flex-wrap gap-2">
      <button onclick="resetDetailCredential()" class="px-3 py-2 rounded-lg text-sm text-white transition" style="background-color: #1f2937; cursor: pointer;" onmouseover="this.style.backgroundColor='#111827'" onmouseout="this.style.backgroundColor='#1f2937'">
        Reset Password Manager
      </button>
      <button onclick="showEmployees(${m.id})" class="px-3 py-2 rounded-lg text-sm text-white transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
        Lihat Daftar Employee Terkait
      </button>
    </div>
  `;
}

async function resetDetailCredential() {
  if (!currentDetailManagerId) return;
  const ok = await showConfirm('Reset password manager ini? Password akan kembali ke format firstname + 2 digit terakhir NIP.', 'Reset Password Manager');
  if (!ok) return;

  const res = await apiPost(`/api/managers/${currentDetailManagerId}/reset-credential`, {}, 'POST');
  if (!(res && res.success === true)) {
    showAlert(res?.message || res?.error || 'Gagal reset password manager', 'error');
    return;
  }

  const preview = res.data || {};
  document.getElementById('credential-username').innerHTML = `
    <div><strong>Username Email:</strong> ${preview.username_email || '-'}</div>
    <div><strong>Username NIP:</strong> ${preview.username_nip || '-'}</div>
  `;
  document.getElementById('credential-password').textContent = preview.temporary_password || '-';
  document.getElementById('modal-credential').classList.remove('hidden');

  showAlert(res.message || 'Password manager berhasil di-reset');
  await openDetailModal(currentDetailManagerId);
}

function closeDetailModal() {
  currentDetailManagerId = null;
  document.getElementById('modal-detail').classList.add('hidden');
}

function closeHiresModal() {
  document.getElementById('modal-hires').classList.add('hidden');
}

function closeCredentialModal() {
  document.getElementById('modal-credential').classList.add('hidden');
  document.getElementById('credential-username').innerHTML = '';
  document.getElementById('credential-password').textContent = '';
}

loadManagers();
loadMasterOptions();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/managers/index.blade.php ENDPATH**/ ?>