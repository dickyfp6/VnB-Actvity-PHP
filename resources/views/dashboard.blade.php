@extends('layouts.app')

@section('title', 'Dashboard - WisCore')

@section('content')
<div class="space-y-6">
    <div id="dashboard-empty" class="hidden card-glass rounded-2xl p-8 border border-dashed border-gray-300">
        <div class="max-w-2xl">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mb-4">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h2 id="empty-title" class="text-2xl font-bold text-gray-900 mb-2">Belum ada data</h2>
            <p id="empty-note" class="text-gray-600 leading-relaxed">Dashboard akan muncul ketika data VnB Activity tersedia.</p>
        </div>
    </div>

    <div id="dashboard-shell" class="hidden space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="card-glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Employee Scope</p>
                <p id="stat-employees" class="text-3xl font-bold text-gray-900 mt-3">0</p>
                <p class="text-sm text-gray-500 mt-1">Employee dalam scope role aktif</p>
            </div>
            <div class="card-glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Participants</p>
                <p id="stat-participants" class="text-3xl font-bold text-gray-900 mt-3">0</p>
                <p class="text-sm text-gray-500 mt-1">Employee yang sudah aktif VnB Activity</p>
            </div>
            <div class="card-glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Overall Progress</p>
                <p id="stat-overall" class="text-3xl font-bold text-emerald-600 mt-3">0%</p>
                <p class="text-sm text-gray-500 mt-1">Rata-rata progress behaviour</p>
            </div>
            <div class="card-glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Overdue</p>
                <p id="stat-overdue" class="text-3xl font-bold text-red-600 mt-3">0</p>
                <p class="text-sm text-gray-500 mt-1">Aktivitas melewati due date</p>
            </div>
        </div>

        <div class="card-glass rounded-2xl p-6 md:p-7">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Overall progress</h2>
                    <p class="text-sm text-gray-500 mt-1">Progress global behaviour VnB berdasarkan scope role aktif.</p>
                </div>
                <div class="w-full lg:max-w-lg">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="font-semibold text-gray-700">Progress keseluruhan</span>
                        <span id="overall-progress-label" class="font-bold text-gray-900">0%</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-gray-200 overflow-hidden">
                        <div id="overall-progress-bar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl bg-gray-50 p-4 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">Behaviour Radar</h3>
                            <p class="text-xs text-gray-500">Perbandingan progres antar value</p>
                        </div>
                    </div>
                    <div class="h-[360px]">
                        <canvas id="value-chart"></canvas>
                    </div>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">Status Distribution</h3>
                            <p class="text-xs text-gray-500">Komposisi status aktivitas VnB</p>
                        </div>
                    </div>
                    <div class="h-[360px]">
                        <canvas id="status-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="card-glass rounded-2xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-1">Progress per Value</h2>
                <p class="text-sm text-gray-500 mb-5">Behaviour global dalam bentuk bar progress individual.</p>
                <div id="value-list" class="space-y-4"></div>
            </div>

            <div class="card-glass rounded-2xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-1">Detail Ringkas</h2>
                <p class="text-sm text-gray-500 mb-5">Ikhtisar tambahan dari dataset VnB Activity yang sama.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Completed</p>
                        <p id="stat-completed" class="text-3xl font-bold text-emerald-600 mt-3">0</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">In Progress</p>
                        <p id="stat-active" class="text-3xl font-bold text-blue-600 mt-3">0</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Not Started</p>
                        <p id="stat-not-started" class="text-3xl font-bold text-gray-700 mt-3">0</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold">Completion Rate</p>
                        <p id="stat-rate" class="text-3xl font-bold text-purple-600 mt-3">0%</p>
                    </div>
                </div>
                <div class="mt-5 rounded-2xl bg-emerald-50 border border-emerald-100 p-4 text-sm text-emerald-900">
                    <strong>Catatan:</strong> dashboard ini hanya membaca employee yang sudah menjadi partisipan VnB Activity aktif di scope role kamu.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let dashboardCharts = [];

function destroyDashboardCharts() {
    dashboardCharts.forEach(chart => chart.destroy());
    dashboardCharts = [];
}

function renderProgressBar(percent) {
    return `
        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
            <div class="h-2.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width: ${percent}%"></div>
        </div>
    `;
}

