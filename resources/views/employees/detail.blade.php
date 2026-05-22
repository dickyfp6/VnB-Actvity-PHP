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
                <button onclick="switchTab('vnb')" id="tab-btn-vnb" class="tab-button tab-button-inactive w-full whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 focus:outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-tasks mr-2"></i>VnB Activity
                    <span id="vnb-approval-badge" class="hidden inline-flex items-center justify-center min-w-[18px] h-5 px-1 rounded-full text-[10px] font-bold text-white transition-all duration-300" style="background-color: #ef4444;">
                        <span id="vnb-badge-count">0</span>
                    </span>
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
                                        <button id="approve-all-btn" onclick="submitApproveAll()" class="px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-full hover:bg-green-700 hover:shadow-md transition-all duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-check-double text-xs"></i>
                                            Setujui Semua
                                        </button>
                                        <button id="batch-submit-btn-header" onclick="showConfirmationModal()" class="px-7 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-full shadow-lg hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
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

@push('scripts')
<script>
const employeeId = @json($employeeId);
let activityReviewActivePhase = '';
let isPlanRevisionMode = false;

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

function isApprovedPlanStatus(status) {
    return ['approved', 'approved_with_revision'].includes(String(status || '').toLowerCase());
}

function getTodayStartDate() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return today;
}

function isFuturePhaseRange(computedRange) {
    if (!computedRange?.startDate) return false;

    const startDate = new Date(computedRange.startDate);
    if (Number.isNaN(startDate.getTime())) return false;

    startDate.setHours(0, 0, 0, 0);
    return startDate > getTodayStartDate();
}

function isPastPhaseRange(computedRange) {
    if (!computedRange?.endDate) return false;

    const endDate = new Date(computedRange.endDate);
    if (Number.isNaN(endDate.getTime())) return false;

    endDate.setHours(0, 0, 0, 0);
    return endDate < getTodayStartDate();
}

