@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Dashboard</h1>
            <p class="text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="text-gray-400 text-sm">
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="text-green-400 text-sm font-medium">+{{ $data['users']['new_this_month'] ?? 0 }} this month</span>
            </div>
            <div>
                <h3 class="text-gray-400 text-sm font-medium">Total Users</h3>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['users']['total'] ?? 0 }}</p>
                <p class="text-gray-500 text-xs mt-2">{{ $data['users']['pending'] ?? 0 }} pending approval</p>
            </div>
        </div>

        <!-- Total Animals -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="text-green-400 text-sm font-medium">{{ $data['animals']['available'] ?? 0 }} available</span>
            </div>
            <div>
                <h3 class="text-gray-400 text-sm font-medium">Total Animals</h3>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['animals']['total'] ?? 0 }}</p>
                <p class="text-gray-500 text-xs mt-2">{{ $data['animals']['sold'] ?? 0 }} sold</p>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <span class="text-yellow-400 text-sm font-medium">{{ $data['orders']['pending'] ?? 0 }} pending</span>
            </div>
            <div>
                <h3 class="text-gray-400 text-sm font-medium">Total Orders</h3>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['orders']['total'] ?? 0 }}</p>
                <p class="text-gray-500 text-xs mt-2">{{ $data['orders']['completed'] ?? 0 }} completed</p>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-green-400 text-sm font-medium">+{{ number_format($data['revenue']['growth_percentage'] ?? 0, 1) }}%</span>
            </div>
            <div>
                <h3 class="text-gray-400 text-sm font-medium">Total Revenue</h3>
                <p class="text-3xl font-bold text-white mt-1">GH₵{{ number_format($data['revenue']['total'] ?? 0, 2) }}</p>
                <p class="text-gray-500 text-xs mt-2">GH₵{{ number_format($data['revenue']['this_month'] ?? 0, 2) }} this month</p>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Revenue Overview</h2>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Recent Activities</h2>
            <div class="space-y-4">
                @forelse($data['recent_activities'] ?? [] as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 mt-2 rounded-full bg-indigo-500"></div>
                        <div class="flex-1">
                            <p class="text-gray-300 text-sm">{{ $activity['description'] }}</p>
                            <p class="text-gray-500 text-xs mt-1">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-8">No recent activities</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders & Pending Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-gray-400 text-xs uppercase">
                            <th class="text-left pb-3">Order ID</th>
                            <th class="text-left pb-3">Customer</th>
                            <th class="text-left pb-3">Amount</th>
                            <th class="text-left pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300 text-sm">
                        @forelse($data['recent_orders'] ?? [] as $order)
                            <tr class="border-t border-gray-800">
                                <td class="py-3">#{{ $order->order_number }}</td>
                                <td class="py-3">{{ $order->customer->name ?? 'N/A' }}</td>
                                <td class="py-3">GH₵{{ number_format($order->total, 2) }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs 
                                        @if($order->status === 'delivered') bg-green-500/20 text-green-400
                                        @elseif($order->status === 'pending') bg-yellow-500/20 text-yellow-400
                                        @else bg-blue-500/20 text-blue-400
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">No recent orders</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending User Approvals -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white">Pending Approvals</h2>
                <a href="{{ route('admin.users.pending') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($data['pending_approvals'] ?? [] as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium">{{ $user->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $user->phone }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.pending') }}" class="text-indigo-400 hover:text-indigo-300 text-xs">Review</a>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-8">No pending approvals</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($revenueChart['labels'] ?? []),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueChart['data'] ?? []),
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
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
                        color: 'rgba(75, 85, 99, 0.2)'
                    },
                    ticks: {
                        color: 'rgb(156, 163, 175)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(75, 85, 99, 0.2)'
                    },
                    ticks: {
                        color: 'rgb(156, 163, 175)'
                    }
                }
            }
        }
    });
</script>
@endpush
