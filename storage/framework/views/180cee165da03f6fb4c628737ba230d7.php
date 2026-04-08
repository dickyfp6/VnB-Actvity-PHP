

<?php $__env->startSection('title', 'Dashboard - VnB Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Plan Status Check Container -->
    <div id="plan-status-container"></div>

    <!-- Dashboard Content (hidden until plan is approved) -->
    <div id="dashboard-content" style="display: none;">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="card-glass rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total New Hire</p>
                        <p id="stat-total" class="text-4xl font-bold text-gray-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card-glass rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Active</p>
                        <p id="stat-active" class="text-4xl font-bold text-green-600 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                        <i class="fas fa-play-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card-glass rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Completed</p>
                        <p id="stat-completed" class="text-4xl font-bold text-emerald-600 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card-glass rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Canceled</p>
                        <p id="stat-canceled" class="text-4xl font-bold text-red-600 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Phase Progress -->
            <div class="card-glass rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Progress per Phase</h2>
                        <p class="text-sm text-gray-500 mt-1">Development timeline overview</p>
                    </div>
                    <button onclick="loadDashboard()" class="p-2 rounded-lg bg-gray-200/50 hover:bg-gray-300/50 text-gray-600 transition-all duration-200" title="Refresh">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </button>
                </div>
                <div id="phase-progress" class="space-y-4"></div>
            </div>

            <!-- Behaviour Progress -->
            <div class="card-glass rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">Progress per Behaviour</h2>
                    <p class="text-sm text-gray-500 mb-6">Value-based competency tracking</p>
                </div>
                <div id="behaviour-progress" class="space-y-3 max-h-[380px] overflow-y-auto pr-2 custom-scrollbar"></div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card-glass rounded-xl p-6 border-l-4 border-green-500">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-green-600 mt-1 flex-shrink-0"></i>
                <p class="text-sm text-gray-700">
                    <span class="font-semibold text-gray-900">Dashboard Summary:</span> Your personalized overview adapts based on your role. Phase and behaviour data automatically reflect your scope and responsibilities.
                </p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function renderPlanStatusLock(planStatus) {
    const container = document.getElementById('plan-status-container');
    const dashboardContent = document.getElementById('dashboard-content');
    
    // Status configuration
    const statusConfig = {
        'not_found': {
            message: 'Rencana Aktivitas Belum Dibuat',
            icon: '📋',
            textColor: 'text-blue-700',
            bgColor: 'bg-blue-50',
            buttonText: 'Buat Rencana VnB',
            description: 'Anda perlu membuat rencana aktivitas VnB sebelum dapat mengakses fitur dashboard dan aktivitas.'
        },
        'draft': {
            message: 'Rencana Aktivitas Masih Draft',
            icon: '✏️',
            textColor: 'text-amber-700',
            bgColor: 'bg-amber-50',
            buttonText: 'Lanjutkan Rencana',
            description: 'Selesaikan dan ajukan rencana Anda untuk dapat mengakses fitur dashboard dan aktivitas.'
        },
        'revision_draft': {
            message: 'Rencana Aktivitas Masih Draft',
            icon: '✏️',
            textColor: 'text-amber-700',
            bgColor: 'bg-amber-50',
            buttonText: 'Lanjutkan Rencana',
            description: 'Selesaikan dan ajukan rencana Anda untuk dapat mengakses fitur dashboard dan aktivitas.'
        },
        'waiting_manager_approval': {
            message: 'Menunggu Persetujuan Rencana',
            icon: '⏳',
            textColor: 'text-purple-700',
            bgColor: 'bg-purple-50',
            buttonText: 'Lihat Status',
            description: 'Rencana aktivitas Anda sedang dalam proses review oleh manager. Anda dapat mengakses fitur setelah rencana disetujui.'
        },
        'submitted': {
            message: 'Menunggu Persetujuan Rencana',
            icon: '⏳',
            textColor: 'text-purple-700',
            bgColor: 'bg-purple-50',
            buttonText: 'Lihat Status',
            description: 'Rencana aktivitas Anda sedang dalam proses review oleh manager. Anda dapat mengakses fitur setelah rencana disetujui.'
        },
        'revision_requested': {
            message: 'Rencana Aktivitas Perlu Perbaikan',
            icon: '🔄',
            textColor: 'text-red-700',
            bgColor: 'bg-red-50',
            buttonText: 'Perbaiki Rencana',
            description: 'Rencana Anda belum disetujui. Silakan lakukan revisi sesuai masukan yang diberikan.'
        }
    };

    const config = statusConfig[planStatus] || statusConfig['not_found'];

    if (planStatus === 'approved') {
        container.innerHTML = '';
        dashboardContent.style.display = 'block';
        loadDashboard();
        return;
    }

    dashboardContent.style.display = 'none';
    container.innerHTML = `
        <div class="card-glass rounded-xl p-8 md:p-12 text-center">
            <div class="text-6xl mb-4 animate-fade-in">${config.icon}</div>
            <h2 class="text-3xl font-bold mb-3 ${config.textColor}">${config.message}</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-base leading-relaxed">
                ${config.description}
            </p>
            <a href="/vnb-plans" class="btn-primary inline-block">
                <i class="fas fa-arrow-right mr-2"></i>${config.buttonText}
            </a>
        </div>
    `;
}

async function checkPlanStatus() {
    try {
        console.log('🔍 Checking plan status from dashboard...');
        
        if (typeof apiGet !== 'function') {
            console.error('❌ apiGet function not found');
            renderPlanStatusLock('not_found');
            return;
        }
        
        const res = await apiGet('/api/vnb-plans/new-hire');
        console.log('📊 Plan status response:', res);
        
        let planStatus = 'not_found';
        
        if (!res.success) {
            console.log('⚠️ API returned success: false');
            planStatus = 'not_found';
        } else if (res.data && res.data.status) {
            planStatus = res.data.status;
            console.log('✅ Found plan with status:', planStatus);
        } else {
            console.log('⚠️ Response has success but no data.status');
            planStatus = 'not_found';
        }
        
        renderPlanStatusLock(planStatus);
    } catch (e) {
        console.error('❌ Error checking plan status:', e);
        const container = document.getElementById('plan-status-container');
        container.innerHTML = `
            <div class="card-glass rounded-xl p-8 text-center border-l-4 border-red-500">
                <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-3"></i>
                <p class="text-red-700 font-semibold mb-2">Error Loading Dashboard</p>
                <p class="text-sm text-gray-600 mb-4">${e.message}</p>
                <button onclick="location.reload()" class="btn-secondary text-sm">
                    <i class="fas fa-redo mr-1"></i>Refresh Page
                </button>
            </div>
        `;
    }
}

function progressBar(percent, color = 'from-green-400 to-green-500') {
    return `
        <div class="w-full bg-gray-200/50 h-2.5 rounded-full overflow-hidden">
            <div class="h-2.5 bg-gradient-to-r ${color} transition-all duration-500" style="width: ${percent}%; animation: pulse-subtle 2s ease-in-out;"></div>
        </div>
    `;
}

function renderPhaseProgress(phases) {
    const container = document.getElementById('phase-progress');
    const keys = ['phase_1', 'phase_2', 'phase_3'];
    container.innerHTML = keys.map((key, idx) => {
        const p = phases[key] || { total: 0, completed: 0, percent: 0 };
        const colors = [
            'from-blue-400 to-blue-500',
            'from-amber-400 to-amber-500',
            'from-green-400 to-green-500'
        ];
        return `
            <div class="hover:bg-gray-100/20 p-3 rounded-lg transition-all duration-200">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="font-semibold text-gray-800">Phase ${idx + 1}</span>
                    <span class="text-xs bg-gray-200/50 px-2 py-1 rounded-full text-gray-700 font-medium">${p.completed}/${p.total} (${p.percent}%)</span>
                </div>
                ${progressBar(p.percent, colors[idx])}
            </div>
        `;
    }).join('');
}

function renderBehaviourProgress(items) {
    const container = document.getElementById('behaviour-progress');
    if (!items.length) {
        container.innerHTML = '<p class="text-sm text-gray-400 py-8 text-center">Belum ada data behaviour.</p>';
        return;
    }

    container.innerHTML = items.map(row => `
        <div class="hover:bg-gray-100/20 p-3 rounded-lg transition-all duration-200">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="font-medium text-gray-800 truncate">${row.behaviour}</span>
                <span class="text-xs bg-gray-200/50 px-2 py-1 rounded-full text-gray-700 font-medium ml-2 flex-shrink-0">${row.percent}%</span>
            </div>
            ${progressBar(row.percent, 'from-emerald-400 to-emerald-500')}
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