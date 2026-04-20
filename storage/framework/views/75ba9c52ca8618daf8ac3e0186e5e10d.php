

<?php $__env->startSection('title', 'VNB Approval - Activity Review'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">VNB Activity Approval</h1>
        <button onclick="loadApprovals()" class="text-white px-4 py-2 rounded-lg text-sm" style="background-color: #144600;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
            <i class="fas fa-sync-alt mr-1"></i> Refresh
        </button>
    </div>

    <!-- Summary Box -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Pending Approval</div>
            <div id="pending-count" class="text-2xl font-bold text-gray-800">0</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs text-gray-500">Total to Review</div>
            <div id="total-count" class="text-2xl font-bold text-gray-800">0</div>
        </div>
    </div>

    <!-- Pending Approval Requests -->
    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Activity</th>
                        <th>Behaviour</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="approvals-body">
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="review-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Review VNB Activity</h2>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>
        <div id="detail-box" class="space-y-3 text-sm text-gray-700 mb-4 max-h-96 overflow-y-auto"></div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Feedback (opsional)</label>
            <textarea id="feedback-notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Berikan feedback jika perlu revisi..."></textarea>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="requestRevisionActivity()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                <i class="fas fa-ban mr-1"></i>Request Revision
            </button>
            <button onclick="approveActivityNow()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                <i class="fas fa-check mr-1"></i>Approve
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    let approvals = [];
    let selectedApproval = null;

    async function loadApprovals() {
        try {
            // TODO: Replace with actual API endpoint
            console.log('Loading VNB activity approvals...');
            document.getElementById('pending-count').textContent = '0';
            document.getElementById('total-count').textContent = '0';
            document.getElementById('approvals-body').innerHTML = '<tr><td colspan="6" class="text-center py-10 text-gray-400">No pending approvals</td></tr>';
        } catch (error) {
            console.error('Error loading approvals:', error);
        }
    }

    function closeReviewModal() {
        document.getElementById('review-modal').classList.add('hidden');
    }

    async function requestRevisionActivity() {
        if (!selectedApproval) return;
        const feedback = document.getElementById('feedback-notes').value;
        console.log('Request revision for activity:', selectedApproval, 'Feedback:', feedback);
        closeReviewModal();
        loadApprovals();
    }

    async function approveActivityNow() {
        if (!selectedApproval) return;
        console.log('Approving activity:', selectedApproval);
        closeReviewModal();
        loadApprovals();
    }

    document.addEventListener('DOMContentLoaded', loadApprovals);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb/approval.blade.php ENDPATH**/ ?>