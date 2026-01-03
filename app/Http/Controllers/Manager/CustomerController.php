<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders')->withSum('orders', 'total');

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Filter by credit status
        if ($request->filled('credit_status')) {
            if ($request->credit_status === 'allowed') {
                $query->where('allow_credit', true);
            } elseif ($request->credit_status === 'not_allowed') {
                $query->where('allow_credit', false);
            } elseif ($request->credit_status === 'has_balance') {
                $query->where('outstanding_balance', '>', 0);
            }
        }

        $customers = $query->latest()->paginate(10);
        
        // Get unique cities and states for filters
        $cities = Customer::distinct()->pluck('city')->filter()->sort()->values();
        $states = Customer::distinct()->pluck('state')->filter()->sort()->values();

        return view('manager.customers.index', compact('customers', 'cities', 'states'));
    }

    public function create(): View
    {
        return view('manager.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'preferred_delivery' => 'nullable|in:pickup,delivery',
            'preferred_processing' => 'nullable|string|max:255',
            'allow_credit' => 'boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['outstanding_balance'] = 0.00;

        Customer::create($validated);

        return redirect()->route('manager.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('manager.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('manager.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'preferred_delivery' => 'nullable|in:pickup,delivery',
            'preferred_processing' => 'nullable|string|max:255',
            'allow_credit' => 'boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('manager.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete customer with existing orders.');
        }

        $customer->delete();

        return redirect()->route('manager.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
