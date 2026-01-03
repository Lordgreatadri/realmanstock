@php
    $routePrefix = request()->route()->getPrefix() === '/manager' ? 'manager' : 'admin';
    $layout = $routePrefix === 'manager' ? 'layouts.manager' : 'layouts.admin';
@endphp

@extends('layouts.manager')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Animal Details</h1>
            <p class="text-gray-400 mt-1">{{ $animal->tag_number ?: 'ID: ' . $animal->id }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('manager.animals.edit', $animal) }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('manager.animals.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Details -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-gray-400 text-sm">Category</label>
                        <p class="text-white font-medium">{{ $animal->category->name }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Tag Number</label>
                        <p class="text-white font-medium">{{ $animal->tag_number ?: 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Breed</label>
                        <p class="text-white font-medium">{{ $animal->breed ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Gender</label>
                        <p class="text-white font-medium">{{ $animal->gender ? ucfirst($animal->gender) : 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Date of Birth</label>
                        <p class="text-white font-medium">{{ $animal->date_of_birth ? $animal->date_of_birth->format('M d, Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Age</label>
                        <p class="text-white font-medium">
                            @if($animal->date_of_birth)
                                {{ $animal->date_of_birth->diffForHumans(null, true) }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Current Weight</label>
                        <p class="text-white font-medium">{{ $animal->current_weight ? $animal->current_weight . ' kg' : 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Status</label>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                            {{ $animal->status === 'available' ? 'bg-green-500/20 text-green-400' : '' }}
                            {{ $animal->status === 'sold' ? 'bg-blue-500/20 text-blue-400' : '' }}
                            {{ $animal->status === 'quarantined' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                            {{ $animal->status === 'under_treatment' ? 'bg-orange-500/20 text-orange-400' : '' }}
                            {{ $animal->status === 'reserved' ? 'bg-purple-500/20 text-purple-400' : '' }}
                            {{ $animal->status === 'deceased' ? 'bg-red-500/20 text-red-400' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $animal->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Purchase & Pricing -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Purchase & Pricing</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-gray-400 text-sm">Purchase Price</label>
                        <p class="text-white font-medium text-xl">GH₵{{ number_format($animal->purchase_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Purchase Date</label>
                        <p class="text-white font-medium">{{ $animal->purchase_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Supplier</label>
                        <p class="text-white font-medium">{{ $animal->supplier ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Days in Inventory</label>
                        <p class="text-white font-medium">{{ $animal->purchase_date->diffInDays() }} days</p>
                    </div>
                    @if($animal->selling_price_per_kg)
                        <div>
                            <label class="text-gray-400 text-sm">Selling Price per KG</label>
                            <p class="text-white font-medium">GH₵{{ number_format($animal->selling_price_per_kg, 2) }}</p>
                        </div>
                    @endif
                    @if($animal->fixed_selling_price)
                        <div>
                            <label class="text-gray-400 text-sm">Fixed Selling Price</label>
                            <p class="text-white font-medium">GH₵{{ number_format($animal->fixed_selling_price, 2) }}</p>
                        </div>
                    @endif
                    @if($animal->selling_price)
                        <div class="col-span-2">
                            <label class="text-gray-400 text-sm">Expected Selling Price</label>
                            <p class="text-green-400 font-bold text-2xl">GH₵{{ number_format($animal->selling_price, 2) }}</p>
                            @if($animal->selling_price > $animal->purchase_price)
                                <p class="text-green-400 text-sm">
                                    Profit: GH₵{{ number_format($animal->selling_price - $animal->purchase_price, 2) }}
                                    ({{ number_format((($animal->selling_price - $animal->purchase_price) / $animal->purchase_price) * 100, 1) }}%)
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Health Information -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Health Information</h2>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg {{ $animal->is_vaccinated ? 'bg-green-500/20' : 'bg-gray-700' }} flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $animal->is_vaccinated ? 'text-green-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">{{ $animal->is_vaccinated ? 'Vaccinated' : 'Not Vaccinated' }}</p>
                            @if($animal->last_vaccination_date)
                                <p class="text-gray-400 text-sm">Last vaccination: {{ $animal->last_vaccination_date->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if($animal->health_notes)
                        <div>
                            <label class="text-gray-400 text-sm">Health Notes</label>
                            <p class="text-white mt-1">{{ $animal->health_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($animal->notes)
                <!-- Additional Notes -->
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Additional Notes</h2>
                    <p class="text-gray-300">{{ $animal->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Image -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Photo</h2>
                @if($animal->image)
                    <img src="{{ asset('storage/' . $animal->image) }}" alt="{{ $animal->tag_number }}" class="w-full rounded-lg">
                @else
                    <img src="{{ asset('images/default-animal.svg') }}" alt="No image" class="w-full rounded-lg">
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Quick Stats</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Health Records</span>
                        <span class="text-white font-medium">{{ $animal->healthRecords->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Weight Records</span>
                        <span class="text-white font-medium">{{ $animal->weightRecords->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Created</span>
                        <span class="text-white font-medium">{{ $animal->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Last Updated</span>
                        <span class="text-white font-medium">{{ $animal->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('manager.animals.edit', $animal) }}" class="block w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-center rounded-lg transition">
                        Edit Animal
                    </a>
                    <form method="POST" action="{{ route('manager.animals.destroy', $animal) }}" onsubmit="return confirm('Are you sure you want to delete this animal? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Delete Animal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
