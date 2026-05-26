

<?php $__env->startSection('title', 'Ajukan Recognition - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'Tambah Ajuan'); ?>
<?php $__env->startSection('page_subtitle', ''); ?>

<?php $__env->startSection('content'); ?>
<style>
    .star-input {
        border-width: 2px;
        border-color: #b8c2d1;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        min-height: 3rem;
    }

    .star-input:focus {
        border-color: #144600;
        box-shadow: 0 0 0 4px rgba(20, 70, 0, 0.14);
        outline: none;
    }

    .date-slim {
        color: transparent;
        caret-color: #111827;
    }

    .date-slim::-webkit-datetime-edit {
        color: transparent;
    }

    .date-slim:focus,
    .date-slim:valid {
        color: #111827;
    }

    .date-slim:focus::-webkit-datetime-edit,
    .date-slim:valid::-webkit-datetime-edit {
        color: #111827;
    }

    .date-slim::-webkit-calendar-picker-indicator {
        opacity: .55;
        cursor: pointer;
    }
</style>
<div class="max-w-4xl mx-auto px-4">
    <script>
        // toggleRecipientChecklist removed due to duplicate click handlers
    </script>
    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Ajuan</h3>
            <a href="<?php echo e(route('star.recognition')); ?>" class="rounded-full border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
        </div>

        <div id="form-alert" class="hidden mb-4 rounded-xl p-3 text-sm"></div>

        <form id="recognition-form" enctype="multipart/form-data" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold uppercase tracking-wide text-gray-500">Nama Employee</label>
                <button id="recipient_button" type="button" onclick="toggleRecipientChecklist()" class="star-input mt-1 flex w-full items-center justify-between rounded-xl bg-white px-4 py-3 text-left font-normal text-gray-500 transition hover:border-gray-400">
                    <span id="recipient_button_label">Pilih employee</span>
                    <span class="text-gray-400">▾</span>
                </button>
                <div id="recipient-results" class="hidden mt-2 max-h-64 overflow-auto rounded-2xl border border-gray-200 bg-white shadow-lg"></div>
                <input id="recipient_ids" name="recipient_ids[]" type="hidden" />
                <div id="recipient-selected" class="mt-2 hidden flex flex-wrap gap-2"></div>
            </div>

            <div>
                <label class="block text-sm font-semibold uppercase tracking-wide text-gray-500">Nama Kegiatan</label>
                <input id="activity_name" name="activity_name" type="text" class="star-input mt-1 block w-full rounded-xl bg-white px-4 py-3" required />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold uppercase tracking-wide text-gray-500">Tanggal Pelaksanaan</label>
                    <input id="activity_date" name="activity_date" type="date" class="star-input date-slim mt-1 block w-full rounded-xl bg-white px-4 py-3" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold uppercase tracking-wide text-gray-500">Penyelenggara</label>
                    <input id="organizer" name="organizer" type="text" class="star-input mt-1 block w-full rounded-xl bg-white px-4 py-3" required />
                </div>
            </div>

            <div class="pt-2">
                <button id="submit-btn" type="button" onclick="submitRecognition()" class="inline-flex items-center gap-2 rounded-xl bg-[#144600] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:opacity-90">
                    <span id="submit-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const managerId = <?php echo json_encode(auth()->user()?->manager?->id, 15, 512) ?>;
let employeeOptions = [];
let selectedEmployeeIds = [];

window.toggleRecipientChecklist = function () {
    const results = document.getElementById('recipient-results');
    if (!results) return;

    if (results.classList.contains('hidden')) {
        renderEmployeeResults(employeeOptions.length ? employeeOptions : [{ id: 0, label: 'Memuat employee...' }]);
        if (!employeeOptions.length) {
            results.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Memuat employee...</div>';
        }
    } else {
        results.classList.add('hidden');
    }
};

async function loadEmployeesForSelect() {
    const buttonLabel = document.getElementById('recipient_button_label');
    const results = document.getElementById('recipient-results');
    if (!buttonLabel || !results) return;
    buttonLabel.textContent = 'Memuat employee...';

    try {
        const url = managerId ? `/api/employees?manager_id=${encodeURIComponent(managerId)}&lifecycle=active` : '/api/employees?lifecycle=active';
        const res = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
        if (!res.ok) throw new Error('no-list');
        const payload = await res.json();
        const list = Array.isArray(payload?.data) ? payload.data : payload;
        if (!Array.isArray(list) || list.length === 0) throw new Error('empty');

        employeeOptions = list.map((e) => ({
            id: e.id,
            label: e.name || e.name_display || e.full_name || e.display_name || e.employee_number || `#${e.id}`,
        }));
        buttonLabel.textContent = 'Pilih employee';
        results.innerHTML = '';
        results.classList.add('hidden');
    } catch (err) {
        buttonLabel.textContent = 'Employee tidak tersedia';
        results.innerHTML = '';
        results.classList.add('hidden');
    }
}

function renderEmployeeResults(items) {
    const results = document.getElementById('recipient-results');
    if (!results) return;

    if (!items.length) {
        results.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada employee yang cocok.</div>';
        results.classList.remove('hidden');
        return;
    }

    results.innerHTML = items.slice(0, 8).map((item) => {
        const checked = selectedEmployeeIds.includes(String(item.id)) ? 'checked' : '';
        return `<label class="flex cursor-pointer items-center gap-3 border-b border-gray-100 px-4 py-3 text-sm text-gray-800 transition hover:bg-gray-50 last:border-b-0">
            <input type="checkbox" data-id="${item.id}" class="h-4 w-4 rounded border-gray-300 text-[#144600] focus:ring-[#144600]" ${checked} />
            <span>${item.label}</span>
        </label>`;
    }).join('');
    results.classList.remove('hidden');

    results.querySelectorAll('input[data-id]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const id = String(checkbox.dataset.id || '');
            if (checkbox.checked) {
                if (!selectedEmployeeIds.includes(id)) selectedEmployeeIds.push(id);
            } else {
                selectedEmployeeIds = selectedEmployeeIds.filter((value) => value !== id);
            }
            syncSelectedEmployeeState();
        });
    });
}

