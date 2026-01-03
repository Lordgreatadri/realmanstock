<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Livestock Categories
            [
                'name' => 'Goats',
                'slug' => 'goats',
                'description' => 'Various breeds of goats for live sale or processing',
                'type' => 'livestock',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sheep',
                'slug' => 'sheep',
                'description' => 'Quality sheep breeds for meat production',
                'type' => 'livestock',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Fowls',
                'slug' => 'fowls',
                'description' => 'Chickens and other domestic fowls',
                'type' => 'livestock',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Guinea Fowls',
                'slug' => 'guinea-fowls',
                'description' => 'Premium guinea fowls for meat',
                'type' => 'livestock',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Turkeys',
                'slug' => 'turkeys',
                'description' => 'Large turkeys for festive occasions',
                'type' => 'livestock',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Rabbits',
                'slug' => 'rabbits',
                'description' => 'Rabbits for meat production',
                'type' => 'livestock',
                'is_active' => true,
                'sort_order' => 6,
            ],
            // Grocery Categories
            [
                'name' => 'Rice & Grains',
                'slug' => 'rice-grains',
                'description' => 'Quality rice and grain products',
                'type' => 'grocery',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Oils & Spices',
                'slug' => 'oils-spices',
                'description' => 'Cooking oils and spices',
                'type' => 'grocery',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Canned Foods',
                'slug' => 'canned-foods',
                'description' => 'Preserved and canned food items',
                'type' => 'grocery',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'description' => 'Soft drinks and beverages',
                'type' => 'grocery',
                'is_active' => true,
                'sort_order' => 10,
            ],
            // Service Category
            [
                'name' => 'Processing Services',
                'slug' => 'processing-services',
                'description' => 'Animal dressing and processing services',
                'type' => 'service',
                'is_active' => true,
                'sort_order' => 11,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
