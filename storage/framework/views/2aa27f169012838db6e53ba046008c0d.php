<?php $__env->startSection('title','Detail Employee'); ?>
<?php $__env->startSection('page_title','Detail Employee'); ?>
<?php $__env->startSection('page_subtitle','Lihat detail profil dan status kemajuan VnB untuk satu employee.'); ?>
<?php $__env->startPush('styles'); ?>
<style>
    .tab-button-active {
        background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        color: #166534;
        border: 1px solid rgba(34, 197, 94, 0.18);
        box-shadow: 0 4px 12px rgba(22, 101, 52, 0.07);
    }

    .tab-button-inactive {
        color: #6b7280;
        background: transparent;
        border: 1px solid transparent;
    }

    .tab-button-inactive:hover {
        color: #111827;
        background: rgba(249, 250, 251, 0.9);
        border-color: rgba(229, 231, 235, 1);
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header Card -->
    <div class="card-glass rounded-xl p-6 md:p-8 flex flex-col md:flex-row md:justify-between md:items-center gap-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900" id="header-employee-name">Memuat...</h2>
            <p class="text-sm text-gray-500 mt-1" id="header-employee-role">Memuat...</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="<?php echo e($backUrl); ?>" class="btn-secondary flex items-center gap-2 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Tabs + Content -->
    <div class="card-glass rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-white/80 px-4 pt-2">
            <nav class="grid grid-cols-1 gap-2 sm:grid-cols-3 rounded-2xl bg-gray-100/90 p-1.5" aria-label="Tabs">
                <button onclick="switchTab('profil')" id="tab-btn-profil" class="tab-button tab-button-active w-full whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 focus:outline-none">
                    <i class="fas fa-user-circle mr-2"></i>Profil
                </button>
                <button onclick="switchTab('star')" id="tab-btn-star" class="tab-button tab-button-inactive w-full whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 focus:outline-none">
                    <i class="fas fa-star mr-2"></i>STAR
                </button>
                <button onclick="switchTab('vnb')" id="tab-btn-vnb" class="tab-button tab-button-inactive w-full whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 focus:outline-none">
                    <i class="fas fa-tasks mr-2"></i>VnB Activity
                </button>
            </nav>
        </div>

        <div class="tab-content-container px-4 md:px-6 py-6 space-y-6 bg-white/70">
        
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
                <!-- Manager Approval UI (hidden unless current_manager_role present) -->
                <div id="manager-approval-root" class="col-span-1 md:col-span-2 space-y-6 hidden">
                    <!-- PLANNING APPROVAL -->
                    <div id="planning-table-box" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-500/10 to-blue-600/10 border-b border-gray-200/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">PLANNING APPROVAL</h2>
                                    <p class="text-sm text-gray-600 mt-1">Persetujuan rencana pengembangan karyawan</p>
                                </div>
                                <div class="flex gap-2">
                                    <button id="approve-all-btn" onclick="submitApproveAll()" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                                        <i class="fas fa-check"></i> Setujui Semua
                                    </button>
                                    <button id="batch-submit-btn-header" onclick="submitBatchReview()" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition flex items-center gap-2" style="background-color: #9ca3af; opacity: 0.5; cursor: not-allowed;" disabled>
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-modern w-full">
                                <thead>
                                    <tr>
                                        <th class="w-1/6">Behaviour</th>
                                        <th class="w-1/4">Integrasi Pengukuran</th>
                                        <th class="w-1/3">Rencana Aktivitas</th>
                                        <th class="w-1/6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="planning-body">
                                    <tr><td colspan="4" class="text-center py-8 text-gray-400">Memuat planning...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="batch-action-bar" class="hidden bg-gray-50 border-t border-gray-200 p-4 flex justify-between items-center">
                            <div>
                                <p class="text-base font-bold text-gray-900">Review Menunggu Konfirmasi</p>
                                <p class="text-xs text-gray-600 mt-1">Pilihan Anda sudah dicatat sementara. Klik kirim untuk menyimpan semua.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fase 1 -->
                    <div id="phase-1-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-500/10 to-blue-600/10 border-b border-gray-200/50">
                            <h2 class="text-lg font-semibold text-gray-900">FASE 1</h2>
                            <p class="text-sm text-gray-600 mt-1">Bulan ke-1 hingga ke-3 | Orientasi & Onboarding</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-modern w-full">
                                <thead>
                                    <tr>
                                        <th class="w-1/6">Behaviour</th>
                                        <th class="w-1/4">Integrasi Pengukuran</th>
                                        <th class="w-1/3">Rencana Aktivitas</th>
                                        <th class="w-1/6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="phase-1-body">
                                    <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Fase 2 -->
                    <div id="phase-2-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-amber-500/10 to-amber-600/10 border-b border-gray-200/50">
                            <h2 class="text-lg font-semibold text-gray-900">FASE 2</h2>
                            <p class="text-sm text-gray-600 mt-1">Bulan ke-4 hingga ke-6 | Pengembangan & Adaptasi</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-modern w-full">
                                <thead>
                                    <tr>
                                        <th class="w-1/6">Behaviour</th>
                                        <th class="w-1/4">Integrasi Pengukuran</th>
                                        <th class="w-1/3">Rencana Aktivitas</th>
                                        <th class="w-1/6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="phase-2-body">
                                    <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Fase 3 -->
                    <div id="phase-3-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-500/10 to-green-600/10 border-b border-gray-200/50">
                            <h2 class="text-lg font-semibold text-gray-900">FASE 3</h2>
                            <p class="text-sm text-gray-600 mt-1">Bulan ke-7 hingga ke-12 | Konsolidasi & Kemandirian</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-modern w-full">
                                <thead>
                                    <tr>
                                        <th class="w-1/6">Behaviour</th>
                                        <th class="w-1/4">Integrasi Pengukuran</th>
                                        <th class="w-1/3">Rencana Aktivitas</th>
                                        <th class="w-1/6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="phase-3-body">
                                    <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Approve All Modal -->
                    <div id="approve-all-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                                    <i class="fas fa-check text-2xl text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Setujui Semua Rencana?</h3>
                                    <p class="text-sm text-gray-500 mt-1">Anda yakin akan menyetujui semua rencana aktivitas?</p>
                                </div>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                    Setelah disetujui, tidak bisa diubah kecuali ada revisi baru.
                                </p>
                            </div>
                            <div class="flex gap-3 justify-end">
                                <button onclick="closeApproveAllModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium">Batal</button>
                                <button onclick="confirmApproveAll()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 font-medium flex items-center gap-2">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Revision Modal -->
                    <div id="revision-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Catatan Revisi</h3>
                            <div class="mb-4 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div id="modal-behaviour-val" class="font-bold text-base mb-3 text-gray-900"></div>
                                <div class="font-semibold text-xs text-gray-500 uppercase mb-2">Integrasi Pengukuran</div>
                                <div id="modal-integrasi-val" class="ml-2 mb-3 whitespace-pre-wrap">-</div>
                                <div class="font-semibold text-xs text-gray-500 uppercase mb-2">Rencana Aktivitas</div>
                                <div id="modal-rencana-val" class="ml-2 whitespace-pre-wrap">-</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi</label>
                                <textarea id="modal-revision-notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Masukkan catatan revisi di sini..."></textarea>
                            </div>
                            <div class="flex justify-between items-center mt-6">
                                <button id="modal-cancel-revision-btn" onclick="cancelRevisionFromModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium hidden">Batalkan</button>
                                <div class="flex gap-2 flex-1 justify-end">
                                    <button onclick="closeRevisionModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium">Batal</button>
                                    <button onclick="submitRevisionModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 font-medium">Kirim Revisi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const employeeId = <?php echo json_encode($employeeId, 15, 512) ?>;

function switchTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block');
    });
    // Reset all buttons
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.classList.remove('tab-button-active');
        btn.classList.add('tab-button-inactive');
    });

    // Show target tab
    document.getElementById(`tab-${tabId}`).classList.remove('hidden');
    document.getElementById(`tab-${tabId}`).classList.add('block');
    
    // Highlight target button
    const activeBtn = document.getElementById(`tab-btn-${tabId}`);
    activeBtn.classList.remove('tab-button-inactive');
    activeBtn.classList.add('tab-button-active');
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

