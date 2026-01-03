@extends('layouts.manager')

@section('title', 'Edit Processing Request')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-bold text-white">Edit Processing Request #{{ $processing->id }}</h1>
        <p class="text-gray-400 mt-2">Update processing request details</p>
    </div>
    <a href="{{ route('manager.processing.show', $processing) }}" 
       class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
        Back to Details
    </a>
</div>

@if ($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-800 border border-red-600 text-red-100 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('manager.processing.update', $processing) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-xl font-bold text-white mb-6">Request Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status *</label>
                <select name="status" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    <option value="pending" {{ old('status', $processing->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status', $processing->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', $processing->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $processing->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Processed By</label>
                <select name="processed_by"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('processed_by', $processing->processed_by) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Processing Fee *</label>
                <input type="number" name="processing_fee" step="0.01" min="0" value="{{ old('processing_fee', $processing->processing_fee) }}" required
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Scheduled Date</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $processing->scheduled_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Completed Date</label>
                <input type="date" name="completed_date" value="{{ old('completed_date', $processing->completed_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Live Weight (kg)</label>
                <input type="number" name="live_weight" step="0.01" min="0" value="{{ old('live_weight', $processing->live_weight) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Dressed Weight (kg)</label>
                <input type="number" name="dressed_weight" step="0.01" min="0" value="{{ old('dressed_weight', $processing->dressed_weight) }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">Special Instructions</label>
                <textarea name="special_instructions" rows="3"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('special_instructions', $processing->special_instructions) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">Quality Notes</label>
                <textarea name="quality_notes" rows="3"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">{{ old('quality_notes', $processing->quality_notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <div>
            @if($processing->status !== 'completed')
            <button type="button" onclick="document.getElementById('deleteForm').submit();" 
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                Delete Request
            </button>
            @endif
        </div>
        <div class="flex gap-4">
            <a href="{{ route('manager.processing.show', $processing) }}" 
               class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition">
                Update Request
            </button>
        </div>
    </div>
</form>

<!-- Delete Form -->
@if($processing->status !== 'completed')
<form id="deleteForm" action="{{ route('manager.processing.destroy', $processing) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection
