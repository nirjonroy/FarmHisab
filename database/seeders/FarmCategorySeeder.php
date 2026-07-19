<?php

namespace Database\Seeders;

use App\Enums\CategoryActivityType;
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
            'activity_type' => CategoryActivityType::PRODUCTION,
            'sort_order' => 1,
            'children' => [
                ['name' => 'Broiler', 'name_en' => 'Broiler', 'name_bn' => 'ব্রয়লার', 'slug' => 'broiler', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 1],
                ['name' => 'Sonali', 'name_en' => 'Sonali', 'name_bn' => 'সোনালি', 'slug' => 'sonali', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 2],
                ['name' => 'Duck', 'name_en' => 'Duck', 'name_bn' => 'হাঁস', 'slug' => 'duck', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 3],
            ],
        ],
        [
            'name' => 'Livestock',
            'name_en' => 'Livestock',
            'name_bn' => 'গবাদিপশু',
            'slug' => 'livestock',
            'activity_type' => CategoryActivityType::PRODUCTION,
            'sort_order' => 2,
            'children' => [
                ['name' => 'Cattle', 'name_en' => 'Cattle', 'name_bn' => 'গরু', 'slug' => 'cattle', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 1],
                ['name' => 'Goat', 'name_en' => 'Goat', 'name_bn' => 'ছাগল', 'slug' => 'goat', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 2],
            ],
        ],
        [
            'name' => 'Aquaculture',
            'name_en' => 'Aquaculture',
            'name_bn' => 'মৎস্য চাষ',
            'slug' => 'aquaculture',
            'activity_type' => CategoryActivityType::PRODUCTION,
            'sort_order' => 3,
            'children' => [
                ['name' => 'Fish', 'name_en' => 'Fish', 'name_bn' => 'মাছ', 'slug' => 'fish', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 1],
            ],
        ],
        [
            'name' => 'Crop Production',
            'name_en' => 'Crop Production',
            'name_bn' => 'ফসল উৎপাদন',
            'slug' => 'crop-production',
            'activity_type' => CategoryActivityType::PRODUCTION,
            'sort_order' => 40,
            'children' => [
                ['name' => 'Paddy Cultivation', 'name_en' => 'Paddy Cultivation', 'name_bn' => 'ধান চাষ', 'slug' => 'paddy-cultivation', 'activity_type' => CategoryActivityType::PRODUCTION, 'sort_order' => 10],
            ],
        ],
        [
            'name' => 'Agricultural Inputs',
            'name_en' => 'Agricultural Inputs',
            'name_bn' => 'কৃষি উপকরণ',
            'slug' => 'agricultural-inputs',
            'activity_type' => CategoryActivityType::TRADING,
            'sort_order' => 50,
            'children' => [
                ['name' => 'Fertilizer', 'name_en' => 'Fertilizer', 'name_bn' => 'সার', 'slug' => 'fertilizer', 'activity_type' => CategoryActivityType::TRADING, 'sort_order' => 10],
                ['name' => 'Seed', 'name_en' => 'Seed', 'name_bn' => 'বীজ', 'slug' => 'seed', 'activity_type' => CategoryActivityType::TRADING, 'sort_order' => 20],
            ],
        ],
        [
            'name' => 'Forestry & Natural Products',
            'name_en' => 'Forestry & Natural Products',
            'name_bn' => 'বনজ ও প্রাকৃতিক পণ্য',
            'slug' => 'forestry-natural-products',
            'activity_type' => CategoryActivityType::HYBRID,
            'sort_order' => 60,
            'children' => [
                ['name' => 'Bamboo', 'name_en' => 'Bamboo', 'name_bn' => 'বাঁশ', 'slug' => 'bamboo', 'activity_type' => CategoryActivityType::HYBRID, 'sort_order' => 10],
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
                    'activity_type' => $categoryData['activity_type'],
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
                        'activity_type' => $childData['activity_type'],
                        'sort_order' => $childData['sort_order'],
                        'is_active' => true,
                        'created_by' => null,
                    ]
                );
            }
        }
    }
}
