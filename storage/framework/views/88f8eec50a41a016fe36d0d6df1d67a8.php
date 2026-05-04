

<?php $__env->startSection('title', 'Bukti VnB - VnB Platform'); ?>
<?php $__env->startSection('page_title', 'Bukti VnB'); ?>
<?php $__env->startSection('page_subtitle', 'Unggah dan kelola bukti pendukung aktivitas VnB dalam satu galeri.'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <button onclick="showUploadModal()" class="text-white px-4 py-2 rounded-lg flex items-center transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
            <i class="fas fa-upload mr-2"></i> Upload Bukti
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="searchItem" placeholder="Cari item..." class="px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'">
            <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'" onchange="filterEvidence()">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <!-- Gallery View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div id="evidenceGallery" class="col-span-full">
            <p class="text-center text-gray-500 py-8">
                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
            </p>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Upload Bukti Baru</h2>
        </div>
        <form onsubmit="submitUpload(event)" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID Plan Item</label>
                <input type="number" id="itemId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File/URL</label>
                <input type="text" id="fileUrl" required placeholder="https://example.com/image.jpg" class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"></textarea>
            </div>
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg transition" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
const API_BASE = 'http://localhost:8000/api';
let allEvidence = [];

async function loadEvidence() {
    try {
        const response = await fetch(`${API_BASE}/evidence`);
        const data = await response.json();
        allEvidence = data.data || [];
        renderEvidence(allEvidence);
    } catch (error) {
        console.error('Error loading evidence:', error);
        document.getElementById('evidenceGallery').innerHTML = `
            <div class="col-span-full text-center text-red-600 py-8">
                <i class="fas fa-exclamation-circle"></i> Gagal memuat data
            </div>
        `;
    }
}

function renderEvidence(evidence) {
    if (evidence.length === 0) {
        document.getElementById('evidenceGallery').innerHTML = `
            <div class="col-span-full text-center text-gray-500 py-8">
                Tidak ada bukti yang diupload
            </div>
        `;
        return;
    }

    document.getElementById('evidenceGallery').innerHTML = evidence.map(item => `
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="${item.file_path || item.file_url}" alt="${item.title}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<i class=\"fas fa-image text-gray-400 text-4xl\"></i>'">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 text-sm mb-2 truncate">${item.title || 'Bukti ' + item.id}</h3>
                <p class="text-xs text-gray-600 mb-3 line-clamp-2">${item.description || 'Tidak ada deskripsi'}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full 
                        ${item.status === 'verified' ? 'bg-green-100 text-green-800' : 
                          'bg-yellow-100 text-yellow-800'}">
                        ${item.status || 'Pending'}
                    </span>
                    <button onclick="viewEvidence(${item.id})" class="text-sm" style="cursor: pointer; color: #144600;" onmouseover="this.style.color='#37AA05'" onmouseout="this.style.color='#144600'">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function filterEvidence() {
    const search = document.getElementById('searchItem').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;

    const filtered = allEvidence.filter(item => {
        const titleMatch = (item.title || '').toLowerCase().includes(search);
        const statusMatch = !status || item.status === status;
        return titleMatch && statusMatch;
    });

    renderEvidence(filtered);
}

function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
}

async function submitUpload(event) {
    event.preventDefault();
    try {
        const response = await fetch(`${API_BASE}/evidence/upload`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                vnb_plan_item_id: document.getElementById('itemId').value,
                file_url: document.getElementById('fileUrl').value,
                description: document.getElementById('description').value
            })
        });

        if (response.ok) {
            closeUploadModal();
            loadEvidence();
            alert('Bukti berhasil diupload');
        } else {
            alert('Gagal upload bukti');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

function viewEvidence(id) {
    const item = allEvidence.find(e => e.id === id);
    if (item) {
        alert(`Bukti: ${item.title}\n\nStatus: ${item.status}\nDeskripsi: ${item.description}`);
    }
}

// Load data on page load
document.addEventListener('DOMContentLoaded', loadEvidence);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views\evidence\index.blade.php ENDPATH**/ ?>