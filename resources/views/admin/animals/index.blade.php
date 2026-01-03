@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Animals Management</h1>
            <p class="text-gray-400 mt-1">Manage livestock inventory and tracking</p>
        </div>
        <a href="{{ route($routePrefix . '.animals.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Animal
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <form method="GET" action="{{ route($routePrefix . '.animals.index') }}" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by tag, breed, supplier..."
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Category Filter -->
            <select name="category_id" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="quarantined" {{ request('status') === 'quarantined' ? 'selected' : '' }}>Quarantined</option>
                <option value="under_treatment" {{ request('status') === 'under_treatment' ? 'selected' : '' }}>Under Treatment</option>
                <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="deceased" {{ request('status') === 'deceased' ? 'selected' : '' }}>Deceased</option>
            </select>

            <!-- Gender Filter -->
            <select name="gender" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Genders</option>
                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
            </select>

            <!-- Buttons -->
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Filter
            </button>
            <a href="{{ route($routePrefix . '.animals.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Animals Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Animal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tag/Breed</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Weight</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($animals as $animal)
                        <tr class="hover:bg-gray-800 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($animal->image)
                                        <img src="{{ asset('storage/' . $animal->image) }}" alt="{{ $animal->tag_number }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <img src="{{ asset('images/default-animal.svg') }}" alt="No image" class="w-12 h-12 rounded-lg object-cover">
                                    @endif
                                    <div>
                                        <p class="text-white font-medium">{{ $animal->tag_number ?: 'No Tag' }}</p>
                                        <p class="text-gray-500 text-sm">{{ $animal->gender ? ucfirst($animal->gender) : 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300">{{ $animal->category->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white">{{ $animal->breed ?: 'Not specified' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300">{{ $animal->current_weight ? $animal->current_weight . ' kg' : 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-medium">GH₵{{ number_format($animal->purchase_price, 2) }}</p>
                                @if($animal->selling_price)
                                    <p class="text-green-400 text-sm">Sell: GH₵{{ number_format($animal->selling_price, 2) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $animal->status === 'available' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $animal->status === 'sold' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                    {{ $animal->status === 'quarantined' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $animal->status === 'under_treatment' ? 'bg-orange-500/20 text-orange-400' : '' }}
                                    {{ $animal->status === 'reserved' ? 'bg-purple-500/20 text-purple-400' : '' }}
                                    {{ $animal->status === 'deceased' ? 'bg-red-500/20 text-red-400' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $animal->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route($routePrefix . '.animals.show', $animal) }}" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded transition">
                                        View
                                    </a>
                                    <a href="{{ route($routePrefix . '.animals.edit', $animal) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route($routePrefix . '.animals.destroy', $animal) }}" onsubmit="return confirm('Are you sure you want to delete this animal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Animals Found</h3>
                                <p class="text-gray-500 mb-4">Start adding animals to your inventory.</p>
                                <a href="{{ route($routePrefix . '.animals.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Animal
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($animals->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $animals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
