

<?php $__env->startSection('title', 'Rencana VnB - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'Rencana VnB'); ?>
<?php $__env->startSection('page_subtitle', 'Susun rencana pengembangan nilai dan perilaku untuk periode berjalan.'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header Section -->
    <div class="card-glass rounded-xl p-6 md:p-8 overflow-hidden relative">
        <!-- Background accent decoration -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-stretch relative z-10">
            <!-- Left Column: Employee & Manager Info (1/4) -->
            <div class="md:col-span-1 flex flex-col justify-center border-r border-gray-200/50 pr-8">
                <div class="space-y-3">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight leading-none" id="employee-name"><?php echo e(auth()->user()->name); ?></h2>
                        <div id="career-stage-info" class="mt-1" style="display: none;"></div>
                    </div>
                    <div class="space-y-2.5 pt-1">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-widest font-black text-gray-400">Manager Fungsional</span>
                            <span class="text-sm font-bold text-gray-700 leading-tight" id="manager-functional">-</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-widest font-black text-gray-400">Manager Operasional</span>
                            <span class="text-sm font-bold text-gray-700 leading-tight" id="manager-operational">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Progress & Actions (3/4) -->
            <div class="md:col-span-3 flex flex-col justify-between pl-2">
                <!-- Top Part: Progress Bar -->
                <div id="progress-container" class="w-full" style="display: none;">
                    <div class="flex items-end justify-between mb-4">
                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-gray-800">Progres Rencana VnB</h3>
                            <p class="text-xs text-gray-500 font-medium">Lengkapi seluruh rencana aktivitas Anda untuk periode ini</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-baseline justify-end gap-1">
                                <span class="text-5xl font-black text-green-600 tracking-tighter leading-none"><span id="progress-percent">0</span></span>
                                <span class="text-xl font-bold text-green-500">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modern Progress Bar -->
                    <div class="w-full bg-gray-100 h-5 rounded-full overflow-hidden shadow-inner border border-gray-200/50 p-1">
                        <div id="progress-bar" class="h-full bg-gradient-to-r from-green-400 via-green-500 to-green-600 rounded-full transition-all duration-1000 ease-out shadow-lg relative" style="width: 0%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-6">
                        <div class="flex items-center gap-4">
                            <p class="text-sm font-black text-gray-700 flex items-center bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 shadow-sm">
                                <i class="fas fa-clipboard-list mr-2.5 text-green-500 text-base"></i>
                                <span id="filled-count">0</span> / <span id="total-count">0</span> Aktivitas
                            </p>
                            <div id="deadline-info" class="text-xs" style="display: none;"></div>
                        </div>

                        <!-- Action Buttons (On the same line) -->
                        <div class="flex items-center gap-3">
                            <button id="save-draft-btn" onclick="saveDraft()" class="btn-secondary px-5 py-2.5 flex items-center gap-2 hover:shadow-md transition-all duration-200" title="Simpan sebagai draft">
                                <i class="fas fa-floppy-disk text-xs"></i>
                                <span class="text-sm font-bold">Simpan Draft</span>
                            </button>
                            <button id="submit-plan-btn" onclick="submitPlan()" class="btn-primary px-7 py-2.5 flex items-center gap-2 shadow-lg hover:scale-[1.02] active:scale-95 transition-all duration-200" title="Ajukan rencana untuk approval">
                                <i class="fas fa-paper-plane text-xs"></i>
                                <span class="text-sm font-bold">Ajukan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase Tables -->
    <div id="phases-container" class="space-y-6">
        <!-- Dynamically generated phase boxes -->
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPlan = null;
let hasUnsavedChanges = false;
let editingIntegrations = new Set(); // Track which (itemId_integIdx) pairs are in edit mode
let phases = {}; // Will be populated dynamically from API response

// Warn about unsaved changes
window.addEventListener('beforeunload', (e) => {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'Ada perubahan yang belum disimpan. Apakah Anda yakin ingin meninggalkan halaman ini?';
    }
});

/**
 * Generate phase boxes dynamically based on phases array from API
 */
function renderPhaseBoxes(phasesList) {
    const container = document.getElementById('phases-container');
    container.innerHTML = ''; // Clear existing boxes
    
    // Build phases map for renderItemsByPhase
    phases = {};
    let phaseCursorDate = parseIsoDate(currentPlan?.induction_date || phasesList?.[0]?.start_date || null);
    
    const colorGradients = [
        'from-blue-500/10 to-blue-600/10',
        'from-amber-500/10 to-amber-600/10',
        'from-green-500/10 to-green-600/10',
        'from-purple-500/10 to-purple-600/10',
        'from-red-500/10 to-red-600/10',
    ];
    
    phasesList.forEach((phaseInfo, index) => {
        const bodyId = `phase-${phaseInfo.phase}-body`;
        phases[phaseInfo.phase] = bodyId;
        const computedRange = buildPhaseDateRange(phaseInfo, phaseCursorDate);
        phaseCursorDate = computedRange.nextCursorDate;
        
        const colorClass = colorGradients[index % colorGradients.length];
        
        const phaseBox = document.createElement('div');
        phaseBox.className = 'card-glass rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300';
        phaseBox.innerHTML = `
            <div class="px-6 py-4 bg-gradient-to-r ${colorClass} border-b border-gray-200/50">
                <h2 class="text-lg font-semibold text-gray-900">${phaseInfo.label}</h2>
                <p class="text-sm text-gray-600 mt-1">${formatPhaseDurationRange(phaseInfo.duration, computedRange.startDate, computedRange.endDate)}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th class="w-1/6">Value</th>
                            <th class="w-1/3">Integrasi Pengukuran</th>
                            <th class="w-1/3">Rencana Aktivitas</th>
                            <th class="w-1/6">Status</th>
                        </tr>
                    </thead>
                    <tbody id="${bodyId}">
                        <tr><td colspan="4" class="text-center py-8 text-gray-400">Memuat template...</td></tr>
                    </tbody>
                </table>
            </div>
        `;
        
        container.appendChild(phaseBox);
    });
}

async function loadEmployeePlan() {
    try {
        const res = await apiGet('/api/vnb-plans/employee');
        if (!res.success) {
            showAlert(res.message || 'Gagal memuat plan', 'error');
            return;
        }
        
        currentPlan = res.data;
        currentPlan.deadline = res.deadline;
        currentPlan.career_stage = res.career_stage;
        currentPlan.induction_date = res.induction_date;
        hasUnsavedChanges = false;
        
        // Render phase boxes dynamically if phases data is available
        if (res.phases && res.phases.length > 0) {
            renderPhaseBoxes(res.phases);
        }
        
        renderItemsByPhase();
        updateProgressBar();
        renderEmployeeHeader();
        
        // Disable buttons if waiting for approval
        const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
        const saveDraftBtn = document.getElementById('save-draft-btn');
        const submitPlanBtn = document.getElementById('submit-plan-btn');
        
        if (saveDraftBtn) saveDraftBtn.disabled = isWaitingApproval;
        if (submitPlanBtn) submitPlanBtn.disabled = isWaitingApproval;
        
        // Add visual feedback
        if (isWaitingApproval && saveDraftBtn) {
            saveDraftBtn.classList.add('opacity-50');
            saveDraftBtn.style.cursor = 'not-allowed';
            submitPlanBtn.classList.add('opacity-50');
            submitPlanBtn.style.cursor = 'not-allowed';
        }
    } catch (e) {
        console.error('Error loading plan:', e);
        showAlert('Gagal memuat rencana VnB', 'error');
    }
}

/**
 * Render Employee & Manager Info in the header
 */
function renderEmployeeHeader() {
    if (!currentPlan) return;

    // Display career stage
    if (currentPlan.career_stage) {
        // Convert snake_case to Title Case
        let stage = currentPlan.career_stage
            .split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
        
        // Special formatting for specific stages
        if (currentPlan.career_stage === 'manage_self_staff') {
            stage = 'Manage Self (Staff)';
        } else if (currentPlan.career_stage === 'manage_self_non_staff') {
            stage = 'Manage Self (Non-Staff)';
        } else if (currentPlan.career_stage === 'manage_others') {
            stage = 'Manage Others';
        } else if (currentPlan.career_stage === 'manage_managers' || currentPlan.career_stage === 'manage_manager') {
            stage = 'Manage Managers';
        }
        
        const careerStageEl = document.getElementById('career-stage-info');
        if (careerStageEl) {
            careerStageEl.innerHTML = `<span class="text-xs font-bold text-green-600">${stage}</span>`;
            careerStageEl.style.display = 'block';
        }
    }
    
    // Display manager names
    if (currentPlan.employee) {
        const functionalName = (currentPlan.employee.manager_functional?.name || currentPlan.employee.managerFunctional?.name) || '-';
        const operationalName = (currentPlan.employee.manager_operational?.name || currentPlan.employee.managerOperational?.name) || '-';
        
        const functionalEl = document.getElementById('manager-functional');
        const operationalEl = document.getElementById('manager-operational');
        
        if (functionalEl) functionalEl.textContent = functionalName;
        if (operationalEl) operationalEl.textContent = operationalName;
        
        const nameEl = document.getElementById('employee-name');
        if (nameEl && currentPlan.employee.name) {
            nameEl.textContent = currentPlan.employee.name;
        }
    }

    // Display deadline
    if (currentPlan.deadline) {
        const deadlineEl = document.getElementById('deadline-info');
        if (!deadlineEl) return;

        const deadlineDate = new Date(currentPlan.deadline);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        deadlineDate.setHours(0, 0, 0, 0);
        
        const daysRemaining = Math.ceil((deadlineDate - today) / (1000 * 60 * 60 * 24));
        const formattedDate = deadlineDate.toLocaleDateString('id-ID', { 
            year: 'numeric', month: 'long', day: 'numeric' 
        });
        
        let countdownText = daysRemaining < 0 ? 'Lewat Batas Waktu' : (daysRemaining === 0 ? 'Hari ini' : `${daysRemaining} hari lagi`);
        let badgeColor = daysRemaining < 0 ? 'red' : 'amber';
        let badgeIcon = daysRemaining < 0 ? 'fa-times-circle' : 'fa-exclamation-triangle';

        deadlineEl.innerHTML = `
            <div class="inline-flex items-center gap-2 bg-${badgeColor}-50 text-${badgeColor}-700 px-2.5 py-1 rounded-md font-semibold border border-${badgeColor}-200 shadow-sm">
                <i class="fas ${badgeIcon} text-[10px]"></i>
                <span class="whitespace-nowrap">Deadline: ${formattedDate} (${countdownText})</span>
            </div>
        `;
        deadlineEl.style.display = 'block';
    }
}

function renderItemsByPhase() {
    if (!currentPlan?.items) {
        Object.values(phases).forEach(bodyId => {
            document.getElementById(bodyId).innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Belum ada item.</td></tr>';
        });
        return;
    }

    // Group items by phase
    const itemsByPhase = {};
    Object.keys(phases).forEach(phase => {
        itemsByPhase[phase] = [];
    });

    currentPlan.items.forEach(item => {
        const phase = extractPhase(item.activity_title);
        if (itemsByPhase[phase]) {
            itemsByPhase[phase].push(item);
        }
    });

    // Render each phase table
    Object.entries(phases).forEach(([phase, bodyId]) => {
        const items = itemsByPhase[phase] || [];
        renderPhaseTable(bodyId, items);
    });
}

function getItemStatusConfig(item, planStatus, integrationRencana) {
    // Determine status for a specific integration based on its rencana content and plan status
    // integrationRencana: the specific rencana content for this integration
    
    // Check if THIS specific integration's rencana has content
    const hasThisRencana = integrationRencana && integrationRencana.trim().length > 0 && integrationRencana.trim() !== '-';
    
    if (!hasThisRencana) {
        // This integration has no rencana yet
        return {
            badge: '⚪',
            text: 'Belum ada Rencana VnB',
            bgColor: '#F3F4F6',
            textColor: '#6B7280',
            borderColor: '#D1D5DB',
            hasNotes: false,
            notes: ''
        };
    }
    
    // Has rencana, now check plan status
    if (planStatus === 'draft' || !currentPlan?.submitted_at) {
        return {
            badge: '📝',
            text: 'Belum diajukan',
            bgColor: '#FFFBEB',  
            textColor: '#B45309',
            borderColor: '#F59E0B',
            hasNotes: false,
            notes: ''
        };
    } else if (planStatus === 'waiting_manager_approval' || planStatus === 'submitted') {
        return {
            badge: '⏳',
            text: 'Menunggu approval',
            bgColor: '#EFF6FF',
            textColor: '#1E40AF',
            borderColor: '#3B82F6',
            hasNotes: false,
            notes: ''
        };
    } else if (planStatus === 'approved') {
        return {
            badge: '✅',
            text: 'Disetujui',
            bgColor: '#ECFDF5',
            textColor: '#065F46',
            borderColor: '#10B981',
            hasNotes: false,
            notes: ''
        };
    } else if (planStatus === 'revision_requested') {
        return {
            badge: '🔴',
            text: 'Revisi diminta',
            bgColor: '#FEE2E2',
            textColor: '#991B1B',
            borderColor: '#EF4444',
            hasNotes: true,
            notes: item.revision_notes || 'Tidak ada catatan'
        };
    }
    
    // Default
    return {
        badge: '⚪',
        text: 'Belum ada Rencana VnB',
        bgColor: '#F3F4F6',
        textColor: '#6B7280',
        borderColor: '#D1D5DB',
        hasNotes: false,
        notes: ''
    };
}

function renderPhaseTable(bodyId, items) {
    const tbody = document.getElementById(bodyId);
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-6 text-gray-400">Belum ada item untuk fase ini.</td></tr>';
        return;
    }

    let html = '';
    
    items.forEach((item, idx) => {
        const behaviour = extractBehaviour(item.activity_title);
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        const statusConfig = getItemStatusConfig(item, currentPlan?.status);
        
        // Debug: Log integration parsing
        if (item.id === 1) {
            console.log(`Item ${item.id}:`, {
                description: item.description,
                parsed_integrations: integrations,
                integration_list: integrationList,
                list_length: integrationList.length
            });
        }
        
        // Check if this item has been saved (has deliverables content)
        // Empty deliverables = not saved yet
        const isSaved = item.deliverables && item.deliverables.trim().length > 0 && item.deliverables.trim() !== '-';
        const deliverables = item.deliverables || '';
        
        // For each integration, create a row
        if (item.id === 1) console.log(`Rendering ${integrationList.length} integrations for item ${item.id}`);
        
        for (let integIdx = 0; integIdx < integrationList.length; integIdx++) {
            const integration = integrationList[integIdx];
            if (item.id === 1) console.log(`  Integration ${integIdx}: "${integration.substring(0, 50)}..."`);
            
            // Determine the specific rencana for this integration
            let thisIntegrationRencana = '';
            if (integIdx === 0) {
                const firstRencana = deliverables.split('\n---\n')[0] || '';
                thisIntegrationRencana = firstRencana;
            } else {
                const rencanaLines = isSaved ? item.deliverables.split('\n---\n') : [];
                thisIntegrationRencana = rencanaLines[integIdx] ? rencanaLines[integIdx].trim() : '';
            }
            
            // Get status for THIS specific integration based on its rencana
            const statusConfig = getItemStatusConfig(item, currentPlan?.status, thisIntegrationRencana);
            
            const textareaId = `act_${item.id}`;
            const statusCell = `<td class="px-4 py-3 w-1/6 align-top">
                <div class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium" style="background-color: ${statusConfig.bgColor}; color: ${statusConfig.textColor}; border-left: 3px solid ${statusConfig.borderColor};">
                    <span>${statusConfig.badge}</span>
                    <span>${statusConfig.text}</span>
                </div>
                ${statusConfig.hasNotes ? `<div class="mt-2 text-xs italic text-red-700 bg-red-50 p-2 rounded border-l-2 border-red-300">"${escapeHtml(statusConfig.notes)}"</div>` : ''}
            </td>`;
            
            if (integIdx === 0) {
                // First row: include behaviour and deliverables section
                // Integration column is always READ-ONLY (display only)
                
                // Check if THIS specific integration's rencana is saved
                const firstRencana = deliverables.split('\n---\n')[0] || '';
                const isThisIntegrationSaved = isSaved && firstRencana.trim().length > 0 && firstRencana.trim() !== '-';
                const isThisIntegrationEditing = editingIntegrations.has(`${item.id}_${integIdx}`);
                
                if (isThisIntegrationEditing) {
                    // EDIT MODE: Integration display-only, rencana is editable textarea
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium w-1/6 align-top">${escapeHtml(behaviour)}</td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <div class="flex gap-2 items-start">
                        <textarea id="${textareaId}" rows="3" class="flex-1 border border-blue-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rencana aktivitas..." onchange="hasUnsavedChanges = true;">${escapeHtml(firstRencana === '-' ? '' : firstRencana)}</textarea>
                        <button onclick="cancelEditIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded whitespace-nowrap self-start hover:bg-red-200">✕</button>
                    </div>
                </td>
                ${statusCell}
            </tr>
                    `;
                } else if (isThisIntegrationSaved) {
                    // SAVED MODE: Both display-only with Edit button for THIS integration only
                    const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
                    const editButton = !isWaitingApproval 
                        ? `<button onclick="editIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded whitespace-nowrap hover:bg-blue-200">✎ Edit</button>`
                        : '';
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium w-1/6 align-top">${escapeHtml(behaviour)}</td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <div class="flex justify-between gap-3 items-start">
                        <div class="flex-1 text-sm text-gray-800 bg-gray-50 rounded px-3 py-2">${escapeHtml(firstRencana).replace(/\n/g, '<br>')}</div>
                        ${editButton}
                    </div>
                </td>
                ${statusCell}
            </tr>
                    `;
                } else {
                    // UNSAVED MODE: Integration display-only, rencana empty textbox (NO button)
                    const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
                    const textareaDisabled = isWaitingApproval ? 'disabled' : '';
                    const textareaBgClass = isWaitingApproval ? 'bg-gray-50 cursor-not-allowed' : '';
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium w-1/6 align-top">${escapeHtml(behaviour)}</td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <textarea id="${textareaId}" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 ${textareaBgClass}" placeholder="Rencana aktivitas..." ${textareaDisabled} onchange="hasUnsavedChanges = true;"></textarea>
                </td>
                ${statusCell}
            </tr>
                    `;
                }
            } else {
                // Subsequent rows: integration (display only) + rencana (editable with own button)
                
                // Check if THIS specific integration's rencana is saved
                const rencanaLines = isSaved ? item.deliverables.split('\n---\n') : [];
                const thisRencana = rencanaLines[integIdx] ? rencanaLines[integIdx].trim() : '';
                const isThisIntegrationSaved = thisRencana.length > 0 && thisRencana !== '-';
                const isThisIntegrationEditing = editingIntegrations.has(`${item.id}_${integIdx}`);
                
                // Always render subsequent integrations (don't skip)
                
                if (isThisIntegrationEditing) {
                    // Edit mode - integration display-only, rencana editable
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <div class="flex gap-2 items-start">
                        <textarea id="plan_${item.id}_${integIdx}" rows="3" class="flex-1 border border-blue-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rencana aktivitas..." onchange="hasUnsavedChanges = true;">${escapeHtml(thisRencana === '-' ? '' : thisRencana)}</textarea>
                        <button onclick="cancelEditIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded whitespace-nowrap self-start hover:bg-red-200">✕</button>
                    </div>
                </td>
                ${statusCell}
            </tr>
                    `;
                } else if (isThisIntegrationSaved) {
                    // Saved mode for subsequent integrations - text display with Edit button for THIS integration
                    const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
                    const editButton = !isWaitingApproval 
                        ? `<button onclick="editIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded whitespace-nowrap hover:bg-blue-200">✎ Edit</button>`
                        : '';
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <div class="flex justify-between gap-3 items-start">
                        <div class="flex-1 text-sm text-gray-800 bg-gray-50 rounded px-3 py-2">${escapeHtml(thisRencana).replace(/\n/g, '<br>')}</div>
                        ${editButton}
                    </div>
                </td>
                ${statusCell}
            </tr>
                    `;
                } else {
                    // Unsaved mode for subsequent integrations - integration display only, rencana textbox (NO button)
                    const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
                    const textareaDisabled = isWaitingApproval ? 'disabled' : '';
                    const textareaBgClass = isWaitingApproval ? 'bg-gray-50 cursor-not-allowed' : '';
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-1/3 align-top">
                    <textarea id="plan_${item.id}_${integIdx}" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 ${textareaBgClass}" placeholder="Rencana aktivitas..." ${textareaDisabled} onchange="hasUnsavedChanges = true;"></textarea>
                </td>
                ${statusCell}
            </tr>
                    `;
                }
            }
        }
    });

    tbody.innerHTML = html;
}

