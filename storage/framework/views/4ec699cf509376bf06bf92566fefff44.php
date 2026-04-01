

<?php $__env->startSection('title', 'Rencana VnB - VnB Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 px-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rencana VnB</h1>
            <div id="career-stage-info" class="mt-2" style="display: none;"></div>
            <div id="deadline-info" class="mt-3" style="display: none;"></div>
        </div>
        <div class="flex gap-3">
            <button onclick="saveDraft()" class="text-white px-4 py-2 rounded-lg text-sm transition" style="background-color: #37AA05; cursor: pointer;" onmouseover="this.style.backgroundColor='#2d8903'" onmouseout="this.style.backgroundColor='#37AA05'">
                <i class="fas fa-floppy-disk mr-1"></i> Simpan Draft
            </button>
            <button onclick="submitPlan()" class="text-white px-4 py-2 rounded-lg text-sm transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#0a2c00'" onmouseout="this.style.backgroundColor='#144600'">
                <i class="fas fa-paper-plane mr-1"></i> Ajukan
            </button>
        </div>
    </div>

    <!-- Progress Bar -->
    <div id="progress-container" class="bg-white rounded-lg shadow p-4" style="display: none;">
        <div class="flex justify-between items-center mb-2">
            <p class="text-sm font-semibold text-gray-700">Progress Pengisian</p>
            <p class="text-sm font-semibold text-gray-900"><span id="progress-percent">0</span>%</p>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progress-bar" class="bg-green-500 h-2 rounded-full transition-all" style="width: 0%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-2"><span id="filled-count">0</span> dari <span id="total-count">0</span> rencana aktivitas terisi</p>
    </div>

    <!-- Phase Tables -->
    <div id="phases-container" class="space-y-6">
        <!-- Fase 1 -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">FASE 1</h2>
                <p class="text-xs text-gray-600 mt-1">Bulan ke-1 hingga ke-3</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-1/5">Value</th>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-2/5">Integrasi Pengukuran</th>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-2/5">Rencana Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody id="phase-1-body" class="divide-y divide-gray-200 text-gray-700">
                        <tr><td colspan="3" class="text-center py-8 text-gray-400">Memuat template...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fase 2 -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">FASE 2</h2>
                <p class="text-xs text-gray-600 mt-1">Bulan ke-4 hingga ke-6</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-1/5">Value</th>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-2/5">Integrasi Pengukuran</th>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-2/5">Rencana Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody id="phase-2-body" class="divide-y divide-gray-200 text-gray-700">
                        <tr><td colspan="3" class="text-center py-8 text-gray-400">Memuat template...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fase 3 -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">FASE 3</h2>
                <p class="text-xs text-gray-600 mt-1">Bulan ke-7 hingga ke-12</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-1/5">Value</th>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-2/5">Integrasi Pengukuran</th>
                            <th class="px-3 py-2 text-left text-xs uppercase font-semibold text-gray-700 w-2/5">Rencana Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody id="phase-3-body" class="divide-y divide-gray-200 text-gray-700">
                        <tr><td colspan="3" class="text-center py-8 text-gray-400">Memuat template...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPlan = null;
let hasUnsavedChanges = false;
let editingIntegrations = new Set(); // Track which (itemId_integIdx) pairs are in edit mode

const phases = {
    '1-3': 'phase-1-body',
    '4-6': 'phase-2-body',
    '6+': 'phase-3-body'
};

// Warn about unsaved changes
window.addEventListener('beforeunload', (e) => {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'Ada perubahan yang belum disimpan. Apakah Anda yakin ingin meninggalkan halaman ini?';
    }
});

