@extends('layouts.manager')

@section('title', 'Orders')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-bold text-white">Orders</h1>
        <p class="text-gray-400 mt-2">Manage customer orders and deliveries</p>
    </div>
    <a href="{{ route('manager.orders.create') }}" 
       class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create Order
    </a>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Orders</div>
        <div class="text-3xl font-bold mt-2">{{ $orders->total() }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Revenue</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($orders->sum('total'), 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Outstanding Balance</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($orders->sum('balance'), 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Avg Order Value</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($orders->avg('total') ?? 0, 2) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('manager.orders.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Search Order #</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="ORD-..." 
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="payment_received" {{ request('status') == 'payment_received' ? 'selected' : '' }}>Payment Received</option>
                <option value="ready_for_delivery" {{ request('status') == 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
                <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <!-- Payment Status -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Payment Status</label>
            <select name="payment_status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            </select>
        </div>

        <!-- Delivery Type -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Type</label>
            <select name="delivery_type" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All</option>
                <option value="pickup" {{ request('delivery_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                <option value="delivery" {{ request('delivery_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
            </select>
        </div>

        <!-- Customer -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
            <select name="customer_id" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
        </div>

        <!-- Date To -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
        </div>

        <!-- Buttons -->
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-1.5 text-sm bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                Filter
            </button>
            <a href="{{ route('manager.orders.index') }}" class="px-4 py-1.5 text-sm bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Delivery</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('manager.orders.show', $order) }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-white">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div>
                        <div class="text-xs text-gray-400">{{ $order->customer?->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $order->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-white">
                        GH₵{{ number_format($order->total, 2) }}
                        <div class="text-xs text-gray-400">{{ $order->items->count() }} items</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($order->balance > 0)
                            <span class="text-sm font-semibold text-red-400">GH₵{{ number_format($order->balance, 2) }}</span>
                        @else
                            <span class="text-sm font-semibold text-green-400">Paid</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-200 text-yellow-900',
                                'processing' => 'bg-blue-200 text-blue-900',
                                'payment_received' => 'bg-green-200 text-green-900',
                                'ready_for_delivery' => 'bg-purple-200 text-purple-900',
                                'out_for_delivery' => 'bg-indigo-200 text-indigo-900',
                                'delivered' => 'bg-emerald-200 text-emerald-900',
                                'cancelled' => 'bg-red-200 text-red-900',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-200 text-gray-900' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->delivery_type === 'delivery' ? 'bg-purple-900 text-purple-300' : 'bg-gray-700 text-gray-300' }}">
                            {{ ucfirst($order->delivery_type) }}
                        </span>
                        @if($order->delivery_date)
                            <div class="text-xs text-gray-400 mt-1">{{ $order->delivery_date->format('M d') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('manager.orders.show', $order) }}" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                View
                            </a>
                            <a href="{{ route('manager.orders.edit', $order) }}" class="px-2 py-1 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs rounded hover:from-emerald-700 hover:to-teal-700">
                                Edit
                            </a>
                            @if($order->status !== 'delivered')
                            <form action="{{ route('manager.orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
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
                        <div class="text-lg mb-2">No orders found</div>
                        <p class="text-sm">Orders will appear here when customers place them.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="bg-gray-900 px-6 py-4">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
