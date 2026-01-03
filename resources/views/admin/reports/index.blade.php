@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-white">Business Reports</h1>
    <p class="text-gray-400 mt-2">Comprehensive business analytics and reports</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Sales Report -->
    <a href="{{ route('admin.reports.sales') }}" class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-6 text-white hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-105 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold">Sales Report</h3>
                <p class="text-indigo-200 text-sm mt-1">Revenue and orders analysis</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <p class="text-sm">View detailed sales reports with charts and export options</p>
    </a>

    <!-- Inventory Report -->
    <a href="{{ route('admin.reports.inventory') }}" class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white hover:from-green-700 hover:to-teal-700 transition transform hover:scale-105 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold">Inventory Report</h3>
                <p class="text-green-200 text-sm mt-1">Stock levels and valuations</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
        <p class="text-sm">Monitor stock levels, low stock items, and inventory value</p>
    </a>

    <!-- Customer Report -->
    <a href="{{ route('admin.reports.customers') }}" class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white hover:from-blue-700 hover:to-cyan-700 transition transform hover:scale-105 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold">Customer Report</h3>
                <p class="text-blue-200 text-sm mt-1">Customer analytics</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
        <p class="text-sm">Top customers, new signups, and credit analysis</p>
    </a>

    <!-- Processing Report -->
    <a href="{{ route('admin.reports.processing') }}" class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-6 text-white hover:from-yellow-700 hover:to-orange-700 transition transform hover:scale-105 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold">Processing Report</h3>
                <p class="text-yellow-200 text-sm mt-1">Processing efficiency</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>
        <p class="text-sm">Processing requests, efficiency, and dressing percentages</p>
    </a>

    <!-- Financial Report -->
    <a href="{{ route('admin.reports.financial') }}" class="bg-gradient-to-br from-pink-600 to-rose-600 rounded-lg p-6 text-white hover:from-pink-700 hover:to-rose-700 transition transform hover:scale-105 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold">Financial Report</h3>
                <p class="text-pink-200 text-sm mt-1">Revenue and payments</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <p class="text-sm">Revenue trends, payment analysis, and financial overview</p>
    </a>

    <!-- Custom Report (Future) -->
    <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg p-6 text-white opacity-75 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold">Custom Report</h3>
                <p class="text-gray-300 text-sm mt-1">Coming soon</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <p class="text-sm">Create custom reports with flexible filters</p>
    </div>
</div>

<!-- Quick Stats -->
<div class="mt-8">
    <h2 class="text-2xl font-bold text-white mb-4">Quick Overview</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-400">Today's Sales</p>
            <p class="text-2xl font-bold text-white mt-1">GH₵{{ number_format(\App\Models\Order::whereDate('created_at', today())->sum('total'), 2) }}</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-400">Pending Orders</p>
            <p class="text-2xl font-bold text-white mt-1">{{ \App\Models\Order::whereIn('status', ['pending', 'processing'])->count() }}</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-400">Low Stock Items</p>
            <p class="text-2xl font-bold text-white mt-1">{{ \App\Models\StoreItem::whereRaw('quantity <= reorder_level')->count() }}</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-400">Outstanding Balance</p>
            <p class="text-2xl font-bold text-white mt-1">GH₵{{ number_format(\App\Models\Order::sum('balance'), 2) }}</p>
        </div>
    </div>
</div>
@endsection
