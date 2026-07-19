<?php

namespace Tests\Feature;

use App\Enums\CategoryActivityType;
use App\Models\FarmCategory;
use App\Models\User;
use Database\Seeders\FarmCategorySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmCategoryActivityTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_existing_seeded_categories_default_to_production(): void
    {
        $this->seed(FarmCategorySeeder::class);

        foreach (['poultry', 'livestock', 'aquaculture', 'broiler', 'sonali', 'duck', 'cattle', 'goat', 'fish'] as $slug) {
            $this->assertDatabaseHas('farm_categories', [
                'slug' => $slug,
                'activity_type' => CategoryActivityType::PRODUCTION,
            ]);
        }
    }

    public function test_category_can_be_created_as_production(): void
    {
        $this->createCategory('Greenhouse', 'greenhouse', CategoryActivityType::PRODUCTION)
            ->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'greenhouse',
            'activity_type' => CategoryActivityType::PRODUCTION,
        ]);
    }

    public function test_category_can_be_created_as_trading(): void
    {
        $this->createCategory('Fertilizer Trading', 'fertilizer-trading', CategoryActivityType::TRADING)
            ->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'fertilizer-trading',
            'activity_type' => CategoryActivityType::TRADING,
        ]);
    }

    public function test_category_can_be_created_as_hybrid(): void
    {
        $this->createCategory('Bamboo Yard', 'bamboo-yard', CategoryActivityType::HYBRID)
            ->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'bamboo-yard',
            'activity_type' => CategoryActivityType::HYBRID,
        ]);
    }

    public function test_invalid_activity_type_is_rejected(): void
    {
        $this->actingAs($this->manager())->post(route('farm-categories.store'), [
            'name_en' => 'Invalid Activity',
            'slug' => 'invalid-activity',
            'activity_type' => 'invalid',
        ])->assertSessionHasErrors('activity_type');
    }

    public function test_activity_type_is_cast_to_enum(): void
    {
        $category = FarmCategory::factory()->create([
            'activity_type' => CategoryActivityType::TRADING,
        ]);

        $this->assertInstanceOf(CategoryActivityType::class, $category->fresh()->activity_type);
        $this->assertSame(CategoryActivityType::TRADING, $category->fresh()->activity_type->value);
    }

    public function test_activity_type_filter_works(): void
    {
        $viewer = $this->viewer();

        FarmCategory::factory()->create(['name' => 'Production Category', 'slug' => 'production-category', 'activity_type' => CategoryActivityType::PRODUCTION]);
        FarmCategory::factory()->create(['name' => 'Trading Category', 'slug' => 'trading-category', 'activity_type' => CategoryActivityType::TRADING]);
        FarmCategory::factory()->create(['name' => 'Hybrid Category', 'slug' => 'hybrid-category', 'activity_type' => CategoryActivityType::HYBRID]);

        $this->actingAs($viewer)
            ->get(route('farm-categories.index', ['activity_type' => CategoryActivityType::TRADING]))
            ->assertOk()
            ->assertSee('trading-category')
            ->assertDontSee('production-category')
            ->assertDontSee('hybrid-category');
    }

    public function test_bengali_activity_labels_display_correctly(): void
    {
        FarmCategory::factory()->create(['name_bn' => 'সার', 'activity_type' => CategoryActivityType::TRADING]);

        $this->actingAs($this->viewer('bn'))
            ->get(route('farm-categories.index'))
            ->assertOk()
            ->assertSee('কেনাবেচা');
    }

    public function test_english_activity_labels_display_correctly(): void
    {
        FarmCategory::factory()->create(['name_en' => 'Bamboo', 'activity_type' => CategoryActivityType::HYBRID]);

        $this->actingAs($this->viewer('en'))
            ->get(route('farm-categories.index'))
            ->assertOk()
            ->assertSee('Production &amp; Trading', false);
    }

    public function test_seeder_creates_new_business_categories_with_correct_hierarchy_and_types(): void
    {
        $this->seed(FarmCategorySeeder::class);
        $this->seed(FarmCategorySeeder::class);

        $this->assertSame(16, FarmCategory::count());

        $crop = FarmCategory::where('slug', 'crop-production')->firstOrFail();
        $inputs = FarmCategory::where('slug', 'agricultural-inputs')->firstOrFail();
        $forestry = FarmCategory::where('slug', 'forestry-natural-products')->firstOrFail();

        $this->assertSame(CategoryActivityType::PRODUCTION, $crop->activity_type->value);
        $this->assertSame(CategoryActivityType::TRADING, $inputs->activity_type->value);
        $this->assertSame(CategoryActivityType::HYBRID, $forestry->activity_type->value);

        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'paddy-cultivation',
            'parent_id' => $crop->id,
            'activity_type' => CategoryActivityType::PRODUCTION,
        ]);
        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'fertilizer',
            'parent_id' => $inputs->id,
            'activity_type' => CategoryActivityType::TRADING,
        ]);
        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'seed',
            'parent_id' => $inputs->id,
            'activity_type' => CategoryActivityType::TRADING,
        ]);
        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'bamboo',
            'parent_id' => $forestry->id,
            'activity_type' => CategoryActivityType::HYBRID,
        ]);
    }

    public function test_existing_categories_and_hierarchy_remain_unchanged(): void
    {
        $this->seed(FarmCategorySeeder::class);

        $poultry = FarmCategory::where('slug', 'poultry')->firstOrFail();
        $livestock = FarmCategory::where('slug', 'livestock')->firstOrFail();
        $aquaculture = FarmCategory::where('slug', 'aquaculture')->firstOrFail();

        $this->assertDatabaseHas('farm_categories', ['slug' => 'broiler', 'parent_id' => $poultry->id]);
        $this->assertDatabaseHas('farm_categories', ['slug' => 'sonali', 'parent_id' => $poultry->id]);
        $this->assertDatabaseHas('farm_categories', ['slug' => 'duck', 'parent_id' => $poultry->id]);
        $this->assertDatabaseHas('farm_categories', ['slug' => 'cattle', 'parent_id' => $livestock->id]);
        $this->assertDatabaseHas('farm_categories', ['slug' => 'goat', 'parent_id' => $livestock->id]);
        $this->assertDatabaseHas('farm_categories', ['slug' => 'fish', 'parent_id' => $aquaculture->id]);
    }

    private function createCategory(string $name, string $slug, string $activityType)
    {
        return $this->actingAs($this->manager())->post(route('farm-categories.store'), [
            'name_en' => $name,
            'slug' => $slug,
            'activity_type' => $activityType,
            'is_active' => '1',
        ]);
    }

    private function manager(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->givePermissionTo('farm-categories.view', 'farm-categories.manage');

        return $user;
    }

    private function viewer(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->givePermissionTo('farm-categories.view');

        return $user;
    }
}
