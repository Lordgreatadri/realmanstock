<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProcessingRequest;
use App\Models\Animal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcessingController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProcessingRequest::with(['animal.category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('processing_number', 'like', "%{$search}%")
                  ->orWhereHas('animal', function($q) use ($search) {
                      $q->where('tag_number', 'like', "%{$search}%");
                  });
            });
        }

        $processingRequests = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => ProcessingRequest::count(),
            'pending' => ProcessingRequest::where('status', 'pending')->count(),
            'in_progress' => ProcessingRequest::where('status', 'in_progress')->count(),
            'completed' => ProcessingRequest::where('status', 'completed')->count(),
        ];

        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('manager.processing.index', compact('processingRequests', 'stats', 'customers'));
    }

    public function create(): View
    {
        $animals = Animal::where('status', 'available')->with('category')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();
        $orders = \App\Models\Order::with('customer')->latest()->get();
        return view('manager.processing.create', compact('animals', 'customers', 'orders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'processing_date' => 'required|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:processing_date',
            'processing_type' => 'required|in:full,partial,custom',
            'cuts_specification' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $validated['processing_number'] = 'PRC-' . strtoupper(uniqid());

        ProcessingRequest::create($validated);

        // Update animal status
        Animal::find($validated['animal_id'])->update(['status' => 'processing']);

        return redirect()->route('manager.processing.index')
            ->with('success', 'Processing request created successfully.');
    }

    public function show(ProcessingRequest $processing): View
    {
        $processing->load('animal.category');
        return view('manager.processing.show', compact('processing'));
    }

    public function edit(ProcessingRequest $processing): View
    {
        $animals = Animal::whereIn('status', ['available', 'processing'])->with('category')->get();
        $users = \App\Models\User::orderBy('name')->get();
        return view('manager.processing.edit', compact('processing', 'animals', 'users'));
    }

    public function update(Request $request, ProcessingRequest $processing): RedirectResponse
    {
        $validated = $request->validate([
            'processing_date' => 'required|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:processing_date',
            'actual_completion_date' => 'nullable|date',
            'processing_type' => 'required|in:full,partial,custom',
            'cuts_specification' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $processing->update($validated);

        return redirect()->route('manager.processing.index')
            ->with('success', 'Processing request updated successfully.');
    }

    public function destroy(ProcessingRequest $processing): RedirectResponse
    {
        $processing->delete();

        return redirect()->route('manager.processing.index')
            ->with('success', 'Processing request deleted successfully.');
    }

    public function updateStatus(Request $request, ProcessingRequest $processing): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $processing->update($validated);

        return redirect()->route('manager.processing.show', $processing)
            ->with('success', 'Processing status updated successfully.');
    }
}
