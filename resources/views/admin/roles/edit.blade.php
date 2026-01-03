@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-white">Edit Role: <span class="capitalize">{{ $role->name }}</span></h1>
        <p class="text-gray-400 mt-1">Update role name and manage permissions</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Role Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Use lowercase with hyphens (e.g., warehouse-manager)</p>
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Permissions -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-300">Manage Permissions</label>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="selectAll()" class="text-xs text-indigo-400 hover:text-indigo-300">
                            Select All
                        </button>
                        <span class="text-gray-600">|</span>
                        <button type="button" onclick="deselectAll()" class="text-xs text-indigo-400 hover:text-indigo-300">
                            Deselect All
                        </button>
                    </div>
                </div>
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 max-h-96 overflow-y-auto">
                    @if($permissions->isEmpty())
                        <p class="text-gray-500 text-sm text-center py-8">
                            No permissions available. 
                            <a href="{{ route('admin.permissions.index') }}" class="text-indigo-400 hover:text-indigo-300">Create permissions first</a>
                        </p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg cursor-pointer transition">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                        class="permission-checkbox w-4 h-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500 focus:ring-2">
                                    <span class="text-gray-300 text-sm">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    <span class="font-semibold text-indigo-400">{{ count($rolePermissions) }}</span> permission(s) currently assigned
                </p>
                @error('permissions')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                <a href="{{ route('admin.roles.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/50 text-white rounded-lg transition">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function selectAll() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAll() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
</script>
@endpush
@endsection
