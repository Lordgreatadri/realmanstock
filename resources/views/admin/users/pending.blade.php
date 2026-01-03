@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Pending Approvals</h1>
            <p class="text-gray-400 mt-1">Review and approve new user registrations</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="search" name="search" value="{{ request('search') }}" 
                    placeholder="Search by name, email, or phone..."
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Search
            </button>
        </form>
    </div>

    <!-- Pending Users List -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800">
                    <tr class="text-gray-400 text-xs uppercase">
                        <th class="text-left px-6 py-4">User</th>
                        <th class="text-left px-6 py-4">Contact</th>
                        <th class="text-left px-6 py-4">Company</th>
                        <th class="text-left px-6 py-4">Purpose</th>
                        <th class="text-left px-6 py-4">Registered</th>
                        <th class="text-right px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @forelse($users as $user)
                        <tr class="border-t border-gray-800 hover:bg-gray-800/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-white font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-white">{{ $user->name }}</p>
                                        @if($user->phone_verified)
                                            <span class="text-xs text-green-400">✓ Phone Verified</span>
                                        @else
                                            <span class="text-xs text-yellow-400">Pending Verification</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm">{{ $user->phone }}</p>
                                @if($user->email)
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm">{{ $user->company_name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm max-w-xs truncate">{{ $user->purpose ?? 'Not provided' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Approve this user?')" 
                                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Reject and delete this user?')" 
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No pending approvals</p>
                                <p class="text-sm mt-1">All users have been reviewed</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
