

<?php $__env->startSection('title', 'Dashboard - VnB Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 px-4">
    <!-- Plan Status Check Container -->
    <div id="plan-status-container"></div>

    <!-- Dashboard Content (hidden until plan is approved) -->
    <div id="dashboard-content" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs text-gray-500 uppercase">Total New Hire</p>
                <p id="stat-total" class="text-3xl font-bold text-gray-900 mt-2">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs text-gray-500 uppercase">Active</p>
                <p id="stat-active" class="text-3xl font-bold mt-2" style="color: #144600;">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs text-gray-500 uppercase">Completed</p>
                <p id="stat-completed" class="text-3xl font-bold text-green-600 mt-2">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs text-gray-500 uppercase">Canceled</p>
                <p id="stat-canceled" class="text-3xl font-bold text-red-600 mt-2">0</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Progress per Phase</h2>
                    <button onclick="loadDashboard()" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded">Refresh</button>
                </div>
                <div id="phase-progress" class="space-y-4"></div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Progress per Behaviour</h2>
                <div id="behaviour-progress" class="space-y-3 max-h-[380px] overflow-y-auto pr-1"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4" style="border-color: #144600; background-color: #f0fdf4;">
            <p class="text-sm" style="color: #144600;">
                Dashboard ini menampilkan ringkasan berdasarkan role Anda.
                Data phase dan behaviour otomatis menyesuaikan scope pengguna.
            </p>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function renderPlanStatusLock(planStatus) {
    const container = document.getElementById('plan-status-container');
    const dashboardContent = document.getElementById('dashboard-content');
    
    // Determine message based on plan status
    let message = '';
    let buttonText = '';
    let icon = '';
    let bgColor = '';
    let textColor = '';
    
    if (!planStatus || planStatus === 'not_found') {
        // No plan created yet
        message = 'Rencana Aktivitas Belum Dibuat';
        icon = '📋';
        bgColor = 'bg-blue-50';
        textColor = 'text-blue-900';
        buttonText = 'Buat Rencana VnB';
    } else if (planStatus === 'draft' || planStatus === 'revision_draft') {
        // Still in draft
        message = 'Rencana Aktivitas Masih Draft';
        icon = '✏️';
        bgColor = 'bg-yellow-50';
        textColor = 'text-yellow-900';
        buttonText = 'Lanjutkan Rencana';
    } else if (planStatus === 'waiting_manager_approval' || planStatus === 'submitted') {
        // Waiting for approval
        message = 'Menunggu Persetujuan Rencana';
        icon = '⏳';
        bgColor = 'bg-purple-50';
        textColor = 'text-purple-900';
        buttonText = 'Lihat Status';
    } else if (planStatus === 'revision_requested') {
        // Needs revision
        message = 'Rencana Aktivitas Perlu Perbaikan';
        icon = '🔄';
        bgColor = 'bg-red-50';
        textColor = 'text-red-900';
        buttonText = 'Perbaiki Rencana';
    } else if (planStatus === 'approved') {
        // Plan is approved, show dashboard
        container.innerHTML = '';
        dashboardContent.style.display = 'block';
        loadDashboard();
        return;
    }
    
    // Show lock screen
    dashboardContent.style.display = 'none';
    container.innerHTML = `
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-5xl mb-4">${icon}</div>
            <h2 class="text-2xl font-bold mb-2 ${textColor}">${message}</h2>
            <p class="text-gray-600 mb-6 max-w-lg mx-auto">
                ${
                    planStatus === 'not_found' ? 'Anda perlu membuat rencana aktivitas VnB sebelum dapat mengakses fitur dashboard dan aktivitas.' :
                    planStatus === 'draft' || planStatus === 'revision_draft' ? 'Selesaikan dan ajukan rencana Anda untuk dapat mengakses fitur dashboard dan aktivitas.' :
                    planStatus === 'waiting_manager_approval' || planStatus === 'submitted' ? 'Rencana aktivitas Anda sedang dalam proses review oleh manager. Anda dapat mengakses fitur setelah rencana disetujui.' :
                    planStatus === 'revision_requested' ? 'Rencana Anda belum disetujui. Silakan lakukan revisi sesuai masukan yang diberikan.' :
                    'Selesaikan pengaturan rencana VnB Anda terlebih dahulu.'
                }
            </p>
            <a href="/vnb-plans" class="inline-block px-8 py-3 rounded-lg text-white font-semibold transition" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#0a2c00'" onmouseout="this.style.backgroundColor='#144600'">
                ${buttonText}
            </a>
        </div>
    `;
}

async function checkPlanStatus() {
    try {
        console.log('🔍 Checking plan status from dashboard...');
        
        // Check if apiGet exists
        if (typeof apiGet !== 'function') {
            console.error('❌ apiGet function not found');
            renderPlanStatusLock('not_found');
            return;
        }
        
        const res = await apiGet('/api/vnb-plans/new-hire');
        console.log('📊 Plan status response:', res);
        
        // Handle response
        let planStatus = 'not_found';
        
        if (!res.success) {
            // API returned error
            console.log('⚠️ API returned success: false');
            planStatus = 'not_found';
        } else if (res.data && res.data.status) {
            // Successfully got plan with status
            planStatus = res.data.status;
            console.log('✅ Found plan with status:', planStatus);
        } else {
            console.log('⚠️ Response has success but no data.status');
            planStatus = 'not_found';
        }
        
        renderPlanStatusLock(planStatus);
    } catch (e) {
        console.error('❌ Error checking plan status:', {
            message: e.message,
            error: e,
            stack: e.stack
        });
        
        // Show error on page temporarily
        const container = document.getElementById('plan-status-container');
        container.innerHTML = `
            <div class="bg-red-50 rounded-lg shadow p-8 text-center text-red-900">
                <p class="text-sm mb-4">Error loading plan status. Please try refreshing the page.</p>
                <p class="text-xs text-red-600">${e.message}</p>
            </div>
        `;
    }
}

function progressBar(percent, color = '#144600') {
    return `
        <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
            <div class="h-2 ${color}" style="width: ${percent}%"></div>
        </div>
    `;
}

function renderPhaseProgress(phases) {
    const container = document.getElementById('phase-progress');
    const keys = ['phase_1', 'phase_2', 'phase_3'];
    container.innerHTML = keys.map((key, idx) => {
        const p = phases[key] || { total: 0, completed: 0, percent: 0 };
        return `
            <div>
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">Phase ${idx + 1}</span>
                    <span class="text-gray-500">${p.completed}/${p.total} (${p.percent}%)</span>
                </div>
                ${progressBar(p.percent, '#144600')}
            </div>
        `;
    }).join('');
}

function renderBehaviourProgress(items) {
    const container = document.getElementById('behaviour-progress');
    if (!items.length) {
        container.innerHTML = '<p class="text-sm text-gray-400">Belum ada data behaviour.</p>';
        return;
    }

    container.innerHTML = items.map(row => `
        <div>
            <div class="flex items-center justify-between text-sm mb-1">
                <span class="font-medium text-gray-700">${row.behaviour}</span>
                <span class="text-gray-500">${row.completed}/${row.total} (${row.percent}%)</span>
            </div>
            ${progressBar(row.percent, 'bg-green-600')}
        </div>
    `).join('');
}

async function loadDashboard() {
    try {
        const res = await apiGet('/api/dashboard/overview');
        const stats = res.stats || {};
        document.getElementById('stat-total').textContent = stats.total || 0;
        document.getElementById('stat-active').textContent = stats.active || 0;
        document.getElementById('stat-completed').textContent = stats.completed || 0;
        document.getElementById('stat-canceled').textContent = stats.canceled || 0;
        renderPhaseProgress(res.phase_progress || {});
        renderBehaviourProgress(res.behaviour_progress || []);
    } catch (e) {
        showAlert('Gagal memuat dashboard overview', 'error');
    }
}

// Check plan status on page load
checkPlanStatus();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/dashboard.blade.php ENDPATH**/ ?>