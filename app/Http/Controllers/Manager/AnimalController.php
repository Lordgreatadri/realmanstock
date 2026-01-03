<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnimalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Animal::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tag_number', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        $animals = $query->latest()->paginate(15);
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();

        return view('manager.animals.index', compact('animals', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();
        return view('manager.animals.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tag_number' => 'required|string|unique:animals,tag_number',
            'breed' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,sold,processing,quarantine',
            'health_status' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        Animal::create($validated);

        return redirect()->route('manager.animals.index')
            ->with('success', 'Animal created successfully.');
    }

    public function show(Animal $animal): View
    {
        $animal->load('category');
        return view('manager.animals.show', compact('animal'));
    }

    public function edit(Animal $animal): View
    {
        $categories = Category::where('type', 'livestock')->where('is_active', true)->get();
        return view('manager.animals.edit', compact('animal', 'categories'));
    }

    public function update(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tag_number' => 'required|string|unique:animals,tag_number,' . $animal->id,
            'breed' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,sold,processing,quarantine',
            'health_status' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $animal->update($validated);

        return redirect()->route('manager.animals.index')
            ->with('success', 'Animal updated successfully.');
    }

    public function destroy(Animal $animal): RedirectResponse
    {
        $animal->delete();

        return redirect()->route('manager.animals.index')
            ->with('success', 'Animal deleted successfully.');
    }
}
