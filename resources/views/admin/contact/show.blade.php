@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp
@extends($layout)

@section('title', 'Contact Message Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route($routePrefix . '.contact.index') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Contact Message</h1>
                <p class="text-gray-400 mt-1">From {{ $contactMessage->name }}</p>
            </div>
        </div>
        
        @if(session('success'))
            <div class="px-4 py-2 bg-green-500/20 border border-green-500 rounded-lg text-green-400">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Message Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message Content -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Message</h2>
                <div class="prose prose-invert max-w-none">
                    <p class="text-gray-300 whitespace-pre-wrap">{{ $contactMessage->message }}</p>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Admin Notes</h2>
                <form action="{{ route($routePrefix . '.contact.update', $contactMessage->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="status" value="{{ $contactMessage->status }}">
                    <input type="hidden" name="assigned_to" value="{{ $contactMessage->assigned_to }}">
                    
                    <textarea name="admin_notes" rows="6" 
                              class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500"
                              placeholder="Add internal notes about this message...">{{ old('admin_notes', $contactMessage->admin_notes) }}</textarea>
                    
                    <button type="submit" class="mt-4 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        Save Notes
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Contact Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-400">Name</label>
                        <p class="text-white font-medium">{{ $contactMessage->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400">Email</label>
                        <p class="text-white">
                            <a href="mailto:{{ $contactMessage->email }}" class="text-indigo-400 hover:text-indigo-300">
                                {{ $contactMessage->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400">Phone</label>
                        <p class="text-white">
                            <a href="tel:{{ $contactMessage->phone }}" class="text-indigo-400 hover:text-indigo-300">
                                {{ $contactMessage->phone }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400">Received</label>
                        <p class="text-white">{{ $contactMessage->created_at->format('M d, Y g:i A') }}</p>
                        <p class="text-gray-500 text-xs">{{ $contactMessage->created_at->diffForHumans() }}</p>
                    </div>
                    @if($contactMessage->read_at)
                        <div>
                            <label class="text-sm text-gray-400">Read At</label>
                            <p class="text-white text-sm">{{ $contactMessage->read_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Management -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Manage Status</h3>
                <form action="{{ route($routePrefix . '.contact.update', $contactMessage->uuid) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="admin_notes" value="{{ $contactMessage->admin_notes }}">
                    
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="new" {{ $contactMessage->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="read" {{ $contactMessage->status === 'read' ? 'selected' : '' }}>Read</option>
                            <option value="responded" {{ $contactMessage->status === 'responded' ? 'selected' : '' }}>Responded</option>
                            <option value="archived" {{ $contactMessage->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Assign To</label>
                        <select name="assigned_to" class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">Unassigned</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ $contactMessage->assigned_to == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Actions -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="mailto:{{ $contactMessage->email }}?subject=Re: Your inquiry" 
                       class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                        Send Email Reply
                    </a>
                    
                    <form action="{{ route($routePrefix . '.contact.destroy', $contactMessage->uuid) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this message?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