function renderValueRows(rows) {
    const container = document.getElementById('value-list');
    if (!rows.length) {
        container.innerHTML = '<div class="text-sm text-gray-500">Belum ada data value yang bisa ditampilkan.</div>';
        return;
    }

    container.innerHTML = rows.map(row => `
        <div class="rounded-2xl bg-gray-50 p-4 border border-gray-100">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-semibold text-gray-900">${row.behaviour}</p>
                    <p class="text-xs text-gray-500">${row.completed}/${row.total} item selesai</p>
                </div>
                <span class="text-sm font-bold text-gray-900">${row.progress}%</span>
            </div>
            ${renderProgressBar(row.progress)}
        </div>
    `).join('');
}

function buildRadarChart(labels, data) {
    const ctx = document.getElementById('value-chart');
    if (!ctx) {
        return;
    }

    const chart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels,
            datasets: [{
                label: 'Progress Behaviour',
                data,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.18)',
                pointBackgroundColor: 'rgb(5, 150, 105)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(16, 185, 129)',
                borderWidth: 2,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    suggestedMax: 100,
                    ticks: {
                        backdropColor: 'transparent',
                        color: '#6B7280'
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.25)'
                    },
                    angleLines: {
                        color: 'rgba(156, 163, 175, 0.25)'
                    },
                    pointLabels: {
                        color: '#374151',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.parsed.r}%`
                    }
                }
            }
        }
    });

    dashboardCharts.push(chart);
}

function buildDoughnutChart(labels, data) {
    const ctx = document.getElementById('status-chart');
    if (!ctx) {
        return;
    }

    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: [
                    'rgba(16, 185, 129, 0.9)',
                    'rgba(59, 130, 246, 0.9)',
                    'rgba(107, 114, 128, 0.9)',
                    'rgba(239, 68, 68, 0.9)',
                ],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#374151',
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 18,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.label}: ${context.parsed}`
                    }
                }
            }
        }
    });

    dashboardCharts.push(chart);
}

async function loadDashboard() {
    try {
        const res = await apiGet('/api/dashboard/overview');
        if (!res || !res.success) {
            throw new Error('Dashboard response tidak valid.');
        }

        const shell = document.getElementById('dashboard-shell');
        const empty = document.getElementById('dashboard-empty');

        if (!res.scope?.has_data) {
            shell.classList.add('hidden');
            empty.classList.remove('hidden');
            document.getElementById('empty-title').textContent = res.scope?.empty_title || 'Belum ada data';
            document.getElementById('empty-note').textContent = res.scope?.empty_note || 'Dashboard akan muncul ketika data VnB Activity tersedia.';
            return;
        }

        empty.classList.add('hidden');
        shell.classList.remove('hidden');

        const stats = res.stats || {};
        document.getElementById('stat-employees').textContent = stats.employees || 0;
        document.getElementById('stat-participants').textContent = stats.participants || 0;
        document.getElementById('stat-overall').textContent = `${stats.overall_progress || 0}%`;
        document.getElementById('stat-overdue').textContent = stats.overdue_items || 0;
        document.getElementById('stat-completed').textContent = stats.completed_items || 0;
        document.getElementById('stat-active').textContent = stats.active_items || 0;
        document.getElementById('stat-not-started').textContent = stats.not_started_items || 0;
        document.getElementById('stat-rate').textContent = `${stats.completion_rate || 0}%`;
        document.getElementById('overall-progress-label').textContent = `${stats.overall_progress || 0}%`;
        document.getElementById('overall-progress-bar').style.width = `${stats.overall_progress || 0}%`;

        const sevenValues = res.seven_values || { labels: [], bars: [], rows: [] };
        renderValueRows(sevenValues.rows || []);

        destroyDashboardCharts();
        buildRadarChart(sevenValues.labels || [], sevenValues.bars || []);
        buildDoughnutChart(
            res.charts?.status_breakdown?.labels || [],
            res.charts?.status_breakdown?.data || []
        );
    } catch (e) {
        const empty = document.getElementById('dashboard-empty');
        const shell = document.getElementById('dashboard-shell');
        shell.classList.add('hidden');
        empty.classList.remove('hidden');
        document.getElementById('empty-title').textContent = 'Gagal memuat dashboard';
        document.getElementById('empty-note').textContent = e.message || 'Silakan refresh halaman.';
    }
}

loadDashboard();
</script>
@endpush
@endsection