async function loadNewHirePlan() {
    try {
        const res = await apiGet('/api/vnb-plans/new-hire');
        if (!res.success) {
            showAlert(res.message || 'Gagal memuat plan', 'error');
            return;
        }
        
        currentPlan = res.data;
        currentPlan.deadline = res.deadline;
        currentPlan.career_stage = res.career_stage;
        hasUnsavedChanges = false;
        renderItemsByPhase();
        updateProgressBar();
        
        // Display career stage below title - with blue highlight
        if (res.career_stage) {
            // Convert snake_case to Title Case
            let stage = res.career_stage
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(' ');
            
            // Special formatting for specific stages
            if (res.career_stage === 'manage_self_staff') {
                stage = 'Manage Self (Staff)';
            } else if (res.career_stage === 'manage_self_non_staff') {
                stage = 'Manage Self (Non-Staff)';
            } else if (res.career_stage === 'manage_others') {
                stage = 'Manage Others';
            } else if (res.career_stage === 'manage_managers') {
                stage = 'Manage Managers';
            }
            
            const careerStageEl = document.getElementById('career-stage-info');
            if (careerStageEl) {
                careerStageEl.innerHTML = `<span style="display: inline-block; background-color: #3B82F6; color: white; padding: 4px 12px; border-radius: 6px; font-size: 14px; font-weight: 500;">${stage}</span>`;
                careerStageEl.style.display = 'block';
            }
        }
        
        // Display deadline with compact countdown badge
        if (res.deadline) {
            const deadlineEl = document.getElementById('deadline-info');
            const deadlineDate = new Date(res.deadline);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            deadlineDate.setHours(0, 0, 0, 0);
            
            // Calculate days remaining
            const daysRemaining = Math.ceil((deadlineDate - today) / (1000 * 60 * 60 * 24));
            
            const formattedDate = deadlineDate.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            const safeDaysRemaining = Math.max(daysRemaining, 0);
            const countdownText = safeDaysRemaining === 0 ? 'Hari ini' : `${safeDaysRemaining} hari lagi`;

            const countdownHTML = `
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #4B5563; font-size: 16px;">
                        <i class="fas fa-clock" style="font-size: 18px; color: #6B7280;"></i>
                        <span>Deadline:</span>
                        <span style="font-weight: 700; color: #1F2937;">${formattedDate}</span>
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 8px; width: fit-content; background-color: #FEF3C7; color: #D97706; padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 13px;"></i>
                        <span>${countdownText}</span>
                    </div>
                </div>
            `;
            
            deadlineEl.innerHTML = countdownHTML;
            deadlineEl.style.display = 'block';
        }
    } catch (e) {
        console.error('Error loading plan:', e);
        showAlert('Gagal memuat rencana VnB', 'error');
    }
}

