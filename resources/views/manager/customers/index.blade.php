@extends('layouts.manager')

@section('title', 'Customers')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Customers</h1>
            <p class="text-gray-400 mt-2">Manage customer information and accounts</p>
        </div>
        <a href="{{ route('manager.customers.create') }}" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
            Add New Customer
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-900 border border-green-700 text-green-300 px-6 py-4 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-900 border border-red-700 text-red-300 px-6 py-4 rounded-lg mb-6">
        {{ session('error') }}
    </div>
@endif

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Total Customers</div>
        <div class="text-3xl font-bold mt-2">{{ $customers->total() }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-green-600 to-teal-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Active Customers</div>
        <div class="text-3xl font-bold mt-2">{{ $customers->filter(fn($c) => $c->orders_count > 0)->count() }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Outstanding Balance</div>
        <div class="text-3xl font-bold mt-2">GH₵{{ number_format($customers->sum('outstanding_balance'), 2) }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg p-6 text-white">
        <div class="text-sm opacity-80">Credit Accounts</div>
        <div class="text-3xl font-bold mt-2">{{ $customers->filter(fn($c) => $c->allow_credit)->count() }}</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('manager.customers.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Name, email, or phone..." 
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
        </div>

        <!-- City Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">City</label>
            <select name="city" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>

        <!-- State Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">State</label>
            <select name="state" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
        </div>

        <!-- Credit Status -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Credit Status</label>
            <select name="credit_status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <option value="">All</option>
                <option value="allowed" {{ request('credit_status') == 'allowed' ? 'selected' : '' }}>Credit Allowed</option>
                <option value="not_allowed" {{ request('credit_status') == 'not_allowed' ? 'selected' : '' }}>No Credit</option>
                <option value="has_balance" {{ request('credit_status') == 'has_balance' ? 'selected' : '' }}>Has Outstanding Balance</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex items-end gap-2 md:col-span-2 lg:col-span-4">
            <button type="submit" class="px-4 py-1.5 text-sm bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                Filter
            </button>
            <a href="{{ route('manager.customers.index') }}" class="px-4 py-1.5 text-sm bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Customers Table -->
<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Credit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-white">{{ $customer->name }}</div>
                        <div class="text-xs text-gray-400">Joined {{ $customer->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-white">{{ $customer->phone }}</div>
                        @if($customer->email)
                            <div class="text-xs text-gray-400">{{ $customer->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-white">{{ $customer->city }}</div>
                        <div class="text-xs text-gray-400">{{ $customer->state }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                        {{ $customer->orders_count ?? 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-white">
                        GH₵{{ number_format($customer->orders_sum_total ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($customer->outstanding_balance > 0)
                            <span class="text-sm font-semibold text-red-400">GH₵{{ number_format($customer->outstanding_balance, 2) }}</span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($customer->allow_credit)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-200 text-green-900">
                                GH₵{{ number_format($customer->credit_limit, 0) }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">No credit</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('manager.customers.show', $customer) }}" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                View
                            </a>
                            <a href="{{ route('manager.customers.edit', $customer) }}" class="px-2 py-1 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs rounded hover:from-emerald-700 hover:to-teal-700">
                                Edit
                            </a>
                            @if(($customer->orders_count ?? 0) == 0)
                            <form action="{{ route('manager.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-lg mb-2">No customers found</div>
                        <p class="text-sm">Click "Add New Customer" to create your first customer.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
    <div class="bg-gray-900 px-6 py-4">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
