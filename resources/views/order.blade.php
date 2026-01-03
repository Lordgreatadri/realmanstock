<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - RealMan Ranch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-950 text-white">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                        </svg>
                        <span class="text-2xl font-bold">RealMan Ranch</span>
                    </a>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-lg text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Form -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h1 class="text-3xl font-bold mb-2">Place Your Order</h1>
                        <p class="text-gray-400 mb-6">Fill in your details and select items to order</p>

                        <form id="orderForm" action="{{ route('order.place') }}" method="POST">
                            @csrf
                            
                            <!-- Customer Information -->
                            <div class="mb-8">
                                <h2 class="text-xl font-bold mb-4 text-indigo-400">Your Information</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500"
                                               placeholder="John Doe">
                                        @error('customer_name')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number *</label>
                                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500"
                                               placeholder="+1 234 567 8900">
                                        @error('customer_phone')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Email Address *</label>
                                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500"
                                               placeholder="john@example.com">
                                        @error('customer_email')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Address</label>
                                        <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500"
                                               placeholder="123 Main St, City, State">
                                        @error('customer_address')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Select Items -->
                            <div class="mb-8">
                                <h2 class="text-xl font-bold mb-4 text-indigo-400">Select Items *</h2>
                                
                                <!-- Livestock -->
                                @if($animals->isNotEmpty())
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold mb-3 text-gray-300">Livestock</h3>
                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach($animals as $animal)
                                                @php
                                                    $animalPrice = $animal->selling_price ?? 0;
                                                @endphp
                                                <label class="flex items-center p-4 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                                                    <input type="checkbox" class="item-checkbox w-5 h-5 text-indigo-600 rounded"
                                                           data-type="animal"
                                                           data-id="{{ $animal->id }}"
                                                           data-name="{{ $animal->tag_number }} - {{ $animal->breed }}"
                                                           data-price="{{ $animalPrice }}">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium">{{ $animal->tag_number }} - {{ $animal->breed }}</div>
                                                        <div class="text-sm text-gray-400">{{ $animal->category->name ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="text-indigo-400 font-bold">
                                                        @if($animalPrice > 0)
                                                            ${{ number_format($animalPrice, 2) }}
                                                        @else
                                                            <span class="text-gray-500 text-sm">Contact for price</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Store Items -->
                                @if($storeItems->isNotEmpty())
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold mb-3 text-gray-300">Store Products</h3>
                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach($storeItems as $item)
                                                @php
                                                    $itemPrice = $item->price ?? 0;
                                                @endphp
                                                <label class="flex items-center p-4 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                                                    <input type="checkbox" class="item-checkbox w-5 h-5 text-indigo-600 rounded"
                                                           data-type="product"
                                                           data-id="{{ $item->id }}"
                                                           data-name="{{ $item->name }}"
                                                           data-price="{{ $itemPrice }}">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium">{{ $item->name }}</div>
                                                        <div class="text-sm text-gray-400">{{ $item->category->name ?? 'N/A' }} - Stock: {{ $item->stock_quantity }}</div>
                                                    </div>
                                                    <div class="flex items-center gap-4">
                                                        <input type="number" min="1" value="1" 
                                                               class="quantity-input w-20 px-2 py-1 bg-gray-900 border border-gray-700 rounded text-white text-center"
                                                               data-id="{{ $item->id }}" disabled>
                                                        <div class="text-indigo-400 font-bold">${{ number_format($itemPrice, 2) }}</div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Delivery Notes -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Notes / Special Instructions</label>
                                <textarea name="delivery_notes" rows="3"
                                          class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Any special requirements...">{{ old('delivery_notes') }}</textarea>
                            </div>

                            <div id="items-container"></div>

                            <button type="submit" id="submitBtn" disabled
                                    class="w-full px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-lg transition opacity-50 cursor-not-allowed">
                                Place Order
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        <div id="orderSummary" class="space-y-3 mb-6">
                            <p class="text-gray-400 text-sm">No items selected</p>
                        </div>
                        <div class="border-t border-gray-800 pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span id="orderTotal" class="text-indigo-400">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedItems = [];

        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const quantityInput = this.closest('label').querySelector('.quantity-input');
                if (quantityInput) {
                    quantityInput.disabled = !this.checked;
                }
                updateOrder();
            });
        });

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', updateOrder);
        });

        function updateOrder() {
            selectedItems = [];
            let total = 0;

            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                const type = checkbox.dataset.type;
                const id = checkbox.dataset.id;
                const name = checkbox.dataset.name;
                const price = parseFloat(checkbox.dataset.price);
                const quantityInput = checkbox.closest('label').querySelector('.quantity-input');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                const subtotal = price * quantity;

                selectedItems.push({ type, id, name, price, quantity, subtotal });
                total += subtotal;
            });

            // Update summary
            const summaryDiv = document.getElementById('orderSummary');
            if (selectedItems.length === 0) {
                summaryDiv.innerHTML = '<p class="text-gray-400 text-sm">No items selected</p>';
            } else {
                summaryDiv.innerHTML = selectedItems.map(item => `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-300">${item.name} ${item.quantity > 1 ? `(×${item.quantity})` : ''}</span>
                        <span class="text-white">$${item.subtotal.toFixed(2)}</span>
                    </div>
                `).join('');
            }

            document.getElementById('orderTotal').textContent = '$' + total.toFixed(2);

            // Update hidden inputs
            const container = document.getElementById('items-container');
            container.innerHTML = selectedItems.map((item, index) => `
                <input type="hidden" name="items[${index}][type]" value="${item.type}">
                <input type="hidden" name="items[${index}][id]" value="${item.id}">
                <input type="hidden" name="items[${index}][name]" value="${item.name}">
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `).join('');

            // Enable/disable submit button
            const submitBtn = document.getElementById('submitBtn');
            if (selectedItems.length > 0) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:from-indigo-700', 'hover:to-purple-700');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:from-indigo-700', 'hover:to-purple-700');
            }
        }
    </script>
</body>
</html>
