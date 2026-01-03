@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">System Logs</h1>
            <p class="text-gray-400 mt-1">View and monitor application errors and logs</p>
        </div>
        <div class="flex space-x-3">
            @if($selectedFile && !in_array($selectedFile, ['laravel-' . date('Y-m-d') . '.log', 'laravel.log']))
                <form action="{{ route('admin.logs.delete', $selectedFile) }}" method="POST" onsubmit="return confirm('Delete this log file?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete File
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Clear all logs older than 7 days?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Old Logs
                </button>
            </form>
            @if($selectedFile)
                <a href="{{ route('admin.logs.download', $selectedFile) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar: Log Files -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-white mb-4">Log Files</h2>
                <div class="space-y-2">
                    @forelse($logFiles as $file)
                        <a href="?file={{ $file['name'] }}" 
                           class="block p-3 rounded-lg transition {{ $selectedFile === $file['name'] ? 'bg-indigo-600 text-white' : 'bg-gray-900 text-gray-300 hover:bg-gray-700' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 truncate">
                                    <div class="text-sm font-medium truncate">{{ $file['name'] }}</div>
                                    <div class="text-xs {{ $selectedFile === $file['name'] ? 'text-indigo-200' : 'text-gray-500' }} mt-1">
                                        {{ $file['size'] }} • {{ $file['modified'] }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500 text-sm">No log files found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main: Log Entries -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Filters -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="hidden" name="file" value="{{ $selectedFile }}">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Level</label>
                        <select name="level" class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Levels</option>
                            <option value="error" {{ $level === 'error' ? 'selected' : '' }}>ERROR</option>
                            <option value="warning" {{ $level === 'warning' ? 'selected' : '' }}>WARNING</option>
                            <option value="info" {{ $level === 'info' ? 'selected' : '' }}>INFO</option>
                            <option value="debug" {{ $level === 'debug' ? 'selected' : '' }}>DEBUG</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Date</label>
                        <input type="date" name="date" value="{{ $date }}" 
                               class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search logs..." 
                               class="w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                            Filter
                        </button>
                        <a href="?file={{ $selectedFile }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Log Entries -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg">
                <div class="p-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold text-white">
                        Log Entries 
                        @if(count($logs) > 0)
                            <span class="text-sm font-normal text-gray-400">({{ count($logs) }} entries)</span>
                        @endif
                    </h2>
                </div>

                <div class="divide-y divide-gray-700 max-h-[600px] overflow-y-auto">
                    @forelse($logs as $log)
                        <div class="p-4 hover:bg-gray-750" x-data="{ expanded: false }">
                            <div class="flex items-start space-x-3">
                                <!-- Level Badge -->
                                <span class="px-2 py-1 rounded text-xs font-semibold shrink-0
                                    @if($log['level'] === 'ERROR') bg-red-500/20 text-red-400 border border-red-500/50
                                    @elseif($log['level'] === 'WARNING') bg-yellow-500/20 text-yellow-400 border border-yellow-500/50
                                    @elseif($log['level'] === 'INFO') bg-blue-500/20 text-blue-400 border border-blue-500/50
                                    @else bg-gray-500/20 text-gray-400 border border-gray-500/50
                                    @endif">
                                    {{ $log['level'] }}
                                </span>

                                <div class="flex-1 min-w-0">
                                    <!-- Date -->
                                    <div class="text-xs text-gray-500 mb-1">{{ $log['date'] }}</div>
                                    
                                    <!-- Message -->
                                    <div class="text-sm text-gray-300 break-words">{{ \Str::limit($log['message'], 200) }}</div>
                                    
                                    <!-- File Location -->
                                    @if($log['file'])
                                        <div class="mt-2 text-xs">
                                            <span class="text-gray-500">📁</span>
                                            <span class="text-indigo-400 font-mono">{{ $log['file'] }}</span>
                                            @if($log['line'])
                                                <span class="text-gray-500">:</span>
                                                <span class="text-yellow-400 font-mono">{{ $log['line'] }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Toggle Stack Trace -->
                                    @if($log['trace'])
                                        <button @click="expanded = !expanded" class="mt-2 text-xs text-indigo-400 hover:text-indigo-300">
                                            <span x-show="!expanded">▸ Show Stack Trace</span>
                                            <span x-show="expanded">▾ Hide Stack Trace</span>
                                        </button>
                                        
                                        <div x-show="expanded" x-collapse class="mt-2 p-3 bg-gray-900 rounded border border-gray-700">
                                            <pre class="text-xs text-gray-400 overflow-x-auto font-mono whitespace-pre-wrap">{{ $log['trace'] }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            @if($selectedFile)
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg">No log entries found</p>
                                <p class="text-sm mt-1">Try adjusting your filters or select a different log file</p>
                            @else
                                <p>Select a log file to view entries</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to top when page loads
    document.addEventListener('DOMContentLoaded', function() {
        window.scrollTo(0, 0);
    });
</script>
@endpush
@endsection
