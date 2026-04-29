@extends('layouts.app')
@section('title','Detail Employee')
@section('page_title','Detail Employee')
@section('page_subtitle','Lihat detail profil dan status kemajuan VnB untuk satu employee.')
@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="card-glass rounded-xl p-6 md:p-8 flex flex-col md:flex-row md:justify-between md:items-center gap-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900" id="header-employee-name">Memuat...</h2>
            <p class="text-sm text-gray-500 mt-1" id="header-employee-role">Memuat...</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ $backUrl }}" class="btn-secondary flex items-center gap-2 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="card-glass rounded-xl px-2 pt-2">
        <nav class="flex space-x-4 border-b border-gray-200 overflow-x-auto" aria-label="Tabs">
            <button onclick="switchTab('profil')" id="tab-btn-profil" class="whitespace-nowrap px-4 py-3 border-b-2 font-medium text-sm transition-colors duration-200 border-green-500 text-green-600 focus:outline-none">
                <i class="fas fa-user-circle mr-2"></i>Profil
            </button>
            <button onclick="switchTab('star')" id="tab-btn-star" class="whitespace-nowrap px-4 py-3 border-b-2 font-medium text-sm transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none">
                <i class="fas fa-star mr-2"></i>STAR
            </button>
            <button onclick="switchTab('vnb')" id="tab-btn-vnb" class="whitespace-nowrap px-4 py-3 border-b-2 font-medium text-sm transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none">
                <i class="fas fa-tasks mr-2"></i>VnB Activity
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content-container">
        
        <!-- TAB 1: Profil -->
        <div id="tab-profil" class="tab-content block space-y-6 animate-fade-in">
            <div class="card-glass rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Employee</h3>
                    <button id="detail-reset-credential-btn" onclick="resetDetailCredential()" class="btn-secondary flex items-center gap-2 hover:bg-green-50 text-xs py-2 px-3">
                        <i class="fas fa-key"></i> Generate Password
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm" id="profile-box">
                    <div class="text-gray-500">Memuat profil...</div>
                </div>
            </div>
        </div>

        <!-- TAB 2: STAR -->
        <div id="tab-star" class="tab-content hidden animate-fade-in">
            <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-star text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Data STAR</h3>
                <p class="text-gray-500 max-w-md">Data pencapaian STAR belum tersedia atau belum terintegrasi untuk employee ini pada sistem saat ini.</p>
            </div>
        </div>

        <!-- TAB 3: VnB Activity -->
        <div id="tab-vnb" class="tab-content hidden animate-fade-in space-y-6">
            <div id="vnb-not-assigned" class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center hidden">
                 <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-4 border border-amber-100">
                    <i class="fas fa-exclamation-triangle text-3xl text-amber-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Di-assign sebagai Participant VnB</h3>
                <p class="text-gray-500 max-w-md">Employee ini belum di-assign sebagai participant VnB, sehingga progress dan aktivitas VnB belum berjalan.</p>
            </div>

            <div id="vnb-content" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                <div class="card-glass rounded-xl p-6 flex flex-col justify-center">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Progress VnB</h3>
                            <p class="text-xs text-gray-500 mt-1">Kemajuan aktivitas VnB employee</p>
                        </div>
                        <span id="progress-label" class="text-2xl font-bold text-green-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200/50 h-3 rounded-full overflow-hidden">
                        <div id="progress-bar" class="h-3 bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-500" style="width:0%;"></div>
                    </div>
                    <div id="phase-label" class="text-xs text-gray-600 mt-4 font-medium px-3 py-2 bg-gray-50 rounded-lg inline-block self-start border border-gray-100">Fase Saat Ini: -</div>
                </div>

                <div class="card-glass rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Planning Status</h3>
                    <div id="planning-status-box"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const employeeId = @json($employeeId);

function switchTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block');
    });
    // Reset all buttons
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.classList.remove('border-green-500', 'text-green-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });

    // Show target tab
    document.getElementById(`tab-${tabId}`).classList.remove('hidden');
    document.getElementById(`tab-${tabId}`).classList.add('block');
    
    // Highlight target button
    const activeBtn = document.getElementById(`tab-btn-${tabId}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-green-500', 'text-green-600');
}

function escapeHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function normalizeDisplayValue(value, fallback = '-') {
    if (value === null || value === undefined) return fallback;
    const text = String(value).trim();
    return text !== '' ? text : fallback;
}

function resolveLabel(value) {
    if (value && typeof value === 'object') {
        return value.name || value.email || '-';
    }
    return normalizeDisplayValue(value, '-');
}

function getEmployeeStatusLabel(status) {
    const key = normalizeDisplayValue(status, '').toLowerCase();
    if (key === 'aktif' || key === 'active') return 'Aktif';
    if (key === 'inactive' || key === 'inaktif' || key === 'nonaktif') return 'Inactive';
    return status || '-';
}

async function loadDetail() {
    const profileBox = document.getElementById('profile-box');
    profileBox.innerHTML = '<div class="text-sm text-gray-500 py-2">Memuat detail...</div>';

    const detailRes = await apiGet(`/api/employees/${employeeId}`);
    if (!(detailRes && detailRes.success === true && detailRes.data)) {
        profileBox.innerHTML = '<div class="text-sm text-red-600 py-2">Gagal memuat detail employee.</div>';
        document.getElementById('header-employee-name').textContent = 'Employee Tidak Ditemukan';
        document.getElementById('header-employee-role').textContent = 'Terjadi kesalahan saat memuat data.';
        return;
    }

    const row = detailRes.data;
    const credential = row.account_credential_preview || null;

    // Update Header
    document.getElementById('header-employee-name').textContent = escapeHtml(row.name || '-');
    document.getElementById('header-employee-role').textContent = escapeHtml((row.position?.name || row.position || '-') + ' • ' + (row.level || '-'));

    const managerFunctionalLabel = resolveLabel(row.manager_functional_label ?? row.manager_functional ?? row.managerFunctional);
    const managerOperationalLabel = resolveLabel(row.manager_operational_label ?? row.manager_operational ?? row.managerOperational);
    const divisionLabel = row.division?.name || row.division || '-';
    const departmentLabel = row.department?.name || row.department || '-';
    const positionLabel = row.position?.name || row.position || '-';

    // VnB Activity Tab logic
    if (row.is_vnb_participant) {
        document.getElementById('vnb-not-assigned').classList.add('hidden');
        document.getElementById('vnb-content').classList.remove('hidden');

        const progress = Number(row.progress || 0);
        document.getElementById('progress-label').textContent = `${progress}%`;
        document.getElementById('progress-bar').style.width = `${Math.min(100, Math.max(0, progress))}%`;
        document.getElementById('phase-label').textContent = `Fase Saat Ini: ${row.phase || '-'}`;

        const planningStatusBox = document.getElementById('planning-status-box');
        const planningText = row.planning_status || 'draft / belum diajukan';
        
        planningStatusBox.innerHTML = `
            <div class="rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm mb-3 font-medium">
                <i class="fas fa-check-circle mr-2"></i>Employee aktif sebagai VnB participant.
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                Status Planning Saat Ini: <span class="font-semibold text-gray-900 ml-1 px-2 py-1 bg-white border border-gray-200 rounded text-xs uppercase">${escapeHtml(planningText)}</span>
            </div>
        `;
    } else {
        document.getElementById('vnb-content').classList.add('hidden');
        document.getElementById('vnb-not-assigned').classList.remove('hidden');
    }

    // Profil Box Update
    document.getElementById('profile-box').innerHTML = `
        <div><div class="text-xs text-gray-500 mb-1">NIP</div><div class="font-semibold text-gray-900">${escapeHtml(row.employee_number || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Nama Lengkap</div><div class="font-semibold text-gray-900">${escapeHtml(row.name || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Tanggal Masuk</div><div class="font-semibold text-gray-900">${escapeHtml(row.date_joined || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Tanggal Induction</div><div class="font-semibold text-gray-900">${escapeHtml(row.induction_date || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Email</div><div class="font-semibold text-gray-900">${escapeHtml(row.email || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Whatsapp</div><div class="font-semibold text-gray-900">${escapeHtml(row.whatsapp || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Perusahaan</div><div class="font-semibold text-gray-900">${escapeHtml(row.company || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Divisi</div><div class="font-semibold text-gray-900">${escapeHtml(divisionLabel)}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Departemen</div><div class="font-semibold text-gray-900">${escapeHtml(departmentLabel)}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Jabatan</div><div class="font-semibold text-gray-900">${escapeHtml(positionLabel)}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Penempatan</div><div class="font-semibold text-gray-900">${escapeHtml(row.placement || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Golongan</div><div class="font-semibold text-gray-900">${escapeHtml(row.level || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Status Pegawai</div><div class="font-semibold text-gray-900">${escapeHtml(row.employee_status || '-')}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Status Employee</div><div class="font-semibold text-gray-900">${escapeHtml(getEmployeeStatusLabel(row.status))}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Manager Fungsional</div><div class="font-semibold text-gray-900">${escapeHtml(managerFunctionalLabel)}</div></div>
        <div><div class="text-xs text-gray-500 mb-1">Manager Operasional</div><div class="font-semibold text-gray-900">${escapeHtml(managerOperationalLabel)}</div></div>
    `;
}

async function resetDetailCredential() {
    const confirmed = await showConfirm('Generate ulang password sementara untuk Employee ini?', 'Reset Password');
    if (!confirmed) return;

    const res = await apiPost(`/api/employees/${employeeId}/reset-credential`, {}, 'POST');
    if (res && res.success === true) {
        const data = res.data || {};
        showAlert(res.message || 'Password sementara berhasil di-generate ulang');

        if (data.username && data.temporary_password) {
            openCredentialModal([
                {
                    name: data.name || '-',
                    username: data.username,
                    password: data.temporary_password,
                    email: data.email || '-',
                    delivery: data.delivery || { email_sent: false, popup_available: true },
                }
            ]);
        }

        await loadDetail();
    } else {
        showAlert(res?.message || res?.error || 'Gagal generate ulang password', 'error');
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    loadDetail();
});
</script>
@endpush
@endsection