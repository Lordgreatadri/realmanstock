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
            <h1 class="text-3xl font-bold text-white">Categories Management</h1>
            <p class="text-gray-400 mt-1">Manage livestock, grocery, and service categories</p>
        </div>
        <a href="{{ route($routePrefix . '.categories.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Category
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <form method="GET" action="{{ route($routePrefix . '.categories.index') }}" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search categories..."
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Type Filter -->
            <select name="type" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Types</option>
                <option value="livestock" {{ request('type') === 'livestock' ? 'selected' : '' }}>Livestock</option>
                <option value="grocery" {{ request('type') === 'grocery' ? 'selected' : '' }}>Grocery</option>
                <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Service</option>
            </select>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <!-- Buttons -->
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Filter
            </button>
            <a href="{{ route($routePrefix . '.categories.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Sort Order</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-800 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <img src="{{ asset('images/default-category.svg') }}" alt="No image" class="w-12 h-12 rounded-lg object-cover">
                                    @endif
                                    <div>
                                        <p class="text-white font-medium">{{ $category->name }}</p>
                                        @if($category->description)
                                            <p class="text-gray-500 text-sm">{{ Str::limit($category->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $category->type === 'livestock' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $category->type === 'grocery' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                    {{ $category->type === 'service' ? 'bg-purple-500/20 text-purple-400' : '' }}">
                                    {{ ucfirst($category->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300">{{ $category->animals_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route($routePrefix . '.categories.toggle-status', $category) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium transition
                                        {{ $category->is_active ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300">{{ $category->sort_order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route($routePrefix . '.categories.edit', $category) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route($routePrefix . '.categories.destroy', $category) }}" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Categories Found</h3>
                                <p class="text-gray-500 mb-4">Create your first category to get started.</p>
                                <a href="{{ route($routePrefix . '.categories.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Category
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
