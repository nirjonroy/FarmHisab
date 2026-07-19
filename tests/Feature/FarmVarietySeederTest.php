<?php

namespace Tests\Feature;

use App\Models\FarmCategory;
use App\Models\FarmVariety;
use Database\Seeders\FarmCategorySeeder;
use Database\Seeders\FarmVarietySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use RuntimeException;
use Tests\TestCase;

class FarmVarietySeederTest extends TestCase
{
    use RefreshDatabase;

    private const EXPECTED = [
        'broiler' => [
            ['name_en' => 'Cobb 500', 'name_bn' => 'কব ৫০০', 'slug' => 'cobb-500', 'code' => 'COBB500'],
            ['name_en' => 'Ross 308', 'name_bn' => 'রস ৩০৮', 'slug' => 'ross-308', 'code' => 'ROSS308'],
        ],
        'duck' => [
            ['name_en' => 'Khaki Campbell', 'name_bn' => 'খাকি ক্যাম্পবেল', 'slug' => 'khaki-campbell', 'code' => 'KHAKI'],
            ['name_en' => 'Pekin', 'name_bn' => 'পেকিন', 'slug' => 'pekin-duck', 'code' => 'PEKIN'],
            ['name_en' => 'Jinding', 'name_bn' => 'জিনডিং', 'slug' => 'jinding-duck', 'code' => 'JINDING'],
        ],
        'cattle' => [
            ['name_en' => 'Holstein Friesian', 'name_bn' => 'হলস্টেইন ফ্রিজিয়ান', 'slug' => 'holstein-friesian', 'code' => 'HF'],
            ['name_en' => 'Sahiwal', 'name_bn' => 'সাহিওয়াল', 'slug' => 'sahiwal', 'code' => 'SAHIWAL'],
            ['name_en' => 'Red Chittagong', 'name_bn' => 'রেড চিটাগাং', 'slug' => 'red-chittagong', 'code' => 'RCC'],
        ],
        'goat' => [
            ['name_en' => 'Black Bengal', 'name_bn' => 'ব্ল্যাক বেঙ্গল', 'slug' => 'black-bengal', 'code' => 'BBG'],
            ['name_en' => 'Jamunapari', 'name_bn' => 'যমুনাপাড়ি', 'slug' => 'jamunapari', 'code' => 'JAMUNA'],
        ],
        'fish' => [
            ['name_en' => 'Rohu', 'name_bn' => 'রুই', 'slug' => 'rohu', 'code' => 'ROHU'],
            ['name_en' => 'Catla', 'name_bn' => 'কাতলা', 'slug' => 'catla', 'code' => 'CATLA'],
            ['name_en' => 'Mrigal', 'name_bn' => 'মৃগেল', 'slug' => 'mrigal', 'code' => 'MRIGAL'],
            ['name_en' => 'Tilapia', 'name_bn' => 'তেলাপিয়া', 'slug' => 'tilapia', 'code' => 'TILAPIA'],
            ['name_en' => 'Pangasius', 'name_bn' => 'পাঙ্গাস', 'slug' => 'pangasius', 'code' => 'PANGAS'],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FarmCategorySeeder::class);
    }

    public function test_farm_variety_seeder_creates_all_expected_varieties(): void
    {
        $this->seed(FarmVarietySeeder::class);

        $this->assertSame(15, FarmVariety::count());

        foreach (self::EXPECTED as $categorySlug => $varieties) {
            $category = FarmCategory::where('slug', $categorySlug)->firstOrFail();

            foreach ($varieties as $expected) {
                $this->assertDatabaseHas('farm_varieties', [
                    'farm_category_id' => $category->id,
                    'name_en' => $expected['name_en'],
                    'name_bn' => $expected['name_bn'],
                    'slug' => $expected['slug'],
                    'code' => $expected['code'],
                    'is_active' => true,
                    'created_by' => null,
                ]);
            }
        }
    }

    public function test_repeated_seeding_does_not_create_duplicates(): void
    {
        $this->seed(FarmVarietySeeder::class);
        $this->seed(FarmVarietySeeder::class);

        $this->assertSame(15, FarmVariety::count());
        $this->assertSame(15, FarmVariety::distinct('slug')->count('slug'));
    }

    public function test_manually_created_varieties_are_not_deleted(): void
    {
        $sonali = FarmCategory::where('slug', 'sonali')->firstOrFail();

        FarmVariety::create([
            'farm_category_id' => $sonali->id,
            'name_en' => 'Manual Sonali Variety',
            'name_bn' => 'ম্যানুয়াল সোনালি জাত',
            'slug' => 'manual-sonali-variety',
            'code' => 'MANUAL',
            'is_active' => true,
        ]);

        $this->seed(FarmVarietySeeder::class);

        $this->assertSame(16, FarmVariety::count());
        $this->assertDatabaseHas('farm_varieties', [
            'farm_category_id' => $sonali->id,
            'slug' => 'manual-sonali-variety',
            'code' => 'MANUAL',
        ]);
    }

    public function test_seeder_throws_clear_error_when_required_category_is_missing(): void
    {
        FarmCategory::where('slug', 'fish')->delete();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Required farm category slug [fish] is missing.');

        $this->seed(FarmVarietySeeder::class);
    }

    public function test_locale_specific_display_names_are_available_for_seeded_varieties(): void
    {
        $this->seed(FarmVarietySeeder::class);

        $tilapia = FarmVariety::where('slug', 'tilapia')->firstOrFail();

        App::setLocale('bn');
        $this->assertSame('তেলাপিয়া', $tilapia->display_name);

        App::setLocale('en');
        $this->assertSame('Tilapia', $tilapia->display_name);
    }
}
