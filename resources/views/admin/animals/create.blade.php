@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-white">Add New Animal</h1>
        <p class="text-gray-400 mt-1">Register a new animal in the inventory</p>
    </div>

    <!-- Create Form -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <form method="POST" action="{{ route($routePrefix . '.animals.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tag Number -->
                <div>
                    <label for="tag_number" class="block text-sm font-medium text-gray-300 mb-2">Tag Number</label>
                    <input type="text" name="tag_number" id="tag_number" value="{{ old('tag_number') }}"
                        placeholder="e.g., GT-001"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('tag_number')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Breed -->
                <div>
                    <label for="breed" class="block text-sm font-medium text-gray-300 mb-2">Breed</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed') }}"
                        placeholder="e.g., Boer, West African Dwarf"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('breed')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-300 mb-2">Gender</label>
                    <select name="gender" id="gender"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-300 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('date_of_birth')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Weight -->
                <div>
                    <label for="current_weight" class="block text-sm font-medium text-gray-300 mb-2">Current Weight (kg)</label>
                    <input type="number" step="0.01" name="current_weight" id="current_weight" value="{{ old('current_weight') }}"
                        placeholder="e.g., 25.5"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('current_weight')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Price -->
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-300 mb-2">Purchase Price (GH₵) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required
                        placeholder="e.g., 50000"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('purchase_price')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Date -->
                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-300 mb-2">Purchase Date <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('purchase_date')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-300 mb-2">Supplier</label>
                    <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}"
                        placeholder="e.g., John Farms"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('supplier')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="quarantined" {{ old('status') === 'quarantined' ? 'selected' : '' }}>Quarantined</option>
                        <option value="under_treatment" {{ old('status') === 'under_treatment' ? 'selected' : '' }}>Under Treatment</option>
                        <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="sold" {{ old('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="deceased" {{ old('status') === 'deceased' ? 'selected' : '' }}>Deceased</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selling Price per KG -->
                <div>
                    <label for="selling_price_per_kg" class="block text-sm font-medium text-gray-300 mb-2">Selling Price per KG (GH₵)</label>
                    <input type="number" step="0.01" name="selling_price_per_kg" id="selling_price_per_kg" value="{{ old('selling_price_per_kg') }}"
                        placeholder="e.g., 2500"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('selling_price_per_kg')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fixed Selling Price -->
                <div>
                    <label for="fixed_selling_price" class="block text-sm font-medium text-gray-300 mb-2">Fixed Selling Price (GH₵)</label>
                    <input type="number" step="0.01" name="fixed_selling_price" id="fixed_selling_price" value="{{ old('fixed_selling_price') }}"
                        placeholder="e.g., 75000"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Leave empty to calculate from weight and price per kg</p>
                    @error('fixed_selling_price')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Vaccination -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_vaccinated" id="is_vaccinated" value="1" {{ old('is_vaccinated') ? 'checked' : '' }}
                        class="w-4 h-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500 focus:ring-2">
                    <label for="is_vaccinated" class="ml-3 text-gray-300">Vaccinated</label>
                </div>

                <div>
                    <label for="last_vaccination_date" class="block text-sm font-medium text-gray-300 mb-2">Last Vaccination Date</label>
                    <input type="date" name="last_vaccination_date" id="last_vaccination_date" value="{{ old('last_vaccination_date') }}"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('last_vaccination_date')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Health Notes -->
            <div>
                <label for="health_notes" class="block text-sm font-medium text-gray-300 mb-2">Health Notes</label>
                <textarea name="health_notes" id="health_notes" rows="3"
                    placeholder="Enter any health concerns or notes..."
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('health_notes') }}</textarea>
                @error('health_notes')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">General Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    placeholder="Enter any additional notes..."
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-300 mb-2">Animal Image</label>
                <div class="flex items-center space-x-4">
                    <div id="imagePreview" class="hidden w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-700">
                        <img src="" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" id="image" accept="image/*"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                    </div>
                </div>
                @error('image')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                <a href="{{ route($routePrefix . '.animals.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
                    Add Animal
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