function filterEmployeeResults(query) {
    const normalized = String(query || '').trim().toLowerCase();
    if (!normalized) {
        renderEmployeeResults(employeeOptions);
        return;
    }

    const filtered = employeeOptions.filter((item) => item.label.toLowerCase().includes(normalized));
    renderEmployeeResults(filtered);
}

function syncSelectedEmployeeState() {
    const hidden = document.getElementById('recipient_ids');
    const label = document.getElementById('recipient_button_label');
    const chips = document.getElementById('recipient-selected');

    if (hidden) {
        hidden.value = selectedEmployeeIds.join(',');
    }

    if (label) {
        if (!selectedEmployeeIds.length) {
            label.textContent = 'Pilih employee';
        } else if (selectedEmployeeIds.length === 1) {
            const found = employeeOptions.find((item) => String(item.id) === selectedEmployeeIds[0]);
            label.textContent = found ? found.label : `${selectedEmployeeIds.length} employee dipilih`;
        } else {
            label.textContent = `${selectedEmployeeIds.length} employee dipilih`;
        }
    }

    if (chips) {
        chips.innerHTML = selectedEmployeeIds.map((id) => {
            const found = employeeOptions.find((item) => String(item.id) === id);
            const text = found ? found.label : `Employee #${id}`;
            return `<span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">${text}</span>`;
        }).join('');
        chips.classList.toggle('hidden', selectedEmployeeIds.length === 0);
    }
}

async function submitRecognition() {
    const btn = document.getElementById('submit-btn');
    const spinner = document.getElementById('submit-spinner');
    const alert = document.getElementById('form-alert');
    if (btn) btn.disabled = true;
    if (spinner) spinner.classList.remove('hidden');

    const fd = new FormData();
    const recipients = selectedEmployeeIds.slice();
    recipients.forEach((id) => fd.append('recipient_ids[]', id));
    fd.append('activity_name', document.getElementById('activity_name').value || '');
    fd.append('activity_date', document.getElementById('activity_date').value || '');
    fd.append('organizer', document.getElementById('organizer').value || '');

    try {
        const res = await fetch('/api/star/recognition', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'same-origin',
            body: fd,
        });

        const payload = await res.json();
        if (!res.ok || !payload.success) {
            if (alert) {
                alert.className = 'mb-4 rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
                alert.textContent = payload.message || 'Gagal mengirim pengajuan.';
                alert.classList.remove('hidden');
            }
            return;
        }

        if (alert) {
            alert.className = 'mb-4 rounded-xl p-3 text-sm bg-green-50 text-green-800 border border-green-100';
            alert.textContent = 'Pengajuan berhasil terkirim.';
            alert.classList.remove('hidden');
        }
    } catch (err) {
        if (alert) {
            alert.className = 'mb-4 rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = 'Terjadi kesalahan. Coba lagi.';
            alert.classList.remove('hidden');
        }
    } finally {
        if (spinner) spinner.classList.add('hidden');
        if (btn) btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadEmployeesForSelect();

    const button = document.getElementById('recipient_button');
    const results = document.getElementById('recipient-results');

    if (button && results) {
        document.addEventListener('click', (event) => {
            if (results.contains(event.target) || button.contains(event.target)) return;
            results.classList.add('hidden');
        });
    }

    syncSelectedEmployeeState();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/star/create.blade.php ENDPATH**/ ?>