function editIntegration(itemId, integIdx) {
    editingIntegrations.add(`${itemId}_${integIdx}`);
    renderItemsByPhase();
    
    // Focus textarea after render
    setTimeout(() => {
        const textarea = integIdx === 0 ? 
            document.getElementById(`act_${itemId}`) :
            document.getElementById(`plan_${itemId}_${integIdx}`);
        if (textarea) textarea.focus();
    }, 0);
}

function cancelEditIntegration(itemId, integIdx) {
    editingIntegrations.delete(`${itemId}_${integIdx}`);
    renderItemsByPhase();
}

function extractBehaviour(title) {
    const parts = title.split(' - Phase');
    return parts[0] || title;
}

function extractPhase(title) {
    // Handle new format: "Behaviour - Phase Fase 1 (1 Bulan)"
    let match = title.match(/Phase (Fase\s+\d+\s+\([^)]+\))/i);
    if (match) return match[1];
    
    // Handle old format: "Behaviour - Phase (1-3|4-6|6+)"
    match = title.match(/Phase (1-3|4-6|6\+)/i);
    if (match) return match[1];
    
    // Fallback: extract any phase-like pattern
    match = title.match(/(Fase\s+\d+.*?(?=\s*$|-))/i);
    if (match) return match[1].trim();
    
    // Final fallback
    return 'Unknown';
}

