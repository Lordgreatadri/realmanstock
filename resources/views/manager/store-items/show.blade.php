@extends('layouts.manager')

@section('title', 'Store Item Details')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('manager.store-items.index') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Store Item Details</h1>
                <p class="text-gray-400 mt-1">{{ $storeItem->name }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.store-items.edit', $storeItem) }}" 
               class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Details -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Basic Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-400">SKU</p>
                    <p class="text-white font-semibold font-mono">{{ $storeItem->sku }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Item Name</p>
                    <p class="text-white font-semibold">{{ $storeItem->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Category</p>
                    <p class="text-white font-semibold">{{ $storeItem->category->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Supplier</p>
                    <p class="text-white font-semibold">{{ $storeItem->supplier ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Status</p>
                    <div class="mt-1">
                        @if($storeItem->is_active)
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-green-200 text-green-900">Active</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-gray-200 text-gray-900">Inactive</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Unit</p>
                    <p class="text-white font-semibold">{{ ucfirst($storeItem->unit) }}</p>
                </div>
            </div>

            @if($storeItem->description)
            <div class="mt-4">
                <p class="text-sm text-gray-400">Description</p>
                <p class="text-white mt-1">{{ $storeItem->description }}</p>
            </div>
            @endif
        </div>

        <!-- Stock Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Stock Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-400">Current Stock</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($storeItem->quantity, 2) }} <span class="text-lg text-gray-400">{{ $storeItem->unit }}</span></p>
                    @if($storeItem->quantity <= 0)
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-red-200 text-red-900 rounded">Out of Stock</span>
                    @elseif($storeItem->quantity <= $storeItem->reorder_level)
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-900 rounded">Low Stock</span>
                    @else
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-green-200 text-green-900 rounded">In Stock</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-400">Reorder Level</p>
                    <p class="text-3xl font-bold text-yellow-400">{{ number_format($storeItem->reorder_level, 2) }} <span class="text-lg text-gray-400">{{ $storeItem->unit }}</span></p>
                </div>
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Pricing Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-400">Cost Price</p>
                    <p class="text-2xl font-bold text-white">GH₵{{ number_format($storeItem->cost_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Selling Price</p>
                    <p class="text-2xl font-bold text-indigo-400">GH₵{{ number_format($storeItem->selling_price, 2) }}</p>
                </div>
            </div>

            @if($storeItem->cost_price > 0)
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-400">Profit per Unit</p>
                    <p class="text-xl font-bold text-green-400">GH₵{{ number_format($storeItem->selling_price - $storeItem->cost_price, 2) }}</p>
                </div>
                <div class="p-4 bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-400">Profit Margin</p>
                    <p class="text-xl font-bold text-yellow-400">
                        {{ number_format((($storeItem->selling_price - $storeItem->cost_price) / $storeItem->cost_price) * 100, 1) }}%
                    </p>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-400">Total Stock Value (at cost)</p>
                <p class="text-2xl font-bold text-white">GH₵{{ number_format($storeItem->quantity * $storeItem->cost_price, 2) }}</p>
            </div>

            <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-400">Potential Revenue (at selling price)</p>
                <p class="text-2xl font-bold text-indigo-400">GH₵{{ number_format($storeItem->quantity * $storeItem->selling_price, 2) }}</p>
            </div>
            @endif
        </div>

        @if($storeItem->notes)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Notes</h2>
            <p class="text-white">{{ $storeItem->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Stock Adjustment -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Quick Stock Adjustment</h3>
            <form action="{{ route('manager.store-items.adjust-stock', $storeItem) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <select name="adjustment_type" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        <option value="add">Add Stock</option>
                        <option value="subtract">Remove Stock</option>
                        <option value="set">Set Stock</option>
                    </select>

                    <input type="number" name="quantity" step="0.01" required placeholder="Quantity"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">

                    <textarea name="notes" rows="2" placeholder="Notes (optional)"
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500"></textarea>

                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        Adjust Stock
                    </button>
                </div>
            </form>
        </div>

        <!-- Toggle Status -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Item Status</h3>
            <form action="{{ route('manager.store-items.toggle-status', $storeItem) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full px-4 py-2 {{ $storeItem->is_active ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition">
                    {{ $storeItem->is_active ? 'Deactivate Item' : 'Activate Item' }}
                </button>
            </form>
        </div>

        <!-- Timestamps -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Timestamps</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-400">Created At</p>
                    <p class="text-white text-sm">{{ $storeItem->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Last Updated</p>
                    <p class="text-white text-sm">{{ $storeItem->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