// --- Manager approval state ---
let detailData = null;
let pendingDecisions = {};
let totalPlanningSubRows = 0;
let currentRevisionItemId = null;
let currentRevisionSubIdx = null;

function savePendingDecisionsLocal() {
    if (detailData && detailData.plan && detailData.plan.id) {
        localStorage.setItem(`vnb_batch_decisions_${detailData.plan.id}`, JSON.stringify(pendingDecisions));
    }
}

function clearPendingDecisionsLocal() {
    if (detailData && detailData.plan && detailData.plan.id) {
        localStorage.removeItem(`vnb_batch_decisions_${detailData.plan.id}`);
    }
}

function toggleBatchButtonBar() {
    const bar = document.getElementById('batch-action-bar');
    const submitBtn = document.getElementById('batch-submit-btn-header');
    if (!bar || !submitBtn) return;
    const hasPendingDecisions = Object.keys(pendingDecisions).length > 0;
    if (hasPendingDecisions) {
        bar.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.style.backgroundColor = '#1e3a8a';
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    } else {
        bar.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.style.backgroundColor = '#9ca3af';
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
    }
}

function cancelPendingDecision(itemId, subIdx) {
    const rowKey = itemId + '_' + subIdx;
    delete pendingDecisions[rowKey];
    savePendingDecisionsLocal();
    if (detailData && detailData.items) {
        renderPlanningTable(detailData.items);
    }
    toggleBatchButtonBar();
}