function formatPhaseDateRange(startDate, endDate) {
    if (!startDate || !endDate) {
        return '';
    }

    const formatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    const start = new Date(startDate);
    const end = new Date(endDate);

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return '';
    }

    const startLabel = start.toLocaleDateString('id-ID', formatOptions);
    const endLabel = end.toLocaleDateString('id-ID', formatOptions);

    return `${startLabel} - ${endLabel}`;
}

function formatPhaseDurationRange(duration, startDate, endDate) {
    const dateRange = formatPhaseDateRange(startDate, endDate);
    if (!dateRange) {
        return duration || '';
    }

    return `${duration || ''} (${dateRange})`;
}

function parseIsoDate(dateString) {
    if (!dateString) return null;

    const date = new Date(dateString);
    return Number.isNaN(date.getTime()) ? null : date;
}

function buildPhaseDateRange(phaseInfo, cursorDate) {
    const explicitStart = parseIsoDate(phaseInfo.start_date);
    const explicitEnd = parseIsoDate(phaseInfo.end_date);

    if (explicitStart && explicitEnd) {
        return {
            startDate: explicitStart,
            endDate: explicitEnd,
            nextCursorDate: new Date(explicitEnd.getTime() + (1000 * 60 * 60 * 24)),
        };
    }

    const startDate = explicitStart || cursorDate;
    if (!startDate) {
        return {
            startDate: null,
            endDate: null,
            nextCursorDate: null,
        };
    }

    const durationMonths = extractDurationMonths(phaseInfo.duration);
    const endDate = new Date(startDate.getTime());
    endDate.setMonth(endDate.getMonth() + durationMonths);

    return {
        startDate: startDate,
        endDate: endDate,
        nextCursorDate: new Date(endDate.getTime() + (1000 * 60 * 60 * 24)),
    };
}

