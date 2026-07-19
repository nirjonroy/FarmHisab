<?php

namespace Tests\Feature;

use App\Models\FarmCategory;
use App\Models\User;
use Database\Seeders\FarmCategorySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class FarmCategoryBilingualTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_category_can_be_created_with_both_english_and_bengali_names(): void
    {
        $user = $this->manager();

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'name_en' => 'Layer',
            'name_bn' => 'লেয়ার',
            'description_en' => 'Egg production birds',
            'description_bn' => 'ডিম উৎপাদনের পাখি',
            'slug' => 'layer',
            'is_active' => '1',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'name' => 'Layer',
            'name_en' => 'Layer',
            'name_bn' => 'লেয়ার',
            'description' => 'Egg production birds',
            'description_en' => 'Egg production birds',
            'description_bn' => 'ডিম উৎপাদনের পাখি',
        ]);
    }

    public function test_category_can_be_created_with_only_english_name(): void
    {
        $this->actingAs($this->manager())->post(route('farm-categories.store'), [
            'name_en' => 'Quail',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'name' => 'Quail',
            'name_en' => 'Quail',
            'name_bn' => null,
            'slug' => 'quail',
        ]);
    }

    public function test_category_can_be_created_with_only_bengali_name(): void
    {
        $this->actingAs($this->manager())->post(route('farm-categories.store'), [
            'name_bn' => 'কবুতর',
            'slug' => 'kobutor',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'name' => 'কবুতর',
            'name_en' => null,
            'name_bn' => 'কবুতর',
            'slug' => 'kobutor',
        ]);
    }

    public function test_category_cannot_be_created_without_any_name(): void
    {
        $this->actingAs($this->manager())->post(route('farm-categories.store'), [
            'slug' => 'nameless',
        ])->assertSessionHasErrors(['name_en', 'name_bn']);
    }

    public function test_bengali_locale_uses_name_bn_as_display_name(): void
    {
        App::setLocale('bn');

        $category = FarmCategory::factory()->create([
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
        ]);

        $this->assertSame('পোল্ট্রি', $category->display_name);
    }

    public function test_english_locale_uses_name_en_as_display_name(): void
    {
        App::setLocale('en');

        $category = FarmCategory::factory()->create([
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
        ]);

        $this->assertSame('Poultry', $category->display_name);
    }

    public function test_display_name_fallback_works_when_one_language_is_missing(): void
    {
        App::setLocale('bn');

        $englishOnly = FarmCategory::factory()->create([
            'name' => 'Quail',
            'name_en' => 'Quail',
            'name_bn' => null,
        ]);

        $this->assertSame('Quail', $englishOnly->display_name);

        App::setLocale('en');

        $bengaliOnly = FarmCategory::factory()->create([
            'name' => 'কবুতর',
            'name_en' => null,
            'name_bn' => 'কবুতর',
        ]);

        $this->assertSame('কবুতর', $bengaliOnly->display_name);
    }

    public function test_search_works_using_bengali_name(): void
    {
        $user = $this->viewer('bn');
        FarmCategory::factory()->create([
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
            'slug' => 'poultry',
        ]);

        $this->actingAs($user)
            ->get(route('farm-categories.index', ['search' => 'পোল্ট্রি']))
            ->assertOk()
            ->assertSee('পোল্ট্রি')
            ->assertSee('Poultry');
    }

    public function test_search_works_using_english_name(): void
    {
        $user = $this->viewer('en');
        FarmCategory::factory()->create([
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
            'slug' => 'poultry',
        ]);

        $this->actingAs($user)
            ->get(route('farm-categories.index', ['search' => 'Poultry']))
            ->assertOk()
            ->assertSee('Poultry')
            ->assertSee('পোল্ট্রি');
    }

    public function test_parent_dropdown_uses_localized_display_name(): void
    {
        $user = $this->manager('bn');
        FarmCategory::factory()->create([
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
        ]);

        $this->actingAs($user)
            ->get(route('farm-categories.create'))
            ->assertOk()
            ->assertSee('পোল্ট্রি')
            ->assertDontSee('Poultry');
    }

    public function test_seeder_creates_bilingual_category_names_idempotently(): void
    {
        $this->seed(FarmCategorySeeder::class);
        $this->seed(FarmCategorySeeder::class);

        $this->assertSame(9, FarmCategory::count());
        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'poultry',
            'name' => 'Poultry',
            'name_en' => 'Poultry',
            'name_bn' => 'পোল্ট্রি',
            'parent_id' => null,
        ]);
        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'fish',
            'name_en' => 'Fish',
            'name_bn' => 'মাছ',
        ]);
    }

    public function test_existing_category_hierarchy_and_permissions_still_work(): void
    {
        $manager = $this->manager();
        $worker = $this->viewer();
        $parent = FarmCategory::factory()->create(['name_en' => 'Poultry']);

        $this->actingAs($manager)->post(route('farm-categories.store'), [
            'parent_id' => $parent->id,
            'name_en' => 'Broiler',
            'slug' => 'broiler',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'slug' => 'broiler',
            'parent_id' => $parent->id,
        ]);

        $this->actingAs($worker)
            ->get(route('farm-categories.create'))
            ->assertForbidden();
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
