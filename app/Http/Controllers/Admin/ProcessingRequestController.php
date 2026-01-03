<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcessingRequest;
use App\Models\Animal;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class ProcessingRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ProcessingRequest::with(['customer', 'animal', 'order', 'processedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('requested_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('requested_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('animal', function($q) use ($request) {
                $q->where('tag_number', 'like', '%' . $request->search . '%');
            });
        }

        $processingRequests = $query->latest()->paginate(10);

        // Statistics
        $stats = [
            'total' => ProcessingRequest::count(),
            'pending' => ProcessingRequest::where('status', 'pending')->count(),
            'in_progress' => ProcessingRequest::where('status', 'in_progress')->count(),
            'completed' => ProcessingRequest::where('status', 'completed')->count(),
        ];

        $customers = Customer::orderBy('name')->get();

        return view('admin.processing.index', compact('processingRequests', 'stats', 'customers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $animals = Animal::where('status', 'available')->orderBy('tag_number')->get();
        $orders = Order::with('customer')->latest()->get();

        return view('admin.processing.create', compact('customers', 'animals', 'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'animal_id' => 'nullable|exists:animals,id',
            'order_id' => 'nullable|exists:orders,id',
            'processing_fee' => 'required|numeric|min:0',
            'requested_date' => 'required|date',
            'scheduled_date' => 'nullable|date|after_or_equal:requested_date',
            'live_weight' => 'nullable|numeric|min:0',
            'special_instructions' => 'nullable|string',
        ]);

        ProcessingRequest::create($validated);

        return redirect()->route('admin.processing.index')
            ->with('success', 'Processing request created successfully.');
    }

    public function show(ProcessingRequest $processing)
    {
        $processing->load(['customer', 'animal', 'order', 'processedBy']);

        return view('admin.processing.show', compact('processing'));
    }

    public function edit(ProcessingRequest $processing)
    {
        $customers = Customer::orderBy('name')->get();
        $animals = Animal::orderBy('tag_number')->get();
        $orders = Order::with('customer')->latest()->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.processing.edit', compact('processing', 'customers', 'animals', 'orders', 'users'));
    }

    public function update(Request $request, ProcessingRequest $processing)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'processed_by' => 'nullable|exists:users,id',
            'processing_fee' => 'required|numeric|min:0',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'live_weight' => 'nullable|numeric|min:0',
            'dressed_weight' => 'nullable|numeric|min:0',
            'special_instructions' => 'nullable|string',
            'quality_notes' => 'nullable|string',
        ]);

        // Auto-set completed date when status changes to completed
        if ($validated['status'] === 'completed' && !$validated['completed_date']) {
            $validated['completed_date'] = now();
        }

        $processing->update($validated);

        return redirect()->route('admin.processing.show', $processing)
            ->with('success', 'Processing request updated successfully.');
    }

    public function destroy(ProcessingRequest $processing)
    {
        if ($processing->status === 'completed') {
            return redirect()->route('admin.processing.index')
                ->with('error', 'Cannot delete completed processing requests.');
        }

        $processing->delete();

        return redirect()->route('admin.processing.index')
            ->with('success', 'Processing request deleted successfully.');
    }

    public function updateStatus(Request $request, ProcessingRequest $processing)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $processing->update([
            'status' => $validated['status'],
            'processed_by' => auth()->id(),
            'completed_date' => $validated['status'] === 'completed' ? now() : $processing->completed_date,
        ]);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }
}