function extractDurationMonths(durationText) {
    const match = String(durationText || '').match(/(\d+)/);
    const months = match ? parseInt(match[1], 10) : 1;
    return Number.isFinite(months) && months > 0 ? months : 1;
}

function parseIntegrations(description) {
    // Extract integration items from description (format: "int1 | int2")
    if (!description) return '-';
    
    // Split by pipe, trim whitespace, remove empty strings
    const parts = description
        .split('|')
        .map(s => s.trim())
        .filter(s => s.length > 0); // More robust filtering
    
    // Return '-' if no integrations found, otherwise join with newline
    return parts.length === 0 ? '-' : parts.join('\n');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateProgressBar() {
    if (!currentPlan?.items) return;
    
    // Calculate total planning slots and filled slots
    // Each integration = 1 planning slot
    let totalSlots = 0;
    let filledSlots = 0;
    
    currentPlan.items.forEach(item => {
        // Parse integrations to count slots
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        const intCount = integrationList.length;
        
        totalSlots += intCount;
        
        // Count how many integrations have saved rencana
        if (item.deliverables && item.deliverables.trim().length > 0 && item.deliverables.trim() !== '-') {
            const rencanaLines = item.deliverables.split('\n---\n');
            
            // Each rencana line represents a filled integration slot
            for (let i = 0; i < intCount && i < rencanaLines.length; i++) {
                if (rencanaLines[i].trim().length > 0 && rencanaLines[i].trim() !== '-') {
                    filledSlots++;
                }
            }
        }
    });
    
    const percentage = totalSlots > 0 ? Math.round((filledSlots / totalSlots) * 100) : 0;
    
    console.log(`Progress: ${filledSlots}/${totalSlots} = ${percentage}%`);
    
    // Update progress bar UI
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');
    const filledCountEl = document.getElementById('filled-count');
    const totalCountEl = document.getElementById('total-count');
    
    if (progressContainer) {
        progressContainer.style.display = totalSlots > 0 ? 'block' : 'none';
    }
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }
    if (progressPercent) {
        progressPercent.textContent = percentage;
    }
    if (filledCountEl) {
        filledCountEl.textContent = filledSlots;
    }
    if (totalCountEl) {
        totalCountEl.textContent = totalSlots;
    }
}

