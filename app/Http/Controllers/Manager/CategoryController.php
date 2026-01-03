<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display all categories
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('animals');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(10);

        return view('manager.categories.index', compact('categories'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('manager.categories.create');
    }

    /**
     * Store new category
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'type' => 'required|in:livestock,grocery,service',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('manager.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show single category
     */
    public function show(Category $category): View
    {
        $category->load(['animals' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('manager.categories.show', compact('category'));
    }

    /**
     * Show edit form
     */
    public function edit(Category $category): View
    {
        return view('manager.categories.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'type' => 'required|in:livestock,grocery,service',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->boolean('is_active', $category->is_active);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('manager.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->animals()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated animals.');
        }

        $category->delete();

        return redirect()->route('manager.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category): RedirectResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', 'Category status updated successfully.');
    }
}
