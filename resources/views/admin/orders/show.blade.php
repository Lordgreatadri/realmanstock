@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp
@extends($layout)

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Order {{ $order->order_number }}</h1>
            <p class="text-gray-400 mt-2">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route($routePrefix . '.orders.edit', $order) }}" class="px-6 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 transition-all">
                Edit Order
            </a>
            <a href="{{ route($routePrefix . '.orders.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                Back to Orders
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Order Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-900 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white">Order Items</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-start p-4 bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <h3 class="text-white font-semibold">{{ $item->item_name }}</h3>
                            <div class="text-sm text-gray-400 mt-1">
                                {{ number_format($item->quantity, 2) }} {{ $item->unit }} × GH₵{{ number_format($item->unit_price, 2) }}
                            </div>
                            @if($item->requires_processing)
                                <div class="mt-2">
                                    <span class="px-2 py-1 bg-purple-900 text-purple-300 text-xs rounded">Requires Processing</span>
                                    @if($item->processing_fee > 0)
                                        <span class="text-xs text-gray-400 ml-2">Fee: GH₵{{ number_format($item->processing_fee, 2) }}</span>
                                    @endif
                                </div>
                            @endif
                            @if($item->notes)
                                <p class="text-sm text-gray-400 mt-2">{{ $item->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-white font-semibold">GH₵{{ number_format($item->subtotal, 2) }}</div>
                            @if($item->processing_fee > 0)
                                <div class="text-xs text-gray-400 mt-1">+GH₵{{ number_format($item->processing_fee, 2) }} processing</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="mt-6 pt-6 border-t border-gray-600 space-y-2">
                    <div class="flex justify-between text-gray-300">
                        <span>Subtotal</span>
                        <span>GH₵{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->processing_fee > 0)
                    <div class="flex justify-between text-gray-300">
                        <span>Processing Fee</span>
                        <span>GH₵{{ number_format($order->processing_fee, 2) }}</span>
                    </div>
                    @endif
                    @if($order->delivery_fee > 0)
                    <div class="flex justify-between text-gray-300">
                        <span>Delivery Fee</span>
                        <span>GH₵{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    @endif
                    @if($order->discount > 0)
                    <div class="flex justify-between text-green-400">
                        <span>Discount</span>
                        <span>-GH₵{{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->tax > 0)
                    <div class="flex justify-between text-gray-300">
                        <span>Tax</span>
                        <span>GH₵{{ number_format($order->tax, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-white pt-2 border-t border-gray-600">
                        <span>Total</span>
                        <span>GH₵{{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-300">
                        <span>Amount Paid</span>
                        <span class="text-green-400">GH₵{{ number_format($order->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold {{ $order->balance > 0 ? 'text-red-400' : 'text-green-400' }}">
                        <span>Balance Due</span>
                        <span>GH₵{{ number_format($order->balance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status History -->
        @if($order->statusHistories->count() > 0)
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-900 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white">Status History</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($order->statusHistories as $history)
                    <div class="flex items-start gap-4 p-4 bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0 w-2 h-2 mt-2 bg-indigo-500 rounded-full"></div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-white font-medium">{{ ucfirst(str_replace('_', ' ', $history->from_status)) }}</span>
                                    <span class="text-gray-400 mx-2">→</span>
                                    <span class="text-indigo-400 font-medium">{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $history->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            @if($history->notes)
                                <p class="text-sm text-gray-400 mt-2">{{ $history->notes }}</p>
                            @endif
                            @if($history->user)
                                <p class="text-xs text-gray-500 mt-1">Updated by {{ $history->user->name }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Order Status -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Order Status</h3>
            @php
                $statusColors = [
                    'pending' => 'from-yellow-600 to-orange-600',
                    'processing' => 'from-blue-600 to-cyan-600',
                    'payment_received' => 'from-green-600 to-teal-600',
                    'ready_for_delivery' => 'from-purple-600 to-pink-600',
                    'out_for_delivery' => 'from-indigo-600 to-blue-600',
                    'delivered' => 'from-green-600 to-emerald-600',
                    'cancelled' => 'from-red-600 to-pink-600',
                ];
            @endphp
            <div class="bg-gradient-to-r {{ $statusColors[$order->status] ?? 'from-gray-600 to-gray-700' }} rounded-lg p-4 text-center">
                <div class="text-white text-2xl font-bold">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Customer Information</h3>
            @if($order->customer)
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400">Name</div>
                        <div class="text-white">{{ $order->customer->name }}</div>
                    </div>
                    @if($order->customer->email)
                    <div>
                        <div class="text-xs text-gray-400">Email</div>
                        <div class="text-white">{{ $order->customer->email }}</div>
                    </div>
                    @endif
                    @if($order->customer->phone)
                    <div>
                        <div class="text-xs text-gray-400">Phone</div>
                        <div class="text-white">{{ $order->customer->phone }}</div>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-gray-400">Walk-in Customer</p>
            @endif
        </div>

        <!-- Delivery Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Delivery Information</h3>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-400">Type</div>
                    <div class="text-white">{{ ucfirst($order->delivery_type) }}</div>
                </div>
                @if($order->delivery_date)
                <div>
                    <div class="text-xs text-gray-400">Delivery Date</div>
                    <div class="text-white">{{ $order->delivery_date->format('F d, Y') }}</div>
                </div>
                @endif
                @if($order->delivery_address)
                <div>
                    <div class="text-xs text-gray-400">Address</div>
                    <div class="text-white">{{ $order->delivery_address }}</div>
                </div>
                @endif
                @if($order->special_instructions)
                <div>
                    <div class="text-xs text-gray-400">Special Instructions</div>
                    <div class="text-white">{{ $order->special_instructions }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Payment Information</h3>
            <div class="space-y-3">
                @if($order->payment_method)
                <div>
                    <div class="text-xs text-gray-400">Payment Method</div>
                    <div class="text-white">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                </div>
                @endif
                <div>
                    <div class="text-xs text-gray-400">Payment Status</div>
                    @if($order->balance > 0)
                        <div class="text-red-400 font-semibold">Unpaid (GH₵{{ number_format($order->balance, 2) }})</div>
                    @else
                        <div class="text-green-400 font-semibold">Fully Paid</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Staff Information -->
        @if($order->user)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Processed By</h3>
            <div class="text-white">{{ $order->user->name }}</div>
            <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
        </div>
        @endif

        <!-- Notes -->
        @if($order->notes)
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Internal Notes</h3>
            <p class="text-gray-300">{{ $order->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
