@extends('layouts.manager')

@section('title', 'Store Items')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-bold text-white">Store Items</h1>
        <p class="text-gray-400 mt-2">Manage store inventory and stock levels</p>
    </div>
    <a href="{{ route('manager.store-items.create') }}" 
       class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Store Item
    </a>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Total Items</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['total'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Active Items</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['active'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Low Stock</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['low_stock'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-red-600 to-pink-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Out of Stock</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['out_of_stock'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Total Quantity</div>
        <div class="text-2xl font-bold mt-1">{{ number_format($stats['total_items_qty'], 0) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-pink-600 to-rose-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Stock Value</div>
        <div class="text-2xl font-bold mt-1">GH₵{{ number_format($stats['total_value'], 0) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" name="search" placeholder="Search name, SKU, supplier..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
        </div>

        <div>
            <select name="category_id" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div>
            <select name="stock_status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Stock Levels</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Filter
            </button>
            <a href="{{ route('manager.store-items.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Items Table -->
<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Reorder Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Selling Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse($storeItems as $item)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">{{ $item->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $item->name }}</div>
                            @if($item->supplier)
                                <div class="text-xs text-gray-400">{{ $item->supplier }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $item->category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-white">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</div>
                            @if($item->quantity <= 0)
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-red-200 text-red-900 rounded">Out of Stock</span>
                            @elseif($item->quantity <= $item->reorder_level)
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-900 rounded">Low Stock</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ number_format($item->reorder_level, 2) }} {{ $item->unit }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">GH₵{{ number_format($item->selling_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-200 text-green-900">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-200 text-gray-900">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('manager.store-items.show', $item) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('manager.store-items.edit', $item) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('manager.store-items.destroy', $item) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this store item?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-lg">No store items found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($storeItems->hasPages())
        <div class="bg-gray-700 px-6 py-4">
            {{ $storeItems->links() }}
        </div>
    @endif
</div>
@endsection
