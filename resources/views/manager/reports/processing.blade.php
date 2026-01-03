@extends('layouts.manager')

@section('title', 'Processing Report')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('manager.reports.index') }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Processing Report</h1>
            <p class="text-gray-400 mt-1">Processing efficiency and analytics</p>
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <div class="flex gap-4 items-end">
        <form method="GET" class="flex gap-4 items-end flex-1">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-green-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                Apply Filter
            </button>
        </form>
        <a href="{{ route('manager.reports.export-processing', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Excel
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-green-600 to-emerald-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Requests</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['total_requests'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Completed</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['completed'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">In Progress</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['in_progress'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Avg Dressing %</div>
        <div class="text-3xl font-bold mt-2">{{ number_format($efficiency->avg_dressing_percentage ?? 0, 1) }}%</div>
    </div>
</div>

<!-- Processing by Status Chart -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Processing by Status</h2>
    <canvas id="statusChart" height="100"></canvas>
</div>

<!-- Monthly Trend -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-bold text-white mb-4">Monthly Processing Trend</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Live Weight (kg)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Dressed Weight (kg)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Avg Dressing %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($monthlyTrend as $month)
                    <tr>
                        <td class="px-6 py-4 text-white font-semibold">{{ \Carbon\Carbon::parse($month->month . '-01')->format('M Y') }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $month->count }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ number_format($month->total_live_weight, 2) }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ number_format($month->total_dressed_weight, 2) }}</td>
                        <td class="px-6 py-4 text-white font-bold">{{ number_format(($month->total_dressed_weight / $month->total_live_weight) * 100, 1) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($statusBreakdown->pluck('status')->map(fn($s) => ucfirst($s))->toArray()) !!},
        datasets: [{
            label: 'Number of Requests',
            data: {!! json_encode($statusBreakdown->pluck('count')->toArray()) !!},
            backgroundColor: [
                'rgba(99, 102, 241, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#fff' } },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Number of Requests: ' + context.parsed.y;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    color: '#9CA3AF',
                    stepSize: 1
                },
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                title: {
                    display: true,
                    text: 'Number of Requests',
                    color: '#fff'
                }
            },
            x: {
                ticks: { color: '#9CA3AF' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' }
            }
        }
    }
});
</script>
@endpush