function isCurrentPhaseRange(computedRange) {
    if (!computedRange?.startDate || !computedRange?.endDate) return false;

    const startDate = new Date(computedRange.startDate);
    const endDate = new Date(computedRange.endDate);
    if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) return false;

    const today = getTodayStartDate();
    startDate.setHours(0, 0, 0, 0);
    endDate.setHours(0, 0, 0, 0);

    return startDate <= today && today <= endDate;
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
                    isPlanRevisionMode = false;
                    
                    // Restore pending decisions
                    if (detailData?.plan?.id) {
                        const saved = localStorage.getItem(`vnb_batch_decisions_${detailData.plan.id}`);
                        if (saved) { 
                            try { 
                                const parsed = JSON.parse(saved);
                                pendingDecisions = {};
                                Object.entries(parsed).forEach(([key, val]) => {
                                    const parts = key.split('::');
                                    const parsedItemId = parseInt(val.item_id) || parseInt(parts[0]);
                                    const parsedSubIdx = (val.sub_idx !== undefined && val.sub_idx !== null && !isNaN(parseInt(val.sub_idx))) 
                                        ? parseInt(val.sub_idx) 
                                        : parseInt(parts[1]);
                                    
                                    if (!isNaN(parsedItemId) && !isNaN(parsedSubIdx)) {
                                        // Look up missing fields from detailData to heal old/corrupted data
                                        const item = detailData?.items?.find(i => i.id === parsedItemId);
                                        let integrationText = val.integration_text || '';
                                        let deliverablesText = val.deliverables_text || '';
                                        
                                        if (item) {
                                            const integrations = splitIntegrations(item.description || '-');
                                            const deliverables = splitDeliverables(item.deliverables || '-');
                                            if (!integrationText) {
                                                integrationText = integrations[parsedSubIdx] || '-';
                                            }
                                            if (!deliverablesText) {
                                                deliverablesText = deliverables[parsedSubIdx] || '-';
                                            }
                                        }

                                        pendingDecisions[key] = {
                                            ...val,
                                            item_id: parsedItemId,
                                            sub_idx: parsedSubIdx,
                                            action: val.action || 'approve',
                                            integration_text: integrationText,
                                            deliverables_text: deliverablesText
                                        };
                                    }
                                });
                            } catch (e) { 
                                pendingDecisions = {}; 
                            } 
                        } else { 
                            pendingDecisions = {}; 
                        }
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
                    const activityWaitingCount = Number(detailData?.approval_requests?.activity_waiting_count || 0);
                    
                    if (planningWaiting) {
                        if (comingSoon) comingSoon.classList.add('hidden');
                        renderPhaseContent(detailData);
                        updateApproveAllButtonState();
                        updateVnbApprovalBadge();
                    } else {
                        const approvalRoot = document.getElementById('manager-approval-root');
                        if (approvalRoot) approvalRoot.classList.add('hidden');
                        if (comingSoon) {
                            comingSoon.classList.remove('hidden');
                            renderActivityReviewContent(detailData);
                        }
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

function isSubmittedForManagerReviewStatus(status) {
    return ['waiting_approval', 'submitted'].includes(String(status || '').toLowerCase());
}

function getActivityPendingApprovalCount(items = []) {
    let pending = 0;

    (items || []).forEach(item => {
        const rows = getActivityRows(item);
        rows.forEach(row => {
            const rowStatus = getActivityRowStatus(item, row);
            if (isSubmittedForManagerReviewStatus(rowStatus)) {
                pending += 1;
            }
        });
    });

    return pending;
}

function getPlanningPendingApprovalCount(items = []) {
    let pending = 0;

    (items || []).forEach(item => {
        const integrations = splitIntegrations(item.description || '-');
        const snapshot = item.manager_review_snapshot && typeof item.manager_review_snapshot === 'object'
            ? item.manager_review_snapshot
            : {};

        integrations.forEach((integration, idx) => {
            const rowKey = buildPlanningRowKey(item.id, idx);
            const hasDecision = !!(pendingDecisions[rowKey] || snapshot[idx]);
            if (!hasDecision) {
                pending += 1;
            }
        });
    });

    return pending;
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
    itemId = parseInt(itemId) || 0;
    subIdx = parseInt(subIdx) || 0;
    if (isNaN(itemId) || isNaN(subIdx)) return;
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    delete pendingDecisions[rowKey];
    savePendingDecisionsLocal();
    updateApprovalProgress();
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
    const isApprovedPlan = isApprovedPlanStatus(detail?.plan?.status);
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

    if (isApprovedPlan) {
        headerBtn.disabled = !isPlanRevisionMode || !hasNewPendingDecisions;
        if (headerBtn.disabled) {
            headerBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            headerBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    } else {
        if (!hasWaitingItems || !allRowsDecided || !hasNewPendingDecisions) {
            headerBtn.disabled = true;
            headerBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            headerBtn.disabled = false;
            headerBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    updateApproveAllButtonState();
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
    const isPlanApproved = isApprovedPlanStatus(detail.plan?.status) || detail.plan?.status === 'in_progress' || detail.plan?.status === 'completed';
    const isPlanningPhase = String(detail.phase || '').toLowerCase() === 'planning';
    const todayStart = getTodayStartDate();

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
    const planningPendingCount = planningWaiting ? getPlanningPendingApprovalCount(items) : 0;
    const planTabBtn = `
        <button onclick="switchManagerVnbTab('plan')" id="vnb-tab-btn-plan" class="vnb-tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm border-blue-500 text-blue-600 focus:outline-none transition-colors duration-200">
            <span class="inline-flex items-center gap-2">Plan${planningPendingCount > 0 ? `<span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1 rounded-full text-[10px] font-bold text-white bg-red-500">${planningPendingCount > 99 ? '99+' : planningPendingCount}</span>` : ''}</span>
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
        const phaseIsFuture = isFuturePhaseRange(computedRange);
        const phaseIsCurrent = isCurrentPhaseRange(computedRange);
        const phaseIsPast = isPastPhaseRange(computedRange);
        const phaseCanEdit = !isPlanApproved || (isPlanRevisionMode && phaseIsFuture);
        
        const desc = formatPhaseDurationRange(phaseInfo.duration, computedRange.startDate, computedRange.endDate);
        const colorClass = colorGradients[index % colorGradients.length];
        const phaseId = `dynamic-phase-${index}`;

        // Add "Fase X" Tab Button
        const phaseActivityPendingCount = getActivityPendingApprovalCount(phaseItems);
        const tabBtn = `
            <button onclick="switchManagerVnbTab('${phaseId}')" id="vnb-tab-btn-${phaseId}" class="vnb-tab-btn whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors duration-200">
                <span class="inline-flex items-center gap-2">${label}${phaseActivityPendingCount > 0 ? `<span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1 rounded-full text-[10px] font-bold text-white bg-red-500">${phaseActivityPendingCount > 99 ? '99+' : phaseActivityPendingCount}</span>` : ''}</span>
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
        renderPhaseActivityTable(`${phaseId}-body`, phaseItems, planningWaiting, phaseCanEdit, isPlanApproved, phaseIsFuture, isPlanningPhase);

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
        } else if (isPlanRevisionMode && phaseIsFuture) {
            phaseContentHtml = `
                <div id="vnb-tab-content-${phaseId}" class="vnb-phase-tab-content hidden space-y-6">
                    <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-4 border border-emerald-100">
                            <i class="fas fa-pen-to-square text-3xl text-amber-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Fase ${label} Belum Berjalan</h3>
                        <p class="text-gray-500 max-w-md">Fase ini belum berjalan, jadi rencana aktivitasnya masih bisa diedit. Gunakan tab <b>Plan</b> untuk menyesuaikan detail aktivitas fase ini.</p>
                    </div>
                </div>
            `;
        } else if (isPlanRevisionMode && (phaseIsCurrent || phaseIsPast)) {
            phaseContentHtml = `
                <div id="vnb-tab-content-${phaseId}" class="vnb-phase-tab-content hidden space-y-6">
                    <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-4 border border-amber-100">
                            <i class="fas fa-lock text-3xl text-amber-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">${phaseIsPast ? 'Fase Sudah Berlalu' : 'Fase Sedang Berjalan'}</h3>
                        <p class="text-gray-500 max-w-md">Fase ini sudah tidak dapat diedit lagi. Hanya fase yang belum berjalan yang bisa direvisi pada mode Ubah Plan.</p>
                    </div>
                </div>
            `;
        } else if (phaseIsCurrent) {
            let phaseRowsHtml = '';

            phaseItems.forEach(item => {
                const behaviourMatch = (item.activity_title || '').match(/^([^-]+)/);
                const behaviour = behaviourMatch ? behaviourMatch[1].trim() : (item.activity_title || '-');
                const rows = getActivityRows(item);
                const deliverables = splitDeliverables(item.deliverables || '-');

                rows.forEach((row, idx) => {
                    const rowStatus = getActivityRowStatus(item, row);
                    const isSubmittedForReview = ['waiting_approval', 'submitted'].includes(rowStatus);
                    const isRevised = rowStatus === 'revision_required';
                    const isApproved = rowStatus === 'completed';
                    const actionHtml = isApproved
                        ? `<div class="flex flex-col items-end gap-2"><span class="inline-flex items-center justify-center h-10 px-3 rounded-lg text-xs font-semibold bg-green-50 text-green-700 border border-green-200 shadow-sm whitespace-nowrap">Disetujui</span>${row.revision_notes ? `<button type="button" onclick="showActivityRevisionNotes('${escapeHtml(String(item.id))}', ${idx})" class="text-xs font-semibold text-amber-600 hover:text-amber-700 underline">Lihat Revisi</button>` : ''}</div>`
                        : isRevised
                            ? `<div class="flex flex-col items-end gap-2"><span class="inline-flex items-center justify-center h-10 px-3 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm whitespace-nowrap">Direvisi</span><button type="button" onclick="showActivityRevisionNotes('${escapeHtml(String(item.id))}', ${idx})" class="text-xs font-semibold text-amber-600 hover:text-amber-700 underline">Lihat Revisi</button></div>`
                            : isSubmittedForReview
                                ? `<div class="flex items-center justify-end gap-2"><button onclick="approveActivityRow(${item.id}, ${idx})" class="inline-flex items-center justify-center w-11 h-11 text-white rounded-lg transition-all shadow-sm hover:shadow-md bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800" title="Setujui" aria-label="Setujui"><i class="fas fa-check text-[11px]"></i></button><button onclick="openActivityRevisionModal(${item.id}, ${idx})" class="inline-flex items-center justify-center w-11 h-11 text-white rounded-lg transition-all shadow-sm hover:shadow-md bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700" title="Revisi" aria-label="Revisi"><i class="fas fa-pen text-[11px]"></i></button></div>`
                                : `<div class="flex justify-end"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-200">Draft</span></div>`;

                    phaseRowsHtml += `
                        <tr class="hover:bg-gray-50 align-top ${isApproved ? 'bg-green-50/40' : isRevised ? 'bg-amber-50/40' : isSubmittedForReview ? 'bg-emerald-50/30' : ''}">
                            ${idx === 0 ? `<td class="px-4 py-4 font-semibold text-gray-800 border-b border-gray-100 w-40" rowspan="${rows.length}">${escapeHtml(behaviour)}</td>` : ''}
                            <td class="px-4 py-4 text-xs border-b border-gray-100 w-64 text-gray-700">${escapeHtml(row.integration_text || '-').replace(/\n/g, '<br>')}</td>
                            <td class="px-4 py-4 text-xs border-b border-gray-100 text-gray-600 min-w-[180px]">${escapeHtml(deliverables[idx] || deliverables[0] || '-').replace(/\n/g, '<br>')}</td>
                            <td class="px-4 py-4 border-b border-gray-100 min-w-[240px]">
                                <div class="text-xs text-gray-700 leading-relaxed bg-white border border-gray-200 rounded-lg p-3">${escapeHtml(row.activity_description || '-')}</div>
                                ${isRevised && row.revision_notes ? `<div class="text-xs text-amber-700 mt-2 bg-amber-50 p-2 rounded border border-amber-100"><i class="fas fa-exclamation-circle mr-1"></i><strong>Revisi:</strong> ${escapeHtml(row.revision_notes)}</div>` : ''}
                            </td>
                            <td class="px-4 py-4 border-b border-gray-100 w-44 text-xs text-gray-700">${formatActivityDateValue(row.activity_date || '-')}</td>
                            <td class="px-4 py-4 border-b border-gray-100 w-48">${renderActivityEvidenceLinks(item, idx)}</td>
                            <td class="px-4 py-4 text-right whitespace-nowrap border-b border-gray-100 align-top w-28">${actionHtml}</td>
                        </tr>
                    `;
                });
            });

            phaseContentHtml = `
                <div id="vnb-tab-content-${phaseId}" class="vnb-phase-tab-content hidden space-y-6">
                    <div class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-b border-gray-200/50">
                            <h2 class="text-lg font-semibold text-gray-900">Monitoring ${label}</h2>
                            <p class="text-sm text-gray-600 mt-1">Tabel aktivitas per baris untuk fase yang sedang berjalan. Manager dapat menyetujui atau mengembalikan baris untuk revisi.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-modern w-full text-left">
                                <thead>
                                    <tr>
                                        <th>Value</th>
                                        <th>Integrasi Pengukuran</th>
                                        <th>Rencana Aktivitas</th>
                                        <th>Implementasi</th>
                                        <th>Tanggal Implementasi</th>
                                        <th>Bukti Implementasi</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${phaseRowsHtml || `<tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada aktivitas untuk fase ini.</td></tr>`}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        } else {
            phaseContentHtml = `
                <div id="vnb-tab-content-${phaseId}" class="vnb-phase-tab-content hidden space-y-6">
                    <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                            <i class="fas fa-lock text-3xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Monitoring ${label}</h3>
                        <p class="text-gray-500 max-w-md">Fase ini belum aktif untuk monitoring. Tabel aktivitas akan muncul saat fase ini sedang berjalan.</p>
                    </div>
                </div>
            `;
        }
        if (phasesRoot) phasesRoot.insertAdjacentHTML('beforeend', phaseContentHtml);
    });

    updateApprovalProgress();
    updateSubmitButtonState(detail);
    switchManagerVnbTab('plan'); // Default active tab
}

function switchManagerVnbTab(tabId) {
    // Update tab buttons
    document.querySelectorAll('.vnb-tab-btn').forEach(btn => {
        if (btn.id === `vnb-tab-btn-${tabId}`) {
            btn.classList.add('border-blue-500', 'text-blue-600', 'font-bold');
            btn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'font-medium');
        } else {
            btn.classList.remove('border-blue-500', 'text-blue-600', 'font-bold');
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'font-medium');
        }
    });

    // Update tab contents
    document.querySelectorAll('.vnb-phase-tab-content').forEach(content => {
        if (content.id === `vnb-tab-content-${tabId}`) {
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

function renderPhaseActivityTable(bodyId, items, planningWaiting = false, phaseCanEdit = true, isPlanApproved = false, phaseIsFuture = false, isPlanningPhase = false) {
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
            const rowEditable = phaseCanEdit;
            if (rowState && rowState.action === 'approve') {
                const isRevised = rowState.was_revised === true;
                rowBgClass = isRevised ? 'bg-amber-50/50' : 'bg-green-50/50';
                if (planningWaiting && isPlanningPhase && rowEditable) {
                    actionHtml = `<div class="flex flex-col items-end gap-2"><span class="inline-flex items-center justify-center h-10 px-3 rounded-lg text-xs font-semibold bg-green-50 text-green-700 border border-green-200 shadow-sm whitespace-nowrap">Disetujui</span><div class="flex gap-2"><button onclick="editPendingDecision(${item.id}, ${idx})" class="text-[11px] font-medium text-blue-600 hover:text-blue-800 transition underline">Revisi</button><button onclick="cancelPendingDecision(${item.id}, ${idx})" class="text-[11px] font-medium text-gray-400 hover:text-gray-600 transition underline">Batalkan</button></div></div>`;
                } else if (isPlanApproved && rowEditable) {
                    actionHtml = `<div class="flex justify-end"><button onclick="startInlineEdit(${item.id}, ${idx})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition flex items-center justify-center shadow-sm" title="Ubah Plan"><i class="fas fa-pen"></i></button></div>`;
                } else {
                    actionHtml = `<div class="flex justify-end"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-green-700 bg-green-50 border border-green-200">Disetujui</span></div>`;
                }
            } else if (rowState && rowState.action === 'revise') {
                rowBgClass = 'bg-amber-50/50';
                actionHtml = rowEditable
                    ? `<div class="flex flex-col items-end"><span class="text-xs font-semibold px-3 py-1.5 rounded-lg backdrop-blur-sm text-amber-700 bg-amber-100 border border-opacity-30 shadow-sm">Direvisi</span><div class="flex gap-2 mt-1"><button onclick="editPendingDecision(${item.id}, ${idx})" class="text-[11px] font-medium text-blue-600 hover:text-blue-800 transition underline">Edit</button><button onclick="cancelPendingDecision(${item.id}, ${idx})" class="text-[11px] font-medium text-gray-400 hover:text-gray-600 transition underline">Batalkan</button></div></div>`
                    : `<div class="flex justify-end"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-amber-700 bg-amber-100 border border-amber-200">Direvisi</span></div>`;
            } else if (isPlanApproved && !rowEditable) {
                const badgeText = phaseIsFuture ? 'Terkunci' : 'Sudah Berjalan';
                const badgeColor = phaseIsFuture ? 'text-slate-700 bg-slate-100' : 'text-gray-700 bg-gray-100';
                actionHtml = `<div class="flex justify-end"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold ${badgeColor} border border-gray-200">${badgeText}</span></div>`;
            } else {
                if (isPlanApproved && phaseCanEdit) {
                    actionHtml = `<div class="flex justify-end gap-2"><button onclick="startInlineEdit(${item.id}, ${idx})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition flex items-center justify-center shadow-sm" title="Edit Plan"><i class="fas fa-pen"></i></button></div>`;
                } else {
                    actionHtml = `<div class="flex justify-end gap-2"><button onclick="approvePlanningRow(${item.id}, ${idx})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-green-600 hover:bg-green-50 hover:border-green-200 transition flex items-center justify-center shadow-sm" title="Setujui"><i class="fas fa-check"></i></button><button onclick="startInlineEdit(${item.id}, ${idx})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition flex items-center justify-center shadow-sm" title="Edit"><i class="fas fa-pen"></i></button></div>`;
                }
            }
            html += `<tr class="${rowBgClass} hover:bg-gray-50/80 transition-colors" data-item-id="${item.id}" data-sub-idx="${idx}" data-edit-mode="false" data-editable="${rowEditable ? 'true' : 'false'}">${idx === 0 ? `<td class="px-4 py-4"><span class="font-bold text-gray-900">${behavior}</span></td>` : '<td class="px-4 py-4"></td>'}<td class="px-4 py-4"><p class="text-xs text-gray-600 leading-relaxed">${displayIntegration}</p></td><td class="px-4 py-4"><p class="text-xs text-gray-700 font-medium leading-relaxed">${displayDeliverables || '-'}</p></td><td class="px-4 py-4 text-right">${actionHtml}</td></tr>`;
        });
    });
    tbody.innerHTML = html;
}

