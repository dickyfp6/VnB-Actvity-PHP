

<?php $__env->startSection('title', 'VNB Participants - Activity Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h1 class="text-3xl font-bold text-gray-900">VNB Activity Participants</h1>
        <p class="mt-3 text-gray-600 leading-relaxed">
            Manage peserta yang terlibat dalam VNB Activities. 
            Hanya employee yang telah di-assign oleh PCX atau Intercomm yang dapat mengakses fitur VNB.
        </p>
    </section>

    <!-- Participants Management -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Active Participants</h2>
            <button class="btn btn-primary">+ Add Participant</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee</th>
                        <th class="px-4 py-3 text-left font-semibold">Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Career Stage</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">Jane Smith</td>
                        <td class="px-4 py-3">jane@example.com</td>
                        <td class="px-4 py-3">Manage Self (Staff)</td>
                        <td class="px-4 py-3"><span class="badge badge-success">Active</span></td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn btn-sm btn-ghost">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Assignment Info -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Assignment Rules</h2>
        <div class="space-y-3 text-gray-600">
            <p><strong>• All Employees:</strong> Semua employee dalam tabel dapat mengakses platform ini.</p>
            <p><strong>• VNB Feature Access:</strong> Hanya employee yang di-assign PCX/Intercomm yang dapat menggunakan VNB features.</p>
            <p><strong>• Manager Role:</strong> Employee menjadi manager saat Intercomm menambahkan mereka di kolom manager (operational/functional).</p>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/vnb/participants.blade.php ENDPATH**/ ?>