function collectUpdatedItems() {
    if (!currentPlan?.items) return [];
    
    return currentPlan.items.map((item) => {
        // Get main rencana aktivitas value (from first integration row)
        const mainTextarea = document.getElementById(`act_${item.id}`);
        
        // Parse integrations to know how many there are
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        
        let finalDeliverables = '';
        
        if (mainTextarea) {
            // Textareas exist in DOM - collect from them
            const mainRencana = mainTextarea.value || '';
            
            // Collect additional rencana for subsequent integrations
            const allRencanaList = [mainRencana];
            for (let i = 1; i < integrationList.length; i++) {
                const rencana = document.getElementById(`plan_${item.id}_${i}`)?.value || '';
                allRencanaList.push(rencana);
            }
            
            // Filter only non-empty rencana and join with separator
            const filledRencana = allRencanaList
                .filter(r => r.trim().length > 0)
                .join('\n---\n');
            
            finalDeliverables = filledRencana.trim().length === 0 ? '' : filledRencana;
        } else {
            // Textareas don't exist in DOM (already saved, just displaying)
            // Use existing deliverables from currentPlan
            finalDeliverables = item.deliverables || '';
        }
        
        return {
            id: item.id,
            implementation_date: item.implementation_date,
            deliverables: finalDeliverables,  // ALWAYS include this key
            behavior_metrics: item.behavior_metrics,
        };
    });
}

