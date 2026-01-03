@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Roles Management</h1>
            <p class="text-gray-400 mt-1">Manage user roles and their permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Role
        </a>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-indigo-500 transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white capitalize">{{ $role->name }}</h3>
                        <p class="text-gray-400 text-sm mt-1">
                            {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-gray-400 text-xs mb-2">Permissions ({{ $role->permissions_count }})</p>
                    <div class="flex flex-wrap gap-1">
                        @if($role->permissions_count > 0)
                            <span class="px-2 py-1 bg-indigo-500/20 text-indigo-400 text-xs rounded">
                                {{ $role->permissions_count }} assigned
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-700 text-gray-400 text-xs rounded">
                                No permissions
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-4 border-t border-gray-800">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg text-center transition">
                        Edit
                    </a>
                    @if(!in_array($role->name, ['admin', 'manager', 'staff', 'customer']))
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Are you sure you want to delete this role?');" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                                Delete
                            </button>
                        </form>
                    @else
                        <button disabled class="flex-1 px-4 py-2 bg-gray-700 text-gray-500 text-sm rounded-lg cursor-not-allowed">
                            Protected
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Roles Found</h3>
                <p class="text-gray-500">Create your first role to get started.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
