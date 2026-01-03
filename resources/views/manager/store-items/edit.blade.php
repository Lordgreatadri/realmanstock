@extends('layouts.manager')

@section('title', 'Edit Store Item')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('manager.store-items.show', $item) }}" class="text-gray-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Store Item</h1>
            <p class="text-gray-400 mt-1">{{ $item->sku }} - {{ $item->name }}</p>
        </div>
    </div>
</div>

<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <form action="{{ route('manager.store-items.update', $item) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Item Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Item Name*</label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $item->name) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- SKU -->
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-300 mb-2">SKU</label>
                <input type="text" name="sku" id="sku"
                       value="{{ old('sku', $item->sku) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('sku') border-red-500 @enderror">
                @error('sku')
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
                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supplier -->
            <div>
                <label for="supplier" class="block text-sm font-medium text-gray-300 mb-2">Supplier (Optional)</label>
                <input type="text" name="supplier" id="supplier"
                       value="{{ old('supplier', $item->supplier) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('supplier') border-red-500 @enderror">
                @error('supplier')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-300 mb-2">Quantity*</label>
                <input type="number" name="quantity" id="quantity" step="0.01" required
                       value="{{ old('quantity', $item->quantity) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit -->
            <div>
                <label for="unit" class="block text-sm font-medium text-gray-300 mb-2">Unit*</label>
                <select name="unit" id="unit" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('unit') border-red-500 @enderror">
                    <option value="piece" {{ old('unit', $item->unit) == 'piece' ? 'selected' : '' }}>Piece</option>
                    <option value="kg" {{ old('unit', $item->unit) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                    <option value="bag" {{ old('unit', $item->unit) == 'bag' ? 'selected' : '' }}>Bag</option>
                    <option value="liter" {{ old('unit', $item->unit) == 'liter' ? 'selected' : '' }}>Liter</option>
                    <option value="box" {{ old('unit', $item->unit) == 'box' ? 'selected' : '' }}>Box</option>
                    <option value="pack" {{ old('unit', $item->unit) == 'pack' ? 'selected' : '' }}>Pack</option>
                    <option value="carton" {{ old('unit', $item->unit) == 'carton' ? 'selected' : '' }}>Carton</option>
                </select>
                @error('unit')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reorder Level -->
            <div>
                <label for="reorder_level" class="block text-sm font-medium text-gray-300 mb-2">Reorder Level*</label>
                <input type="number" name="reorder_level" id="reorder_level" step="0.01" required
                       value="{{ old('reorder_level', $item->reorder_level) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('reorder_level') border-red-500 @enderror">
                @error('reorder_level')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Low stock alert will trigger when quantity reaches this level</p>
            </div>

            <!-- Cost Price -->
            <div>
                <label for="cost_price" class="block text-sm font-medium text-gray-300 mb-2">Cost Price (GH₵)*</label>
                <input type="number" name="cost_price" id="cost_price" step="0.01" required
                       value="{{ old('cost_price', $item->cost_price) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('cost_price') border-red-500 @enderror">
                @error('cost_price')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selling Price -->
            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-300 mb-2">Selling Price (GH₵)*</label>
                <input type="number" name="selling_price" id="selling_price" step="0.01" required
                       value="{{ old('selling_price', $item->selling_price) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('selling_price') border-red-500 @enderror">
                @error('selling_price')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $item->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="2"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes', $item->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-300">Item is active</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition">
                Update Store Item
            </button>
            <a href="{{ route('manager.store-items.show', $item) }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>

    <form action="{{ route('manager.store-items.destroy', $item) }}" method="POST" 
          onsubmit="return confirm('Are you sure you want to delete this store item? This action cannot be undone.');" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete Store Item
        </button>
    </form>
</div>
@endsection
