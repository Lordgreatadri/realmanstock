@extends('layouts.manager')

@section('title', 'Freezer Inventory')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-bold text-white">Freezer Inventory</h1>
        <p class="text-gray-400 mt-2">Manage freezer stock and inventory</p>
    </div>
    <a href="{{ route('manager.freezer.create') }}" 
       class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Inventory
    </a>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Total Items</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['total'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">In Stock</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['in_stock'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Reserved</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['reserved'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Sold</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['sold'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Expiring Soon</div>
        <div class="text-2xl font-bold mt-1">{{ $stats['expiring_soon'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-pink-600 to-rose-600 rounded-lg p-4 text-white">
        <div class="text-xs opacity-80">Total Weight</div>
        <div class="text-2xl font-bold mt-1">{{ number_format($stats['total_weight'], 1) }}kg</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" name="search" placeholder="Search product or batch..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
        </div>

        <div>
            <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Status</option>
                <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
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
            <select name="expiring_soon" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Dates</option>
                <option value="7" {{ request('expiring_soon') == '7' ? 'selected' : '' }}>Expiring in 7 days</option>
                <option value="14" {{ request('expiring_soon') == '14' ? 'selected' : '' }}>Expiring in 14 days</option>
                <option value="30" {{ request('expiring_soon') == '30' ? 'selected' : '' }}>Expiring in 30 days</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Filter
            </button>
            <a href="{{ route('manager.freezer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Inventory Table -->
<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Batch #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Weight</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price/kg</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Expiry Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse($inventories as $inventory)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">{{ $inventory->batch_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $inventory->product_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $inventory->category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ number_format($inventory->weight, 2) }}kg</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">GH₵{{ number_format($inventory->selling_price_per_kg, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $inventory->expiry_date->format('M d, Y') }}
                            @if($inventory->expiry_date->isPast())
                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-200 text-red-900 rounded">Expired</span>
                            @elseif($inventory->expiry_date->diffInDays(now()) <= 7)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-900 rounded">Soon</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'in_stock' => 'bg-green-200 text-green-900',
                                    'reserved' => 'bg-blue-200 text-blue-900',
                                    'sold' => 'bg-gray-200 text-gray-900',
                                    'expired' => 'bg-red-200 text-red-900',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$inventory->status] ?? 'bg-gray-200 text-gray-900' }}">
                                {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('manager.freezer.show', $inventory) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('manager.freezer.edit', $inventory) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                @if($inventory->status !== 'sold')
                                <form action="{{ route('manager.freezer.destroy', $inventory) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this inventory item?');" class="inline">
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
                                @endif
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
                                <p class="text-lg">No inventory items found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($inventories->hasPages())
        <div class="bg-gray-700 px-6 py-4">
            {{ $inventories->links() }}
        </div>
    @endif
</div>
@endsection
