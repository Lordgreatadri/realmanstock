@extends('layouts.admin')

@section('title', 'Financial Report')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.reports.index') }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Financial Report</h1>
            <p class="text-gray-400 mt-1">Revenue and financial analytics</p>
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
                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Apply Filter
            </button>
        </form>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export-financial', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('admin.reports.print-financial', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print PDF
            </a>
        </div>
    </div>
</div>

<!-- Revenue Summary -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Sales</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($revenue['total_sales'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Amount Paid</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($revenue['total_paid'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-red-600 to-pink-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Outstanding</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($revenue['total_balance'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Processing Fees</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($revenue['processing_fees'], 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Delivery Fees</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($revenue['delivery_fees'], 2) }}</div>
    </div>
</div>

<!-- Inventory Value -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Inventory Value</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-400">Store Items Value</p>
            <p class="text-3xl font-bold text-white mt-2">GH₵{{ number_format($inventoryValue['store_items'], 2) }}</p>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-400">Freezer Inventory Value</p>
            <p class="text-3xl font-bold text-white mt-2">GH₵{{ number_format($inventoryValue['freezer_inventory'], 2) }}</p>
        </div>
    </div>
</div>

<!-- Monthly Revenue Trend -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Monthly Revenue Trend</h2>
    <canvas id="revenueChart" height="100"></canvas>
</div>

<!-- Payment Status -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-bold text-white mb-4">Payment Status Breakdown</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Payment Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($paymentStatus as $status)
                    <tr>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'paid' => 'bg-green-200 text-green-900',
                                    'partial' => 'bg-yellow-200 text-yellow-900',
                                    'pending' => 'bg-red-200 text-red-900',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-sm font-semibold rounded {{ $statusColors[$status->payment_status] ?? 'bg-gray-200 text-gray-900' }}">
                                {{ ucfirst($status->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $status->count }}</td>
                        <td class="px-6 py-4 text-white font-bold">GH₵{{ number_format($status->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyRevenue->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->format('M Y'))->toArray()) !!},
        datasets: [
            {
                label: 'Total Revenue (GH₵)',
                data: {!! json_encode($monthlyRevenue->pluck('revenue')->toArray()) !!},
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Amount Paid (GH₵)',
                data: {!! json_encode($monthlyRevenue->pluck('paid')->toArray()) !!},
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#fff' } }
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
</script>
@endpush
