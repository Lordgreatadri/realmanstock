@extends('layouts.manager')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div class="flex items-center gap-4">
        <a href="{{ route('manager.reports.index') }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Sales Report</h1>
            <p class="text-gray-400 mt-1">Revenue and orders analysis</p>
        </div>
    </div>
    <div class="flex gap-2">
        <form action="{{ route('manager.reports.export-sales') }}" method="GET" class="inline">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
        </form>
        <form action="{{ route('manager.reports.print-sales') }}" method="GET" class="inline">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print PDF
            </button>
        </form>
    </div>
</div>

<!-- Date Filter -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" class="flex gap-4 items-end">
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
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-green-600 to-emerald-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Orders</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['total_orders'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Revenue</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($summary['total_revenue'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Amount Paid</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($summary['total_paid'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Outstanding Balance</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($summary['total_balance'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-pink-600 to-rose-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Completed Orders</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['completed_orders'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-red-600 to-pink-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Pending Orders</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['pending_orders'] }}</div>
    </div>
</div>

<!-- Daily Sales Chart -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Daily Sales Trend</h2>
    <canvas id="dailySalesChart" height="80"></canvas>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Top Customers -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-white mb-4">Top Customers</h2>
        <div class="space-y-3">
            @forelse($customerSales as $sale)
                <div class="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                    <div>
                        <p class="text-white font-semibold">{{ $sale->customer->name }}</p>
                        <p class="text-sm text-gray-400">{{ $sale->order_count }} orders</p>
                    </div>
                    <div class="text-right">
                        <p class="text-white font-bold">GH₵{{ number_format($sale->total_sales, 2) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">No customer data available</p>
            @endforelse
        </div>
    </div>

    <!-- Sales by Status -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-white mb-4">Sales by Status</h2>
        <canvas id="statusChart" height="200"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Sales Chart
const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
new Chart(dailySalesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailySales->pluck('date')->toArray()) !!},
        datasets: [{
            label: 'Revenue (GH₵)',
            data: {!! json_encode($dailySales->pluck('total')->toArray()) !!},
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: { color: '#fff' }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#9CA3AF' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' }
            },
            x: {
                ticks: { color: '#9CA3AF' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' }
            }
        }
    }
});

// Status Pie Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusBreakdown->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))->toArray()) !!},
        datasets: [{
            data: {!! json_encode($statusBreakdown->pluck('count')->toArray()) !!},
            backgroundColor: [
                'rgb(99, 102, 241)',
                'rgb(16, 185, 129)',
                'rgb(59, 130, 246)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(236, 72, 153)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#fff' }
            }
        }
    }
});
</script>
@endpush

