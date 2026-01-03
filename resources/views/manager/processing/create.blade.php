@extends('layouts.manager')

@section('title', 'Create Processing Request')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-bold text-white">Create Processing Request</h1>
        <p class="text-gray-400 mt-2">Add a new animal processing request</p>
    </div>
    <a href="{{ route('manager.processing.create') }}" 
       class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
        Back to List
    </a>
</div>

@if ($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-800 border border-red-600 text-red-100 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('manager.processing.store') }}" method="POST">
    @csrf

    <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-xl font-bold text-white mb-6">Request Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Customer *</label>
                <select name="customer_id" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} - {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Animal (Optional)</label>
                <select name="animal_id"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Select Animal --</option>
                    @foreach($animals as $animal)
                        <option value="{{ $animal->id }}" {{ old('animal_id') == $animal->id ? 'selected' : '' }}>
                            {{ $animal->tag_number }} - {{ $animal->breed }} ({{ $animal->current_weight }}kg)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Order (Optional)</label>
                <select name="order_id"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Select Order --</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            {{ $order->order_number }} - {{ $order->customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Processing Fee *</label>
                <input type="number" name="processing_fee" step="0.01" min="0" value="{{ old('processing_fee') }}" required
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Requested Date *</label>
                <input type="date" name="requested_date" value="{{ old('requested_date', date('Y-m-d')) }}" required
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Scheduled Date</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Live Weight (kg)</label>
                <input type="number" name="live_weight" step="0.01" min="0" value="{{ old('live_weight') }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">Special Instructions</label>
                <textarea name="special_instructions" rows="3"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('special_instructions') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('manager.processing.create') }}" 
           class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            Cancel
        </a>
        <button type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition">
            Create Request
        </button>
    </div>
</form>
@endsection
