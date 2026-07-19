<?php

namespace Database\Seeders;

use App\Models\FarmCategory;
use Illuminate\Database\Seeder;

class FarmCategorySeeder extends Seeder
{
    private const CATEGORIES = [
        [
            'name' => 'Poultry',
            'slug' => 'poultry',
            'sort_order' => 1,
            'children' => [
                ['name' => 'Broiler', 'slug' => 'broiler', 'sort_order' => 1],
                ['name' => 'Sonali', 'slug' => 'sonali', 'sort_order' => 2],
                ['name' => 'Duck', 'slug' => 'duck', 'sort_order' => 3],
            ],
        ],
        [
            'name' => 'Livestock',
            'slug' => 'livestock',
            'sort_order' => 2,
            'children' => [
                ['name' => 'Cattle', 'slug' => 'cattle', 'sort_order' => 1],
                ['name' => 'Goat', 'slug' => 'goat', 'sort_order' => 2],
            ],
        ],
        [
            'name' => 'Aquaculture',
            'slug' => 'aquaculture',
            'sort_order' => 3,
            'children' => [
                ['name' => 'Fish', 'slug' => 'fish', 'sort_order' => 1],
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $categoryData) {
            $parent = FarmCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'parent_id' => null,
                    'name' => $categoryData['name'],
                    'description' => null,
                    'icon' => null,
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true,
                    'created_by' => null,
                ]
            );

            foreach ($categoryData['children'] as $childData) {
                FarmCategory::updateOrCreate(
                    ['slug' => $childData['slug']],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childData['name'],
                        'description' => null,
                        'icon' => null,
                        'sort_order' => $childData['sort_order'],
                        'is_active' => true,
                        'created_by' => null,
                    ]
                );
            }
        }
    }
}
