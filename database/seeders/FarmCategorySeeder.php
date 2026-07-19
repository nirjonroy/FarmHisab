<?php

namespace Database\Seeders;

use App\Models\FarmCategory;
use Illuminate\Database\Seeder;

class FarmCategorySeeder extends Seeder
{
    private const CATEGORIES = [
        [
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
            'slug' => 'poultry',
            'sort_order' => 1,
            'children' => [
                ['name' => 'Broiler', 'name_en' => 'Broiler', 'name_bn' => 'ব্রয়লার', 'slug' => 'broiler', 'sort_order' => 1],
                ['name' => 'Sonali', 'name_en' => 'Sonali', 'name_bn' => 'সোনালি', 'slug' => 'sonali', 'sort_order' => 2],
                ['name' => 'Duck', 'name_en' => 'Duck', 'name_bn' => 'হাঁস', 'slug' => 'duck', 'sort_order' => 3],
            ],
        ],
        [
            'name' => 'Livestock',
            'name_en' => 'Livestock',
            'name_bn' => 'গবাদিপশু',
            'slug' => 'livestock',
            'sort_order' => 2,
            'children' => [
                ['name' => 'Cattle', 'name_en' => 'Cattle', 'name_bn' => 'গরু', 'slug' => 'cattle', 'sort_order' => 1],
                ['name' => 'Goat', 'name_en' => 'Goat', 'name_bn' => 'ছাগল', 'slug' => 'goat', 'sort_order' => 2],
            ],
        ],
        [
            'name' => 'Aquaculture',
            'name_en' => 'Aquaculture',
            'name_bn' => 'মৎস্য চাষ',
            'slug' => 'aquaculture',
            'sort_order' => 3,
            'children' => [
                ['name' => 'Fish', 'name_en' => 'Fish', 'name_bn' => 'মাছ', 'slug' => 'fish', 'sort_order' => 1],
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
                    'name_en' => $categoryData['name_en'],
                    'name_bn' => $categoryData['name_bn'],
                    'description' => null,
                    'description_en' => null,
                    'description_bn' => null,
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
                        'name_en' => $childData['name_en'],
                        'name_bn' => $childData['name_bn'],
                        'description' => null,
                        'description_en' => null,
                        'description_bn' => null,
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
