<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreezerInventory;
use App\Models\Category;
use App\Models\ProcessingRequest;
use Illuminate\Http\Request;

class FreezerInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = FreezerInventory::with(['category', 'processingRequest']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by temperature zone
        if ($request->filled('temperature_zone')) {
            $query->where('temperature_zone', $request->temperature_zone);
        }

        // Filter by expiring soon
        if ($request->filled('expiring_soon')) {
            $query->expiringSoon($request->expiring_soon);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                  ->orWhere('batch_number', 'like', '%' . $request->search . '%');
            });
        }

        $inventories = $query->latest()->paginate(10);

        // Statistics
        $stats = [
            'total' => FreezerInventory::count(),
            'in_stock' => FreezerInventory::where('status', 'in_stock')->count(),
            'reserved' => FreezerInventory::where('status', 'reserved')->count(),
            'sold' => FreezerInventory::where('status', 'sold')->count(),
            'total_weight' => FreezerInventory::where('status', 'in_stock')->sum('weight'),
            'expiring_soon' => FreezerInventory::expiringSoon(7)->count(),
        ];

        $categories = Category::orderBy('name')->get();

        return view('admin.freezer.index', compact('inventories', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $processingRequests = ProcessingRequest::with('customer')
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('admin.freezer.create', compact('categories', 'processingRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'processing_request_id' => 'nullable|exists:processing_requests,id',
            'product_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price_per_kg' => 'required|numeric|min:0',
            'processing_date' => 'required|date',
            'expiry_date' => 'required|date|after:processing_date',
            'storage_location' => 'nullable|string|max:255',
            'temperature_zone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Generate unique batch number
        $validated['batch_number'] = 'BATCH-' . strtoupper(uniqid());

        FreezerInventory::create($validated);

        return redirect()->route('admin.freezer.index')
            ->with('success', 'Freezer inventory item created successfully.');
    }

    public function show(FreezerInventory $freezer)
    {
        $freezer->load(['category', 'processingRequest.customer', 'processingRequest.animal']);

        return view('admin.freezer.show', ['inventory' => $freezer]);
    }

    public function edit(FreezerInventory $freezer)
    {
        $categories = Category::orderBy('name')->get();
        $processingRequests = ProcessingRequest::with(['customer', 'animal'])->latest()->get();

        return view('admin.freezer.edit', ['inventory' => $freezer, 'categories' => $categories, 'processingRequests' => $processingRequests]);
    }

    public function update(Request $request, FreezerInventory $freezer)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'processing_request_id' => 'nullable|exists:processing_requests,id',
            'product_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price_per_kg' => 'required|numeric|min:0',
            'processing_date' => 'required|date',
            'expiry_date' => 'required|date|after:processing_date',
            'storage_location' => 'nullable|string|max:255',
            'temperature_zone' => 'nullable|string|max:255',
            'status' => 'required|in:in_stock,reserved,sold,expired',
            'quality_notes' => 'nullable|string',
        ]);

        $freezer->update($validated);

        return redirect()->route('admin.freezer.show', $freezer)
            ->with('success', 'Freezer inventory item updated successfully.');
    }

    public function destroy(FreezerInventory $freezer)
    {
        if ($freezer->status === 'sold') {
            return redirect()->route('admin.freezer.index')
                ->with('error', 'Cannot delete sold inventory items.');
        }

        $freezer->delete();

        return redirect()->route('admin.freezer.index')
            ->with('success', 'Freezer inventory item deleted successfully.');
    }

    public function updateStatus(Request $request, FreezerInventory $freezer)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_stock,reserved,sold,expired',
        ]);

        $freezer->update($validated);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }
}