async function saveDraft() {
    if (!currentPlan?.id) {
        showAlert('Plan belum dimuat', 'error');
        return;
    }
    
    // Check if waiting for approval
    const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
    if (isWaitingApproval) {
        showAlert('Rencana sedang menunggu approval dari manager. Tidak bisa diubah.', 'warning');
        return;
    }

    const payload = {
        title: currentPlan.title,
        description: currentPlan.description,
        items: collectUpdatedItems(),
    };

    const res = await apiPost(`/api/vnb-plans/${currentPlan.id}/draft`, payload);
    
    if (res.success) {
        hasUnsavedChanges = false;
        editingIntegrations.clear(); // Clear all editing modes after save
        currentPlan = res.data;
        
        // Render items first
        renderItemsByPhase();
        updateProgressBar();
        
        // Get updated progress for notification
        let totalSlots = 0;
        let filledSlots = 0;
        
        currentPlan.items.forEach(item => {
            const integrations = parseIntegrations(item.description);
            const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
            const intCount = integrationList.length;
            
            totalSlots += intCount;
            
            if (item.deliverables && item.deliverables.trim().length > 0 && item.deliverables.trim() !== '-') {
                const rencanaLines = item.deliverables.split('\n---\n');
                for (let i = 0; i < intCount && i < rencanaLines.length; i++) {
                    if (rencanaLines[i].trim().length > 0 && rencanaLines[i].trim() !== '-') {
                        filledSlots++;
                    }
                }
            }
        });
        
        const percentage = totalSlots > 0 ? Math.round((filledSlots / totalSlots) * 100) : 0;
        showAlert(`✓ Draft berhasil disimpan (${filledSlots}/${totalSlots} rencana terisi - ${percentage}%)`);
    } else {
        showAlert(res.message || res.error || 'Gagal menyimpan draft', 'error');
    }
}

