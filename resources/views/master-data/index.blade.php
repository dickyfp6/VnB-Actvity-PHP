@extends('layouts.app')
@section('title','Master Data')
@section('content')
<div class="px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Master Data</h1>
    <div class="flex items-center gap-2">
      <button onclick="openBulkModal()" class="px-4 py-2 rounded text-sm transition border border-gray-300 text-gray-700 hover:bg-gray-50" style="cursor: pointer;">
        <i class="fas fa-list mr-1"></i> Tambah Massal
      </button>
      <button onclick="openAddModal()" class="text-white px-4 py-2 rounded text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
        <i class="fas fa-plus mr-1"></i> Tambah
      </button>
    </div>
  </div>

  {{-- Category Tabs --}}
  <div class="flex space-x-1 mb-6 bg-gray-100 p-1 rounded-lg w-fit">
    @php
    $cats = [
      'companies'   => 'Perusahaan',
      'divisions'   => 'Divisi',
      'departments' => 'Departemen',
      'positions'   => 'Jabatan',
      'placements'  => 'Penempatan',
      'levels'      => 'Golongan',
      'employee_statuses' => 'Status Pegawai',
    ];
    @endphp
    @foreach($cats as $key => $label)
    <button onclick="switchTab('{{ $key }}')" id="tab-{{ $key }}"
      class="px-4 py-2 rounded-md text-sm font-medium transition-colors tab-btn" style="color: #37AA05;" onmouseover="if(this.id !== 'tab-' + currentTab) this.style.backgroundColor='#f0f9ff'" onmouseout="if(this.id !== 'tab-' + currentTab) this.style.backgroundColor=''">
      {{ $label }}
    </button>
    @endforeach
  </div>

  {{-- Table --}}
  <div class="table-container">
    <table class="table-modern">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tr><td colspan="3" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Add/Edit --}}
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
    <h2 id="modal-title" class="text-lg font-bold text-gray-800 mb-4">Tambah Data</h2>
    <form id="modal-form" class="space-y-3">
      <input type="hidden" id="edit-id">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input id="f-name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'" required>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Bulk Add --}}
<div id="bulk-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl">
    <h2 class="text-lg font-bold text-gray-800 mb-2">Tambah Massal <span id="bulk-title"></span></h2>
    <p class="text-sm text-gray-500 mb-4">Paste data dari Excel (1 baris = 1 data). Tekan Enter untuk baris berikutnya. Jika copy banyak kolom, hanya kolom pertama yang dipakai.</p>
    <form id="bulk-form" class="space-y-3">
      <textarea id="bulk-input" rows="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Contoh:
PT Wismilak Inti Makmur
PT Gelora Djaja" required></textarea>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeBulkModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Simpan Massal</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
let currentTab = 'companies';
let tableData = [];
const catLabels = {
  companies:'Perusahaan', divisions:'Divisi', departments:'Departemen',
  positions:'Jabatan', placements:'Penempatan', levels:'Golongan', employee_statuses:'Status Pegawai'
};

function switchTab(cat) {
  currentTab = cat;
  document.querySelectorAll('.tab-btn').forEach(b => {
    const active = b.id === 'tab-' + cat;
    b.style.backgroundColor = active ? '#144600' : 'white';
    b.style.color = active ? 'white' : '#37AA05';
    b.style.boxShadow = active ? '0 1px 2px rgba(0, 0, 0, 0.1)' : 'none';
  });
  loadData();
}

async function loadData() {
  document.getElementById('table-body').innerHTML = '<tr><td colspan="3" class="text-center py-10 text-gray-400">Memuat...</td></tr>';
  const res = await apiGet('/api/master/' + currentTab);
  tableData = res.data || res || [];
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!tableData.length) {
    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-10 text-gray-400">Belum ada data</td></tr>';
    return;
  }
  tbody.innerHTML = tableData.map(row => `
    <tr style="transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#144600'; this.style.color='white'" onmouseout="this.style.backgroundColor=''; this.style.color=''">
      <td class="px-6 py-3">${row.code ?? '-'}</td>
      <td class="px-6 py-3 font-medium">${row.name}</td>
      <td class="px-6 py-3 text-right space-x-2">
        <button onclick="openEditModal(${row.id})" style="cursor: pointer;" onmouseover="this.style.color='#37AA05'" onmouseout="this.style.color='#144600'" class="text-sm transition" style="color: #144600;"><i class="fas fa-edit"></i></button>
        <button onclick="deleteRow(${row.id}, '${row.name}')" class="text-red-500 hover:text-red-700 text-sm" style="cursor: pointer;"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
}

function openAddModal() {
  document.getElementById('modal-title').textContent = 'Tambah ' + catLabels[currentTab];
  document.getElementById('edit-id').value = '';
  document.getElementById('f-name').value = '';
  document.getElementById('modal').classList.remove('hidden');
}

function openEditModal(id) {
  const row = tableData.find(r => r.id == id);
  if (!row) return;
  document.getElementById('modal-title').textContent = 'Edit ' + catLabels[currentTab];
  document.getElementById('edit-id').value = row.id;
  document.getElementById('f-name').value = row.name;
  document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
  document.getElementById('modal').classList.add('hidden');
}

function openBulkModal() {
  document.getElementById('bulk-title').textContent = catLabels[currentTab];
  document.getElementById('bulk-input').value = '';
  document.getElementById('bulk-modal').classList.remove('hidden');
}

function closeBulkModal() {
  document.getElementById('bulk-modal').classList.add('hidden');
}

document.getElementById('modal-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('edit-id').value;
  const payload = {
    name: document.getElementById('f-name').value,
  };
  const url = id ? `/api/master/${currentTab}/${id}` : `/api/master/${currentTab}`;
  const method = id ? 'PUT' : 'POST';
  const res = await apiPost(url, payload, method);
  if (res && res.success === true) {
    showAlert(id ? 'Data diperbarui' : 'Data ditambahkan');
    closeModal();
    loadData();
  } else {
    showAlert(res?.message || res?.error || 'Gagal menyimpan', 'error');
  }
});

async function deleteRow(id, name) {
  if (!confirm(`Hapus "${name}"?`)) return;
  const res = await apiPost(`/api/master/${currentTab}/${id}`, {}, 'DELETE');
  if (res && res.success === true) {
    showAlert(res.message || 'Data dihapus');
    loadData();
  }
  else showAlert(res?.message || res?.error || 'Gagal menghapus', 'error');
}

document.getElementById('bulk-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const raw = document.getElementById('bulk-input').value || '';
  const names = raw
    .split(/\r?\n/)
    .map(line => line.split('\t')[0])
    .map(name => name.trim())
    .filter(Boolean);

  if (!names.length) {
    showAlert('Tidak ada data valid untuk disimpan', 'error');
    return;
  }

  const res = await apiPost(`/api/master/${currentTab}/bulk`, { names }, 'POST');
  if (res && res.success === true) {
    showAlert(res.message || 'Data massal berhasil ditambahkan');
    closeBulkModal();
    loadData();
  } else {
    showAlert(res?.message || res?.error || 'Gagal menyimpan data massal', 'error');
  }
});

// Init
switchTab('companies');
</script>
@endpush
@endsection