function toLabelStatus(status) {
    const map = { waiting_approval: 'Waiting Approval', revision_required: 'Perlu Revisi', completed: 'Completed', draft: 'Draft' };
    return map[status] || status || '-';
}

function resolvePhaseNumberFromItem(item) {
    const metrics = Array.isArray(item.behavior_metrics) ? item.behavior_metrics : [];
    const metricPhase = metrics.find(v => typeof v === 'string' && /^phase_[1-3]$/i.test(v));
    if (metricPhase) return Number((metricPhase.match(/phase_(\d)/i) || [])[1] || 1);
    const title = String(item.activity_title || '');
    const titleMatch = title.match(/phase\s*(\d)/i);
    if (titleMatch) return Number(titleMatch[1]);
    return 1;
}

function normalizeCurrentStage(detail) {
    const phaseLabel = String(detail.phase || '').toLowerCase();
    if (phaseLabel === 'planning') return 'planning';
    const num = Number(detail.plan?.phase_number || 1);
    if (num >= 1 && num <= 3) return `phase_${num}`;
    return 'planning';
}

function updateSubmitButtonState(detail) {
    const headerBtn = document.getElementById('batch-submit-btn-header');
    if (!headerBtn) return;
    const items = detail.items || [];
    const hasWaitingItems = items.some(item => item.submission_status === 'waiting_approval');
    if (hasWaitingItems) {
        headerBtn.disabled = true;
        headerBtn.classList.add('opacity-50', 'cursor-not-allowed');
        headerBtn.style.backgroundColor = '#9CA3AF';
    } else {
        headerBtn.disabled = false;
        headerBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        headerBtn.style.backgroundColor = '#144600';
    }
}

