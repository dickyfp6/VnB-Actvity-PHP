@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard PCX & Intercomm</h1>
            <p class="text-gray-600">Visualisasi data onboarding V&B secara holistik untuk analisa strategis</p>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <form method="GET" class="flex gap-4 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <select name="period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="current_year" @selected($selectedPeriod === 'current_year')>Tahun Ini</option>
                        <option value="last_quarter" @selected($selectedPeriod === 'last_quarter')>Kuartal Terakhir</option>
                        <option value="last_month" @selected($selectedPeriod === 'last_month')>Bulan Terakhir</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                    <select name="department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" @selected($selectedDepartment === (string)$dept->id)>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Terapkan Filter
                </button>
            </form>
        </div>

        <!-- Headline Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <!-- Total Active -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total New Hire Aktif</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_active'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Avg Completion -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Rata-rata Penyelesaian</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['avg_completion'] }}%</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Critical Alerts -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Perlu Perhatian</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['critical_alerts'] }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Top Department -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Departemen Terbaik</p>
                        <p class="text-lg font-bold text-gray-900 mt-2">{{ $stats['top_department'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $stats['top_department_progress'] }}% progress</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-trophy text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Graduated -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Telah Lulus</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-check-circle text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Behaviour Mastery Radar Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Global Behaviour Mastery</h3>
                <canvas id="behaviourChart" height="300"></canvas>
            </div>

            <!-- Completion Velocity Line Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Onboarding Velocity (30 Hari Terakhir)</h3>
                <canvas id="velocityChart" height="300"></canvas>
            </div>
        </div>

        <!-- Departmental Heatmap -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Departmental Heatmap (Progres per Fase)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Departemen</th>
                            @foreach(['Phase 1', 'Phase 2', 'Phase 3'] as $phase)
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">{{ $phase }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($heatmapData as $row)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-900">{{ $row['department'] }}</td>
                                @foreach($row['phases'] as $phase)
                                    <td class="text-center py-3 px-4">
                                        @php
                                            $bgClass = match($phase['status']) {
                                                'excellent' => 'bg-green-100 text-green-800',
                                                'good' => 'bg-yellow-100 text-yellow-800',
                                                'needs_attention' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 rounded-full font-semibold text-xs {{ $bgClass }}">
                                            {{ $phase['progress'] }}%
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500">Belum ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Employee List Summary -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Daftar New Hire ({{ count($employees) }} employees)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">NIP</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Departemen</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Progress</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees->take(10) as $employee)
                            @php
                                $plans = $employee->vnbPlans;
                                $totalItems = $plans->sum(fn($p) => $p->items->count());
                                $completedItems = $plans->sum(fn($p) => $p->items->where('completion_percentage', 100)->count());
                                $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                                $statusBadge = match(true) {
                                    $progress === 100 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Selesai'],
                                    $progress >= 75 => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Hampir Selesai'],
                                    $progress >= 50 => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Sedang Berjalan'],
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Baru Dimulai']
                                };
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-900">{{ $employee->employee_number }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-medium text-gray-900">{{ $employee->name }}</span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">{{ $employee->department?->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">{{ $progress }}%</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500">Belum ada data tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($employees) > 10)
                <div class="mt-4 text-center text-sm text-gray-600">
                    Menampilkan 10 dari {{ count($employees) }} employees
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
    // Behaviour Radar Chart
    const behaviourCtx = document.getElementById('behaviourChart').getContext('2d');
    new Chart(behaviourCtx, {
        type: 'radar',
        data: {
            labels: {!! json_encode($behaviourData['labels']) !!},
            datasets: [{
                label: 'Rata-rata Penyelesaian Behaviour',
                data: {!! json_encode($behaviourData['data']) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                pointRadius: 5,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Velocity Line Chart
    const velocityCtx = document.getElementById('velocityChart').getContext('2d');
    new Chart(velocityCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($velocityData['dates']) !!},
            datasets: [{
                label: 'Rata-rata Progress (%)',
                data: {!! json_encode($velocityData['data']) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
