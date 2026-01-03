@extends('layouts.manager')

@section('title', 'Inventory Report')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div class="flex items-center gap-4">
        <a href="{{ route('manager.reports.index') }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Inventory Report</h1>
            <p class="text-gray-400 mt-1">Stock levels and valuations</p>
        </div>
    </div>
    <form action="{{ route('manager.reports.export-inventory') }}" method="GET" class="inline">
        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Excel
        </button>
    </form>
</div>

<!-- Store Items by Category -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Store Items by Category</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($storeItems as $item)
                    <tr>
                        <td class="px-6 py-4 text-white">{{ $item->category->name }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $item->item_count }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ number_format($item->total_quantity, 2) }}</td>
                        <td class="px-6 py-4 text-white font-semibold">GH₵{{ number_format($item->total_value, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Low Stock Items -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-white mb-4">Low Stock Items ({{ $lowStockItems->count() }})</h2>
        <div class="space-y-2 max-h-96 overflow-y-auto">
            @forelse($lowStockItems as $item)
                <div class="flex justify-between items-center p-3 bg-yellow-900/20 border border-yellow-700 rounded-lg">
                    <div>
                        <p class="text-white font-semibold">{{ $item->name }}</p>
                        <p class="text-sm text-gray-400">{{ $item->category->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-yellow-400 font-bold">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</p>
                        <p class="text-xs text-gray-400">Reorder: {{ number_format($item->reorder_level, 2) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">No low stock items</p>
            @endforelse
        </div>
    </div>

    <!-- Out of Stock Items -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-white mb-4">Out of Stock Items ({{ $outOfStockItems->count() }})</h2>
        <div class="space-y-2 max-h-96 overflow-y-auto">
            @forelse($outOfStockItems as $item)
                <div class="flex justify-between items-center p-3 bg-red-900/20 border border-red-700 rounded-lg">
                    <div>
                        <p class="text-white font-semibold">{{ $item->name }}</p>
                        <p class="text-sm text-gray-400">{{ $item->category->name }}</p>
                    </div>
                    <span class="px-3 py-1 bg-red-200 text-red-900 text-xs font-semibold rounded">Out of Stock</span>
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">All items in stock</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Freezer Inventory -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Freezer Inventory by Status</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($freezerInventory as $item)
            <div class="bg-gray-700 rounded-lg p-4">
                <p class="text-sm text-gray-400">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $item->count }}</p>
                <p class="text-sm text-gray-400 mt-2">{{ number_format($item->total_weight, 2) }} kg</p>
                <p class="text-sm text-green-400 font-semibold">GH₵{{ number_format($item->total_value, 2) }}</p>
            </div>
        @endforeach
    </div>
</div>

<!-- Expiring Items -->
@if($expiringItems->count() > 0)
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-white mb-4">Expiring Soon ({{ $expiringItems->count() }})</h2>
    <div class="space-y-2">
        @foreach($expiringItems as $item)
            <div class="flex justify-between items-center p-3 bg-orange-900/20 border border-orange-700 rounded-lg">
                <div>
                    <p class="text-white font-semibold">{{ $item->product_name }}</p>
                    <p class="text-sm text-gray-400">{{ $item->batch_number }} - {{ $item->category->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-orange-400 font-bold">{{ $item->expiry_date->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $item->expiry_date->diffForHumans() }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Animals Inventory -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-bold text-white mb-4">Animals Inventory</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($animalsInventory as $item)
            <div class="bg-gray-700 rounded-lg p-4">
                <p class="text-sm text-gray-400">{{ ucfirst($item->status) }}</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $item->count }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection

