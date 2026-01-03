@extends('layouts.manager')

@section('title', 'Manager Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Manager Dashboard</h1>
            <p class="text-gray-400 mt-1">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('manager.orders.create') }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>New Order</span>
            </a>
        </div>
    </div>

    <!-- Urgent Tasks Alert -->
    @if($data['urgent_tasks']->isNotEmpty())
    <div class="bg-gradient-to-r from-red-900/50 to-orange-900/50 border border-red-700 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-300 mb-2">Urgent Tasks Require Attention</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($data['urgent_tasks'] as $task)
                    <a href="{{ $task['url'] }}" class="bg-gray-800/50 p-3 rounded-lg hover:bg-gray-700/50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-300">{{ $task['title'] }}</p>
                                <p class="text-2xl font-bold text-white">{{ $task['count'] }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $task['priority'] === 'high' ? 'bg-red-600 text-white' : 'bg-yellow-600 text-white' }}">
                                {{ ucfirst($task['priority']) }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Today's Operations Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Orders -->
        <div class="bg-gradient-to-br from-blue-900/50 to-indigo-900/50 border border-blue-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-300 text-sm font-medium">Today's Orders</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $data['today']['orders'] }}</p>
                    <p class="text-sm text-gray-400 mt-1">Revenue: GHS {{ number_format($data['today']['revenue'], 2) }}</p>
                </div>
                <div class="p-3 bg-blue-600/30 rounded-lg">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today's Processing -->
        <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/50 border border-purple-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-300 text-sm font-medium">Today's Processing</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $data['today']['processing'] }}</p>
                    <p class="text-sm text-gray-400 mt-1">Animals dressed</p>
                </div>
                <div class="p-3 bg-purple-600/30 rounded-lg">
                    <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today's Deliveries -->
        <div class="bg-gradient-to-br from-green-900/50 to-emerald-900/50 border border-green-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-300 text-sm font-medium">Today's Deliveries</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $data['today']['deliveries'] }}</p>
                    <p class="text-sm text-gray-400 mt-1">Scheduled deliveries</p>
                </div>
                <div class="p-3 bg-green-600/30 rounded-lg">
                    <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Available Animals -->
        <div class="bg-gradient-to-br from-orange-900/50 to-red-900/50 border border-orange-700 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-300 text-sm font-medium">Available Animals</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $data['inventory']['animals_available'] }}</p>
                    <p class="text-sm text-gray-400 mt-1">In stock</p>
                </div>
                <div class="p-3 bg-orange-600/30 rounded-lg">
                    <svg class="w-8 h-8 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Last 7 Days Sales</h3>
            <div style="height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Processing Status Chart -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Processing Status (This Month)</h3>
            <div style="height: 300px;">
                <canvas id="processingChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Operations Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Orders Status -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Orders Status
            </h3>
            <div class="space-y-3">
                <a href="{{ route('manager.orders.index', ['status' => 'pending']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Pending</span>
                    <span class="px-3 py-1 bg-yellow-600 text-white rounded-full text-sm font-semibold">{{ $data['orders']['pending'] }}</span>
                </a>
                <a href="{{ route('manager.orders.index', ['status' => 'processing']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Processing</span>
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm font-semibold">{{ $data['orders']['processing'] }}</span>
                </a>
                <a href="{{ route('manager.orders.index', ['status' => 'ready']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Ready for Delivery</span>
                    <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm font-semibold">{{ $data['orders']['ready_delivery'] }}</span>
                </a>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                    <span class="text-gray-300">Completed Today</span>
                    <span class="px-3 py-1 bg-gray-600 text-white rounded-full text-sm font-semibold">{{ $data['orders']['completed_today'] }}</span>
                </div>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Inventory Overview
            </div>
            <div class="space-y-3">
                <a href="{{ route('manager.animals.index', ['status' => 'available']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Available Animals</span>
                    <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm font-semibold">{{ $data['inventory']['animals_available'] }}</span>
                </a>
                <a href="{{ route('manager.animals.index', ['status' => 'quarantine']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">In Quarantine</span>
                    <span class="px-3 py-1 bg-orange-600 text-white rounded-full text-sm font-semibold">{{ $data['inventory']['animals_quarantine'] }}</span>
                </a>
                <a href="{{ route('manager.freezer.index') }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Freezer Items</span>
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm font-semibold">{{ $data['inventory']['freezer_items'] }}</span>
                </a>
                <a href="{{ route('manager.store-items.index', ['filter' => 'low-stock']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Low Stock Items</span>
                    <span class="px-3 py-1 bg-red-600 text-white rounded-full text-sm font-semibold">{{ $data['inventory']['low_stock_items'] }}</span>
                </a>
            </div>
        </div>

        <!-- Processing Status -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                Processing Status
            </h3>
            <div class="space-y-3">
                <a href="{{ route('manager.processing.index', ['status' => 'pending']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">Pending</span>
                    <span class="px-3 py-1 bg-yellow-600 text-white rounded-full text-sm font-semibold">{{ $data['processing']['pending'] }}</span>
                </a>
                <a href="{{ route('manager.processing.index', ['status' => 'in_progress']) }}" class="flex items-center justify-between p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <span class="text-gray-300">In Progress</span>
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm font-semibold">{{ $data['processing']['in_progress'] }}</span>
                </a>
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                    <span class="text-gray-300">Completed Today</span>
                    <span class="px-3 py-1 bg-gray-600 text-white rounded-full text-sm font-semibold">{{ $data['processing']['completed_today'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Performance -->
    <div class="bg-gradient-to-r from-indigo-900/50 to-purple-900/50 border border-indigo-700 rounded-lg p-6 mt-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-white">This Week's Performance</h3>
                <p class="text-gray-300 mt-1">Compare with last week</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">This Week</p>
                <p class="text-3xl font-bold text-white">GHS {{ number_format($data['weekly']['this_week'], 2) }}</p>
                <p class="text-sm mt-1">
                    <span class="{{ $data['weekly']['growth_percentage'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $data['weekly']['growth_percentage'] >= 0 ? '↑' : '↓' }}
                        {{ abs($data['weekly']['growth_percentage']) }}%
                    </span>
                    <span class="text-gray-400">vs last week (GHS {{ number_format($data['weekly']['last_week'], 2) }})</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Today's Deliveries -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Recent Orders -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
                <a href="{{ route('manager.orders.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($data['recent_orders'] as $order)
                <a href="{{ route('manager.orders.show', $order) }}" class="block p-4 bg-gray-700/50 hover:bg-gray-700 rounded-lg transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-medium text-white">#{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-400">{{ $order->customer->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-white">GHS {{ number_format($order->total, 2) }}</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                {{ $order->status === 'pending' ? 'bg-yellow-600' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-600' : '' }}
                                {{ $order->status === 'ready' ? 'bg-green-600' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-gray-600' : '' }}
                                text-white">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <p class="text-gray-400 text-center py-4">No recent orders</p>
                @endforelse
            </div>
        </div>

        <!-- Today's Deliveries -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Today's Deliveries</h3>
                <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm font-semibold">{{ $data['today_deliveries']->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($data['today_deliveries'] as $delivery)
                <div class="p-4 bg-gray-700/50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-medium text-white">#{{ $delivery->order_number }}</p>
                            <p class="text-sm text-gray-400">{{ $delivery->customer->name }}</p>
                            @if($delivery->delivery_address)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($delivery->delivery_address, 40) }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                {{ $delivery->status === 'ready' ? 'bg-green-600' : 'bg-blue-600' }}
                                text-white">
                                {{ ucfirst($delivery->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">No deliveries scheduled for today</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart.js Dark Theme Configuration
    Chart.defaults.color = '#9CA3AF';
    Chart.defaults.borderColor = '#374151';
    
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesChart['labels']),
            datasets: [{
                label: 'Sales (GHS)',
                data: @json($salesChart['data']),
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#374151'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Processing Chart
    const processingCtx = document.getElementById('processingChart').getContext('2d');
    new Chart(processingCtx, {
        type: 'doughnut',
        data: {
            labels: @json($processingChart['labels']),
            datasets: [{
                data: @json($processingChart['data']),
                backgroundColor: [
                    'rgba(234, 179, 8, 0.8)',   // pending - yellow
                    'rgba(59, 130, 246, 0.8)',  // in_progress - blue
                    'rgba(34, 197, 94, 0.8)',   // completed - green
                ],
                borderColor: '#1F2937',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        color: '#9CA3AF'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
