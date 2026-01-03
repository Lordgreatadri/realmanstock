@extends('layouts.manager')

@section('title', 'Customer Details - ' . $customer->name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">{{ $customer->name }}</h1>
            <p class="text-gray-400 mt-2">Customer since {{ $customer->created_at->format('F d, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('manager.customers.edit', $customer) }}" class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all">
                Edit Customer
            </a>
            <a href="{{ route('manager.customers.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                Back to Customers
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-900 border border-green-700 text-green-300 px-6 py-4 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Recent Orders -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-900 border-b border-gray-700 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">Recent Orders</h2>
                @if($customer->orders->count() > 0)
                    <span class="text-sm text-gray-400">Showing latest 10</span>
                @endif
            </div>
            
            @if($customer->orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($customer->orders as $order)
                        <tr class="hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('manager.orders.show', $order) }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-white">
                                GH₵{{ number_format($order->total, 2) }}
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('manager.orders.show', $order) }}" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center text-gray-400">
                <div class="text-lg mb-2">No orders yet</div>
                <p class="text-sm">This customer hasn't placed any orders.</p>
            </div>
            @endif
        </div>

        <!-- Order Statistics -->
        @if($customer->orders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-6 text-white">
                <div class="text-sm opacity-80">Total Orders</div>
                <div class="text-3xl font-bold mt-2">{{ $customer->orders()->count() }}</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
                <div class="text-sm opacity-80">Total Spent</div>
                <div class="text-3xl font-bold mt-2">GH₵{{ number_format($customer->orders()->sum('total'), 2) }}</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
                <div class="text-sm opacity-80">Average Order</div>
                <div class="text-3xl font-bold mt-2">GH₵{{ number_format($customer->orders()->avg('total'), 2) }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Contact Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-400">Phone</div>
                    <div class="text-white">{{ $customer->phone }}</div>
                </div>
                @if($customer->email)
                <div>
                    <div class="text-xs text-gray-400">Email</div>
                    <div class="text-white">{{ $customer->email }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Address -->
        @if($customer->address || $customer->city || $customer->state)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Address</h3>
            <div class="text-gray-300 space-y-1">
                @if($customer->address)
                    <div>{{ $customer->address }}</div>
                @endif
                @if($customer->city || $customer->state)
                    <div>{{ $customer->city }}{{ $customer->city && $customer->state ? ', ' : '' }}{{ $customer->state }}</div>
                @endif
            </div>
        </div>
        @endif

        <!-- Credit Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Credit Information</h3>
            <div class="space-y-4">
                @if($customer->allow_credit)
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Credit Limit</div>
                        <div class="text-2xl font-bold text-white">GH₵{{ number_format($customer->credit_limit, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Outstanding Balance</div>
                        <div class="text-2xl font-bold {{ $customer->outstanding_balance > 0 ? 'text-red-400' : 'text-green-400' }}">
                            GH₵{{ number_format($customer->outstanding_balance, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Available Credit</div>
                        <div class="text-xl font-semibold text-white">
                            GH₵{{ number_format($customer->credit_limit - $customer->outstanding_balance, 2) }}
                        </div>
                    </div>
                @else
                    <div class="text-gray-400 text-sm">Credit purchases not allowed</div>
                @endif
            </div>
        </div>

        <!-- Preferences -->
        @if($customer->preferred_delivery || $customer->preferred_processing)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Preferences</h3>
            <div class="space-y-3">
                @if($customer->preferred_delivery)
                <div>
                    <div class="text-xs text-gray-400">Delivery Method</div>
                    <div class="text-white">{{ ucfirst($customer->preferred_delivery) }}</div>
                </div>
                @endif
                @if($customer->preferred_processing)
                <div>
                    <div class="text-xs text-gray-400">Processing</div>
                    <div class="text-white">{{ $customer->preferred_processing }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($customer->notes)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Notes</h3>
            <p class="text-gray-300 text-sm">{{ $customer->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
