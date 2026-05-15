@extends('layouts.app')
@section('title','Detail Employee')
@section('page_title','Detail Employee')
@section('page_subtitle','Lihat detail profil dan status kemajuan VnB untuk satu employee.')
@push('styles')
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
@endpush
@section('content')
<div class="space-y-6">
    <!-- Tabs + Content -->
    <div class="card-glass rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-white/80 px-4 pt-4">
            <div class="flex items-center gap-2">
                <a href="{{ $backUrl }}" title="Kembali" aria-label="Kembali" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100 hover:text-emerald-800 hover:border-emerald-300">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <nav class="flex-1 grid grid-cols-1 gap-2 sm:grid-cols-3 rounded-2xl bg-gray-100/90 p-1.5" aria-label="Tabs">
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

            <div id="vnb-content" class="space-y-6 hidden">
                <!-- Header Section (1/4 - 3/4) -->
                <div class="card-glass rounded-xl p-6 md:p-8 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-stretch relative z-10">
                        <!-- Left Column: Employee & Manager Info (1/4) -->
                        <div class="md:col-span-1 flex flex-col justify-center border-r border-gray-200/50 pr-8">
                            <div class="space-y-3">
                                <div>
                                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight leading-none" id="vnb-employee-name">Memuat...</h2>
                                    <div id="vnb-career-stage-info" class="mt-1 text-sm font-medium text-blue-600"></div>
                                </div>
                                <div class="space-y-2.5 pt-1">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase tracking-widest font-black text-gray-400">Manager Fungsional</span>
                                        <span class="text-sm font-bold text-gray-700 leading-tight" id="vnb-manager-functional">-</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase tracking-widest font-black text-gray-400">Manager Operasional</span>
                                        <span class="text-sm font-bold text-gray-700 leading-tight" id="vnb-manager-operational">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Progress & Actions (3/4) -->
                        <div class="md:col-span-3 flex flex-col justify-between pl-2">
                            <!-- Top Part: Progress Bar -->
                            <div id="vnb-progress-container" class="w-full">
                                <div class="flex items-end justify-between mb-4">
                                    <div class="space-y-1">
                                        <h3 class="text-base font-bold text-gray-800">Progres VnB Employee</h3>
                                        <p class="text-xs text-gray-500 font-medium" id="vnb-planning-status-text">Status: Memuat...</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-baseline justify-end gap-1">
                                            <span class="text-5xl font-black text-green-600 tracking-tighter leading-none" id="vnb-progress-percent">0</span>
                                            <span class="text-xl font-bold text-green-500">%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modern Progress Bar -->
                                <div class="w-full bg-gray-100 h-5 rounded-full overflow-hidden shadow-inner border border-gray-200/50 p-1">
                                    <div id="vnb-progress-bar" class="h-full bg-gradient-to-r from-green-400 via-green-500 to-green-600 rounded-full transition-all duration-1000 ease-out shadow-lg relative" style="width: 0%">
                                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center mt-6">
                                    <div class="flex items-center gap-4">
                                        <p class="text-sm font-black text-gray-700 flex items-center bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 shadow-sm">
                                            <i class="fas fa-clipboard-check mr-2.5 text-green-500 text-base"></i>
                                            <span id="vnb-phase-label">Fase Saat Ini: -</span>
                                        </p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div id="plan-action-buttons" class="flex items-center gap-3">
                                        <button id="approve-all-btn" onclick="submitApproveAll()" class="px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-full hover:bg-green-700 hover:shadow-md transition-all duration-200 flex items-center gap-2">
                                            <i class="fas fa-check-double text-xs"></i>
                                            Setujui Semua
                                        </button>
                                        <button id="batch-submit-btn-header" onclick="submitBatchReview()" class="px-7 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-full shadow-lg hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                            <i class="fas fa-save text-xs"></i>
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phase Tabs Navigation -->
                <div id="vnb-tabs-nav-container" class="hidden mt-6 mb-4">
                    <div class="border-b border-gray-200">
                        <nav id="vnb-tabs-nav" class="-mb-px flex space-x-6" aria-label="Tabs">
                            <!-- Tabs will be injected here dynamically -->
                        </nav>
                    </div>
                </div>

                <!-- Manager Approval UI (Plan Tab Content) -->
                <div id="vnb-tab-content-plan" class="vnb-phase-tab-content space-y-6 hidden">
                    <!-- Batch approval tables will be rendered here -->
                    <div id="dynamic-phases-container" class="space-y-6"></div>
                </div>
                
                <!-- Dynamic Phase Content Containers will be injected here -->
                <div id="vnb-phases-content-root"></div>

            </div>


                </div>
            </div>

            <div id="vnb-activity-soon" class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center hidden">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                    <i class="fas fa-hourglass-half text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">VnB Activity Segera Hadir</h3>
                <p class="text-gray-500 max-w-md">Semua plan sudah disetujui. Fitur implementasi VnB Activity masih dalam pengembangan dan akan ditampilkan di sini saat siap digunakan.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modals Moved Outside of Transformed Containers -->
