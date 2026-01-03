<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\FreezerInventory;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreezerController extends Controller
{
    public function index(Request $request): View
    {
        $query = FreezerInventory::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        $inventories = $query->latest()->paginate(15);
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();

        // Statistics
        $stats = [
            'total' => FreezerInventory::count(),
            'in_stock' => FreezerInventory::where('status', 'in_stock')->count(),
            'reserved' => FreezerInventory::where('status', 'reserved')->count(),
            'sold' => FreezerInventory::where('status', 'sold')->count(),
            'total_weight' => FreezerInventory::where('status', 'in_stock')->sum('weight'),
            'expiring_soon' => FreezerInventory::expiringSoon(7)->count(),
        ];

        return view('manager.freezer.index', compact('inventories', 'categories', 'stats'));
    }

    public function create(): View
    {
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();
        return view('manager.freezer.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'batch_number' => 'required|string|unique:freezer_inventories,batch_number',
            'weight' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,lbs,pieces',
            'storage_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:storage_date',
            'location' => 'required|string|max:255',
            'temperature' => 'nullable|numeric',
            'notes' => 'nullable|string'
        ]);

        FreezerInventory::create($validated);

        return redirect()->route('manager.freezer.index')
            ->with('success', 'Freezer inventory item created successfully.');
    }

    public function show(FreezerInventory $freezer): View
    {
        $freezer->load('category');
        $inventory = $freezer;
        return view('manager.freezer.show', compact('inventory'));
    }

    public function edit(FreezerInventory $freezer): View
    {
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();
        $processingRequests = \App\Models\ProcessingRequest::with(['customer', 'animal'])
            ->where('status', 'completed')
            ->latest()
            ->get();
        $inventory = $freezer;
        return view('manager.freezer.edit', compact('inventory', 'categories', 'processingRequests'));
    }

    public function update(Request $request, FreezerInventory $freezer): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'batch_number' => 'required|string|unique:freezer_inventories,batch_number,' . $freezer->id,
            'weight' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,lbs,pieces',
            'storage_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:storage_date',
            'location' => 'required|string|max:255',
            'temperature' => 'nullable|numeric',
            'notes' => 'nullable|string'
        ]);

        $freezer->update($validated);

        return redirect()->route('manager.freezer.index')
            ->with('success', 'Freezer inventory updated successfully.');
    }

    public function destroy(FreezerInventory $freezer): RedirectResponse
    {
        $freezer->delete();

        return redirect()->route('manager.freezer.index')
            ->with('success', 'Freezer inventory deleted successfully.');
    }
}