function renderPhaseOverview(detail) {
    const currentStage = normalizeCurrentStage(detail);
    const planningWaiting = !!detail.approval_requests?.planning_waiting;
    const items = detail.items || [];
    const waitingByPhase = { phase_1: 0, phase_2: 0, phase_3: 0 };
    items.forEach(item => {
        if (item.submission_status !== 'waiting_approval') return;
        const p = resolvePhaseNumberFromItem(item);
        const key = `phase_${p}`;
        if (Object.prototype.hasOwnProperty.call(waitingByPhase, key)) waitingByPhase[key] += 1;
    });

    const setBadge = (id, count) => {
        const el = document.getElementById(id);
        if (!el) return;
        if (count > 0) { el.textContent = count > 99 ? '99+' : String(count); el.classList.remove('hidden'); } else { el.classList.add('hidden'); }
    };

    setBadge('badge-planning', planningWaiting ? 1 : 0);
    setBadge('badge-phase-1', waitingByPhase.phase_1 || 0);
    setBadge('badge-phase-2', waitingByPhase.phase_2 || 0);
    setBadge('badge-phase-3', waitingByPhase.phase_3 || 0);

    updateSubmitButtonState(detail);

    const statusList = document.getElementById('phase-status-list');
    if (!statusList) return;
    const currentNum = currentStage === 'planning' ? 0 : Number((currentStage.match(/phase_(\d)/) || [])[1] || 1);
    const activePhase = currentStage;
    if (activePhase === 'planning') {
        const planningStatusText = planningWaiting ? 'Planning menunggu approval manager.' : (currentStage === 'planning' ? 'Planning masih draft / belum diajukan.' : 'Planning sudah disetujui manager.');
        statusList.innerHTML = `<div class="border border-amber-200 bg-amber-50 rounded-lg px-3 py-2">${planningStatusText}</div>`;
        return;
    }
    const tabNum = Number((activePhase.match(/phase_(\d)/) || [])[1] || 1);
    let msg = '';
    if (currentStage === 'planning') msg = `Planning belum disetujui. Fase ${tabNum} belum dimulai.`;
    else if (tabNum < currentNum) msg = `Fase ${tabNum} sudah selesai.`;
    else if (tabNum === currentNum) msg = `Fase ${tabNum} sedang berjalan.`;
    else msg = `Fase ${tabNum} belum dimulai.`;
    statusList.innerHTML = `<div class="border border-gray-200 bg-white rounded-lg px-3 py-2">${msg}</div>`;
}

function renderPhaseContent(detail) {
    ['planning-table-box', 'phase-1-section', 'phase-2-section', 'phase-3-section'].forEach(id => { const el = document.getElementById(id); if (el) el.classList.add('hidden'); });
    const currentStage = normalizeCurrentStage(detail);
    const planningWaiting = !!detail.approval_requests?.planning_waiting;
    const items = detail.items || [];
    if (currentStage === 'planning' && planningWaiting) {
        document.getElementById('planning-table-box').classList.remove('hidden');
        renderPlanningTable(items);
    }
    ['phase_1', 'phase_2', 'phase_3'].forEach((phaseKey, idx) => {
        const phaseNum = idx + 1;
        const phaseItems = items.filter(item => resolvePhaseNumberFromItem(item) === phaseNum);
        const sectionId = `phase-${phaseNum}-section`;
        const bodyId = `phase-${phaseNum}-body`;
        if (phaseItems.length > 0) {
            document.getElementById(sectionId).classList.remove('hidden');
            renderPhaseActivityTable(bodyId, phaseItems);
        }
    });
}

function renderPhaseActivityTable(bodyId, items) {
    const tbody = document.getElementById(bodyId);
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>'; return; }
    let html = '';
    items.forEach(item => {
        const behaviorMatch = (item.activity_title || '').match(/^([^-]+)/);
        const behavior = behaviorMatch ? behaviorMatch[1].trim() : (item.activity_title || '-');
        const integrations = (item.description || '-').split('|').map(s => s.trim()).filter(s => s);
        if (integrations.length === 0) integrations.push('-');
        const rencanaList = (item.deliverables || '').split('\n---\n').map(s => s.trim());
        integrations.forEach((integration, idx) => {
            const waiting = item.submission_status === 'waiting_approval';
            let actionHtml = '';
            if (waiting) {
                actionHtml = `
                    <button onclick="approveActivityRow(${item.id}, ${idx})" class="inline-flex items-center justify-center w-8 h-8 rounded bg-green-600 text-white hover:bg-green-700 transition mr-2 cursor-pointer shadow-sm" title="Approve">✓</button>
                    <button onclick="reviseActivityRow(${item.id}, ${idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(integration).replace(/'/g, "%27")}', '${encodeURIComponent(rencanaList[idx] || '-').replace(/'/g, "%27")}')" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 text-white hover:bg-red-700 transition cursor-pointer shadow-sm" title="Revise">✕</button>
                `;
            } else {
                const statusLabel = toLabelStatus(item.submission_status);
                actionHtml = `<span class="text-xs text-gray-500">${statusLabel}</span>`;
            }
            html += `
                <tr class="hover:bg-gray-50 transition-colors">
                    ${idx === 0 ? `<td class="px-4 py-3 font-medium">${behavior}</td>` : '<td class="px-4 py-3"></td>'}
                    <td class="px-4 py-3"><span class="text-xs text-gray-700 whitespace-pre-wrap">${integration}</span></td>
                    <td class="px-4 py-3 whitespace-pre-wrap">${rencanaList[idx] || '-'}</td>
                    <td class="px-4 py-3 text-right">${actionHtml}</td>
                </tr>
            `;
        });
    });
    tbody.innerHTML = html;
}

