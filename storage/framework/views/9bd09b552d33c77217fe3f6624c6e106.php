

<?php $__env->startSection('title', 'VNB Monitor - Activity Tracking'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">VNB Activity Monitor</h1>
        <button onclick="loadMonitoring()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
            <i class="fas fa-sync-alt mr-1"></i> Refresh
        </button>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">In Progress</div>
            <div id="inprogress-count" class="text-2xl font-bold text-gray-800">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Completed</div>
            <div id="completed-count" class="text-2xl font-bold text-gray-800">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Pending Review</div>
            <div id="pending-count" class="text-2xl font-bold text-gray-800">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Total Activities</div>
            <div id="total-count" class="text-2xl font-bold text-gray-800">0</div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="flex gap-2 items-center">
        <input type="text" id="search-input" placeholder="Search employee atau activity..." class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm" onkeyup="filterActivities()">
        <select id="status-filter" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" onchange="filterActivities()">
            <option value="">All Status</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="pending_review">Pending Review</option>
        </select>
    </div>

    <!-- Activity Monitoring Table -->
    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Activity / Behaviour</th>
                        <th>Phase</th>
                        <th>Start Date</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="monitoring-body">
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Activity Details</h2>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>
        <div id="activity-detail" class="space-y-3 text-sm text-gray-700 max-h-96 overflow-y-auto">
            <!-- Activity details will be loaded here -->
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="closeDetailModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm hover:bg-gray-300">
                Close
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    let allActivities = [];

    async function loadMonitoring() {
        try {
            // TODO: Replace with actual API endpoint
            console.log('Loading VNB activity monitoring data...');
            allActivities = [];
            updateSummary();
            renderActivities(allActivities);
        } catch (error) {
            console.error('Error loading monitoring data:', error);
        }
    }

    function updateSummary() {
        // TODO: Calculate based on actual data
        document.getElementById('inprogress-count').textContent = '0';
        document.getElementById('completed-count').textContent = '0';
        document.getElementById('pending-count').textContent = '0';
        document.getElementById('total-count').textContent = '0';
    }

    function renderActivities(activities) {
        const tbody = document.getElementById('monitoring-body');
        if (activities.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-400">No activities to monitor</td></tr>';
            return;
        }

        tbody.innerHTML = activities.map(activity => `
            <tr>
                <td class="px-4 py-3">${activity.employee || '-'}</td>
                <td class="px-4 py-3">${activity.activity || '-'}</td>
                <td class="px-4 py-3">${activity.phase || '-'}</td>
                <td class="px-4 py-3">${activity.start_date || '-'}</td>
                <td class="px-4 py-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: ${activity.progress || 0}%"></div>
                    </div>
                    <small>${activity.progress || 0}%</small>
                </td>
                <td class="px-4 py-3"><span class="badge badge-info">${activity.status || 'Unknown'}</span></td>
                <td class="px-4 py-3 text-center">
                    <button onclick="showActivityDetail('${activity.id}')" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View</button>
                </td>
            </tr>
        `).join('');
    }

    function filterActivities() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;

        const filtered = allActivities.filter(activity => {
            const matchSearch = !searchTerm || 
                (activity.employee && activity.employee.toLowerCase().includes(searchTerm)) ||
                (activity.activity && activity.activity.toLowerCase().includes(searchTerm));
            
            const matchStatus = !statusFilter || activity.status === statusFilter;
            
            return matchSearch && matchStatus;
        });

        renderActivities(filtered);
    }

    function showActivityDetail(activityId) {
        // TODO: Load and display activity details
        document.getElementById('detail-modal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', loadMonitoring);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb/monitor.blade.php ENDPATH**/ ?>