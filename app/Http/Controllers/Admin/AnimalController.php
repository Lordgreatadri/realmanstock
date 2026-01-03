<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnimalController extends Controller
{
    /**
     * Display all animals
     */
    public function index(Request $request): View
    {
        $query = Animal::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tag_number', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        $animals = $query->latest()->paginate(10);
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();

        return view('admin.animals.index', compact('animals', 'categories'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();
        return view('admin.animals.create', compact('categories'));
    }

    /**
     * Store new animal
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tag_number' => 'nullable|string|unique:animals,tag_number',
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'current_weight' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,quarantined,under_treatment,reserved,sold,deceased',
            'health_notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_vaccinated' => 'boolean',
            'last_vaccination_date' => 'nullable|date',
            'selling_price_per_kg' => 'nullable|numeric|min:0',
            'fixed_selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['is_vaccinated'] = $request->has('is_vaccinated');

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('animals', 'public');
        }

        Animal::create($validated);

        return redirect()->route('admin.animals.index')->with('success', 'Animal added successfully!');
    }

    /**
     * Show animal details
     */
    public function show(Animal $animal): View
    {
        $animal->load('category', 'healthRecords', 'weightRecords');
        return view('admin.animals.show', compact('animal'));
    }

    /**
     * Show edit form
     */
    public function edit(Animal $animal): View
    {
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();
        return view('admin.animals.edit', compact('animal', 'categories'));
    }

    /**
     * Update animal
     */
    public function update(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tag_number' => 'nullable|string|unique:animals,tag_number,' . $animal->id,
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'current_weight' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,quarantined,under_treatment,reserved,sold,deceased',
            'health_notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_vaccinated' => 'boolean',
            'last_vaccination_date' => 'nullable|date',
            'selling_price_per_kg' => 'nullable|numeric|min:0',
            'fixed_selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['is_vaccinated'] = $request->has('is_vaccinated');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($animal->image && \Storage::disk('public')->exists($animal->image)) {
                \Storage::disk('public')->delete($animal->image);
            }
            $validated['image'] = $request->file('image')->store('animals', 'public');
        }

        $animal->update($validated);

        return redirect()->route('admin.animals.index')->with('success', 'Animal updated successfully!');
    }

    /**
     * Delete animal
     */
    public function destroy(Animal $animal): RedirectResponse
    {
        // Delete image if exists
        if ($animal->image && \Storage::disk('public')->exists($animal->image)) {
            \Storage::disk('public')->delete($animal->image);
        }

        $animal->delete();

        return back()->with('success', 'Animal deleted successfully!');
    }
}

