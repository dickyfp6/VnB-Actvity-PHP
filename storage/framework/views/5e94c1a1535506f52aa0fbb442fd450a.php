

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

    .date-slim:disabled {
        color: #111827;
        -webkit-text-fill-color: #111827;
        opacity: 1;
    }

    .date-slim:disabled::-webkit-datetime-edit {
        color: #111827;
        -webkit-text-fill-color: #111827;
    }

    .date-slim::-webkit-calendar-picker-indicator {
        opacity: .55;
        cursor: pointer;
    }

    .upload-card {
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        cursor: pointer;
    }

    .upload-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transform: translateY(-1px);
    }

    .upload-preview-btn {
        transition: opacity 0.15s ease, transform 0.15s ease;
    }

    .upload-preview-btn:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    .file-preview-modal {
        backdrop-filter: blur(4px);
    }
</style>
<div class="max-w-5xl mx-auto px-4">
    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Tambah Ajuan</h3>
                <p class="text-sm text-gray-500">Simpan draft dulu, lalu lengkapi dokumen, dokumentasi, dan skema di bawah.</p>
            </div>
            <a href="<?php echo e(route('star.recognition')); ?>" class="rounded-full border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
        </div>

        <div id="form-alert" class="hidden rounded-xl p-3 text-sm"></div>

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

            <div id="draft-section" class="hidden border-t border-gray-200 pt-6">
                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wide text-gray-500">Dokumentasi Saat Kegiatan</label>
                        <input id="activity_documentation_file" type="file" class="hidden" accept="image/*,.pdf" />
                        <button id="activity_documentation_trigger" type="button" class="upload-card star-input mt-1 flex w-full items-center justify-between rounded-xl bg-white px-4 py-3 text-left">
                            <span id="activity_documentation_preview" class="text-sm text-gray-500">Choose File</span>
                            <span id="activity_documentation_remove" class="hidden upload-preview-btn inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-700" aria-label="Hapus file">×</span>
                        </button>
                        <p id="activity-documentation-name" class="mt-2 text-xs text-gray-500">Foto saat mengikuti kegiatan</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wide text-gray-500">Dokumen Pendukung</label>
                        <input id="support_document" type="file" class="hidden" accept="image/*,.pdf,.doc,.docx" />
                        <button id="support_document_trigger" type="button" class="upload-card star-input mt-1 flex w-full items-center justify-between rounded-xl bg-white px-4 py-3 text-left">
                            <span id="support_document_preview" class="text-sm text-gray-500">Choose File</span>
                            <span id="support_document_remove" class="hidden upload-preview-btn inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-700" aria-label="Hapus file">×</span>
                        </button>
                        <p id="support-document-name" class="mt-2 text-xs text-gray-500">Sertifikat, Piagam, atau Surat Keterangan Lainnya yang Menyatakan Keikutsertaan</p>
                    </div>
                </div>

                <div id="schema-draft-container" class="mt-6 space-y-4"></div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-6">
                <button id="save-draft-btn" type="button" onclick="saveDraftGroup()" class="inline-flex items-center gap-2 rounded-xl bg-[#0b5a00] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:opacity-90">
                    <span id="save-draft-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    <span>Simpan Draft</span>
                </button>
                <button id="submit-btn" type="button" onclick="submitDraftGroup()" class="inline-flex items-center gap-2 rounded-xl bg-[#144600] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-40" disabled>
                    <span id="submit-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    <span>Ajukan</span>
                </button>
                <span id="draft-save-status" class="text-sm text-gray-600"></span>
            </div>

            <div id="intercomm-review-footer" class="hidden">
                <div class="pb-4">
                    <button id="intercomm-review-btn" type="button" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Lihat Penilaian</span>
                    </button>
                </div>
                <div class="border-t border-gray-200"></div>
                <div class="border-t border-gray-100"></div>
            </div>
        </form>
    </div>

    <div id="file-preview-modal" class="file-preview-modal fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 px-4">
        <div class="w-full max-w-3xl rounded-2xl bg-white p-4 shadow-2xl">
            <div class="mb-3 flex items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div>
                    <h4 id="file-preview-title" class="text-sm font-semibold text-gray-900">Preview File</h4>
                    <p class="text-xs text-gray-500">Klik di luar atau tombol X untuk menutup</p>
                </div>
                <button id="file-preview-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200" aria-label="Tutup preview">×</button>
            </div>
            <div class="max-h-[75vh] overflow-auto rounded-xl bg-gray-50 p-2">
                <img id="file-preview-image" alt="Preview file" class="hidden max-h-[70vh] w-full object-contain" />
                <iframe id="file-preview-frame" class="hidden h-[70vh] w-full rounded-lg border border-gray-200 bg-white"></iframe>
                <div id="file-preview-fallback" class="hidden rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-600">
                    File ini tidak bisa dipreview langsung di browser. Gunakan tombol download jika perlu.
                </div>
            </div>
        </div>
    </div>

    <div id="intercomm-review-modal" class="file-preview-modal fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4">
        <div class="w-full max-w-4xl rounded-2xl bg-white p-4 shadow-2xl">
            <div class="mb-3 flex items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Penilaian Intercomm</h4>
                    <p class="text-xs text-gray-500">Pertanyaan skema, penyesuaian nilai, nilai akhir, dan catatan.</p>
                </div>
                <button id="intercomm-review-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200" aria-label="Tutup review">×</button>
            </div>
            <div id="intercomm-review-content" class="max-h-[75vh] overflow-auto rounded-xl bg-gray-50 p-4">
                <div class="text-sm text-gray-500">Memuat penilaian...</div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const managerId = <?php echo json_encode(auth()->user()?->manager?->id, 15, 512) ?>;