<!-- Approve All Modal -->
<div id="approve-all-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] flex items-center justify-center p-4">
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
<div id="revision-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Edit Draft Baris</h3>
        <div class="mb-4 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-3">
            <div id="modal-behaviour-val" class="font-bold text-base text-gray-900"></div>
            <div>
                <label class="block font-semibold text-xs text-gray-500 uppercase mb-2">Integrasi Pengukuran</label>
                <textarea id="modal-integrasi-input" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Edit integrasi pengukuran"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-xs text-gray-500 uppercase mb-2">Rencana Aktivitas</label>
                <textarea id="modal-rencana-input" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Edit rencana aktivitas"></textarea>
            </div>
        </div>
        <p class="text-xs text-gray-500 mb-4">Jika baris ini diubah, statusnya akan menjadi <span class="font-semibold text-red-600">Direvisi</span>.</p>
        <div class="flex justify-between items-center mt-6">
            <button id="modal-cancel-revision-btn" onclick="cancelRevisionFromModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium hidden">Batalkan</button>
            <div class="flex gap-2 flex-1 justify-end">
                <button onclick="closeRevisionModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 font-medium">Batal</button>
                <button onclick="submitRevisionModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 font-medium">Simpan Perubahan</button>
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

function formatCareerStage(value) {
    const key = normalizeDisplayValue(value, '').toLowerCase();
    const map = {
        manage_self_non_staff: 'Manage Self (Non-Staff)',
        manage_self_staff: 'Manage Self (Staff)',
        manage_others: 'Manage Others',
        manage_managers: 'Manage Managers',
        manage_manager: 'Manage Managers',
        manage_function: 'Manage Function',
    };
    return map[key] || normalizeDisplayValue(value, '-');
}

function getEmployeeStatusLabel(status) {
    const key = normalizeDisplayValue(status, '').toLowerCase();
    if (key === 'aktif' || key === 'active') return 'Aktif';
    if (key === 'inactive' || key === 'inaktif' || key === 'nonaktif') return 'Inactive';
    return status || '-';
}