function renderPlanningTable(items) {
    const tbody = document.getElementById('planning-body');
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada data planning</td></tr>'; return; }
    function convertMonthRangeToPhase(monthRange) {
        if (!monthRange || monthRange === '-') return '-';
        if (monthRange.includes('1-3') || monthRange.includes('1 - 3')) return '1';
        if (monthRange.includes('4-6') || monthRange.includes('4 - 6')) return '2';
        if (monthRange.includes('7-12') || monthRange.includes('7 - 12')) return '3';
        const monthNum = parseInt(monthRange);
        if (!isNaN(monthNum)) { if (monthNum >= 1 && monthNum <= 3) return '1'; if (monthNum >= 4 && monthNum <= 6) return '2'; if (monthNum >= 7 && monthNum <= 12) return '3'; }
        return monthRange || '-';
    }
    const groupedByBehavior = {};
    totalPlanningSubRows = 0;
    items.forEach(item => {
        const behaviorMatch = (item.activity_title || '').match(/^([^-]+)/);
        const behavior = behaviorMatch ? behaviorMatch[1].trim() : (item.activity_title || '-');
        const phaseMatch = (item.activity_title || '').match(/phase\s+([\d\-]+)/i);
        const phaseRaw = phaseMatch ? phaseMatch[1] : '-';
        const phase = convertMonthRangeToPhase(phaseRaw);
        const integrations = (item.description || '-').split('|').map(s => s.trim()).filter(s => s);
        if (integrations.length === 0) integrations.push('-');
        if (!groupedByBehavior[behavior]) groupedByBehavior[behavior] = [];
        const rencanaList = (item.deliverables || '').split('\n---\n').map(s => s.trim());
        integrations.forEach((integration, idx) => {
            totalPlanningSubRows++;
            groupedByBehavior[behavior].push({ ...item, extracted_phase: phase, integration_text: integration, rencana_text: rencanaList[idx] || '-', sub_idx: idx });
        });
    });
    let html = '';
    Object.entries(groupedByBehavior).forEach(([behavior, itemsInGroup]) => {
        itemsInGroup.forEach((item, idx) => {
            const showBehavior = idx === 0;
            const rowKey = item.id + '_' + item.sub_idx;
            const decision = pendingDecisions[rowKey];
            let actionHtml = '';
            let rowBgClass = '';
            if (decision) {
                if (decision.action === 'approve') {
                    rowBgClass = 'bg-green-50';
                    actionHtml = `
                        <span class="text-xs font-semibold text-green-700 block mb-1">✓ Disetujui</span>
                        <button onclick="cancelPendingDecision(${item.id}, ${item.sub_idx})" class="px-2 py-1 text-xs text-gray-600 bg-gray-200 hover:bg-gray-300 rounded transition cursor-pointer w-full text-center">Batalkan</button>
                    `;
                } else if (decision.action === 'revise') {
                    rowBgClass = 'bg-red-50';
                    actionHtml = `
                        <span class="text-xs font-semibold text-red-700 block mb-1">✕ Revisi</span>
                        <div class="flex flex-col gap-1 items-center justify-center auto">
                            <button onclick="editPendingDecision(${item.id}, ${item.sub_idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(item.integration_text).replace(/'/g, "%27")}', '${encodeURIComponent(item.rencana_text || '-').replace(/'/g, "%27")}')" class="px-2 py-1 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded transition cursor-pointer w-full text-center">Edit</button>
                            <button onclick="cancelPendingDecision(${item.id}, ${item.sub_idx})" class="px-2 py-1 text-xs text-gray-600 bg-gray-200 hover:bg-gray-300 rounded transition cursor-pointer w-full text-center">Batalkan</button>
                        </div>
                    `;
                }
            } else {
                actionHtml = `
                    <button onclick="approvePlanningRow(${item.id}, ${item.sub_idx})" class="inline-flex items-center justify-center w-8 h-8 rounded bg-green-600 text-white hover:bg-green-700 transition mr-2 cursor-pointer shadow-sm" title="Approve" style="font-size: 14px; font-weight: bold;">✓</button>
                    <button onclick="revisePlanningRow(${item.id}, ${item.sub_idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(item.integration_text).replace(/'/g, "%27")}', '${encodeURIComponent(item.rencana_text || '-').replace(/'/g, "%27")}')" class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-600 text-white hover:bg-red-700 transition cursor-pointer shadow-sm" title="Revise" style="font-size: 14px; font-weight: bold;">✕</button>
                `;
            }
            html += `
        <tr class="${rowBgClass} hover:bg-gray-50 transition-colors">
            ${showBehavior ? `<td class="px-4 py-3 align-top font-medium" style="vertical-align: top;">${behavior}</td>` : '<td class="px-4 py-3"></td>'}
            <td class="px-4 py-3 align-top"><span class="text-xs text-gray-700 whitespace-pre-wrap">${item.integration_text}</span></td>
            <td class="px-4 py-3 align-top whitespace-pre-wrap">${item.rencana_text || '-'}</td>
            <td class="px-4 py-3 align-top text-center">
                ${actionHtml}
            </td>
        </tr>
            `;
        });
    });
    tbody.innerHTML = html;
    toggleBatchButtonBar();
}

