<?php

namespace Database\Seeders;

use App\Models\FarmCategory;
use App\Models\FarmVariety;
use Illuminate\Database\Seeder;
use RuntimeException;

class FarmVarietySeeder extends Seeder
{
    private const VARIETIES = [
        'broiler' => [
            ['name_en' => 'Cobb 500', 'name_bn' => 'কব ৫০০', 'slug' => 'cobb-500', 'code' => 'COBB500', 'sort_order' => 10],
            ['name_en' => 'Ross 308', 'name_bn' => 'রস ৩০৮', 'slug' => 'ross-308', 'code' => 'ROSS308', 'sort_order' => 20],
        ],
        'duck' => [
            ['name_en' => 'Khaki Campbell', 'name_bn' => 'খাকি ক্যাম্পবেল', 'slug' => 'khaki-campbell', 'code' => 'KHAKI', 'sort_order' => 10],
            ['name_en' => 'Pekin', 'name_bn' => 'পেকিন', 'slug' => 'pekin-duck', 'code' => 'PEKIN', 'sort_order' => 20],
            ['name_en' => 'Jinding', 'name_bn' => 'জিনডিং', 'slug' => 'jinding-duck', 'code' => 'JINDING', 'sort_order' => 30],
        ],
        'cattle' => [
            ['name_en' => 'Holstein Friesian', 'name_bn' => 'হলস্টেইন ফ্রিজিয়ান', 'slug' => 'holstein-friesian', 'code' => 'HF', 'sort_order' => 10],
            ['name_en' => 'Sahiwal', 'name_bn' => 'সাহিওয়াল', 'slug' => 'sahiwal', 'code' => 'SAHIWAL', 'sort_order' => 20],
            ['name_en' => 'Red Chittagong', 'name_bn' => 'রেড চিটাগাং', 'slug' => 'red-chittagong', 'code' => 'RCC', 'sort_order' => 30],
        ],
        'goat' => [
            ['name_en' => 'Black Bengal', 'name_bn' => 'ব্ল্যাক বেঙ্গল', 'slug' => 'black-bengal', 'code' => 'BBG', 'sort_order' => 10],
            ['name_en' => 'Jamunapari', 'name_bn' => 'যমুনাপাড়ি', 'slug' => 'jamunapari', 'code' => 'JAMUNA', 'sort_order' => 20],
        ],
        'fish' => [
            ['name_en' => 'Rohu', 'name_bn' => 'রুই', 'slug' => 'rohu', 'code' => 'ROHU', 'sort_order' => 10],
            ['name_en' => 'Catla', 'name_bn' => 'কাতলা', 'slug' => 'catla', 'code' => 'CATLA', 'sort_order' => 20],
            ['name_en' => 'Mrigal', 'name_bn' => 'মৃগেল', 'slug' => 'mrigal', 'code' => 'MRIGAL', 'sort_order' => 30],
            ['name_en' => 'Tilapia', 'name_bn' => 'তেলাপিয়া', 'slug' => 'tilapia', 'code' => 'TILAPIA', 'sort_order' => 40],
            ['name_en' => 'Pangasius', 'name_bn' => 'পাঙ্গাস', 'slug' => 'pangasius', 'code' => 'PANGAS', 'sort_order' => 50],
        ],
    ];

    public function run(): void
    {
        foreach (self::VARIETIES as $categorySlug => $varieties) {
            $category = FarmCategory::where('slug', $categorySlug)->first();

            if (! $category) {
                throw new RuntimeException("Required farm category slug [{$categorySlug}] is missing. Run FarmCategorySeeder before FarmVarietySeeder.");
            }

            foreach ($varieties as $variety) {
                FarmVariety::updateOrCreate(
                    ['slug' => $variety['slug']],
                    [
                        'farm_category_id' => $category->id,
                        'name_en' => $variety['name_en'],
                        'name_bn' => $variety['name_bn'],
                        'code' => $variety['code'],
                        'description_en' => null,
                        'description_bn' => null,
                        'sort_order' => $variety['sort_order'],
                        'is_active' => true,
                        'created_by' => null,
                    ]
                );
            }
        }
    }
}