async function loadDetail() {
    const profileBox = document.getElementById('profile-box');
    if (profileBox) profileBox.innerHTML = '<div class="text-sm text-gray-500 py-2">Memuat detail...</div>';

    try {
        const detailRes = await apiGet(`/api/employees/${employeeId}`);
        if (!(detailRes && detailRes.success === true && detailRes.data)) {
            if (profileBox) profileBox.innerHTML = '<div class="text-sm text-red-600 py-2">Gagal memuat detail employee.</div>';
            return;
        }

        const row = detailRes.data;
        const careerStageText = formatCareerStage(row.career_stage);
        // Update Page Header
        const headerNameEl = document.getElementById('header-employee-name');
        const headerRoleEl = document.getElementById('header-employee-role');
        if (headerNameEl) headerNameEl.textContent = escapeHtml(row.name || '-');
        if (headerRoleEl) headerRoleEl.textContent = escapeHtml(careerStageText);

        const managerFunctionalLabel = resolveLabel(row.manager_functional_label ?? row.manager_functional ?? row.managerFunctional);
        const managerOperationalLabel = resolveLabel(row.manager_operational_label ?? row.manager_operational ?? row.managerOperational);
        const divisionLabel = row.division?.name || row.division || '-';
        const departmentLabel = row.department?.name || row.department || '-';
        const positionLabel = row.position?.name || row.position || '-';

        // Profil Box Update
        if (profileBox) {
            profileBox.innerHTML = `
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

        if (row.is_vnb_participant) {
            document.getElementById('vnb-not-assigned').classList.add('hidden');
            document.getElementById('vnb-content').classList.remove('hidden');
            
            // Fetch Manager Approval Data
            try {
                const mgrRes = await apiGet(`/api/manager/employees/${employeeId}`);
                if (mgrRes && mgrRes.success && mgrRes.data) {
                    detailData = mgrRes.data;
                    
                    // Restore pending decisions
                    if (detailData?.plan?.id) {
                        const saved = localStorage.getItem(`vnb_batch_decisions_${detailData.plan.id}`);
                        if (saved) { try { pendingDecisions = JSON.parse(saved); } catch (e) { pendingDecisions = {}; } } else { pendingDecisions = {}; }
                    }

                    // Update VnB Header UI
                    document.getElementById('vnb-employee-name').textContent = detailData.employee.name;
                    document.getElementById('vnb-career-stage-info').textContent = careerStageText;
                    document.getElementById('vnb-manager-functional').textContent = detailData.employee.manager_functional || '-';
                    document.getElementById('vnb-manager-operational').textContent = detailData.employee.manager_operational || '-';

                    const progress = Math.round(detailData.progress || 0);
                    document.getElementById('vnb-progress-percent').textContent = progress;
                    document.getElementById('vnb-progress-bar').style.width = progress + '%';
                    document.getElementById('vnb-phase-label').textContent = 'Fase Saat Ini: ' + (detailData.phase || '-');
                    document.getElementById('vnb-planning-status-text').textContent = 'Status: ' + (detailData.plan?.status?.replace(/_/g, ' ').toUpperCase() || 'DRAFT');

                    const comingSoon = document.getElementById('vnb-activity-soon');
                    const planningWaiting = !!detailData?.approval_requests?.planning_waiting;
                    
                    if (planningWaiting) {
                        if (comingSoon) comingSoon.classList.add('hidden');
                        renderPhaseContent(detailData);
                        updateApproveAllButtonState();
                    } else {
                        document.getElementById('manager-approval-root').classList.add('hidden');
                        if (comingSoon) comingSoon.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error("Error loading manager approval detail:", err);
            }
        } else {
            document.getElementById('vnb-content').classList.add('hidden');
            document.getElementById('vnb-not-assigned').classList.remove('hidden');
        }
    } catch (e) {
        console.error("Error in loadDetail:", e);
    }
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

function buildPlanningRowKey(itemId, subIdx) {
    return `${itemId}::${subIdx}`;
}

function splitIntegrations(value) {
    return String(value || '-')
        .split('|')
        .map(part => part.trim())
        .filter(part => part !== '');
}

function splitDeliverables(value) {
    return String(value || '-')
        .split('\n---\n')
        .map(part => part.trim())
        .filter((part, index, list) => !(index === list.length - 1 && part === ''));
}

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
    if (typeof detailData !== 'undefined' && detailData) {
        updateSubmitButtonState(detailData);
    }
}

function cancelPendingDecision(itemId, subIdx) {
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    delete pendingDecisions[rowKey];
    savePendingDecisionsLocal();
    if (detailData && detailData.items) {
        renderPhaseContent(detailData);
    }
    toggleBatchButtonBar();
}

function toLabelStatus(status) {
    const map = { waiting_approval: 'Waiting Approval', revision_required: 'Perlu Revisi', completed: 'Completed', draft: 'Draft' };
    return map[status] || status || '-';
}

function extractPhase(title) {
    let match = title.match(/Phase (Fase\s+\d+\s+\([^)]+\))/i);
    if (match) return match[1];
    
    match = title.match(/Phase (1-3|4-6|6\+)/i);
    if (match) return match[1];
    
    match = title.match(/(Fase\s+\d+.*?(?=\s*$|-))/i);
    if (match) return match[1].trim();
    
    return 'Unknown';
}

function parseIsoDate(dateString) {
    if (!dateString) return null;
    const date = new Date(dateString);
    return Number.isNaN(date.getTime()) ? null : date;
}

function extractDurationMonths(durationText) {
    const match = String(durationText || '').match(/(\d+)/);
    const months = match ? parseInt(match[1], 10) : 1;
    return Number.isFinite(months) && months > 0 ? months : 1;
}

function formatPhaseDateRange(startDate, endDate) {
    if (!startDate || !endDate) return '';
    const formatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    const start = new Date(startDate);
    const end = new Date(endDate);
    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) return '';
    return `${start.toLocaleDateString('id-ID', formatOptions)} - ${end.toLocaleDateString('id-ID', formatOptions)}`;
}

function formatPhaseDurationRange(duration, startDate, endDate) {
    const dateRange = formatPhaseDateRange(startDate, endDate);
    if (!dateRange) return duration || '';
    return `${duration || ''} (${dateRange})`;
}

function buildPhaseDateRange(phaseInfo, cursorDate) {
    const explicitStart = parseIsoDate(phaseInfo.start_date);
    const explicitEnd = parseIsoDate(phaseInfo.end_date);

    if (explicitStart && explicitEnd) {
        return { startDate: explicitStart, endDate: explicitEnd, nextCursorDate: new Date(explicitEnd.getTime() + (1000 * 60 * 60 * 24)) };
    }

    const startDate = explicitStart || cursorDate;
    if (!startDate) return { startDate: null, endDate: null, nextCursorDate: null };

    const durationMonths = extractDurationMonths(phaseInfo.duration);
    const endDate = new Date(startDate.getTime());
    endDate.setMonth(endDate.getMonth() + durationMonths);

    return { startDate: startDate, endDate: endDate, nextCursorDate: new Date(endDate.getTime() + (1000 * 60 * 60 * 24)) };
}

function updateSubmitButtonState(detail) {
    const headerBtn = document.getElementById('batch-submit-btn-header');
    if (!headerBtn) return;
    const items = detail.items || [];
    const hasWaitingItems = detail.approval_requests?.planning_waiting && items.length > 0;
    
    let totalSubRows = 0;
    let decidedSubRows = 0;
    
    items.forEach(item => {
        const integrations = splitIntegrations(item.description || '-');
        const snapshot = item.manager_review_snapshot && typeof item.manager_review_snapshot === 'object' ? item.manager_review_snapshot : {};
        
        integrations.forEach((integration, idx) => {
            totalSubRows++;
            const rowKey = buildPlanningRowKey(item.id, idx);
            if (pendingDecisions[rowKey] || snapshot[idx]) {
                decidedSubRows++;
            }
        });
    });
    
    const hasNewPendingDecisions = Object.keys(pendingDecisions).length > 0;
    const allRowsDecided = totalSubRows > 0 && decidedSubRows >= totalSubRows;
    
    if (!hasWaitingItems || !allRowsDecided || !hasNewPendingDecisions) {
        headerBtn.disabled = true;
        headerBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        headerBtn.disabled = false;
        headerBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function renderPhaseContent(detail) {
    const planTabRoot = document.getElementById('vnb-tab-content-plan');
    const container = document.getElementById('dynamic-phases-container');
    const navContainer = document.getElementById('vnb-tabs-nav-container');
    const tabsNav = document.getElementById('vnb-tabs-nav');
    const phasesRoot = document.getElementById('vnb-phases-content-root');
    
    if (container) container.innerHTML = ''; 
    if (tabsNav) tabsNav.innerHTML = '';
    if (phasesRoot) phasesRoot.innerHTML = '';

    if (!detail.current_manager_role) {
        if (planTabRoot) planTabRoot.classList.add('hidden');
        if (navContainer) navContainer.classList.add('hidden');
        return;
    }

    if (navContainer) navContainer.classList.remove('hidden');
    
    const planningWaiting = detail.approval_requests?.planning_waiting;
    const isPlanApproved = detail.plan?.status === 'approved' || detail.plan?.status === 'in_progress' || detail.plan?.status === 'completed';

    totalPlanningSubRows = 0;
    const items = detail.items || [];
    
    // Group items dynamically by extracted phase string
    const itemsByPhase = {};
    items.forEach(item => {
        const phase = extractPhase(item.activity_title || '');
        if (!itemsByPhase[phase]) itemsByPhase[phase] = [];
        itemsByPhase[phase].push(item);
    });

    // Custom sorting to ensure numeric ordering if present
    const uniquePhases = Object.keys(itemsByPhase).sort((a, b) => {
        const numA = (a.match(/\d+/) || [999])[0];
        const numB = (b.match(/\d+/) || [999])[0];
        return numA - numB;
    });

    // Add "Plan" Tab Button
    const planTabBtn = `
        <button onclick="switchManagerVnbTab('plan')" id="vnb-tab-btn-plan" class="vnb-tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm border-blue-500 text-blue-600 focus:outline-none transition-colors duration-200">
            Plan
        </button>
    `;
    if (tabsNav) tabsNav.insertAdjacentHTML('beforeend', planTabBtn);
    
    const colorGradients = [
        'from-blue-500/10 to-blue-600/10',
        'from-amber-500/10 to-amber-600/10',
        'from-green-500/10 to-green-600/10',
        'from-purple-500/10 to-purple-600/10',
        'from-red-500/10 to-red-600/10',
    ];

    let phaseCursorDate = parseIsoDate(detail.employee?.induction_date || detail.plan?.submitted_at || new Date().toISOString());

    uniquePhases.forEach((phaseString, index) => {
        const phaseItems = itemsByPhase[phaseString];
        
        let label = 'FASE ' + (index + 1);
        let duration = '1 Bulan';
        
        // Try parsing format like "Fase 1 (1 Bulan)"
        let m = phaseString.match(/Fase\s+(\d+)\s+\(([^)]+)\)/i);
        if (m) {
            label = 'FASE ' + m[1];
            duration = m[2];
        } else {
            // Older format like "1-3"
            let m2 = phaseString.match(/(1-3|4-6|6\+)/i);
            if (m2) {
                duration = m2[1] + ' Bulan';
            }
        }
        
        const phaseInfo = { duration: duration };
        const computedRange = buildPhaseDateRange(phaseInfo, phaseCursorDate);
        phaseCursorDate = computedRange.nextCursorDate;
        
        const desc = formatPhaseDurationRange(phaseInfo.duration, computedRange.startDate, computedRange.endDate);
        const colorClass = colorGradients[index % colorGradients.length];
        const phaseId = `dynamic-phase-${index}`;

        // Add "Fase X" Tab Button
        const tabBtn = `
            <button onclick="switchManagerVnbTab('${phaseId}')" id="vnb-tab-btn-${phaseId}" class="vnb-tab-btn whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors duration-200">
                ${label}
            </button>
        `;
        if (tabsNav) tabsNav.insertAdjacentHTML('beforeend', tabBtn);

        // Render Batch Approval Table inside Plan Tab
        const planPhaseHtml = `
            <div id="${phaseId}-section" class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300">
                <div class="px-6 py-4 bg-gradient-to-r ${colorClass} border-b border-gray-200/50">
                    <h2 class="text-lg font-semibold text-gray-900">${label}</h2>
                    <p class="text-sm text-gray-600 mt-1">${desc}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th class="w-1/6">Value</th>
                                <th class="w-1/4">Integrasi Pengukuran</th>
                                <th class="w-1/3">Rencana Aktivitas</th>
                                <th class="w-1/6 text-right">Approval</th>
                            </tr>
                        </thead>
                        <tbody id="${phaseId}-body"></tbody>
                    </table>
                </div>
            </div>
        `;
        if (container) container.insertAdjacentHTML('beforeend', planPhaseHtml);
        renderPhaseActivityTable(`${phaseId}-body`, phaseItems, planningWaiting);

        // Render Content Block for "Fase X" Tab
        let phaseContentHtml = '';
        if (!isPlanApproved) {
            phaseContentHtml = `
                <div id="vnb-tab-content-${phaseId}" class="vnb-phase-tab-content hidden space-y-6">
                    <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-4 border border-blue-100">
                            <i class="fas fa-lock text-3xl text-blue-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">VnB Plan Belum Disetujui</h3>
                        <p class="text-gray-500 max-w-md">VnB Plan dari Employee ini belum disetujui sepenuhnya. Silakan lakukan review dan persetujuan di tab <b>Plan</b> terlebih dahulu sebelum dapat melakukan monitoring fase ini.</p>
                    </div>
                </div>
            `;
        } else {
            // Temporary placeholder for implementation monitoring
            phaseContentHtml = `
                <div id="vnb-tab-content-${phaseId}" class="vnb-phase-tab-content hidden space-y-6">
                    <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mb-4 border border-green-100">
                            <i class="fas fa-chart-line text-3xl text-green-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Monitoring ${label}</h3>
                        <p class="text-gray-500 max-w-md">VnB Plan telah disetujui. Tabel monitoring untuk evaluasi implementasi dan dokumentasi ${label} akan segera hadir di sini.</p>
                    </div>
                </div>
            `;
        }
        if (phasesRoot) phasesRoot.insertAdjacentHTML('beforeend', phaseContentHtml);
    });

    updateSubmitButtonState(detail);
    switchManagerVnbTab('plan'); // Default active tab
}

function switchManagerVnbTab(tabId) {
    // Update tab buttons
    document.querySelectorAll('.vnb-tab-btn').forEach(btn => {
        if (btn.id === \`vnb-tab-btn-\${tabId}\`) {
            btn.classList.add('border-blue-500', 'text-blue-600', 'font-bold');
            btn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'font-medium');
        } else {
            btn.classList.remove('border-blue-500', 'text-blue-600', 'font-bold');
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'font-medium');
        }
    });

    // Update tab contents
    document.querySelectorAll('.vnb-phase-tab-content').forEach(content => {
        if (content.id === \`vnb-tab-content-\${tabId}\`) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });

    // Toggle global action buttons (only show in 'plan' tab)
    const actionBtns = document.getElementById('plan-action-buttons');
    if (actionBtns) {
        if (tabId === 'plan') {
            actionBtns.classList.remove('hidden');
            actionBtns.classList.add('flex');
        } else {
            actionBtns.classList.add('hidden');
            actionBtns.classList.remove('flex');
        }
    }
}

function renderPhaseActivityTable(bodyId, items, planningWaiting = false) {
    const tbody = document.getElementById(bodyId);
    if (!tbody) return;
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada aktivitas untuk fase ini</td></tr>'; return; }
    let html = '';
    items.forEach(item => {
        const behaviorMatch = (item.activity_title || '').match(/^([^-]+)/);
        const behavior = behaviorMatch ? behaviorMatch[1].trim() : (item.activity_title || '-');
        const integrations = splitIntegrations(item.description || '-');
        const rencanaList = splitDeliverables(item.deliverables || '-');
        const snapshot = item.manager_review_snapshot && typeof item.manager_review_snapshot === 'object' ? item.manager_review_snapshot : {};
        integrations.forEach((integration, idx) => {
            totalPlanningSubRows++;
            const rowKey = buildPlanningRowKey(item.id, idx);
            const pendingDecision = pendingDecisions[rowKey] || null;
            const savedDecision = snapshot[idx] || null;
            const rowState = pendingDecision || savedDecision;
            const displayIntegration = pendingDecision?.integration_text ?? savedDecision?.integration_text ?? integration;
            const displayDeliverables = pendingDecision?.deliverables_text ?? savedDecision?.deliverables_text ?? (rencanaList[idx] || '-');
            let actionHtml = '';
            let rowBgClass = '';
            if (rowState && rowState.action === 'approve') {
                rowBgClass = 'bg-green-50/50';
                actionHtml = `<div class="flex flex-col items-end"><span class="text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-100 px-2 py-0.5 rounded-full mb-1">✓ Disetujui</span><button onclick="cancelPendingDecision(${item.id}, ${idx})" class="text-[10px] font-bold text-gray-400 hover:text-gray-600 transition underline">Batalkan</button></div>`;
            } else if (rowState && rowState.action === 'revise') {
                rowBgClass = 'bg-red-50/50';
                actionHtml = `<div class="flex flex-col items-end"><span class="text-[10px] font-black uppercase tracking-widest text-red-600 bg-red-100 px-2 py-0.5 rounded-full mb-1">✕ Direvisi</span><div class="flex gap-2"><button onclick="editPendingDecision(${item.id}, ${idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(displayIntegration).replace(/'/g, "%27")}', '${encodeURIComponent(displayDeliverables || '-').replace(/'/g, "%27")}')" class="text-[10px] font-bold text-blue-500 hover:text-blue-700 transition underline">Edit</button><button onclick="cancelPendingDecision(${item.id}, ${idx})" class="text-[10px] font-bold text-gray-400 hover:text-gray-600 transition underline">Batalkan</button></div></div>`;
            } else {
                actionHtml = `<div class="flex justify-end gap-2"><button onclick="approvePlanningRow(${item.id}, ${idx})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-green-600 hover:bg-green-50 hover:border-green-200 transition flex items-center justify-center shadow-sm" title="Setujui"><i class="fas fa-check"></i></button><button onclick="revisePlanningRow(${item.id}, ${idx}, '${encodeURIComponent(behavior).replace(/'/g, "%27")}', '${encodeURIComponent(displayIntegration).replace(/'/g, "%27")}', '${encodeURIComponent(displayDeliverables || '-').replace(/'/g, "%27")}')" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 transition flex items-center justify-center shadow-sm" title="Revisi"><i class="fas fa-times"></i></button></div>`;
            }
            html += `<tr class="${rowBgClass} hover:bg-gray-50/80 transition-colors">${idx === 0 ? `<td class="px-4 py-4"><span class="font-bold text-gray-900">${behavior}</span></td>` : '<td class="px-4 py-4"></td>'}<td class="px-4 py-4"><p class="text-xs text-gray-600 leading-relaxed">${displayIntegration}</p></td><td class="px-4 py-4"><p class="text-xs text-gray-700 font-medium leading-relaxed">${displayDeliverables || '-'}</p></td><td class="px-4 py-4 text-right">${actionHtml}</td></tr>`;
        });
    });
    tbody.innerHTML = html;
}

