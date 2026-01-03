@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Permissions Management</h1>
            <p class="text-gray-400 mt-1">Manage system permissions and assign them to roles</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Permission
        </button>
    </div>

    <!-- Permissions Grouped by Category -->
    <div class="space-y-6">
        @forelse($permissions as $category => $categoryPermissions)
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600/20 to-purple-600/20 border-b border-gray-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white capitalize">{{ $category }}</h2>
                    <p class="text-gray-400 text-sm">{{ count($categoryPermissions) }} {{ Str::plural('permission', count($categoryPermissions)) }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categoryPermissions as $permission)
                            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 flex items-center justify-between hover:border-indigo-500 transition">
                                <div class="flex-1">
                                    <h3 class="text-white font-medium">{{ $permission->name }}</h3>
                                    <p class="text-gray-500 text-xs mt-1">
                                        Used by {{ $permission->roles_count }} {{ Str::plural('role', $permission->roles_count) }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" onsubmit="return confirm('Are you sure? This will remove the permission from all roles.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-4 text-red-400 hover:text-red-300 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Permissions Found</h3>
                <p class="text-gray-500">Create your first permission to get started.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Permission Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-800 rounded-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white">Create New Permission</h2>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Permission Name</label>
                <input type="text" name="name" id="name" required
                    placeholder="e.g., view-users, create-orders"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Use format: action-resource (e.g., view-users, create-orders)</p>
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
