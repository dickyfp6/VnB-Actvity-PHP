

<?php $__env->startSection('title', 'STAR Dashboard - WisCore'); ?>
<?php $__env->startSection('page_title', 'STAR Dashboard'); ?>
<?php $__env->startSection('page_subtitle', 'Top 5 employee dan top 5 departemen berdasarkan points STAR yang approved.'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
	<div id="star-dashboard-shell" class="grid gap-6 xl:grid-cols-2">
		<section class="rounded-3xl border border-gray-200 bg-white/80 p-5 shadow-sm backdrop-blur">
			<div class="mb-4 flex items-start justify-between gap-3">
				<div>
					<h2 class="text-lg font-bold text-gray-900">Top 5 Employee</h2>
					<p class="text-sm text-gray-600">Perolehan points STAR tertinggi.</p>
				</div>
			</div>
			<div class="h-[380px]">
				<canvas id="starTopEmployeesChart"></canvas>
			</div>
		</section>

		<section class="rounded-3xl border border-gray-200 bg-white/80 p-5 shadow-sm backdrop-blur">
			<div class="mb-4 flex items-start justify-between gap-3">
				<div>
					<h2 class="text-lg font-bold text-gray-900">Top 5 Departemen</h2>
					<p class="text-sm text-gray-600">Akumulasi points STAR per departemen.</p>
				</div>
			</div>
			<div class="h-[380px]">
				<canvas id="starTopDepartmentsChart"></canvas>
			</div>
		</section>
	</div>
</div>

<script>
let starDashboardCharts = [];

function destroyStarDashboardCharts() {
	starDashboardCharts.forEach((chart) => chart.destroy());
	starDashboardCharts = [];
}

function renderStarBarChart(canvasId, labels, values, palette) {
	const canvas = document.getElementById(canvasId);
	if (!canvas) {
		return;
	}

	const ctx = canvas.getContext('2d');
	const chart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels,
			datasets: [{
				data: values,
				borderWidth: 0,
				backgroundColor: labels.map((_, index) => palette[index % palette.length]),
				borderRadius: 10,
				barThickness: 18,
			}],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			indexAxis: 'y',
			plugins: {
				legend: { display: false },
				tooltip: {
					callbacks: {
						label(context) {
							return ` Points: ${context.raw}`;
						},
					},
				},
			},
			scales: {
				x: {
					beginAtZero: true,
					grid: { color: 'rgba(148, 163, 184, 0.18)' },
					ticks: { precision: 0, color: '#64748b' },
				},
				y: {
					grid: { display: false },
					ticks: { color: '#334155', font: { weight: '600' } },
				},
			},
		},
	});

	starDashboardCharts.push(chart);
}

async function loadStarDashboard() {
	const shell = document.getElementById('star-dashboard-shell');

	try {
		const res = await apiGet('/api/star/dashboard');
		if (!(res && res.success)) {
			throw new Error('Dashboard STAR tidak valid.');
		}

		const data = res.data || {};
		const employeeItems = Array.isArray(data.top_employees) ? data.top_employees : [];
		const departmentItems = Array.isArray(data.top_departments) ? data.top_departments : [];

		shell.classList.remove('hidden');
		destroyStarDashboardCharts();

		renderStarBarChart(
			'starTopEmployeesChart',
			employeeItems.length ? employeeItems.map((item) => item.label) : ['Belum ada data'],
			employeeItems.length ? employeeItems.map((item) => Number(item.points || 0)) : [0],
			['#14532d', '#166534', '#15803d', '#16a34a', '#22c55e']
		);

		renderStarBarChart(
			'starTopDepartmentsChart',
			departmentItems.length ? departmentItems.map((item) => item.label) : ['Belum ada data'],
			departmentItems.length ? departmentItems.map((item) => Number(item.points || 0)) : [0],
			['#0f766e', '#0d9488', '#14b8a6', '#2dd4bf', '#5eead4']
		);
	} catch (error) {
		console.error(error);
		destroyStarDashboardCharts();
		shell.classList.add('hidden');
	}
}

document.addEventListener('DOMContentLoaded', loadStarDashboard);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/star/index.blade.php ENDPATH**/ ?>