async function submitBatchReview() {
    const planId = detailData?.plan?.id;
    if (!planId) return;
    const pendingCount = Object.keys(pendingDecisions).length;
    if (pendingCount < totalPlanningSubRows) {
        showAlert('Harap berikan keputusan (Setujui atau Revisi) untuk seluruh rencana aktivitas terlebih dahulu.', 'error');
        return;
    }
    const reviews = Object.values(pendingDecisions).map(row => ({
        id: row.item_id,
        sub_idx: row.sub_idx,
        action: row.action,
        integration_text: row.integration_text,
        deliverables_text: row.deliverables_text,
    }));
    if (!reviews.length) return;
    const btn = document.getElementById('batch-submit-btn-header');
    const orgHtml = btn.innerHTML;
    btn.innerHTML = 'Sedang Memproses...';
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    const res = await apiPost(`/api/manager/plans/${planId}/batch-review`, { reviews });
    btn.innerHTML = orgHtml;
    btn.disabled = false;
    btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
    const item = detailData?.items?.find(i => i.id === itemId);
    if (!item) return;
    const integrations = splitIntegrations(item.description || '-');
    const deliverables = splitDeliverables(item.deliverables || '-');
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    pendingDecisions[rowKey] = {
        item_id: itemId,
        sub_idx: subIdx,
        action: 'approve',
        integration_text: integrations[subIdx] || '-',
        deliverables_text: deliverables[subIdx] || '-',
    };
    savePendingDecisionsLocal();
    if (detailData && detailData.items) renderPhaseContent(detailData);
    toggleBatchButtonBar();
}