async function submitBatchReview() {
    const planId = detailData?.plan?.id;
    if (!planId) return;
    const pendingCount = Object.keys(pendingDecisions).length;
    if (pendingCount < totalPlanningSubRows) {
        showAlert('Harap berikan keputusan (Setujui atau Revisi) untuk seluruh rencana aktivitas terlebih dahulu.', 'error');
        return;
    }
    const groupedReviews = {};
    let validActionCount = 0;
    Object.values(pendingDecisions).forEach(data => {
        validActionCount++;
        if (!groupedReviews[data.item_id]) groupedReviews[data.item_id] = { id: data.item_id, action: data.action, notes: [] };
        if (data.action === 'revise') { groupedReviews[data.item_id].action = 'revise'; if (data.notes) groupedReviews[data.item_id].notes.push(data.notes); }
    });
    if (validActionCount === 0) return;
    const reviews = Object.values(groupedReviews).map(g => ({ id: g.id, action: g.action, notes: g.notes.join('\n\n') || null }));
    const btn = document.getElementById('batch-submit-btn-header');
    const orgHtml = btn.innerHTML;
    btn.innerHTML = 'Sedang Memproses...';
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    const res = await apiPost(`/api/manager/plans/${planId}/batch-review`, { reviews });
    btn.innerHTML = orgHtml;
    btn.disabled = false;
    btn.classList.remove('opacity-50', 'cursor-not-allowed');
    btn.classList.add('cursor-pointer');
    if (res && res.success) {
        pendingDecisions = {};
        clearPendingDecisionsLocal();
        toggleBatchButtonBar();
        showAlert(res.message || 'Review berhasil disimpan', 'success');
        loadDetail();
    } else {
        showAlert(res?.message || res?.error || 'Gagal menyimpan review', 'error');
    }
}

async function approvePlanningRow(itemId, subIdx) {
    const rowKey = itemId + '_' + subIdx;
    pendingDecisions[rowKey] = { item_id: itemId, action: 'approve' };
    savePendingDecisionsLocal();
    if (detailData && detailData.items) renderPlanningTable(detailData.items);
    toggleBatchButtonBar();
}

