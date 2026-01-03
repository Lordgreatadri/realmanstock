@extends('layouts.manager')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-white">Edit Category</h1>
        <p class="text-gray-400 mt-1">Update category information</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <form method="POST" action="{{ route('manager.categories.update', $category) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-300 mb-2">Category Type <span class="text-red-500">*</span></label>
                <select name="type" id="type" required
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="livestock" {{ old('type', $category->type) === 'livestock' ? 'selected' : '' }}>Livestock</option>
                    <option value="grocery" {{ old('type', $category->type) === 'grocery' ? 'selected' : '' }}>Grocery</option>
                    <option value="service" {{ old('type', $category->type) === 'service' ? 'selected' : '' }}>Service</option>
                </select>
                @error('type')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-300 mb-2">Category Image</label>
                <div class="flex items-center space-x-4">
                    <div id="imagePreview" class="{{ $category->image ? '' : 'hidden' }} w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-700">
                        <img src="{{ $category->image ? asset('storage/' . $category->image) : '' }}" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" id="image" accept="image/*"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB. Leave empty to keep current image.</p>
                    </div>
                </div>
                @error('image')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                @error('sort_order')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500 focus:ring-2">
                <label for="is_active" class="ml-3 text-gray-300">Active (visible to users)</label>
            </div>

            <!-- Category Info -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-300 mb-2">Category Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Items Count:</span>
                        <span class="text-white ml-2">{{ $category->animals_count ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created:</span>
                        <span class="text-white ml-2">{{ $category->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                <a href="{{ route('manager.categories.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
