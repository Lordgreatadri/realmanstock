<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\StoreItem;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = StoreItem::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $storeItems = $query->latest()->paginate(15);
        $categories = Category::where('type', 'grocery')->where('is_active', true)->get();

        // Statistics for summary cards
        $stats = [
            'total' => StoreItem::count(),
            'active' => StoreItem::where('is_active', true)->count(),
            'low_stock' => StoreItem::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'out_of_stock' => StoreItem::where('quantity', '<=', 0)->count(),
            'total_items_qty' => StoreItem::sum('quantity'),
            'total_value' => StoreItem::where('quantity', '>', 0)->sum(\DB::raw('quantity * selling_price')),
        ];

        return view('manager.store-items.index', compact('storeItems', 'categories', 'stats'));
    }

    public function create(): View
    {
        $categories = Category::where('type', 'grocery')->where('is_active', true)->get();
        return view('manager.store-items.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:store_items,sku',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        StoreItem::create($validated);

        return redirect()->route('manager.store-items.index')
            ->with('success', 'Store item created successfully.');
    }

    public function show(StoreItem $storeItem): View
    {
        $storeItem->load('category');
        return view('manager.store-items.show', compact('storeItem'));
    }

    public function edit(StoreItem $storeItem): View
    {
        $categories = Category::where('type', 'grocery')->where('is_active', true)->get();
        return view('manager.store-items.edit', compact('storeItem', 'categories'));
    }

    public function update(Request $request, StoreItem $storeItem): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:store_items,sku,' . $storeItem->id,
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->boolean('is_active', $storeItem->is_active);

        $storeItem->update($validated);

        return redirect()->route('manager.store-items.index')
            ->with('success', 'Store item updated successfully.');
    }

    public function destroy(StoreItem $storeItem): RedirectResponse
    {
        $storeItem->delete();

        return redirect()->route('manager.store-items.index')
            ->with('success', 'Store item deleted successfully.');
    }
}