function editPendingDecision(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    const decision = pendingDecisions[rowKey];
    currentRevisionItemId = itemId; currentRevisionSubIdx = subIdx;
    document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
    document.getElementById('modal-integrasi-input').value = decision.integration_text || decodeURIComponent(integrasiEnc);
    document.getElementById('modal-rencana-input').value = decision.deliverables_text || decodeURIComponent(rencanaEnc);
    document.getElementById('modal-cancel-revision-btn').classList.remove('hidden');
    document.getElementById('revision-modal').classList.remove('hidden');
}

function cancelRevisionFromModal() {
    if (currentRevisionItemId && currentRevisionSubIdx !== null) cancelPendingDecision(currentRevisionItemId, currentRevisionSubIdx);
    closeRevisionModal();
}

function revisePlanningRow(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
    currentRevisionItemId = itemId; currentRevisionSubIdx = subIdx;
    document.getElementById('modal-behaviour-val').textContent = decodeURIComponent(behaviorEnc);
    document.getElementById('modal-integrasi-input').value = decodeURIComponent(integrasiEnc);
    document.getElementById('modal-rencana-input').value = decodeURIComponent(rencanaEnc);
    document.getElementById('modal-cancel-revision-btn').classList.add('hidden');
    document.getElementById('revision-modal').classList.remove('hidden');
}

