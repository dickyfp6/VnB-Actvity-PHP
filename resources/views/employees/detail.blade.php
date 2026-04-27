@extends('layouts.app')
@section('title','Detail Employee')
@section('content')
<div class="space-y-6">
    <div class="card-glass rounded-xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6">
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Detail Employee</h1>
                <p class="text-gray-600 mb-4">Informasi profile dan status kemajuan VnB employee</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button id="detail-reset-credential-btn" onclick="resetDetailCredential()" class="btn-secondary flex items-center gap-2 hover:bg-gray-50">
                    <i class="fas fa-key"></i> Generate Ulang Password
                </button>
                <a href="{{ $backUrl }}" class="btn-secondary flex items-center gap-2 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card-glass rounded-xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm" id="profile-box">
            <div class="text-gray-500">Memuat profil...</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-glass rounded-xl p-6">
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
            <div id="phase-label" class="text-xs text-gray-600 mt-3">Fase Saat Ini: -</div>
        </div>

        <div class="card-glass rounded-xl p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Planning Status</h3>
            <div id="planning-status-box"></div>
        </div>
    </div>

    <div class="card-glass rounded-xl p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Ringkasan Detail</h3>
        <div id="detail-list" class="divide-y divide-gray-100"></div>
    </div>
</div>

@push('scripts')
<script>
const employeeId = @json($employeeId);

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

function getEmployeeStatusLabel(status) {
    const key = normalizeDisplayValue(status, '').toLowerCase();
    if (key === 'aktif' || key === 'active') return 'Aktif';
    if (key === 'inactive' || key === 'inaktif' || key === 'nonaktif') return 'Inactive';
    return status || '-';
}

async function loadDetail() {
    const detailList = document.getElementById('detail-list');
    detailList.innerHTML = '<div class="text-sm text-gray-500 py-2">Memuat detail...</div>';

    const detailRes = await apiGet(`/api/employees/${employeeId}`);
    if (!(detailRes && detailRes.success === true && detailRes.data)) {
        detailList.innerHTML = '<div class="text-sm text-red-600 py-2">Gagal memuat detail employee.</div>';
        return;
    }

    const row = detailRes.data;
    const credential = row.account_credential_preview || null;

    const managerFunctionalLabel = normalizeDisplayValue(row.manager_functional, null)
        || row.managerFunctional?.name
        || row.managerFunctional?.email
        || '-';
    const managerOperationalLabel = normalizeDisplayValue(row.manager_operational, null)
        || row.managerOperational?.name
        || row.managerOperational?.email
        || '-';
    const divisionLabel = row.division?.name || row.division || '-';
    const departmentLabel = row.department?.name || row.department || '-';
    const positionLabel = row.position?.name || row.position || '-';

        const progress = Number(row.progress || 0);
        document.getElementById('progress-label').textContent = `${progress}%`;
        document.getElementById('progress-bar').style.width = `${Math.min(100, Math.max(0, progress))}%`;
        document.getElementById('phase-label').textContent = `Fase Saat Ini: ${row.phase || '-'}`;

        document.getElementById('profile-box').innerHTML = `
            <div><div class="text-xs text-gray-500">NIP</div><div class="font-semibold text-gray-900">${escapeHtml(row.employee_number || '-')}</div></div>
            <div><div class="text-xs text-gray-500">Nama</div><div class="font-semibold text-gray-900">${escapeHtml(row.name || '-')}</div></div>
            <div><div class="text-xs text-gray-500">Email</div><div class="font-semibold text-gray-900">${escapeHtml(row.email || '-')}</div></div>
            <div><div class="text-xs text-gray-500">Perusahaan</div><div class="font-semibold text-gray-900">${escapeHtml(row.company || '-')}</div></div>
            <div><div class="text-xs text-gray-500">Divisi</div><div class="font-semibold text-gray-900">${escapeHtml(divisionLabel)}</div></div>
            <div><div class="text-xs text-gray-500">Career Stage</div><div class="font-semibold text-gray-900">${escapeHtml(row.career_stage || '-')}</div></div>
            <div><div class="text-xs text-gray-500">Manager Fungsional</div><div class="font-semibold text-gray-900">${escapeHtml(managerFunctionalLabel)}</div></div>
            <div><div class="text-xs text-gray-500">Manager Operasional</div><div class="font-semibold text-gray-900">${escapeHtml(managerOperationalLabel)}</div></div>
        `;

        const planningStatusBox = document.getElementById('planning-status-box');
        const planningText = row.planning_status || 'draft / belum diajukan';
        const participantText = row.is_vnb_participant
                ? 'Employee sudah di-assign sebagai VnB participant.'
                : 'Employee belum di-assign sebagai VnB participant. Progress/activity VnB mungkin belum berjalan.';

        planningStatusBox.innerHTML = `
            <div class="rounded-lg border px-4 py-3 text-sm mb-3 ${row.is_vnb_participant ? 'border-green-200 bg-green-50 text-green-800' : 'border-amber-300 bg-amber-50 text-amber-800'}">
                ${escapeHtml(participantText)}
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                Status Planning: <span class="font-semibold">${escapeHtml(planningText)}</span>
            </div>
        `;

        const fields = [
        ['NIP', row.employee_number], ['Nama', row.name_display || row.name], ['Tanggal Masuk', row.date_joined],
        ['Username Login', credential?.username || row.employee_number],
        ['Password Sementara', credential?.temporary_password || '-'],
        ['Waktu Generate Password', credential?.temporary_password_generated_at || '-'],
        ['Tanggal Induction', row.induction_date], ['Email', row.email], ['Whatsapp', row.whatsapp], ['Periode Awal', row.vnb_period_start],
        ['Periode Akhir', row.vnb_period_end], ['Career Stage', row.career_stage], ['Fase', row.phase], ['Progress', `${row.progress ?? 0}%`],
        ['Manager Fungsional', managerFunctionalLabel], ['Manager Operasional', managerOperationalLabel], ['Perusahaan', row.company],
        ['Divisi', divisionLabel], ['Departemen', departmentLabel], ['Jabatan', positionLabel], ['Penempatan', row.placement],
        ['Golongan', row.level], ['Status Pegawai', row.employee_status], ['Status Employee', getEmployeeStatusLabel(row.status)]
    ];

    detailList.innerHTML = fields.map(([k, v]) => `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm py-2">
            <div class="text-gray-500">${escapeHtml(k)}</div>
            <div class="md:col-span-2 font-medium text-gray-800">${escapeHtml(v || '-')}</div>
        </div>
    `).join('');
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

loadDetail();
</script>
@endpush
@endsection