async function approveActivityRow(itemId, subIdx) {
    const rowKey = itemId + '_' + subIdx;
    pendingDecisions[rowKey] = { item_id: itemId, action: 'approve' };
    savePendingDecisionsLocal();
    if (detailData && detailData.items) {
        const item = detailData.items.find(i => i.id === itemId);
        if (item) {
            const phaseNum = resolvePhaseNumberFromItem(item);
            const phaseItems = detailData.items.filter(i => resolvePhaseNumberFromItem(i) === phaseNum);
            const bodyId = `phase-${phaseNum}-body`;
            renderPhaseActivityTable(bodyId, phaseItems);
        }
    }
    toggleBatchButtonBar();
}

async function reviseActivityRow(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
    const planId = detailData?.plan?.id;
    if (!planId) { showAlert('ID planning tidak ditemukan', 'error'); return; }
    currentRevisionItemId = itemId; currentRevisionSubIdx = subIdx;
    document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
    document.getElementById('modal-integrasi-val').textContent = '- ' + decodeURIComponent(integrasiEnc);
    document.getElementById('modal-rencana-val').textContent = '- ' + decodeURIComponent(rencanaEnc);
    document.getElementById('modal-revision-notes').value = '';
    document.getElementById('modal-cancel-revision-btn').classList.add('hidden');
    document.getElementById('revision-modal').classList.remove('hidden');
}

function cancelRevisionFromModal() {
    if (currentRevisionItemId && currentRevisionSubIdx !== null) cancelPendingDecision(currentRevisionItemId, currentRevisionSubIdx);
    closeRevisionModal();
}

function editPendingDecision(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
    const rowKey = itemId + '_' + subIdx;
    const decision = pendingDecisions[rowKey];
    currentRevisionItemId = itemId; currentRevisionSubIdx = subIdx;
    document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
    document.getElementById('modal-integrasi-val').textContent = '- ' + decodeURIComponent(integrasiEnc);
    document.getElementById('modal-rencana-val').textContent = '- ' + decodeURIComponent(rencanaEnc);
    document.getElementById('modal-revision-notes').value = decision.notes || '';
    document.getElementById('modal-cancel-revision-btn').classList.remove('hidden');
    document.getElementById('revision-modal').classList.remove('hidden');
}

function revisePlanningRow(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
    const planId = detailData?.plan?.id; if (!planId) { showAlert('ID planning tidak ditemukan', 'error'); return; }
    currentRevisionItemId = itemId; currentRevisionSubIdx = subIdx;
    document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
    document.getElementById('modal-integrasi-val').textContent = '- ' + decodeURIComponent(integrasiEnc);
    document.getElementById('modal-rencana-val').textContent = '- ' + decodeURIComponent(rencanaEnc);
    document.getElementById('modal-revision-notes').value = '';
    document.getElementById('modal-cancel-revision-btn').classList.add('hidden');
    document.getElementById('revision-modal').classList.remove('hidden');
}

function closeRevisionModal() { document.getElementById('revision-modal').classList.add('hidden'); currentRevisionItemId = null; currentRevisionSubIdx = null; }

async function submitRevisionModal() {
    if (!currentRevisionItemId || currentRevisionSubIdx === null) return;
    const revisionNotes = document.getElementById('modal-revision-notes').value.trim();
    if (!revisionNotes) { showAlert('Harap isi catatan revisi', 'error'); return; }
    const rowKey = currentRevisionItemId + '_' + currentRevisionSubIdx;
    pendingDecisions[rowKey] = { item_id: currentRevisionItemId, action: 'revise', notes: revisionNotes };
    savePendingDecisionsLocal();
    closeRevisionModal();
    if (detailData && detailData.items) renderPlanningTable(detailData.items);
    toggleBatchButtonBar();
}

async function submitApproveAll() {
    const planId = detailData?.plan?.id; if (!planId) { showAlert('Plan ID tidak ditemukan', 'error'); return; }
    document.getElementById('approve-all-modal').classList.remove('hidden');
}

function closeApproveAllModal() { document.getElementById('approve-all-modal').classList.add('hidden'); }

