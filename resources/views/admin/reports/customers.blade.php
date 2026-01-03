@extends('layouts.admin')

@section('title', 'Customer Report')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.reports.index') }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Customer Report</h1>
            <p class="text-gray-400 mt-1">Customer analytics and insights</p>
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
        <a href="{{ route('admin.reports.export-customers', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Excel
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Customers</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['total_customers'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">New Customers</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['new_customers'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Active Customers</div>
        <div class="text-3xl font-bold mt-2">{{ $summary['active_customers'] }}</div>
    </div>
</div>

<!-- Top Customers -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Top Customers by Revenue</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Credit Used</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($topCustomers as $index => $customer)
                    <tr>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-600 text-white rounded-full font-bold">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white font-semibold">{{ $customer->name }}</div>
                            <div class="text-sm text-gray-400">{{ $customer->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $customer->orders_count }}</td>
                        <td class="px-6 py-4 text-white font-bold">GH₵{{ number_format($customer->orders_sum_total ?? 0, 2) }}</td>
                        <td class="px-6 py-4 text-gray-300">GH₵{{ number_format($customer->credit_used, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">No customer data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Credit Analysis -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-bold text-white mb-4">Credit Analysis</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-400">Customers with Credit</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $creditAnalysis['customer_count'] }}</p>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-400">Total Credit Limit</p>
            <p class="text-2xl font-bold text-white mt-1">GH₵{{ number_format($creditAnalysis['total_credit_limit'], 2) }}</p>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-400">Credit Used</p>
            <p class="text-2xl font-bold text-orange-400 mt-1">GH₵{{ number_format($creditAnalysis['total_credit_used'], 2) }}</p>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-400">Available Credit</p>
            <p class="text-2xl font-bold text-green-400 mt-1">GH₵{{ number_format($creditAnalysis['available_credit'], 2) }}</p>
        </div>
    </div>
</div>
@endsection
