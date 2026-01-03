<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreItemController extends Controller
{
    public function index(Request $request)
    {
        $query = StoreItem::with('category');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low_stock') {
                $query->whereRaw('quantity <= reorder_level');
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('quantity', '>', 0);
            }
        }

        $items = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => StoreItem::count(),
            'active' => StoreItem::where('is_active', true)->count(),
            'low_stock' => StoreItem::whereRaw('quantity <= reorder_level')->where('quantity', '>', 0)->count(),
            'out_of_stock' => StoreItem::where('quantity', '<=', 0)->count(),
            'total_value' => StoreItem::selectRaw('SUM(quantity * cost_price) as total')->value('total') ?? 0,
            'total_items_qty' => StoreItem::sum('quantity') ?? 0,
        ];

        $categories = Category::orderBy('name')->get();

        return view('admin.store-items.index', compact('items', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.store-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:store_items,sku',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'reorder_level' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }

        $validated['is_active'] = $request->has('is_active');

        $item = StoreItem::create($validated);

        return redirect()->route('admin.store-items.show', $item)
            ->with('success', 'Store item created successfully.');
    }

    public function show(StoreItem $storeItem)
    {
        $storeItem->load('category');
        return view('admin.store-items.show', ['item' => $storeItem]);
    }

    public function edit(StoreItem $storeItem)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.store-items.edit', ['item' => $storeItem, 'categories' => $categories]);
    }

    public function update(Request $request, StoreItem $storeItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:store_items,sku,' . $storeItem->id,
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'reorder_level' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $storeItem->update($validated);

        return redirect()->route('admin.store-items.show', $storeItem)
            ->with('success', 'Store item updated successfully.');
    }

    public function destroy(StoreItem $storeItem)
    {
        $storeItem->delete();

        return redirect()->route('admin.store-items.index')
            ->with('success', 'Store item deleted successfully.');
    }

    public function toggleStatus(Request $request, StoreItem $storeItem)
    {
        $storeItem->update([
            'is_active' => !$storeItem->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }

    public function adjustStock(Request $request, StoreItem $storeItem)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $oldQuantity = $storeItem->quantity;

        if ($validated['adjustment_type'] === 'add') {
            $storeItem->quantity += $validated['quantity'];
        } elseif ($validated['adjustment_type'] === 'subtract') {
            $storeItem->quantity = max(0, $storeItem->quantity - $validated['quantity']);
        } else {
            $storeItem->quantity = $validated['quantity'];
        }

        $storeItem->save();

        return redirect()->back()
            ->with('success', "Stock adjusted from {$oldQuantity} to {$storeItem->quantity} {$storeItem->unit}.");
    }
}