async function confirmApproveAll() {
    const planId = detailData?.plan?.id; if (!planId) { showAlert('Plan ID tidak ditemukan', 'error'); return; }
    closeApproveAllModal();
    try {
        const res = await apiPost(`/api/vnb-plans/${planId}/approve-all`, {});
        if (res && res.success) { showAlert('Semua rencana aktivitas berhasil disetujui!', 'success'); setTimeout(() => { loadDetail(); }, 1000); }
        else { showAlert(res?.message || 'Gagal menyetujui semua rencana aktivitas', 'error'); }
    } catch (err) { console.error('Error submitting approve all:', err); showAlert('Error: ' + err.message, 'error'); }
}

function updateApproveAllButtonState() {
    const approveAllBtn = document.getElementById('approve-all-btn');
    const submitBtn = document.getElementById('batch-submit-btn-header');
    if (!approveAllBtn || !submitBtn) return;
    const managerRole = detailData?.current_manager_role; // 'functional' | 'operational' | 'both' | null
    const canReviewPlanning = ['functional', 'operational', 'both'].includes(managerRole);
    if (!canReviewPlanning) {
        approveAllBtn.disabled = true; approveAllBtn.style.opacity = '0.5'; approveAllBtn.style.backgroundColor = '#9ca3af'; approveAllBtn.style.cursor = 'not-allowed'; approveAllBtn.style.pointerEvents = 'none'; approveAllBtn.title = 'Anda tidak memiliki otorisasi untuk mereview planning employee ini.';
        submitBtn.disabled = true; submitBtn.style.backgroundColor = '#9ca3af'; submitBtn.style.opacity = '0.5'; submitBtn.style.cursor = 'not-allowed'; submitBtn.title = 'Anda tidak memiliki otorisasi untuk mereview planning employee ini.';
    } else {
        approveAllBtn.disabled = false; approveAllBtn.style.opacity = '1'; approveAllBtn.style.backgroundColor = '#16a34a'; approveAllBtn.style.cursor = 'pointer'; approveAllBtn.style.pointerEvents = 'auto'; approveAllBtn.title = '';
        submitBtn.style.cursor = 'pointer'; submitBtn.title = '';
    }
}

async function loadManagerApprovalDetail() {
    const root = document.getElementById('manager-approval-root');
    if (!root) return;
    try {
        const res = await apiGet(`/api/manager/employees/${employeeId}`);
        if (!(res && res.success && res.data)) { root.classList.add('hidden'); return; }
        detailData = res.data;
        // restore pending decisions
        if (detailData?.plan?.id) {
            const saved = localStorage.getItem(`vnb_batch_decisions_${detailData.plan.id}`);
            if (saved) { try { pendingDecisions = JSON.parse(saved); } catch (e) { pendingDecisions = {}; } } else { pendingDecisions = {}; }
        }
        root.classList.remove('hidden');
        renderPhaseOverview(detailData);
        renderPhaseContent(detailData);
        updateApproveAllButtonState();
    } catch (err) {
        console.error(err);
        document.getElementById('manager-approval-root').classList.add('hidden');
    }
}

// Wire manager approval into main loadDetail
async function loadDetail() {
    await loadDetailInner();
}

// Re-define loadDetail to preserve previous logic then run manager flow
// We'll call the inner function defined earlier by the original name 'loadDetailInner'
// To avoid complex refactor, copy-paste the earlier implementation into 'loadDetailInner' and then call it, then proceed.

async function loadDetailInner() {
    // replicate previous loadDetail logic (fetch /api/employees and render profile & vnb content)
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
    document.getElementById('header-employee-name').textContent = escapeHtml(row.name || '-');
    document.getElementById('header-employee-role').textContent = escapeHtml((row.position?.name || row.position || '-') + ' • ' + (row.level || '-'));
    const managerFunctionalLabel = resolveLabel(row.manager_functional_label ?? row.manager_functional ?? row.managerFunctional);
    const managerOperationalLabel = resolveLabel(row.manager_operational_label ?? row.manager_operational ?? row.managerOperational);
    const divisionLabel = row.division?.name || row.division || '-';
    const departmentLabel = row.department?.name || row.department || '-';
    const positionLabel = row.position?.name || row.position || '-';
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

    try {
        await loadManagerApprovalDetail();
    } catch (e) {
        console.error(e);
        document.getElementById('manager-approval-root').classList.add('hidden');
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
        loadDetail();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/employees/detail.blade.php ENDPATH**/ ?>