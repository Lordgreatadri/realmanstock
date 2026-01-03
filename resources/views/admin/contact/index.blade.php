@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp
@extends($layout)

@section('title', 'Contact Messages')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Contact Messages</h1>
            <p class="text-gray-400 mt-1">View and manage customer inquiries</p>
        </div>
        <div class="flex items-center gap-3">
            @if($newCount > 0)
                <span class="px-4 py-2 bg-indigo-500/20 border border-indigo-500 rounded-lg text-indigo-400">
                    {{ $newCount }} New {{ Str::plural('Message', $newCount) }}
                </span>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, email, phone..." 
                       class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                <option value="responded" {{ request('status') === 'responded' ? 'selected' : '' }}>Responded</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>

            <!-- Buttons -->
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Filter
            </button>
            <a href="{{ route($routePrefix . '.contact.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                Clear
            </a>
        </form>
    </div>

    <!-- Messages List -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Received</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($messages as $message)
                        <tr class="hover:bg-gray-800/50 transition {{ $message->status === 'new' ? 'bg-indigo-500/5' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($message->status === 'new')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-500/20 text-indigo-400">New</span>
                                @elseif($message->status === 'read')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-500/20 text-blue-400">Read</span>
                                @elseif($message->status === 'responded')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400">Responded</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400">Archived</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $message->name }}</div>
                                @if(!$message->read_at)
                                    <span class="text-xs text-indigo-400">Unread</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-300 text-sm">{{ $message->email }}</div>
                                <div class="text-gray-400 text-xs">{{ $message->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-300 text-sm max-w-md truncate">
                                    {{ Str::limit($message->message, 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ $message->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route($routePrefix . '.contact.show', $message->uuid) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-4 text-gray-400">No contact messages found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