function splitActivityEntries(value) {
    return String(value || '')
        .split('\n---\n')
        .map(part => part.trim())
        .filter((part, index, list) => !(index === list.length - 1 && part === ''));
}

function formatActivityDateValue(value) {
    if (!value || value === '-') return '-';
    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) return escapeHtml(value);
    return parsed.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function switchActivityReviewTab(tabId) {
    document.querySelectorAll('.activity-review-tab-btn').forEach(btn => {
        if (btn.id === `activity-review-tab-btn-${tabId}`) {
            btn.classList.add('border-green-500', 'text-green-600', 'font-bold');
            btn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'font-medium');
        } else {
            btn.classList.remove('border-green-500', 'text-green-600', 'font-bold');
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'font-medium');
        }
    });

    document.querySelectorAll('.activity-review-tab-content').forEach(content => {
        if (content.id === `activity-review-tab-content-${tabId}`) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
}

function getActivityRows(activity) {
    if (Array.isArray(activity?.activity_rows) && activity.activity_rows.length > 0) {
        return activity.activity_rows;
    }

    const integrations = splitIntegrations(activity?.description || '-');
    const integrationList = Array.isArray(integrations) && integrations.length > 0 ? integrations : ['-'];
    const descList = String(activity?.activity_description || '').split('\n---\n').map(s => s.trim());
    const dateList = String(activity?.activity_date || '').split('\n---\n').map(s => s.trim());

    return integrationList.map((integration, idx) => ({
        integration_index: idx,
        integration_text: integration,
        activity_description: descList[idx] === '-' ? '' : (descList[idx] || ''),
        activity_date: dateList[idx] === '-' ? '' : (dateList[idx] || ''),
        submission_status: activity?.submission_status || 'draft',
        revision_notes: activity?.revision_notes || null,
    }));
}

function getActivityRowStatus(activity, row) {
    return String(row?.submission_status || activity?.submission_status || 'draft').toLowerCase();
}

function getActivityRowStatusLabel(status) {
    const map = {
        draft: 'Draft',
        waiting_approval: 'Menunggu approval',
        submitted: 'Diajukan',
        completed: 'Disetujui',
        revision_required: 'Direvisi',
    };

    return map[String(status || '').toLowerCase()] || 'Tidak diketahui';
}

function renderActivityEvidenceLinks(item, rowIndex) {
    const evidences = (item.evidences || []).filter(ev => ev.description === 'Integration ' + rowIndex);
    if (!evidences.length) {
        return '<div class="text-xs text-gray-400 italic">Belum ada bukti</div>';
    }

    return evidences.map(ev => {
        const url = ev.preview_url || (ev.file_path ? `/storage/${ev.file_path}` : '');
        return `<a href="${escapeHtml(url)}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs text-[#144600] font-bold hover:underline bg-[#144600]/10 px-2.5 py-1 rounded mt-1.5 border border-[#144600]/20"><i class="fas fa-file-download"></i> ${escapeHtml(ev.file_name || 'file')}</a>`;
    }).join('<div class="mt-2"></div>');
}

function renderActivityReviewContent(detail) {
    const container = document.getElementById('vnb-activity-soon');
    if (!container) return;

    const isPlanApproved = detail.plan?.status === 'approved' || detail.plan?.status === 'in_progress' || detail.plan?.status === 'completed';
    if (!isPlanApproved) {
        container.innerHTML = `
            <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-4 border border-blue-100">
                    <i class="fas fa-lock text-3xl text-blue-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">VnB Activity Belum Tersedia</h3>
                <p class="text-gray-500 max-w-md">VnB Plan dari Employee ini belum disetujui sepenuhnya. Silakan lakukan review di tab <b>Plan</b> terlebih dahulu sebelum aktivitas dapat direview.</p>
            </div>
        `;
        container.classList.remove('hidden');
        return;
    }

    const items = (detail.items || []).filter(item => Array.isArray(getActivityRows(item)));
    if (!items.length) {
        container.innerHTML = `
            <div class="card-glass rounded-xl p-10 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                    <i class="fas fa-clipboard-check text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Aktivitas</h3>
                <p class="text-gray-500 max-w-md">Aktivitas untuk employee ini belum tersedia.</p>
            </div>
        `;
        container.classList.remove('hidden');
        return;
    }

    const itemsByPhase = {};
    items.forEach(item => {
        const phase = extractPhase(item.activity_title || '');
        if (!itemsByPhase[phase]) itemsByPhase[phase] = [];
        itemsByPhase[phase].push(item);
    });

    const phases = Object.keys(itemsByPhase).sort((a, b) => {
        const numA = (a.match(/\d+/) || [999])[0];
        const numB = (b.match(/\d+/) || [999])[0];
        return numA - numB;
    });

    if (!activityReviewActivePhase || !phases.includes(activityReviewActivePhase)) {
        activityReviewActivePhase = phases[0];
    }

    let tabsHtml = '';
    let contentHtml = '';

    phases.forEach((phase, index) => {
        const phaseId = `phase-${index}`;
        const isActive = phase === activityReviewActivePhase;
        const phasePendingCount = getActivityPendingApprovalCount(itemsByPhase[phase] || []);
        tabsHtml += `
            <button onclick="switchActivityReviewTab('${phaseId}')" id="activity-review-tab-btn-${phaseId}" class="activity-review-tab-btn whitespace-nowrap py-4 px-1 border-b-2 ${isActive ? 'border-green-500 text-green-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'} text-sm focus:outline-none transition-colors duration-200">
                <span class="inline-flex items-center gap-2">${escapeHtml(phase)}${phasePendingCount > 0 ? `<span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1 rounded-full text-[10px] font-bold text-white bg-red-500">${phasePendingCount > 99 ? '99+' : phasePendingCount}</span>` : ''}</span>
            </button>
        `;

        const phaseItems = itemsByPhase[phase];
        let tableRows = '';

        phaseItems.forEach(item => {
            const behaviourMatch = (item.activity_title || '').match(/^([^-]+)/);
            const behaviour = behaviourMatch ? behaviourMatch[1].trim() : (item.activity_title || '-');
            const rows = getActivityRows(item);
            const deliverables = splitDeliverables(item.deliverables || '-');

            rows.forEach((row, idx) => {
                const rowStatus = getActivityRowStatus(item, row);
                const isSubmittedForReview = ['waiting_approval', 'submitted'].includes(rowStatus);
                const isRevised = rowStatus === 'revision_required';
                const isApproved = rowStatus === 'completed';
                const actionHtml = isApproved
                    ? `<div class="flex flex-col items-end gap-2"><span class="inline-flex items-center justify-center h-10 px-3 rounded-lg text-xs font-semibold bg-green-50 text-green-700 border border-green-200 shadow-sm whitespace-nowrap">Disetujui</span>${row.revision_notes ? `<button type="button" onclick="showActivityRevisionNotes('${escapeHtml(String(item.id))}', ${idx})" class="text-xs font-semibold text-amber-600 hover:text-amber-700 underline">Lihat Revisi</button>` : ''}</div>`
                    : isRevised
                        ? `<div class="flex flex-col items-end gap-2"><span class="inline-flex items-center justify-center h-10 px-3 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm whitespace-nowrap">Direvisi</span><button type="button" onclick="showActivityRevisionNotes('${escapeHtml(String(item.id))}', ${idx})" class="text-xs font-semibold text-amber-600 hover:text-amber-700 underline">Lihat Revisi</button></div>`
                        : isSubmittedForReview
                            ? `<div class="flex items-center justify-end gap-2"><button onclick="approveActivityRow(${item.id}, ${idx})" class="inline-flex items-center justify-center w-11 h-11 text-white rounded-lg transition-all shadow-sm hover:shadow-md bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800" title="Setujui" aria-label="Setujui"><i class="fas fa-check text-[11px]"></i></button><button onclick="openActivityRevisionModal(${item.id}, ${idx})" class="inline-flex items-center justify-center w-11 h-11 text-white rounded-lg transition-all shadow-sm hover:shadow-md bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700" title="Revisi" aria-label="Revisi"><i class="fas fa-pen text-[11px]"></i></button></div>`
                            : `<div class="flex justify-end"><span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-200">Draft</span></div>`;

                tableRows += `
                    <tr class="hover:bg-gray-50 align-top ${isApproved ? 'bg-green-50/40' : isRevised ? 'bg-amber-50/40' : isSubmittedForReview ? 'bg-emerald-50/30' : ''}">
                        ${idx === 0 ? `<td class="px-4 py-4 font-semibold text-gray-800 border-b border-gray-100 w-40" rowspan="${rows.length}">${escapeHtml(behaviour)}</td>` : ''}
                        <td class="px-4 py-4 text-xs border-b border-gray-100 w-64 text-gray-700">${escapeHtml(row.integration_text || '-').replace(/\n/g, '<br>')}</td>
                        <td class="px-4 py-4 text-xs border-b border-gray-100 text-gray-600 min-w-[180px]">${escapeHtml(deliverables[idx] || deliverables[0] || '-').replace(/\n/g, '<br>')}</td>
                        <td class="px-4 py-4 border-b border-gray-100 min-w-[240px]">
                            <div class="text-xs text-gray-700 leading-relaxed bg-white border border-gray-200 rounded-lg p-3">${escapeHtml(row.activity_description || '-')}</div>
                            ${isRevised && row.revision_notes ? `<div class="text-xs text-amber-700 mt-2 bg-amber-50 p-2 rounded border border-amber-100"><i class="fas fa-exclamation-circle mr-1"></i><strong>Revisi:</strong> ${escapeHtml(row.revision_notes)}</div>` : ''}
                        </td>
                        <td class="px-4 py-4 border-b border-gray-100 w-44 text-xs text-gray-700">${formatActivityDateValue(row.activity_date || '-')}</td>
                        <td class="px-4 py-4 border-b border-gray-100 w-48">${renderActivityEvidenceLinks(item, idx)}</td>
                        <td class="px-4 py-4 text-right whitespace-nowrap border-b border-gray-100 align-top w-28">${actionHtml}</td>
                    </tr>
                `;
            });
        });

        contentHtml += `
            <div id="activity-review-tab-content-${phaseId}" class="activity-review-tab-content ${isActive ? '' : 'hidden'} space-y-6">
                <div class="card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-b border-gray-200/50">
                        <h2 class="text-lg font-semibold text-gray-900">${escapeHtml(phase)}</h2>
                        <p class="text-sm text-gray-600 mt-1">Tabel aktivitas per baris untuk review manager. Setujui atau kembalikan baris yang perlu revisi.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-modern w-full text-left">
                            <thead>
                                <tr>
                                    <th>Value</th>
                                    <th>Integrasi Pengukuran</th>
                                    <th>Rencana Aktivitas</th>
                                    <th>Implementasi</th>
                                    <th>Tanggal Implementasi</th>
                                    <th>Bukti Implementasi</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = `
        <div class="card-glass rounded-xl p-6 md:p-8 overflow-hidden relative space-y-6">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">VnB Activity Review</h3>
                        <p class="text-sm text-gray-500 mt-1">Tabel aktivitas per baris, mirip employee. Manager hanya bisa setujui atau revisi.</p>
                    </div>
                    <div class="text-xs font-semibold px-3 py-1.5 rounded-full bg-green-100 text-green-700 border border-green-200">${phases.length} fase</div>
                </div>
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Activity review tabs">
                        ${tabsHtml}
                    </nav>
                </div>
            </div>
            <div class="relative z-10">
                ${contentHtml}
            </div>
        </div>
    `;
    container.classList.remove('hidden');
}

async function approveActivityRow(itemId, rowIndex) {
    const item = detailData?.items?.find(entry => entry.id === itemId);
    if (!item) return;
    const confirmed = await showConfirm('Setujui baris aktivitas ini?', 'Konfirmasi Approval');
    if (!confirmed) return;

    const res = await apiPost(`/api/vnb-activities/${itemId}/approve`, { row_index: rowIndex });
    if (res?.success) {
        showAlert(res.message || 'Baris aktivitas disetujui', 'success');
        await loadDetail();
    } else {
        showAlert(res?.message || res?.error || 'Gagal approve baris aktivitas', 'error');
    }
}

function openActivityRevisionModal(itemId, rowIndex) {
    const existing = document.getElementById('activity-revision-modal');
    if (existing) existing.remove();

    const item = detailData?.items?.find(entry => entry.id === itemId);
    if (!item) return;

    const row = getActivityRows(item)[rowIndex] || {};

    const modal = document.createElement('div');
    modal.id = 'activity-revision-modal';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[9999] p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 space-y-4 animate-fade-in">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Request Revisi Aktivitas</h3>
                    <p class="text-sm text-gray-500 mt-1">${escapeHtml(item.activity_title || '-')}</p>
                </div>
                <button type="button" onclick="closeActivityRevisionModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
            </div>
            <div class="rounded-lg border border-amber-100 bg-amber-50 p-3 text-xs text-amber-800">
                ${escapeHtml(row.integration_text || '-')}
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan revisi</label>
                <textarea id="activity-revision-notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Tuliskan catatan revisi..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeActivityRevisionModal()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm font-medium">Batal</button>
                <button type="button" onclick="submitActivityRevision(${itemId}, ${rowIndex})" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Revisi
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeActivityRevisionModal();
    });
}

function closeActivityRevisionModal() {
    const modal = document.getElementById('activity-revision-modal');
    if (modal) modal.remove();
}

function showActivityRevisionNotes(itemId, rowIndex) {
    const item = detailData?.items?.find(entry => entry.id === parseInt(itemId, 10));
    const row = item ? getActivityRows(item)[rowIndex] : null;
    const notes = row?.revision_notes || item?.revision_notes || 'Tidak ada catatan revisi';
    showAlert(notes, 'info');
}

async function submitActivityRevision(itemId, rowIndex) {
    const notesEl = document.getElementById('activity-revision-notes');
    const notes = notesEl ? notesEl.value.trim() : '';
    if (!notes) {
        showAlert('Isi catatan revisi terlebih dahulu', 'error');
        return;
    }

    const res = await apiPost(`/api/vnb-activities/${itemId}/request-revision`, { row_index: rowIndex, revision_notes: notes });
    if (res?.success) {
        showAlert(res.message || 'Revisi dikirim ke Employee', 'success');
        closeActivityRevisionModal();
        await loadDetail();
    } else {
        showAlert(res?.message || res?.error || 'Gagal request revisi', 'error');
    }
}

function updateApprovalProgress() {
    // Count both pending decisions AND existing snapshots
    let decidedCount = 0;
    
    if (detailData?.items) {
        detailData.items.forEach(item => {
            const integrations = splitIntegrations(item.description || '-');
            const snapshot = item.manager_review_snapshot && typeof item.manager_review_snapshot === 'object' ? item.manager_review_snapshot : {};
            
            integrations.forEach((integration, idx) => {
                const rowKey = buildPlanningRowKey(item.id, idx);
                // Count if there's a pending decision OR an existing snapshot
                if (pendingDecisions[rowKey] || snapshot[idx]) {
                    decidedCount++;
                }
            });
        });
    }
    
    const totalCount = totalPlanningSubRows || 1;
    const percentage = Math.round((decidedCount / totalCount) * 100);
    
    // Update progress bar visual
    const progressBar = document.getElementById('vnb-progress-bar');
    const progressPercent = document.getElementById('vnb-progress-percent');
    
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }
    if (progressPercent) {
        progressPercent.textContent = percentage;
    }
    
    // Update submit button state
    const submitBtn = document.getElementById('batch-submit-btn-header');
    if (submitBtn) {
        if (isApprovedPlanStatus(detailData?.plan?.status)) {
            const hasPendingRevision = Object.keys(pendingDecisions).length > 0;
            submitBtn.disabled = !hasPendingRevision;
            if (submitBtn.disabled) {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } else {
            if (percentage === 100) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }
    
    // Update approve-all button state - disable when 100%
    const approveAllBtn = document.getElementById('approve-all-btn');
    if (approveAllBtn) {
        if (percentage === 100) {
            approveAllBtn.disabled = true;
            approveAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            approveAllBtn.disabled = false;
            approveAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    // Update VnB Activity badge
    updateVnbApprovalBadge();
}

function updateVnbApprovalBadge() {
    const badge = document.getElementById('vnb-approval-badge');
    const badgeCount = document.getElementById('vnb-badge-count');
    
    if (!badge || !badgeCount) return;
    const pendingCount = getActivityPendingApprovalCount(detailData?.items || []);

    if (pendingCount > 0) {
        badge.classList.remove('hidden');
        badgeCount.classList.remove('invisible');
        badgeCount.textContent = pendingCount > 99 ? '99+' : pendingCount;
        badge.style.minWidth = '18px';
        badge.style.width = 'auto';
        badge.style.height = '20px';
        badge.style.padding = '0 4px';
    } else {
        badge.classList.add('hidden');
    }
}

function showConfirmationModal() {
    const isRevisionFlow = isApprovedPlanStatus(detailData?.plan?.status) && isPlanRevisionMode;

    if (isRevisionFlow) {
        showConfirm('Simpan revisi plan VnB ini?', 'Konfirmasi Revisi').then(async (confirmed) => {
            if (confirmed) {
                await submitBatchReview();
            }
        });
        return;
    }

    const modal = document.createElement('div');
    modal.id = 'confirmation-modal-overlay';
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6 space-y-4 animate-fade-in">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mx-auto">
                <i class="fas fa-question text-2xl text-blue-600"></i>
            </div>
            <h3 class="text-lg font-bold text-center text-gray-900">Konfirmasi Penetapan Rencana Aktivitas</h3>
            <p class="text-sm text-gray-600 text-center">Anda yakin menetapkan rencana aktivitas ini sesuai dengan yang telah direviu?</p>
            <div class="flex gap-3 pt-4">
                <button onclick="closeConfirmationModal()" class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition text-sm">Batal</button>
                <button onclick="proceedBatchReview()" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Yakin
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeConfirmationModal();
    });
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmation-modal-overlay');
    if (modal) modal.remove();
}

async function proceedBatchReview() {
    closeConfirmationModal();
    await submitBatchReview();
}

async function submitBatchReview() {
    const planId = detailData?.plan?.id;
    if (!planId) return;
    const pendingCount = Object.keys(pendingDecisions).length;
    const isRevisionFlow = isApprovedPlanStatus(detailData?.plan?.status);
    if (isRevisionFlow) {
        if (!isPlanRevisionMode) {
            showAlert('Klik Ubah Plan VnB terlebih dahulu untuk mulai revisi.', 'info');
            return;
        }
        if (pendingCount === 0) {
            showAlert('Belum ada perubahan revisi yang perlu disimpan.', 'info');
            return;
        }
    } else if (pendingCount < totalPlanningSubRows) {
        showAlert('Harap berikan keputusan (Setujui atau Revisi) untuk seluruh rencana aktivitas terlebih dahulu.', 'error');
        return;
    }
    const reviews = Object.values(pendingDecisions).map(row => ({
        id: parseInt(row.item_id),
        sub_idx: parseInt(row.sub_idx),
        action: row.action,
        integration_text: row.integration_text || '',
        deliverables_text: row.deliverables_text || '',
    }));
    
    // Validate reviews data
    const validReviews = reviews.filter(r => {
        if (!r.id || !Number.isInteger(r.id)) {
            console.warn('Invalid review: missing or non-integer id', r);
            return false;
        }
        if (r.sub_idx === undefined || r.sub_idx === null || !Number.isInteger(r.sub_idx)) {
            console.warn('Invalid review: missing or non-integer sub_idx', r);
            return false;
        }
        return true;
    });
    
    if (validReviews.length !== reviews.length) {
        showAlert('Data review tidak valid. Beberapa item memiliki index yang tidak valid.', 'error');
        return;
    }
    
    if (!validReviews.length) return;
    const btn = document.getElementById('batch-submit-btn-header');
    const orgHtml = btn.innerHTML;
    btn.innerHTML = 'Sedang Memproses...';
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    const res = await apiPost(`/api/manager/plans/${planId}/batch-review`, { reviews: validReviews });
    btn.innerHTML = orgHtml;
    btn.disabled = false;
    btn.classList.remove('opacity-50', 'cursor-not-allowed');
    if (res && res.success) {
        pendingDecisions = {};
        clearPendingDecisionsLocal();
        isPlanRevisionMode = false;
        toggleBatchButtonBar();
        showAlert(res.message || (isRevisionFlow ? 'Revisi plan berhasil disimpan' : 'Review berhasil disimpan'), 'success');
        loadDetail();
    } else {
        showAlert(res?.message || res?.error || 'Gagal menyimpan review', 'error');
    }
}

async function approvePlanningRow(itemId, subIdx) {
    itemId = parseInt(itemId) || 0;
    subIdx = parseInt(subIdx) || 0;
    if (isNaN(itemId) || isNaN(subIdx)) return;
    const item = detailData?.items?.find(i => i.id === itemId);
    if (!item) return;
    const integrations = splitIntegrations(item.description || '-');
    const deliverables = splitDeliverables(item.deliverables || '-');
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    pendingDecisions[rowKey] = {
        item_id: parseInt(itemId),
        sub_idx: parseInt(subIdx),
        action: 'approve',
        integration_text: integrations[subIdx] || '-',
        deliverables_text: deliverables[subIdx] || '-',
    };
    savePendingDecisionsLocal();
    updateApprovalProgress();
    if (detailData && detailData.items) renderPhaseContent(detailData);
    toggleBatchButtonBar();
}



function startInlineEdit(itemId, subIdx) {
    itemId = parseInt(itemId) || 0;
    subIdx = parseInt(subIdx) || 0;
    if (isNaN(itemId) || isNaN(subIdx)) return;
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    const item = detailData?.items?.find(i => i.id === itemId);
    if (!item) return;
    
    const integrations = splitIntegrations(item.description || '-');
    const rencanaList = splitDeliverables(item.deliverables || '-');
    const pendingDecision = pendingDecisions[rowKey] || null;
    const savedDecision = item.manager_review_snapshot?.[subIdx] || null;
    const currentIntegration = pendingDecision?.integration_text ?? savedDecision?.integration_text ?? integrations[subIdx] ?? '';
    const currentDeliverables = pendingDecision?.deliverables_text ?? savedDecision?.deliverables_text ?? (rencanaList[subIdx] || '');
    
    // Find the row and make it editable
    const rows = document.querySelectorAll('table tbody tr');
    let rowIndex = 0;
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].dataset.itemId == itemId && rows[i].dataset.subIdx == subIdx) {
            rowIndex = i;
            break;
        }
    }
    
    // Mark this row as in edit mode
    const row = rows[rowIndex];
    if (!row) return;

    if (row.dataset.editable === 'false') {
        showAlert('Fase ini sudah berjalan, hanya fase yang belum dimulai yang bisa diubah.', 'warning');
        return;
    }
    
    // Store original values
    row.dataset.editMode = 'true';
    row.dataset.originalDeliverables = currentDeliverables;
    
    // Update cells to editable textareas
    const cells = row.querySelectorAll('td');
    if (cells.length >= 4) {
        // Integration cell (index 1) - read-only display (dari framework) - tetap tampilan default
        cells[1].innerHTML = `<p class="text-xs text-gray-600 leading-relaxed">${currentIntegration}</p>`;
        // Deliverables cell (index 2) - EDITABLE ONLY
        cells[2].innerHTML = `<textarea class="w-full border border-blue-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" rows="3" data-deliverables-input>${currentDeliverables}</textarea>`;
        // Action cell (index 3)
        cells[3].innerHTML = `<div class="flex justify-end gap-2"><button onclick="saveInlineEdit(${itemId}, ${subIdx})" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 font-medium flex items-center gap-1"><i class="fas fa-save"></i> Simpan</button><button onclick="cancelInlineEdit(${itemId}, ${subIdx})" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-lg text-xs hover:bg-gray-400 font-medium">Batal</button></div>`;
    }
    
    // Focus on deliverables input (only editable field)
    const deliverableInput = cells[2]?.querySelector('textarea');
    if (deliverableInput) setTimeout(() => deliverableInput.focus(), 0);
}

function saveInlineEdit(itemId, subIdx) {
    itemId = parseInt(itemId) || 0;
    subIdx = parseInt(subIdx) || 0;
    if (isNaN(itemId) || isNaN(subIdx)) {
        showAlert('Error: Data tidak valid', 'error');
        return;
    }
    const rows = document.querySelectorAll('table tbody tr');
    let row = null;
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].dataset.itemId == itemId && rows[i].dataset.subIdx == subIdx) {
            row = rows[i];
            break;
        }
    }
    
    if (!row) return;
    
    const cells = row.querySelectorAll('td');
    const deliverablesText = cells[2]?.querySelector('textarea')?.value.trim() || '';
    
    if (!deliverablesText) {
        showAlert('Rencana aktivitas tidak boleh kosong', 'error');
        return;
    }
    
    // Get current integration value from item data (tidak dari textarea karena read-only)
    const item = detailData?.items?.find(i => i.id === itemId);
    if (!item) return;
    const integrations = splitIntegrations(item.description || '-');
    const integrationText = integrations[subIdx] || '-';
    
    const rowKey = buildPlanningRowKey(itemId, subIdx);
    pendingDecisions[rowKey] = {
        item_id: parseInt(itemId),
        sub_idx: parseInt(subIdx),
        action: 'revise',
        integration_text: integrationText,
        deliverables_text: deliverablesText,
    };
    
    savePendingDecisionsLocal();
    row.dataset.editMode = 'false';
    updateApprovalProgress();
    updateApproveAllButtonState();
    if (detailData && detailData.items) renderPhaseContent(detailData);
    toggleBatchButtonBar();
}

function cancelInlineEdit(itemId, subIdx) {
    itemId = parseInt(itemId) || 0;
    subIdx = parseInt(subIdx) || 0;
    if (isNaN(itemId) || isNaN(subIdx)) return;
    const rows = document.querySelectorAll('table tbody tr');
    let row = null;
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].dataset.itemId == itemId && rows[i].dataset.subIdx == subIdx) {
            row = rows[i];
            break;
        }
    }
    
    if (!row) return;
    
    row.dataset.editMode = 'false';
    
    if (detailData && detailData.items) renderPhaseContent(detailData);
}

function togglePlanRevisionMode() {
    if (!isApprovedPlanStatus(detailData?.plan?.status)) return;
    isPlanRevisionMode = true;
    renderPhaseContent(detailData);
    updateApprovalProgress();
    updateApproveAllButtonState();
}

function cancelPlanRevisionMode() {
    if (!isApprovedPlanStatus(detailData?.plan?.status)) return;
    isPlanRevisionMode = false;
    pendingDecisions = {};
    clearPendingDecisionsLocal();
    renderPhaseContent(detailData);
    updateApprovalProgress();
    updateApproveAllButtonState();
}

function editPendingDecision(itemId, subIdx, behaviorEnc, integrasiEnc, rencanaEnc) {
    itemId = parseInt(itemId) || 0;
    subIdx = parseInt(subIdx) || 0;
    if (isNaN(itemId) || isNaN(subIdx)) return;
    startInlineEdit(itemId, subIdx);
}

async function submitApproveAll() {
    // Don't allow if already 100% approved
    if (document.getElementById('approve-all-btn').disabled) {
        showAlert('Gunakan Ubah Plan VnB untuk merevisi fase yang belum berjalan', 'info');
        return;
    }
    const planId = detailData?.plan?.id; if (!planId) { showAlert('Plan ID tidak ditemukan', 'error'); return; }
    if (isApprovedPlanStatus(detailData?.plan?.status)) {
        togglePlanRevisionMode();
        return;
    }
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
                // Hanya approve item yang BELUM direvisi. Item yang sudah direvisi (action='revise') tetap direvise
                const existingDecision = pendingDecisions[rowKey];
                if (existingDecision && existingDecision.action === 'revise') {
                    // Skip - biarkan tetap direvisi
                    return;
                }
                pendingDecisions[rowKey] = {
                    item_id: parseInt(item.id),
                    sub_idx: parseInt(idx),
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
    const isApprovedPlan = isApprovedPlanStatus(detailData?.plan?.status);
    const hasRevisions = Object.values(pendingDecisions).some(decision => decision.action === 'revise');
    
    if (!canReviewPlanning) {
        approveAllBtn.disabled = true;
        approveAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
        approveAllBtn.title = 'Anda tidak memiliki otorisasi untuk mereview planning employee ini.';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        submitBtn.title = 'Anda tidak memiliki otorisasi untuk mereview planning employee ini.';
    } else {
        if (isApprovedPlan) {
            approveAllBtn.disabled = false;
            approveAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            approveAllBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            approveAllBtn.classList.add('bg-amber-500', 'hover:bg-amber-600');
            approveAllBtn.title = 'Ubah plan VnB yang belum berjalan';
            approveAllBtn.innerHTML = '<i class="fas fa-pen-to-square text-xs"></i> Ubah Plan VnB';
            approveAllBtn.setAttribute('onclick', 'togglePlanRevisionMode()');

            submitBtn.innerHTML = '<i class="fas fa-save text-xs"></i> Simpan Revisi';
            submitBtn.disabled = !hasRevisions;
            if (submitBtn.disabled) {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } else if (hasRevisions) {
            approveAllBtn.disabled = true;
            approveAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
            approveAllBtn.title = 'Tidak bisa Setujui Semua karena ada item yang sudah direvisi. Silakan selesaikan revisi terlebih dahulu.';
        } else {
            approveAllBtn.disabled = false;
            approveAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            approveAllBtn.classList.remove('bg-amber-500', 'hover:bg-amber-600');
            approveAllBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            approveAllBtn.innerHTML = '<i class="fas fa-check-double text-xs"></i> Setujui Semua';
            approveAllBtn.setAttribute('onclick', 'submitApproveAll()');
            approveAllBtn.title = '';
            submitBtn.innerHTML = '<i class="fas fa-save text-xs"></i> Simpan';
        }
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    loadDetail();
});
</script>
@endpush
@endsection