@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp
@extends($layout)

@section('title', 'Edit Order - ' . $order->order_number)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Order {{ $order->order_number }}</h1>
            <p class="text-gray-400 mt-2">Update order status, payment, and delivery information</p>
        </div>
        <a href="{{ route($routePrefix . '.orders.show', $order) }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
            Cancel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-900 border border-green-700 text-green-300 px-6 py-4 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-900 border border-red-700 text-red-300 px-6 py-4 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route($routePrefix . '.orders.update', $order) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Order Status</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                            <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="payment_received" {{ old('status', $order->status) == 'payment_received' ? 'selected' : '' }}>Payment Received</option>
                            <option value="ready_for_delivery" {{ old('status', $order->status) == 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
                            <option value="out_for_delivery" {{ old('status', $order->status) == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status Notes</label>
                        <input type="text" name="status_notes" value="{{ old('status_notes') }}" 
                               placeholder="Reason for status change..."
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Payment Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                            <option value="">Select method...</option>
                            <option value="cash" {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_money" {{ old('payment_method', $order->payment_method) == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="credit" {{ old('payment_method', $order->payment_method) == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Record Payment (GH₵)</label>
                        <input type="number" step="0.01" min="0" name="amount_paid" value="{{ old('amount_paid') }}" 
                               placeholder="0.00"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Current balance: GH₵{{ number_format($order->balance, 2) }}</p>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Total:</span>
                            <span class="text-white font-semibold ml-2">GH₵{{ number_format($order->total, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Paid:</span>
                            <span class="text-green-400 font-semibold ml-2">GH₵{{ number_format($order->amount_paid, 2) }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400">Balance:</span>
                            <span class="text-red-400 font-semibold ml-2">GH₵{{ number_format($order->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Delivery Information</h2>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Type *</label>
                            <select name="delivery_type" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                <option value="pickup" {{ old('delivery_type', $order->delivery_type) == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                <option value="delivery" {{ old('delivery_type', $order->delivery_type) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Date</label>
                            <input type="datetime-local" name="delivery_date" 
                                   value="{{ old('delivery_date', $order->delivery_date?->format('Y-m-d\TH:i')) }}" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Address</label>
                        <textarea name="delivery_address" rows="3" 
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Special Instructions</label>
                        <textarea name="special_instructions" rows="3" 
                                  placeholder="Any special delivery or handling instructions..."
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('special_instructions', $order->special_instructions) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Internal Notes -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Internal Notes</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Notes (internal only)</label>
                    <textarea name="notes" rows="4" 
                              placeholder="Internal notes about this order..."
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('notes', $order->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-6">
            <!-- Order Items Summary -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-white mb-4">Order Items</h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-300">{{ $item->item_name }}</span>
                        <span class="text-white">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-600">
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-300">Total Items:</span>
                        <span class="text-white">{{ $order->items->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-white mb-4">Customer</h3>
                @if($order->customer)
                    <div class="space-y-2 text-sm">
                        <div class="text-white font-medium">{{ $order->customer->name }}</div>
                        @if($order->customer->phone)
                            <div class="text-gray-400">{{ $order->customer->phone }}</div>
                        @endif
                        @if($order->customer->email)
                            <div class="text-gray-400">{{ $order->customer->email }}</div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Walk-in Customer</p>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                Update Order
            </button>
        </div>
    </div>
</form>

<!-- Delete Button (outside the update form) -->
@if($order->status !== 'delivered')
<div class="mt-6">
    <form action="{{ route($routePrefix . '.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all">
            Delete Order
        </button>
    </form>
</div>
@endif
@endsection