const currentDraftGroup = <?php echo json_encode(request('group'), 15, 512) ?>;
let employeeOptions = [];
let selectedEmployeeIds = [];
let draftSchema = null;
let draftDetail = null;
let draftReadyToSubmit = false;
let isReadOnlyMode = false;
const uploadState = {
    support_document: { objectUrl: null, previewUrl: '', previewName: '' },
    activity_documentation_file: { objectUrl: null, previewUrl: '', previewName: '' },
};

async function parseApiResponse(res) {
    const contentType = String(res.headers.get('content-type') || '').toLowerCase();
    const bodyText = await res.text();

    let payload = null;
    if (bodyText) {
        if (contentType.includes('application/json')) {
            payload = JSON.parse(bodyText);
        } else {
            try {
                payload = JSON.parse(bodyText);
            } catch (_) {
                payload = null;
            }
        }
    }

    if (payload && typeof payload === 'object') {
        return payload;
    }

    if (res.status === 419) {
        throw new Error('Sesi login habis. Silakan refresh halaman lalu coba lagi.');
    }

    if (res.status === 401 || res.status === 403) {
        throw new Error('Akses ditolak. Pastikan Anda masih login dengan role yang sesuai.');
    }

    throw new Error(`Server mengembalikan respons non-JSON (HTTP ${res.status}).`);
}

