@extends('layouts.manager')

@section('title', 'Processing Request Details')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-bold text-white">Processing Request #{{ $processing->id }}</h1>
        <p class="text-gray-400 mt-2">View processing request details</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('manager.processing.edit', $processing) }}" 
           class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg transition">
            Edit
        </a>
        <a href="{{ route('manager.processing.index') }}" 
           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            Back to List
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-800 border border-green-600 text-green-100 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<!-- Request Overview -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium mb-2">Status</h3>
        @php
            $statusColors = [
                'pending' => 'bg-yellow-200 text-yellow-900',
                'in_progress' => 'bg-blue-200 text-blue-900',
                'completed' => 'bg-green-200 text-green-900',
                'cancelled' => 'bg-red-200 text-red-900',
            ];
        @endphp
        <span class="px-3 py-1 text-sm font-semibold rounded {{ $statusColors[$processing->status] ?? 'bg-gray-200 text-gray-900' }}">
            {{ ucfirst(str_replace('_', ' ', $processing->status)) }}
        </span>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium mb-2">Processing Fee</h3>
        <p class="text-2xl font-bold text-white">GH₵{{ number_format($processing->processing_fee, 2) }}</p>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium mb-2">Requested Date</h3>
        <p class="text-2xl font-bold text-white">{{ $processing->requested_date->format('M d, Y') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Customer & Animal Information -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-white mb-6">Request Information</h3>
        
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-400">Customer</p>
                <p class="text-white font-medium">{{ $processing->customer->name }}</p>
                <p class="text-sm text-gray-400">{{ $processing->customer->phone }}</p>
            </div>

            @if($processing->animal)
            <div>
                <p class="text-sm text-gray-400">Animal</p>
                <p class="text-white font-medium">{{ $processing->animal->tag_number }} - {{ $processing->animal->breed }}</p>
                <p class="text-sm text-gray-400">Current Weight: {{ $processing->animal->current_weight }}kg</p>
            </div>
            @endif

            @if($processing->order)
            <div>
                <p class="text-sm text-gray-400">Related Order</p>
                <a href="{{ route('manager.orders.show', $processing->order) }}" class="text-indigo-400 hover:text-indigo-300">
                    {{ $processing->order->order_number }}
                </a>
            </div>
            @endif

            @if($processing->processedBy)
            <div>
                <p class="text-sm text-gray-400">Processed By</p>
                <p class="text-white font-medium">{{ $processing->processedBy->name }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Processing Details -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-white mb-6">Processing Details</h3>
        
        <div class="space-y-4">
            @if($processing->scheduled_date)
            <div>
                <p class="text-sm text-gray-400">Scheduled Date</p>
                <p class="text-white font-medium">{{ $processing->scheduled_date->format('M d, Y') }}</p>
            </div>
            @endif

            @if($processing->completed_date)
            <div>
                <p class="text-sm text-gray-400">Completed Date</p>
                <p class="text-white font-medium">{{ $processing->completed_date->format('M d, Y') }}</p>
            </div>
            @endif

            @if($processing->live_weight)
            <div>
                <p class="text-sm text-gray-400">Live Weight</p>
                <p class="text-white font-medium">{{ $processing->live_weight }}kg</p>
            </div>
            @endif

            @if($processing->dressed_weight)
            <div>
                <p class="text-sm text-gray-400">Dressed Weight</p>
                <p class="text-white font-medium">{{ $processing->dressed_weight }}kg</p>
                @if($processing->live_weight)
                    <p class="text-sm text-gray-400">
                        Dressing Percentage: {{ number_format(($processing->dressed_weight / $processing->live_weight) * 100, 1) }}%
                    </p>
                @endif
            </div>
            @endif

            @if($processing->special_instructions)
            <div>
                <p class="text-sm text-gray-400">Special Instructions</p>
                <p class="text-white">{{ $processing->special_instructions }}</p>
            </div>
            @endif

            @if($processing->quality_notes)
            <div>
                <p class="text-sm text-gray-400">Quality Notes</p>
                <p class="text-white">{{ $processing->quality_notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Status Update -->
@if($processing->status !== 'completed' && $processing->status !== 'cancelled')
<div class="mt-6 bg-gray-800 rounded-lg shadow-lg p-6">
    <h3 class="text-xl font-bold text-white mb-4">Quick Status Update</h3>
    
    <form action="{{ route('manager.processing.update-status', $processing) }}" method="POST" class="flex gap-4">
        @csrf
        <select name="status" required
                class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            <option value="pending" {{ $processing->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ $processing->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ $processing->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $processing->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
            Update Status
        </button>
    </form>
</div>
@endif
@endsection
