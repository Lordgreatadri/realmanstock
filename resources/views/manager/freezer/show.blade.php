@extends('layouts.manager')

@section('title', 'Freezer Inventory Details')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('manager.freezer.index') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Inventory Details</h1>
                <p class="text-gray-400 mt-1">{{ $inventory->product_name }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('manager.freezer.edit', $inventory) }}" 
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
                    <p class="text-sm text-gray-400">Batch Number</p>
                    <p class="text-white font-semibold font-mono">{{ $inventory->batch_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Product Name</p>
                    <p class="text-white font-semibold">{{ $inventory->product_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Category</p>
                    <p class="text-white font-semibold">{{ $inventory->category->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Status</p>
                    <div class="mt-1">
                        @php
                            $statusColors = [
                                'in_stock' => 'bg-green-200 text-green-900',
                                'reserved' => 'bg-blue-200 text-blue-900',
                                'sold' => 'bg-gray-200 text-gray-900',
                                'expired' => 'bg-red-200 text-red-900',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded {{ $statusColors[$inventory->status] ?? 'bg-gray-200 text-gray-900' }}">
                            {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weight & Pricing -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Weight & Pricing</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-400">Weight</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($inventory->weight, 2) }} kg</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Cost Price</p>
                    <p class="text-2xl font-bold text-white">GH₵{{ number_format($inventory->cost_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Selling Price/kg</p>
                    <p class="text-2xl font-bold text-indigo-400">GH₵{{ number_format($inventory->selling_price_per_kg, 2) }}</p>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-400">Total Value (at selling price)</p>
                <p class="text-3xl font-bold text-green-400">GH₵{{ number_format($inventory->weight * $inventory->selling_price_per_kg, 2) }}</p>
            </div>

            @if($inventory->cost_price > 0)
            <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-400">Potential Profit</p>
                <p class="text-2xl font-bold text-yellow-400">
                    GH₵{{ number_format(($inventory->weight * $inventory->selling_price_per_kg) - $inventory->cost_price, 2) }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ number_format((($inventory->weight * $inventory->selling_price_per_kg) - $inventory->cost_price) / $inventory->cost_price * 100, 1) }}% margin
                </p>
            </div>
            @endif
        </div>

        <!-- Storage Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Storage Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-400">Storage Location</p>
                    <p class="text-white font-semibold">{{ $inventory->storage_location }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Temperature Zone</p>
                    <p class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $inventory->temperature_zone)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Processing Date</p>
                    <p class="text-white font-semibold">{{ $inventory->processing_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Expiry Date</p>
                    <p class="text-white font-semibold">{{ $inventory->expiry_date->format('M d, Y') }}</p>
                    @if($inventory->expiry_date->isPast())
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-red-200 text-red-900 rounded">Expired</span>
                    @elseif($inventory->expiry_date->diffInDays(now()) <= 7)
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-900 rounded">Expiring in {{ $inventory->expiry_date->diffInDays(now()) }} days</span>
                    @else
                        <span class="text-xs text-gray-500">{{ $inventory->expiry_date->diffForHumans() }}</span>
                    @endif
                </div>
            </div>

            @if($inventory->quality_notes)
            <div class="mt-4">
                <p class="text-sm text-gray-400">Quality Notes</p>
                <p class="text-white mt-1">{{ $inventory->quality_notes }}</p>
            </div>
            @endif
        </div>

        <!-- Processing Request Link -->
        @if($inventory->processingRequest)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-4">Processing Request</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Batch Number</p>
                    <p class="text-white font-semibold font-mono">{{ $inventory->processingRequest->batch_number }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Customer: {{ $inventory->processingRequest->customer->name }} | 
                        Animal: {{ $inventory->processingRequest->animal->name }}
                    </p>
                </div>
                <a href="{{ route('manager.processing.show', $inventory->processingRequest) }}" 
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                    View Processing Request
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Status Update -->
        @if($inventory->status !== 'sold')
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Quick Status Update</h3>
            <form action="{{ route('manager.freezer.update-status', $inventory) }}" method="POST">
                @csrf
                @method('PATCH')
                <select name="status" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 mb-3">
                    <option value="in_stock" {{ $inventory->status == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="reserved" {{ $inventory->status == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="sold" {{ $inventory->status == 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="expired" {{ $inventory->status == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    Update Status
                </button>
            </form>
        </div>
        @endif

        <!-- Timestamps -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Timestamps</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-400">Created At</p>
                    <p class="text-white text-sm">{{ $inventory->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Last Updated</p>
                    <p class="text-white text-sm">{{ $inventory->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
