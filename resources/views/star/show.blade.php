@extends('layouts.app')

@section('title', 'STAR Recognition - Detail')
@section('page_title', 'Detail Recognition')

@section('content')
<div class="max-w-4xl mx-auto px-4">
  <div id="star-show-root" class="space-y-4">
    <div class="rounded-lg border bg-white p-4">
      <h3 class="font-bold text-lg" id="activity-name">Memuat...</h3>
      <p id="employee-names" class="text-sm text-gray-600"></p>
      <p class="text-sm text-gray-500" id="activity-date"></p>
      <div class="mt-4" id="files-row"></div>
      <div class="mt-4" id="schema-answers"></div>
      <div class="mt-4">
        <a href="/star/recognition" class="inline-block rounded-full border px-3 py-2">Kembali</a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadStarShow() {
  const id = {{ $id }};
  const root = document.getElementById('star-show-root');
  try {
    const res = await fetch(`/api/star/recognition/${id}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
    const payload = await res.json();
    if (!res.ok || !payload.success) throw new Error(payload.message || 'Gagal memuat');
    const data = payload.data;

    document.getElementById('activity-name').textContent = data.activity_name || '-';
    document.getElementById('employee-names').textContent = (Array.isArray(data.employee_names) ? data.employee_names.join(', ') : (data.employee?.name || ''));
    document.getElementById('activity-date').textContent = data.activity_date || '-';

    const filesRow = document.getElementById('files-row');
    filesRow.innerHTML = `
      <div><a href="${data.certificate_path ? '/storage/'+data.certificate_path : '#'}" target="_blank">Dokumen Pendukung: ${data.certificate_original_name || '-'}</a></div>
      <div><a href="${data.activity_documentation_path ? '/storage/'+data.activity_documentation_path : '#'}" target="_blank">Dokumentasi: ${data.activity_documentation_original_name || '-'}</a></div>
    `;

    const schemaDiv = document.getElementById('schema-answers');
    if (data.responses && Array.isArray(data.responses)) {
      schemaDiv.innerHTML = '<h4 class="font-semibold">Jawaban Skema</h4>' + data.responses.map(r => `<div class="text-sm text-gray-700">${r.indicator_label || r.star_schema_indicator_id}: ${r.option_label || r.star_schema_indicator_option_id}</div>`).join('');
    }
  } catch (err) {
    root.innerHTML = `<div class="text-red-600">${err.message}</div>`;
  }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') loadStarShow();
else document.addEventListener('DOMContentLoaded', loadStarShow);
</script>
@endpush
@extends('layouts.app')

@section('title', 'STAR Recognition - Lihat')
@section('page_title', 'Detail Recognition')

@section('content')
<div class="max-w-3xl mx-auto p-4">
  <div id="star-show-root" class="space-y-4">
    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <h3 id="activity-name" class="text-lg font-bold"></h3>
      <p id="employee-names" class="text-sm text-gray-600"></p>
      <div class="mt-3 grid grid-cols-2 gap-4">
        <div>
          <div class="text-xs text-gray-500">Tanggal</div>
          <div id="activity-date" class="text-sm"></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Penyelenggara</div>
          <div id="organizer" class="text-sm"></div>
        </div>
      </div>

      <div class="mt-4">
        <div class="text-xs text-gray-500">Dokumen Pendukung</div>
        <a id="certificate-link" class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 mt-2" href="#" target="_blank">-</a>
      </div>

      <div class="mt-3">
        <div class="text-xs text-gray-500">Dokumentasi</div>
        <a id="documentation-link" class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800 mt-2" href="#" target="_blank">-</a>
      </div>

      <div class="mt-4">
        <div class="text-xs text-gray-500">Status</div>
        <div id="status-badge" class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold"></div>
      </div>

      <div class="mt-4">
        <div class="text-xs text-gray-500">Total Points</div>
        <div id="total-points" class="text-sm font-bold mt-1">-</div>
      </div>
    </div>

    <div class="bg-white border rounded-lg p-4 shadow-sm">
      <h4 class="font-semibold mb-2">Skema STAR (jawaban)</h4>
      <div id="schema-answers">Memuat...</div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const recognitionId = {{ $id }};

function mapRecognitionStatusLabel(status) {
  const raw = String(status || '').toLowerCase();
  if (['rejected','ditolak'].includes(raw)) return { label: 'Ditolak', className: 'bg-red-50 text-red-700 border-red-100' };
  if (raw === 'draft') return { label: 'Draft', className: 'bg-slate-50 text-slate-700 border-slate-200' };
  return { label: 'Diajukan', className: 'bg-amber-50 text-amber-700 border-amber-100' };
}

async function loadRecognition() {
  try {
    const res = await fetch(`/api/star/recognition/${recognitionId}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
    const payload = await res.json();
    if (!res.ok || !payload.success) throw new Error(payload.message || 'Gagal memuat');
    const r = payload.data;

    document.getElementById('activity-name').textContent = r.activity_name || '-';
    document.getElementById('employee-names').textContent = r.employee?.name || r.employee_name || '';
    document.getElementById('activity-date').textContent = r.activity_date || '-';
    document.getElementById('organizer').textContent = r.organizer || '-';

    const certLink = document.getElementById('certificate-link');
    if (r.certificate_path) {
      certLink.href = `/storage/${r.certificate_path}`;
      certLink.textContent = r.certificate_original_name || r.certificate_path.split('/').pop();
    } else {
      certLink.href = '#'; certLink.textContent = '-';
      certLink.classList.add('pointer-events-none', 'opacity-50');
    }

    const docLink = document.getElementById('documentation-link');
    if (r.activity_documentation_path) {
      docLink.href = `/storage/${r.activity_documentation_path}`;
      docLink.textContent = r.activity_documentation_original_name || r.activity_documentation_path.split('/').pop();
    } else {
      docLink.href = '#'; docLink.textContent = '-';
      docLink.classList.add('pointer-events-none', 'opacity-50');
    }

    const statusMeta = mapRecognitionStatusLabel(r.status);
    const badge = document.getElementById('status-badge');
    badge.textContent = statusMeta.label;
    badge.className = 'inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold ' + statusMeta.className;

    document.getElementById('total-points').textContent = r.total_points ?? '-';

    // Show responses
    const schemaRoot = document.getElementById('schema-answers');
    if (Array.isArray(r.responses) && r.responses.length) {
      const rows = r.responses.map(resp => `
        <div class="mb-2">
          <div class="text-sm font-medium">${resp.indicator_label || resp.star_schema_indicator_id}</div>
          <div class="text-sm text-gray-700">Jawaban: ${resp.option_label || resp.star_schema_indicator_option_id} — Skor: ${resp.response_score}</div>
        </div>
      `).join('');
      schemaRoot.innerHTML = rows;
    } else {
      schemaRoot.innerHTML = '<div class="text-sm text-gray-500">Belum ada jawaban skema.</div>';
    }

  } catch (e) {
    document.getElementById('star-show-root').innerHTML = '<div class="text-red-600">Gagal memuat data recognition.</div>';
  }
}

document.addEventListener('DOMContentLoaded', loadRecognition);
</script>
@endpush

@endsection
