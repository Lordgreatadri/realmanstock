@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp
@extends($layout)

@section('title', 'Edit Customer')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Customer</h1>
            <p class="text-gray-400 mt-2">Update customer information</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route($routePrefix . '.customers.show', $customer) }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                View Customer
            </a>
            <a href="{{ route($routePrefix . '.customers.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                Back to List
            </a>
        </div>
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

<form action="{{ route($routePrefix . '.customers.update', $customer) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Personal Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number *</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required
                               placeholder="+233..."
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Address Information</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Street Address</label>
                        <textarea name="address" rows="3" 
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">City</label>
                            <input type="text" name="city" value="{{ old('city', $customer->city) }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">State/Region</label>
                            <input type="text" name="state" value="{{ old('state', $customer->state) }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Preferences</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Preferred Delivery Method</label>
                        <select name="preferred_delivery" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                            <option value="">Select...</option>
                            <option value="pickup" {{ old('preferred_delivery', $customer->preferred_delivery) == 'pickup' ? 'selected' : '' }}>Pickup</option>
                            <option value="delivery" {{ old('preferred_delivery', $customer->preferred_delivery) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Preferred Processing</label>
                        <select name="preferred_processing" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                            <option value="">Select...</option>
                            <option value="live" {{ old('preferred_processing', $customer->preferred_processing) == 'live' ? 'selected' : '' }}>Live</option>
                            <option value="dressed" {{ old('preferred_processing', $customer->preferred_processing) == 'dressed' ? 'selected' : '' }}>Dressed</option>
                            <option value="both" {{ old('preferred_processing', $customer->preferred_processing) == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-white mb-6">Notes</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Internal Notes</label>
                    <textarea name="notes" rows="4" 
                              placeholder="Any special notes about this customer..."
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Credit Settings -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-white mb-6">Credit Settings</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="allow_credit" name="allow_credit" value="1" 
                               {{ old('allow_credit', $customer->allow_credit) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500">
                        <label for="allow_credit" class="ml-2 text-sm text-gray-300">Allow Credit Purchases</label>
                    </div>

                    <div id="credit_limit_field" class="{{ old('allow_credit', $customer->allow_credit) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Credit Limit (GH₵)</label>
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="p-4 bg-gray-700 rounded-lg">
                        <div class="text-xs text-gray-400 mb-1">Current Outstanding Balance</div>
                        <div class="text-2xl font-bold {{ $customer->outstanding_balance > 0 ? 'text-red-400' : 'text-green-400' }}">
                            GH₵{{ number_format($customer->outstanding_balance, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                Update Customer
            </button>
        </div>
    </div>
</form>

@if($customer->orders()->count() == 0)
<div class="mt-6">
    <form action="{{ route($routePrefix . '.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all">
            Delete Customer
        </button>
    </form>
</div>
@endif

<script>
    document.getElementById('allow_credit').addEventListener('change', function() {
        const creditLimitField = document.getElementById('credit_limit_field');
        if (this.checked) {
            creditLimitField.classList.remove('hidden');
        } else {
            creditLimitField.classList.add('hidden');
        }
    });
</script>
@endsection
