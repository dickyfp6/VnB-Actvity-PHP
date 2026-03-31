

<?php $__env->startSection('title', 'Dashboard - VnB Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 px-4">
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

<?php $__env->startPush('scripts'); ?>
<script>
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

loadDashboard();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/dashboard.blade.php ENDPATH**/ ?>