window.toggleRecipientChecklist = function () {
    if (isReadOnlyMode) return;
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

function restoreSelectedEmployees(ids) {
    selectedEmployeeIds = (ids || []).map((value) => String(value));
    syncSelectedEmployeeState();
}

function getDisplayFileName(filePath, originalName) {
    if (originalName) {
        return originalName;
    }

    if (filePath) {
        const parts = String(filePath).split('/');
        return parts[parts.length - 1] || filePath;
    }

    return '';
}

function guessPreviewMode(fileName) {
    const lower = String(fileName || '').toLowerCase();
    if (lower.endsWith('.pdf')) return 'pdf';
    if (/(\.png|\.jpe?g|\.gif|\.webp|\.bmp|\.svg)$/.test(lower)) return 'image';
    return 'other';
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function openIntercommReviewModal() {
    const modal = document.getElementById('intercomm-review-modal');
    const content = document.getElementById('intercomm-review-content');
    if (!modal || !content) return;

    const schema = draftSchema || {};
    const responses = Array.isArray(draftDetail?.responses) ? draftDetail.responses : [];
    const responseLookup = new Map(responses.map((item) => [String(item.star_schema_indicator_id), item]));
    const baseScore = responses.reduce((sum, item) => sum + Number(item.response_score || 0), 0);
    const finalScoreValue = draftDetail?.total_points !== null && draftDetail?.total_points !== undefined
        ? Number(draftDetail.total_points)
        : null;
    const adjustment = finalScoreValue !== null ? Number((finalScoreValue - baseScore).toFixed(2)) : 0;
    const notes = String(draftDetail?.approval_notes || draftDetail?.notes || '').trim();

    if (!schema || !Array.isArray(schema.indicators) || !schema.indicators.length) {
        content.innerHTML = '<div class="text-sm text-red-600">Skema tidak tersedia.</div>';
    } else {
        const cards = schema.indicators.map((indicator) => {
            const selected = responseLookup.get(String(indicator.id));
            const options = Array.isArray(indicator.options) ? indicator.options : [];
            return `
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between gap-4">
                        <div class="text-sm font-bold text-gray-900">${escapeHtml(indicator.label)}</div>
                    </div>
                    <div class="space-y-2">
                        ${options.map((option) => {
                            const isSelected = selected && String(selected.star_schema_indicator_option_id) === String(option.id);
                            return `<div class="rounded-xl px-3 py-2 ${isSelected ? 'bg-emerald-50 text-emerald-800' : 'bg-gray-50 text-gray-700'}">
                                <div class="text-sm ${isSelected ? 'font-semibold' : ''}">${escapeHtml(option.label)}</div>
                            </div>`;
                        }).join('')}
                    </div>
                </div>
            `;
        }).join('');

        content.innerHTML = `
            <div class="space-y-4">
                ${cards}
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="border-t border-gray-200"></div>
                    <div class="mt-4 grid grid-cols-[minmax(0,1fr)_auto] gap-4 items-center">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Penyesuaian Nilai</div>
                        <div class="text-sm font-semibold text-gray-900 tabular-nums text-right">${adjustment >= 0 ? '+' + adjustment : adjustment}</div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Nilai Akhir</div>
                        <div class="text-sm font-bold text-gray-900 tabular-nums text-right">${finalScoreValue !== null ? finalScoreValue.toFixed(2) : '-'}</div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Catatan</div>
                        <div class="mt-1 text-sm text-gray-900 leading-7">${notes ? escapeHtml(notes) : '-'}</div>
                    </div>
                </div>
            </div>
        `;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeIntercommReviewModal() {
    const modal = document.getElementById('intercomm-review-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function closeFilePreview() {
    const modal = document.getElementById('file-preview-modal');
    const image = document.getElementById('file-preview-image');
    const frame = document.getElementById('file-preview-frame');
    const fallback = document.getElementById('file-preview-fallback');

    if (modal) modal.classList.add('hidden');
    if (image) {
        image.removeAttribute('src');
        image.classList.add('hidden');
    }
    if (frame) {
        frame.removeAttribute('src');
        frame.classList.add('hidden');
    }
    if (fallback) fallback.classList.add('hidden');
}

function openFilePreview(url, name) {
    const modal = document.getElementById('file-preview-modal');
    const title = document.getElementById('file-preview-title');
    const image = document.getElementById('file-preview-image');
    const frame = document.getElementById('file-preview-frame');
    const fallback = document.getElementById('file-preview-fallback');

    if (!modal || !title || !image || !frame || !fallback || !url) return;

    title.textContent = name || 'Preview File';
    image.classList.add('hidden');
    frame.classList.add('hidden');
    fallback.classList.add('hidden');

    const mode = guessPreviewMode(name || url);
    if (mode === 'image') {
        image.src = url;
        image.classList.remove('hidden');
    } else if (mode === 'pdf') {
        frame.src = url;
        frame.classList.remove('hidden');
    } else {
        fallback.classList.remove('hidden');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function syncUploadPreview(inputId, previewId, removeId, placeholderText, existingName) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const removeButton = document.getElementById(removeId);
    if (!input || !preview || !removeButton) return;

    const file = input.files && input.files[0] ? input.files[0] : null;
    const label = file ? file.name : (existingName || '');
    const state = uploadState[inputId];

    if (state && state.objectUrl) {
        URL.revokeObjectURL(state.objectUrl);
        state.objectUrl = null;
    }

    if (state) {
        state.previewName = label;
        state.previewUrl = file ? URL.createObjectURL(file) : '';
        state.objectUrl = state.previewUrl || null;
    }

    if (label) {
        preview.textContent = label;
        preview.classList.remove('text-gray-500');
        preview.classList.add('text-gray-900', 'font-medium');
        removeButton.classList.remove('hidden');
    } else {
        preview.textContent = placeholderText || 'Choose File';
        preview.classList.add('text-gray-500');
        preview.classList.remove('text-gray-900', 'font-medium');
        removeButton.classList.add('hidden');
    }
}

function bindCustomUploadField(options) {
    const input = document.getElementById(options.inputId);
    const trigger = document.getElementById(options.triggerId);
    const removeButton = document.getElementById(options.removeId);

    if (!input || !trigger || !removeButton) return;

    trigger.addEventListener('click', () => {
        if (isReadOnlyMode) {
            if (uploadState[options.inputId]?.previewUrl) {
                openFilePreview(uploadState[options.inputId].previewUrl, uploadState[options.inputId].previewName || options.placeholderText || 'Preview File');
            }
            return;
        }

        if (input.files && input.files[0] && uploadState[options.inputId]?.previewUrl) {
            openFilePreview(uploadState[options.inputId].previewUrl, uploadState[options.inputId].previewName || input.files[0].name);
            return;
        }

        if (uploadState[options.inputId]?.previewUrl && !input.files?.length && options.existingNameGetter) {
            const existingName = options.existingNameGetter();
            if (existingName) {
                openFilePreview(uploadState[options.inputId].previewUrl, existingName);
                return;
            }
        }

        input.click();
    });
    input.addEventListener('change', () => {
        syncUploadPreview(
            options.inputId,
            options.previewId,
            options.removeId,
            options.placeholderText,
            options.existingNameGetter ? options.existingNameGetter() : ''
        );
        updateActionButtons();
    });

    removeButton.addEventListener('click', (event) => {
        if (isReadOnlyMode) return;
        event.preventDefault();
        event.stopPropagation();
        input.value = '';
        if (uploadState[options.inputId]?.objectUrl) {
            URL.revokeObjectURL(uploadState[options.inputId].objectUrl);
            uploadState[options.inputId].objectUrl = null;
        }
        if (uploadState[options.inputId]) {
            uploadState[options.inputId].previewUrl = '';
            uploadState[options.inputId].previewName = '';
        }
        syncUploadPreview(options.inputId, options.previewId, options.removeId, options.placeholderText, '');
        updateActionButtons();
    });

    syncUploadPreview(
        options.inputId,
        options.previewId,
        options.removeId,
        options.placeholderText,
        options.existingNameGetter ? options.existingNameGetter() : ''
    );
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
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: fd,
        });

        const payload = await parseApiResponse(res);
        if (!res.ok || !payload.success) {
            if (alert) {
                alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
                alert.textContent = payload.message || 'Gagal mengirim pengajuan.';
                alert.classList.remove('hidden');
            }
            return;
        }

        const draftGroup = payload.draft_group || payload?.data?.[0]?.draft_group;
        if (!draftGroup) {
            throw new Error('draft-group-missing');
        }

        window.location.href = `/star/recognition/create?group=${encodeURIComponent(draftGroup)}`;
    } catch (err) {
        if (alert) {
            alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = 'Terjadi kesalahan. Coba lagi.';
            alert.classList.remove('hidden');
        }
    } finally {
        if (spinner) spinner.classList.add('hidden');
        if (btn) btn.disabled = false;
    }
}

function mapRecognitionStatus(status) {
    const raw = String(status || '').toLowerCase();
    if (raw === 'draft') {
        return { label: 'Draft', className: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
    if (raw === 'rejected') {
        return { label: 'Ditolak', className: 'bg-red-50 text-red-700 border-red-100' };
    }
    return { label: 'Diajukan', className: 'bg-amber-50 text-amber-700 border-amber-100' };
}

function renderDraftSchemaForm(schema, selectedResponses = []) {
    const container = document.getElementById('schema-draft-container');
    if (!container) return;

    const responseLookup = new Map((selectedResponses || []).map((item) => [String(item.star_schema_indicator_id), String(item.star_schema_indicator_option_id)]));

    if (!schema || !Array.isArray(schema.indicators) || schema.indicators.length === 0) {
        container.innerHTML = '<div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500">Schema belum tersedia.</div>';
        return;
    }

    container.innerHTML = schema.indicators.map((indicator) => {
        const options = Array.isArray(indicator.options) ? indicator.options : [];
        const optionsHtml = options.map((option) => {
            const selected = responseLookup.get(String(indicator.id)) === String(option.id) ? 'selected' : '';
            return `<option value="${option.id}" ${selected}>${option.label}</option>`;
        }).join('');

        return `<div class="rounded-2xl border border-gray-100 p-4">
            <h5 class="font-semibold text-gray-900">${indicator.label}</h5>
            <div class="mt-3">
                <select class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm" name="indicator_${indicator.id}" ${isReadOnlyMode ? 'disabled aria-disabled="true" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm pointer-events-none"' : ''}>
                    <option value="">Pilih kategori penilaian</option>
                    ${optionsHtml}
                </select>
            </div>
        </div>`;
    }).join('');

    container.querySelectorAll('select').forEach((select) => {
        if (isReadOnlyMode) {
            select.disabled = true;
            select.classList.add('pointer-events-none', 'bg-gray-50');
            return;
        }
        select.addEventListener('change', updateActionButtons);
    });
}

function getDraftFormState() {
    const activityName = (document.getElementById('activity_name')?.value || '').trim();
    const activityDate = (document.getElementById('activity_date')?.value || '').trim();
    const organizer = (document.getElementById('organizer')?.value || '').trim();
    const supportFileInput = document.getElementById('support_document');
    const documentationFileInput = document.getElementById('activity_documentation_file');
    const hasSupportDocument = !!(supportFileInput && supportFileInput.files && supportFileInput.files[0]) || !!draftDetail?.certificate_original_name;
    const hasActivityDocumentationFile = !!(documentationFileInput && documentationFileInput.files && documentationFileInput.files[0]) || !!draftDetail?.activity_documentation_original_name;

    const schema = draftSchema || {};
    const responses = [];
    let allSchemaAnswered = true;

    (schema.indicators || []).forEach((indicator) => {
        const select = document.querySelector(`select[name="indicator_${indicator.id}"]`);
        const value = select ? String(select.value || '').trim() : '';
        if (!value) {
            allSchemaAnswered = false;
        } else {
            responses.push({
                star_schema_indicator_id: indicator.id,
                star_schema_indicator_option_id: Number(value),
            });
        }
    });

    return {
        activityName,
        activityDate,
        organizer,
        hasSupportDocument,
        hasActivityDocumentationFile,
        hasRecipients: selectedEmployeeIds.length > 0,
        allSchemaAnswered,
        responses,
    };
}

function updateActionButtons() {
    const submitBtn = document.getElementById('submit-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const state = getDraftFormState();
    const ready = !!currentDraftGroup
        && state.hasRecipients
        && !!state.activityName
        && !!state.activityDate
        && !!state.organizer
        && state.hasSupportDocument
        && state.hasActivityDocumentationFile
        && state.allSchemaAnswered;

    draftReadyToSubmit = ready;

    if (submitBtn) {
        submitBtn.disabled = isReadOnlyMode ? true : !ready;
        submitBtn.classList.toggle('hidden', isReadOnlyMode);
    }

    if (saveDraftBtn) {
        saveDraftBtn.disabled = isReadOnlyMode ? true : false;
        saveDraftBtn.classList.toggle('hidden', isReadOnlyMode);
    }
}

function setReadOnlyMode(shouldBeReadOnly) {
    isReadOnlyMode = !!shouldBeReadOnly;

    const form = document.getElementById('recognition-form');
    if (form) {
        form.querySelectorAll('input, textarea, select').forEach((field) => {
            if (field.type === 'hidden') return;
            field.disabled = isReadOnlyMode;
        });
    }

    const recipientButton = document.getElementById('recipient_button');
    if (recipientButton) {
        recipientButton.disabled = isReadOnlyMode;
        recipientButton.classList.toggle('cursor-not-allowed', isReadOnlyMode);
        recipientButton.classList.toggle('opacity-80', isReadOnlyMode);
    }

    const recipientResults = document.getElementById('recipient-results');
    if (recipientResults && isReadOnlyMode) {
        recipientResults.classList.add('hidden');
    }

    document.querySelectorAll('[id$="_remove"]').forEach((btn) => {
        if (isReadOnlyMode) {
            btn.classList.add('hidden');
            btn.disabled = true;
        }
    });

    const draftSection = document.getElementById('draft-section');
    if (draftSection) {
        draftSection.classList.toggle('opacity-90', isReadOnlyMode);
    }

    const headerTitle = document.querySelector('h3.text-lg.font-bold.text-gray-900');
    const headerSubtitle = document.querySelector('p.text-sm.text-gray-500');
    if (isReadOnlyMode && headerTitle) {
        headerTitle.textContent = 'Detail Ajuan';
    }
    if (isReadOnlyMode && headerSubtitle) {
        headerSubtitle.textContent = 'Ajuan sudah dikirim. Halaman ini hanya untuk melihat detail.';
    }

    const reviewFooter = document.getElementById('intercomm-review-footer');
    if (reviewFooter) {
        reviewFooter.classList.toggle('hidden', !isReadOnlyMode);
    }

    updateActionButtons();
}

async function loadDraftGroup() {
    if (!currentDraftGroup) return;

    const section = document.getElementById('draft-section');
    const saveStatus = document.getElementById('draft-save-status');

    try {
        const res = await fetch(`/api/star/recognition/draft/${currentDraftGroup}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        const payload = await parseApiResponse(res);
        if (!res.ok || !payload.success) throw new Error(payload.message || 'draft-load-failed');

        draftDetail = payload.data || null;
        draftSchema = draftDetail?.schema || null;
        const currentStatus = String(draftDetail?.status || '').toLowerCase();
        setReadOnlyMode(currentStatus !== 'draft');

        if (draftDetail?.activity_name) {
            const activityName = document.getElementById('activity_name');
            if (activityName) activityName.value = draftDetail.activity_name || '';
        }
        if (draftDetail?.activity_date) {
            const activityDate = document.getElementById('activity_date');
            if (activityDate) activityDate.value = draftDetail.activity_date || '';
        }
        if (draftDetail?.organizer) {
            const organizer = document.getElementById('organizer');
            if (organizer) organizer.value = draftDetail.organizer || '';
        }
        if (draftDetail?.certificate_original_name) {
            syncUploadPreview('support_document', 'support_document_preview', 'support_document_remove', 'Klik untuk pilih file pendukung', draftDetail.certificate_original_name);
            if (draftDetail?.certificate_path && uploadState.support_document) {
                uploadState.support_document.previewUrl = `/storage/${draftDetail.certificate_path}`;
                uploadState.support_document.previewName = draftDetail.certificate_original_name;
            }
        }
        if (draftDetail?.activity_documentation_original_name) {
            syncUploadPreview('activity_documentation_file', 'activity_documentation_preview', 'activity_documentation_remove', 'Klik untuk pilih file dokumentasi', draftDetail.activity_documentation_original_name);
            if (draftDetail?.activity_documentation_path && uploadState.activity_documentation_file) {
                uploadState.activity_documentation_file.previewUrl = `/storage/${draftDetail.activity_documentation_path}`;
                uploadState.activity_documentation_file.previewName = draftDetail.activity_documentation_original_name;
            }
        }

        restoreSelectedEmployees(draftDetail?.employee_ids || []);
        renderDraftSchemaForm(draftSchema, draftDetail?.responses || []);

        if (saveStatus) {
            saveStatus.textContent = '';
        }
        if (section) section.classList.remove('hidden');
        updateActionButtons();
    } catch (error) {
        if (section) section.classList.remove('hidden');
        if (saveStatus) saveStatus.textContent = 'Gagal memuat draft.';
        updateActionButtons();
    }
}

async function saveDraftGroup() {
    const fd = new FormData();
    const state = getDraftFormState();
    const alert = document.getElementById('form-alert');

    if (!state.hasRecipients) {
        if (alert) {
            alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = 'Pilih minimal satu employee.';
            alert.classList.remove('hidden');
        }
        return;
    }

    // Initial draft creation still requires tahap 1 data on backend.
    if (!currentDraftGroup && (!state.activityName || !state.activityDate || !state.organizer)) {
        if (alert) {
            alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = 'Lengkapi Nama Kegiatan, Tanggal Pelaksanaan, dan Penyelenggara terlebih dulu.';
            alert.classList.remove('hidden');
        }
        return;
    }

    selectedEmployeeIds.forEach((id) => {
        fd.append('recipient_ids[]', id);
    });
    fd.append('activity_name', state.activityName);
    fd.append('activity_date', state.activityDate);
    fd.append('organizer', state.organizer);
    fd.append('finalize', '0');

    const fileInput = document.getElementById('support_document');
    const documentationFileInput = document.getElementById('activity_documentation_file');
    if (fileInput && fileInput.files && fileInput.files[0]) {
        fd.append('certificate', fileInput.files[0]);
    }
    if (documentationFileInput && documentationFileInput.files && documentationFileInput.files[0]) {
        fd.append('activity_documentation_file', documentationFileInput.files[0]);
    }

    state.responses.forEach((response, index) => {
        fd.append(`responses[${index}][star_schema_indicator_id]`, response.star_schema_indicator_id);
        fd.append(`responses[${index}][star_schema_indicator_option_id]`, response.star_schema_indicator_option_id);
    });

    const status = document.getElementById('draft-save-status');
    const btn = document.getElementById('save-draft-btn');
    const spinner = document.getElementById('save-draft-spinner');

    if (btn) btn.disabled = true;
    if (spinner) spinner.classList.remove('hidden');
    if (status) status.textContent = currentDraftGroup ? 'Menyimpan draft...' : 'Membuat draft...';

    try {
        const url = currentDraftGroup ? `/api/star/recognition/draft/${currentDraftGroup}` : '/api/star/recognition';
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: fd,
        });
        const payload = await parseApiResponse(res);
        if (!res.ok || !payload.success) {
            const fieldErrors = payload?.errors && typeof payload.errors === 'object'
                ? Object.values(payload.errors).flat().filter(Boolean)
                : [];
            const firstFieldError = fieldErrors.length ? String(fieldErrors[0]) : '';
            throw new Error(firstFieldError || payload.message || 'save-draft-failed');
        }

        if (!currentDraftGroup) {
            const draftGroup = payload.draft_group || payload?.data?.[0]?.draft_group;
            if (draftGroup) {
                window.location.href = `/star/recognition/create?group=${encodeURIComponent(draftGroup)}`;
                return;
            }
        }

        draftDetail = payload.data || draftDetail;
        if (status) status.textContent = 'Draft tersimpan.';
        if (alert) {
            alert.classList.add('hidden');
            alert.textContent = '';
        }

        if (fileInput && fileInput.files && fileInput.files[0]) {
            const supportName = document.getElementById('support-document-name');
            if (supportName) supportName.textContent = fileInput.files[0].name;
        }

        updateActionButtons();
    } catch (error) {
        const message = error instanceof Error && error.message
            ? error.message
            : 'Gagal menyimpan draft.';
        if (status) status.textContent = message;
        if (alert) {
            alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = message;
            alert.classList.remove('hidden');
        }
    } finally {
        if (spinner) spinner.classList.add('hidden');
        if (btn) btn.disabled = false;
    }
}

async function submitDraftGroup() {
    if (!currentDraftGroup) return;
    if (!draftReadyToSubmit) {
        const alert = document.getElementById('form-alert');
        if (alert) {
            alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = 'Lengkapi semua field sebelum ajukan.';
            alert.classList.remove('hidden');
        }
        return;
    }

    const state = getDraftFormState();
    const fd = new FormData();
    fd.append('activity_name', state.activityName);
    fd.append('activity_date', state.activityDate);
    fd.append('organizer', state.organizer);
    fd.append('finalize', '1');

    const supportFileInput = document.getElementById('support_document');
    if (supportFileInput && supportFileInput.files && supportFileInput.files[0]) {
        fd.append('certificate', supportFileInput.files[0]);
    }

    const documentationFileInput = document.getElementById('activity_documentation_file');
    if (documentationFileInput && documentationFileInput.files && documentationFileInput.files[0]) {
        fd.append('activity_documentation_file', documentationFileInput.files[0]);
    }

    state.responses.forEach((response, index) => {
        fd.append(`responses[${index}][star_schema_indicator_id]`, response.star_schema_indicator_id);
        fd.append(`responses[${index}][star_schema_indicator_option_id]`, response.star_schema_indicator_option_id);
    });

    const btn = document.getElementById('submit-btn');
    const spinner = document.getElementById('submit-spinner');
    const status = document.getElementById('draft-save-status');
    const alert = document.getElementById('form-alert');

    if (btn) btn.disabled = true;
    if (spinner) spinner.classList.remove('hidden');
    if (status) status.textContent = 'Mengajukan...';

    try {
        const res = await fetch(`/api/star/recognition/draft/${currentDraftGroup}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: fd,
        });
        const payload = await parseApiResponse(res);
        if (!res.ok || !payload.success) {
            const fieldErrors = payload?.errors && typeof payload.errors === 'object'
                ? Object.values(payload.errors).flat().filter(Boolean)
                : [];
            const firstFieldError = fieldErrors.length ? String(fieldErrors[0]) : '';
            throw new Error(firstFieldError || payload.message || 'submit-draft-failed');
        }

        if (status) status.textContent = 'Ajuan berhasil dikirim.';
        if (alert) {
            alert.classList.add('hidden');
            alert.textContent = '';
        }
        setTimeout(() => { window.location.href = '/star/recognition'; }, 900);
    } catch (error) {
        const message = error instanceof Error && error.message
            ? error.message
            : 'Gagal mengajukan draft.';
        if (status) status.textContent = message;
        if (alert) {
            alert.className = 'rounded-xl p-3 text-sm bg-red-50 text-red-700 border border-red-100';
            alert.textContent = message;
            alert.classList.remove('hidden');
        }
    } finally {
        if (spinner) spinner.classList.add('hidden');
        if (btn) btn.disabled = !draftReadyToSubmit;
    }
}

function bindDraftFileLabel() {
    bindCustomUploadField({
        inputId: 'support_document',
        triggerId: 'support_document_trigger',
        previewId: 'support_document_preview',
        removeId: 'support_document_remove',
        placeholderText: 'Klik untuk pilih file pendukung',
        existingNameGetter: () => getDisplayFileName(draftDetail?.certificate_path, draftDetail?.certificate_original_name),
    });

    bindCustomUploadField({
        inputId: 'activity_documentation_file',
        triggerId: 'activity_documentation_trigger',
        previewId: 'activity_documentation_preview',
        removeId: 'activity_documentation_remove',
        placeholderText: 'Klik untuk pilih file dokumentasi',
        existingNameGetter: () => getDisplayFileName(draftDetail?.activity_documentation_path, draftDetail?.activity_documentation_original_name),
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    await loadEmployeesForSelect();
    bindDraftFileLabel();

    const previewModal = document.getElementById('file-preview-modal');
    const closePreview = document.getElementById('file-preview-close');
    const reviewModal = document.getElementById('intercomm-review-modal');
    const closeReview = document.getElementById('intercomm-review-close');
    if (closePreview) {
        closePreview.addEventListener('click', closeFilePreview);
    }
    if (previewModal) {
        previewModal.addEventListener('click', (event) => {
            if (event.target === previewModal) {
                closeFilePreview();
            }
        });
    }
    if (closeReview) {
        closeReview.addEventListener('click', closeIntercommReviewModal);
    }
    if (reviewModal) {
        reviewModal.addEventListener('click', (event) => {
            if (event.target === reviewModal) {
                closeIntercommReviewModal();
            }
        });
    }

    const reviewBtn = document.getElementById('intercomm-review-btn');
    if (reviewBtn) {
        reviewBtn.addEventListener('click', openIntercommReviewModal);
    }

    const button = document.getElementById('recipient_button');
    const results = document.getElementById('recipient-results');

    if (button && results) {
        document.addEventListener('click', (event) => {
            if (results.contains(event.target) || button.contains(event.target)) return;
            results.classList.add('hidden');
        });
    }

    if (currentDraftGroup) {
        await loadDraftGroup();
    }

    document.querySelectorAll('#recognition-form input, #recognition-form textarea, #recognition-form select').forEach((field) => {
        field.addEventListener('input', updateActionButtons);
        field.addEventListener('change', updateActionButtons);
    });

    syncSelectedEmployeeState();
    updateActionButtons();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/star/create.blade.php ENDPATH**/ ?>