function renderItemsByPhase() {
    if (!currentPlan?.items) {
        Object.values(phases).forEach(bodyId => {
            document.getElementById(bodyId).innerHTML = '<tr><td colspan="3" class="text-center py-8 text-gray-400">Belum ada item.</td></tr>';
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

function renderPhaseTable(bodyId, items) {
    const tbody = document.getElementById(bodyId);
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-6 text-gray-400">Belum ada item untuk fase ini.</td></tr>';
        return;
    }

    let html = '';
    
    items.forEach((item, idx) => {
        const behaviour = extractBehaviour(item.activity_title);
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        
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
            
            const textareaId = `act_${item.id}`;
            
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
                <td class="px-4 py-3 text-sm font-medium w-1/5 align-top">${escapeHtml(behaviour)}</td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <div class="flex gap-2 items-start">
                        <textarea id="${textareaId}" rows="3" class="flex-1 border border-blue-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rencana aktivitas..." onchange="hasUnsavedChanges = true;">${escapeHtml(firstRencana === '-' ? '' : firstRencana)}</textarea>
                        <button onclick="cancelEditIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded whitespace-nowrap self-start hover:bg-red-200">✕</button>
                    </div>
                </td>
            </tr>
                    `;
                } else if (isThisIntegrationSaved) {
                    // SAVED MODE: Both display-only with Edit button for THIS integration only
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium w-1/5 align-top">${escapeHtml(behaviour)}</td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <div class="flex justify-between gap-3 items-start">
                        <div class="flex-1 text-sm text-gray-800 bg-gray-50 rounded px-3 py-2">${escapeHtml(firstRencana).replace(/\n/g, '<br>')}</div>
                        <button onclick="editIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded whitespace-nowrap hover:bg-blue-200">✎ Edit</button>
                    </div>
                </td>
            </tr>
                    `;
                } else {
                    // UNSAVED MODE: Integration display-only, rencana empty textbox (NO button)
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium w-1/5 align-top">${escapeHtml(behaviour)}</td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <textarea id="${textareaId}" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Rencana aktivitas..." onchange="hasUnsavedChanges = true;"></textarea>
                </td>
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
                <td class="px-4 py-3 w-2/5 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <div class="flex gap-2 items-start">
                        <textarea id="plan_${item.id}_${integIdx}" rows="3" class="flex-1 border border-blue-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rencana aktivitas..." onchange="hasUnsavedChanges = true;">${escapeHtml(thisRencana === '-' ? '' : thisRencana)}</textarea>
                        <button onclick="cancelEditIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded whitespace-nowrap self-start hover:bg-red-200">✕</button>
                    </div>
                </td>
            </tr>
                    `;
                } else if (isThisIntegrationSaved) {
                    // Saved mode for subsequent integrations - text display with Edit button for THIS integration
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <div class="flex justify-between gap-3 items-start">
                        <div class="flex-1 text-sm text-gray-800 bg-gray-50 rounded px-3 py-2">${escapeHtml(thisRencana).replace(/\n/g, '<br>')}</div>
                        <button onclick="editIntegration(${item.id}, ${integIdx})" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded whitespace-nowrap hover:bg-blue-200">✎ Edit</button>
                    </div>
                </td>
            </tr>
                    `;
                } else {
                    // Unsaved mode for subsequent integrations - integration display only, rencana textbox (NO button)
                    html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <span class="text-xs text-gray-700">${escapeHtml(integration).replace(/\n/g, '<br>')}</span>
                </td>
                <td class="px-4 py-3 w-2/5 align-top">
                    <textarea id="plan_${item.id}_${integIdx}" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Rencana aktivitas..." onchange="hasUnsavedChanges = true;"></textarea>
                </td>
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
    const match = title.match(/Phase (1-3|4-6|6\+)/);
    return match ? match[1] : '1-3';
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
        
        // If main textarea doesn't exist in DOM, user didn't edit this item
        // Don't include deliverables to preserve existing data
        if (!mainTextarea) {
            return {
                id: item.id,
                implementation_date: item.implementation_date,
                behavior_metrics: item.behavior_metrics,
                // Deliberately omit deliverables to prevent overwriting existing data
            };
        }
        
        const mainRencana = mainTextarea.value || '';
        
        // Collect all integration textbox values
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        
        // Collect additional rencana for subsequent integrations (if any)
        const allRencanaList = [mainRencana];
        for (let i = 1; i < integrationList.length; i++) {
            const rencana = document.getElementById(`plan_${item.id}_${i}`)?.value || '';
            allRencanaList.push(rencana);
        }
        
        // Filter only non-empty rencana and join with separator
        const filledRencana = allRencanaList
            .filter(r => r.trim().length > 0)
            .join('\n---\n');
        
        // If all rencana are empty, explicitly set to empty string for clearing
        const finalDeliverables = filledRencana.trim().length === 0 ? '' : filledRencana;
        
        return {
            id: item.id,
            implementation_date: item.implementation_date,
            deliverables: finalDeliverables,
            behavior_metrics: item.behavior_metrics,
        };
    });
}

async function saveDraft() {
    if (!currentPlan?.id) {
        showAlert('Plan belum dimuat', 'error');
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

    // Validasi: hitung rencana kosong
    const emptyFields = [];
    
    currentPlan.items.forEach((item) => {
        const integrations = parseIntegrations(item.description);
        const integrationList = integrations === '-' ? ['-'] : integrations.split('\n').filter(s => s);
        
        // Check each integration
        for (let integIdx = 0; integIdx < integrationList.length; integIdx++) {
            let textareaValue = '';
            
            if (integIdx === 0) {
                textareaValue = document.getElementById(`act_${item.id}`)?.value || '';
            } else {
                textareaValue = document.getElementById(`plan_${item.id}_${integIdx}`)?.value || '';
            }
            
            if (!textareaValue || textareaValue.trim().length === 0) {
                emptyFields.push({ itemId: item.id, integIdx: integIdx });
            }
        }
    });
    
    // Jika ada field kosong, highlight dan tampilkan peringatan
    if (emptyFields.length > 0) {
        // Highlight empty textboxes
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

    if (!confirm('Yakin ingin mengajukan rencana VnB? Setelah diajukan, Anda tidak dapat mengubahnya.')) {
        return;
    }

    const res = await apiPost(`/api/vnb-plans/${currentPlan.id}/submit-approval`, {});
    if (res.success) {
        showAlert(res.message || 'Rencana berhasil diajukan');
        currentPlan = res.data;
    } else {
        showAlert(res.message || res.error || 'Gagal mengajukan rencana', 'error');
    }
}

// Load plan ketika page muncul
loadNewHirePlan();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb-plans/index.blade.php ENDPATH**/ ?>