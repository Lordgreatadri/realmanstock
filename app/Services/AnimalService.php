<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalService
{
    public function getAllAnimals($filters = [])
    {
        $query = Animal::with(['category', 'healthRecords', 'weightRecords']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('tag_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('breed', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createAnimal(array $data)
    {
        try {
            DB::beginTransaction();

            if (isset($data['image'])) {
                $data['image'] = $this->uploadImage($data['image']);
            }

            $animal = Animal::create($data);

            // Create initial weight record if weight is provided
            if (isset($data['current_weight'])) {
                $animal->weightRecords()->create([
                    'weight' => $data['current_weight'],
                    'record_date' => now(),
                    'notes' => 'Initial weight',
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();
            return $animal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateAnimal(Animal $animal, array $data)
    {
        try {
            DB::beginTransaction();

            if (isset($data['image']) && !is_string($data['image'])) {
                // Delete old image
                if ($animal->image) {
                    Storage::disk('public')->delete($animal->image);
                }
                $data['image'] = $this->uploadImage($data['image']);
            }

            // Track weight changes
            if (isset($data['current_weight']) && $data['current_weight'] != $animal->current_weight) {
                $animal->weightRecords()->create([
                    'weight' => $data['current_weight'],
                    'record_date' => now(),
                    'notes' => 'Weight updated',
                    'user_id' => auth()->id(),
                ]);
            }

            $animal->update($data);

            DB::commit();
            return $animal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteAnimal(Animal $animal)
    {
        try {
            DB::beginTransaction();

            // Delete image
            if ($animal->image) {
                Storage::disk('public')->delete($animal->image);
            }

            $animal->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAvailableAnimals($categoryId = null)
    {
        $query = Animal::available()->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }

    public function addHealthRecord(Animal $animal, array $data)
    {
        $data['user_id'] = auth()->id();
        return $animal->healthRecords()->create($data);
    }

    public function getLowStockCategories()
    {
        return Category::livestock()
            ->withCount(['animals' => function ($query) {
                $query->where('status', 'available');
            }])
            ->having('animals_count', '<', 5)
            ->get();
    }

    private function uploadImage($image)
    {
        return $image->store('animals', 'public');
    }
}