function closeRevisionModal() { document.getElementById('revision-modal').classList.add('hidden'); currentRevisionItemId = null; currentRevisionSubIdx = null; }

async function submitRevisionModal() {
    if (!currentRevisionItemId || currentRevisionSubIdx === null) return;
    const integrationText = document.getElementById('modal-integrasi-input').value.trim();
    const deliverablesText = document.getElementById('modal-rencana-input').value.trim();
    if (!integrationText || !deliverablesText) {
        showAlert('Integrasi pengukuran dan rencana aktivitas harus diisi.', 'error');
        return;
    }
    const rowKey = buildPlanningRowKey(currentRevisionItemId, currentRevisionSubIdx);
    pendingDecisions[rowKey] = {
        item_id: currentRevisionItemId,
        sub_idx: currentRevisionSubIdx,
        action: 'revise',
        integration_text: integrationText,
        deliverables_text: deliverablesText,
    };
    savePendingDecisionsLocal();
    closeRevisionModal();
    if (detailData && detailData.items) renderPhaseContent(detailData);
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
        if (!detailData?.items?.length) { showAlert('Data item planning belum tersedia', 'error'); return; }
        detailData.items.forEach(item => {
            const integrations = splitIntegrations(item.description || '-');
            const deliverables = splitDeliverables(item.deliverables || '-');
            integrations.forEach((integration, idx) => {
                const rowKey = buildPlanningRowKey(item.id, idx);
                pendingDecisions[rowKey] = {
                    item_id: item.id,
                    sub_idx: idx,
                    action: 'approve',
                    integration_text: integration,
                    deliverables_text: deliverables[idx] || '-',
                };
            });
        });
        savePendingDecisionsLocal();
        renderPhaseContent(detailData);
        await submitBatchReview();
    } catch (err) { console.error('Error submitting approve all:', err); showAlert('Error: ' + err.message, 'error'); }
}

function updateApproveAllButtonState() {
    const approveAllBtn = document.getElementById('approve-all-btn');
    const submitBtn = document.getElementById('batch-submit-btn-header');
    if (!approveAllBtn || !submitBtn) return;
    const managerRole = detailData?.current_manager_role;
    const canReviewPlanning = ['functional', 'operational', 'both'].includes(managerRole);
    
    if (!canReviewPlanning) {
        approveAllBtn.disabled = true;
        approveAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
        approveAllBtn.title = 'Anda tidak memiliki otorisasi untuk mereview planning employee ini.';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        submitBtn.title = 'Anda tidak memiliki otorisasi untuk mereview planning employee ini.';
    } else {
        approveAllBtn.disabled = false;
        approveAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        approveAllBtn.title = '';
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    loadDetail();
});
</script>
@endpush
@endsection