async function submitPlan() {
    if (!currentPlan?.id) {
        showAlert('Plan belum dimuat', 'error');
        return;
    }
    
    // Check if waiting for approval
    const isWaitingApproval = currentPlan?.status === 'waiting_manager_approval' || currentPlan?.status === 'submitted';
    if (isWaitingApproval) {
        showAlert('Rencana sedang menunggu approval dari manager. Tidak bisa diubah.', 'warning');
        return;
    }

    // Pastikan rencana disimpan terlebih dahulu jika ada yang masih di-edit
    const itemsToSave = collectUpdatedItems();
    if (itemsToSave && itemsToSave.length > 0) {
        // Ada perubahan yang belum disimpan, simpan dulu
        const saveRes = await apiPost(`/api/vnb-plans/${currentPlan.id}/draft`, {
            title: currentPlan.title,
            description: currentPlan.description,
            items: itemsToSave,
        });
        
        if (!saveRes.success) {
            showAlert(saveRes.message || saveRes.error || 'Gagal menyimpan draft sebelum submit', 'error');
            return;
        }
        
        // Update currentPlan dengan data terbaru
        currentPlan = saveRes.data;
    }

    // Validasi: hitung rencana kosong
    const emptyFields = [];
    
    currentPlan.items.forEach((item) => {
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        
        // Check each integration
        for (let integIdx = 0; integIdx < integrationList.length; integIdx++) {
            let textareaValue = '';
            
            // Setelah save, semua items seharusnya ada di currentPlan.deliverables
            // Tapi tetap cek DOM terlebih dahulu untuk kemungkinan perubahan belum tersimpan
            let textareaId;
            if (integIdx === 0) {
                textareaId = `act_${item.id}`;
            } else {
                textareaId = `plan_${item.id}_${integIdx}`;
            }
            
            const textarea = document.getElementById(textareaId);
            if (textarea) {
                // Textarea exists in DOM - get value from it
                textareaValue = textarea.value || '';
            } else {
                // Textarea doesn't exist - get from currentPlan.deliverables (already saved)
                if (item.deliverables && item.deliverables.trim().length > 0 && item.deliverables.trim() !== '-') {
                    const rencanaLines = item.deliverables.split('\n---\n');
                    textareaValue = rencanaLines[integIdx] ? rencanaLines[integIdx].trim() : '';
                }
            }
            
            if (!textareaValue || textareaValue.trim().length === 0) {
                emptyFields.push({ itemId: item.id, integIdx: integIdx });
            }
        }
    });
    
    // Jika ada field kosong, highlight dan tampilkan peringatan
    if (emptyFields.length > 0) {
        // Highlight empty textboxes (if they exist in DOM)
        emptyFields.forEach(({ itemId, integIdx }) => {
            let textareaId;
            if (integIdx === 0) {
                textareaId = `act_${itemId}`;
            } else {
                textareaId = `plan_${itemId}_${integIdx}`;
            }
            
            const textarea = document.getElementById(textareaId);
            if (textarea) {
                textarea.style.borderColor = '#DC2626';
                textarea.style.backgroundColor = '#FEE2E2';
                
                // Remove highlight after 3 seconds
                setTimeout(() => {
                    textarea.style.borderColor = '';
                    textarea.style.backgroundColor = '';
                }, 3000);
            }
        });
        
        // Show alert
        showAlert(`⚠️ Selesaikan ${emptyFields.length} rencana aktivitas yang masih kosong`, 'error');
        return;
    }

    if (!(await showConfirm('Yakin ingin mengajukan rencana VnB? Setelah diajukan, Anda tidak dapat mengubahnya.', 'Konfirmasi Pengajuan'))) {
        return;
    }

    const res = await apiPost(`/api/vnb-plans/${currentPlan.id}/submit-approval`, {});
    if (res.success) {
        showAlert(res.message || 'Rencana berhasil diajukan');
        currentPlan = res.data;
        renderItemsByPhase();
    } else {
        showAlert(res.message || res.error || 'Gagal mengajukan rencana', 'error');
    }
}

// Load plan ketika page muncul
loadEmployeePlan();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-plans/index.blade.php ENDPATH**/ ?>