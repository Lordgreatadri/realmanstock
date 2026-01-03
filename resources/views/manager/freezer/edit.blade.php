@extends('layouts.manager')

@section('title', 'Edit Freezer Inventory')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('manager.freezer.show', $inventory) }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Freezer Inventory</h1>
            <p class="text-gray-400 mt-1">{{ $inventory->batch_number }} - {{ $inventory->product_name }}</p>
        </div>
    </div>
</div>

<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <form action="{{ route('manager.freezer.update', $inventory) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Batch Number (Read-only) -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Batch Number</label>
                <input type="text" value="{{ $inventory->batch_number }}" disabled
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-gray-400 cursor-not-allowed">
            </div>

            <!-- Product Name -->
            <div>
                <label for="product_name" class="block text-sm font-medium text-gray-300 mb-2">Product Name*</label>
                <input type="text" name="product_name" id="product_name" required
                       value="{{ old('product_name', $inventory->product_name) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('product_name') border-red-500 @enderror">
                @error('product_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category*</label>
                <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $inventory->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Processing Request (Optional) -->
            <div>
                <label for="processing_request_id" class="block text-sm font-medium text-gray-300 mb-2">Processing Request (Optional)</label>
                <select name="processing_request_id" id="processing_request_id"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('processing_request_id') border-red-500 @enderror">
                    <option value="">None</option>
                    @foreach($processingRequests as $request)
                        <option value="{{ $request->id }}" {{ old('processing_request_id', $inventory->processing_request_id) == $request->id ? 'selected' : '' }}>
                            {{ $request->batch_number }} - {{ $request->customer->name }} ({{ $request->animal->name }})
                        </option>
                    @endforeach
                </select>
                @error('processing_request_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Weight -->
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-300 mb-2">Weight (kg)*</label>
                <input type="number" name="weight" id="weight" step="0.01" required
                       value="{{ old('weight', $inventory->weight) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('weight') border-red-500 @enderror">
                @error('weight')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cost Price -->
            <div>
                <label for="cost_price" class="block text-sm font-medium text-gray-300 mb-2">Cost Price (GH₵)*</label>
                <input type="number" name="cost_price" id="cost_price" step="0.01" required
                       value="{{ old('cost_price', $inventory->cost_price) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('cost_price') border-red-500 @enderror">
                @error('cost_price')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selling Price Per Kg -->
            <div>
                <label for="selling_price_per_kg" class="block text-sm font-medium text-gray-300 mb-2">Selling Price per kg (GH₵)*</label>
                <input type="number" name="selling_price_per_kg" id="selling_price_per_kg" step="0.01" required
                       value="{{ old('selling_price_per_kg', $inventory->selling_price_per_kg) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('selling_price_per_kg') border-red-500 @enderror">
                @error('selling_price_per_kg')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Processing Date -->
            <div>
                <label for="processing_date" class="block text-sm font-medium text-gray-300 mb-2">Processing Date*</label>
                <input type="date" name="processing_date" id="processing_date" required
                       value="{{ old('processing_date', $inventory->processing_date->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('processing_date') border-red-500 @enderror">
                @error('processing_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expiry Date -->
            <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-300 mb-2">Expiry Date*</label>
                <input type="date" name="expiry_date" id="expiry_date" required
                       value="{{ old('expiry_date', $inventory->expiry_date->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('expiry_date') border-red-500 @enderror">
                @error('expiry_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Storage Location -->
            <div>
                <label for="storage_location" class="block text-sm font-medium text-gray-300 mb-2">Storage Location*</label>
                <input type="text" name="storage_location" id="storage_location" required
                       value="{{ old('storage_location', $inventory->storage_location) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('storage_location') border-red-500 @enderror">
                @error('storage_location')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Temperature Zone -->
            <div>
                <label for="temperature_zone" class="block text-sm font-medium text-gray-300 mb-2">Temperature Zone*</label>
                <select name="temperature_zone" id="temperature_zone" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('temperature_zone') border-red-500 @enderror">
                    <option value="">Select Zone</option>
                    <option value="freezer" {{ old('temperature_zone', $inventory->temperature_zone) == 'freezer' ? 'selected' : '' }}>Freezer (-18°C or below)</option>
                    <option value="chiller" {{ old('temperature_zone', $inventory->temperature_zone) == 'chiller' ? 'selected' : '' }}>Chiller (0-4°C)</option>
                    <option value="cold_room" {{ old('temperature_zone', $inventory->temperature_zone) == 'cold_room' ? 'selected' : '' }}>Cold Room (4-8°C)</option>
                </select>
                @error('temperature_zone')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status*</label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('status') border-red-500 @enderror">
                    <option value="in_stock" {{ old('status', $inventory->status) == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="reserved" {{ old('status', $inventory->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="sold" {{ old('status', $inventory->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="expired" {{ old('status', $inventory->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quality Notes -->
            <div class="md:col-span-2">
                <label for="quality_notes" class="block text-sm font-medium text-gray-300 mb-2">Quality Notes (Optional)</label>
                <textarea name="quality_notes" id="quality_notes" rows="3"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('quality_notes') border-red-500 @enderror">{{ old('quality_notes', $inventory->quality_notes) }}</textarea>
                @error('quality_notes')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition">
                Update Inventory Item
            </button>
            <a href="{{ route('manager.freezer.show', $inventory) }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>

    @if($inventory->status !== 'sold')
    <form action="{{ route('manager.freezer.destroy', $inventory) }}" method="POST" 
          onsubmit="return confirm('Are you sure you want to delete this inventory item? This action cannot be undone.');" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete Inventory
        </button>
    </form>
    @endif
</div>
@endsection
