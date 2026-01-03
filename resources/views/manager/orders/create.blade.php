@extends('layouts.manager')

@section('title', 'Create Order')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Create Order</h1>
            <p class="text-gray-400 mt-2">Add a new order to the system</p>
        </div>
        <a href="{{ route('manager.orders.index') }}" 
           class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-all">
            Back to Orders
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-800 border border-green-600 text-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-800 border border-red-600 text-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-800 border border-red-600 text-red-100 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('manager.orders.store') }}" method="POST" id="orderForm">
                @csrf

                <!-- Customer Information -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold text-white mb-6">Customer Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Select Customer *</label>
                            <select name="customer_id" id="customer_id" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}
                                            data-phone="{{ $customer->phone }}"
                                            data-address="{{ $customer->address }}"
                                            data-delivery="{{ $customer->preferred_delivery }}"
                                            data-processing="{{ $customer->preferred_processing }}">
                                        {{ $customer->name }} - {{ $customer->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Customer Phone (Auto-filled)</label>
                            <input type="text" id="customer_phone" readonly
                                   class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold text-white mb-6">Delivery Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Type *</label>
                            <select name="delivery_type" id="delivery_type" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                <option value="pickup" {{ old('delivery_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                <option value="delivery" {{ old('delivery_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Date</label>
                            <input type="date" name="delivery_date" value="{{ old('delivery_date') }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Delivery Address</label>
                            <textarea name="delivery_address" id="delivery_address" rows="2"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('delivery_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-white">Order Items</h3>
                        <button type="button" onclick="addOrderItem()" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition">
                            + Add Item
                        </button>
                    </div>

                    <div id="order-items-container">
                        <!-- Order items will be added here -->
                    </div>

                    <div class="mt-6 flex justify-end">
                        <div class="bg-gray-700 rounded-lg p-4 min-w-[300px]">
                            <div class="flex justify-between items-center text-lg font-bold text-white">
                                <span>Total Amount:</span>
                                <span id="total_amount">GH₵ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment & Notes -->
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold text-white mb-6">Payment & Additional Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method *</label>
                            <select name="payment_method" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Special Instructions</label>
                            <input type="text" name="special_instructions" value="{{ old('special_instructions') }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Internal Notes</label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('manager.orders.index') }}" 
                       class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition">
                        Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCounter = 0;
        const animals = @json($animals);

        // Auto-fill customer details
        document.getElementById('customer_id').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            document.getElementById('customer_phone').value = option.dataset.phone || '';
            document.getElementById('delivery_address').value = option.dataset.address || '';
            
            if (option.dataset.delivery) {
                document.getElementById('delivery_type').value = option.dataset.delivery;
            }
        });

        // Add order item
        function addOrderItem() {
            itemCounter++;
            const container = document.getElementById('order-items-container');
            
            // Build animal options
            let animalOptions = '<option value="">-- Select Animal --</option>';
            animals.forEach(animal => {
                animalOptions += `<option value="${animal.id}" 
                                        data-weight="${animal.current_weight}"
                                        data-price-kg="${animal.selling_price_per_kg || 0}"
                                        data-fixed-price="${animal.fixed_selling_price || 0}">
                                    ${animal.tag_number} - ${animal.breed} (${animal.current_weight}kg)
                                </option>`;
            });
            
            const itemHtml = `
                <div class="order-item bg-gray-700 rounded-lg p-4 mb-4" id="item-${itemCounter}">
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="text-white font-semibold">Item #${itemCounter}</h4>
                        <button type="button" onclick="removeOrderItem(${itemCounter})" 
                                class="text-red-400 hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Animal *</label>
                            <select name="items[${itemCounter}][animal_id]" required onchange="updateItemPrice(${itemCounter})"
                                    class="animal-select w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                ${animalOptions}
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Quantity *</label>
                            <input type="number" name="items[${itemCounter}][quantity]" value="1" min="1" required
                                   onchange="updateItemPrice(${itemCounter})"
                                   class="quantity-input w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Processing *</label>
                            <select name="items[${itemCounter}][processing_type]" required
                                    class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                <option value="live">Live</option>
                                <option value="dressed">Dressed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3 flex justify-end">
                        <span class="text-gray-300">Subtotal: <span class="item-total font-bold text-white">GH₵ 0.00</span></span>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
        }

        // Remove order item
        function removeOrderItem(id) {
            document.getElementById(`item-${id}`).remove();
            calculateTotal();
        }

        // Update item price
        function updateItemPrice(itemId) {
            const item = document.getElementById(`item-${itemId}`);
            const select = item.querySelector('.animal-select');
            const quantityInput = item.querySelector('.quantity-input');
            const totalSpan = item.querySelector('.item-total');
            
            const option = select.options[select.selectedIndex];
            const quantity = parseInt(quantityInput.value) || 0;
            
            if (option.value) {
                const fixedPrice = parseFloat(option.dataset.fixedPrice);
                const pricePerKg = parseFloat(option.dataset.priceKg);
                const weight = parseFloat(option.dataset.weight);
                
                let unitPrice = fixedPrice || (pricePerKg * weight);
                let total = unitPrice * quantity;
                
                totalSpan.textContent = `GH₵ ${total.toFixed(2)}`;
            } else {
                totalSpan.textContent = 'GH₵ 0.00';
            }
            
            calculateTotal();
        }

        // Calculate total
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-total').forEach(span => {
                const value = parseFloat(span.textContent.replace('GH₵ ', '')) || 0;
                total += value;
            });
            
            document.getElementById('total_amount').textContent = `GH₵ ${total.toFixed(2)}`;
        }

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addOrderItem();
        });

        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const items = document.querySelectorAll('.order-item');
            if (items.length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the order.');
                return false;
            }
        });
    </script>
@endsection
