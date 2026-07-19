<?php

namespace Tests\Feature;

use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class FarmVarietyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('farm-varieties.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_varieties(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-varieties.view');

        $this->actingAs($user)->get(route('farm-varieties.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('farm-varieties.index'))->assertForbidden();
    }

    public function test_manager_can_create_and_edit_a_variety(): void
    {
        $manager = $this->manager();
        $category = $this->childCategory();

        $this->actingAs($manager)->get(route('farm-varieties.create'))->assertOk();

        $this->actingAs($manager)->post(route('farm-varieties.store'), [
            'farm_category_id' => $category->id,
            'name_en' => 'Cobb 500',
            'name_bn' => 'কব ৫০০',
            'slug' => 'cobb-500',
            'code' => 'COBB500',
            'is_active' => '1',
        ])->assertRedirect(route('farm-varieties.index'));

        $variety = FarmVariety::where('slug', 'cobb-500')->firstOrFail();

        $this->actingAs($manager)->get(route('farm-varieties.edit', $variety))->assertOk();

        $this->actingAs($manager)->put(route('farm-varieties.update', $variety), [
            'farm_category_id' => $category->id,
            'name_en' => 'Ross 308',
            'name_bn' => 'রস ৩০৮',
            'slug' => 'ross-308',
            'code' => 'ROSS308',
            'is_active' => '0',
        ])->assertRedirect(route('farm-varieties.index'));

        $this->assertDatabaseHas('farm_varieties', [
            'id' => $variety->id,
            'name_en' => 'Ross 308',
            'name_bn' => 'রস ৩০৮',
            'slug' => 'ross-308',
            'code' => 'ROSS308',
            'is_active' => false,
        ]);
    }

    public function test_worker_can_view_but_cannot_manage(): void
    {
        $worker = User::factory()->create();
        $worker->syncRoles('worker');
        $variety = FarmVariety::factory()->create(['farm_category_id' => $this->childCategory()->id]);

        $this->actingAs($worker)->get(route('farm-varieties.index'))->assertOk();
        $this->actingAs($worker)->get(route('farm-varieties.create'))->assertForbidden();
        $this->actingAs($worker)->put(route('farm-varieties.update', $variety), [
            'farm_category_id' => $variety->farm_category_id,
            'name_en' => 'Blocked',
            'slug' => $variety->slug,
        ])->assertForbidden();
    }

    public function test_variety_can_be_created_with_both_language_names(): void
    {
        $category = $this->childCategory();

        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $category->id,
            'name_en' => 'Khaki Campbell',
            'name_bn' => 'খাকি ক্যাম্পবেল',
            'slug' => 'khaki-campbell',
        ])->assertRedirect(route('farm-varieties.index'));

        $this->assertDatabaseHas('farm_varieties', [
            'farm_category_id' => $category->id,
            'name_en' => 'Khaki Campbell',
            'name_bn' => 'খাকি ক্যাম্পবেল',
        ]);
    }

    public function test_variety_can_be_created_with_only_one_language_name(): void
    {
        $category = $this->childCategory();

        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $category->id,
            'name_bn' => 'রুই',
        ])->assertRedirect(route('farm-varieties.index'));

        $this->assertDatabaseHas('farm_varieties', [
            'farm_category_id' => $category->id,
            'name_en' => null,
            'name_bn' => 'রুই',
            'slug' => 'rui',
        ]);
    }

    public function test_variety_cannot_be_created_without_any_name(): void
    {
        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $this->childCategory()->id,
        ])->assertSessionHasErrors(['name_en', 'name_bn']);
    }

    public function test_variety_must_belong_to_a_child_category(): void
    {
        $topLevel = FarmCategory::factory()->create(['parent_id' => null]);

        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $topLevel->id,
            'name_en' => 'Invalid',
        ])->assertSessionHasErrors('farm_category_id');
    }

    public function test_top_level_category_cannot_be_selected_on_create_page(): void
    {
        $topLevel = FarmCategory::factory()->create(['name_en' => 'Poultry', 'parent_id' => null]);
        $child = FarmCategory::factory()->create(['name_en' => 'Broiler', 'parent_id' => $topLevel->id]);

        $response = $this->actingAs($this->manager())
            ->get(route('farm-varieties.create'))
            ->assertOk()
            ->assertSee('Poultry')
            ->assertSee('Broiler');

        $html = $response->getContent();

        $this->assertStringNotContainsString('<option value="'.$topLevel->id.'"', $html);
        $this->assertStringContainsString('<option value="'.$child->id.'"', $html);
    }

    public function test_slug_is_generated_automatically(): void
    {
        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $this->childCategory()->id,
            'name_en' => 'Black Bengal',
        ])->assertRedirect(route('farm-varieties.index'));

        $this->assertDatabaseHas('farm_varieties', ['slug' => 'black-bengal']);
    }

    public function test_slug_must_be_unique(): void
    {
        FarmVariety::factory()->create(['slug' => 'duplicate', 'farm_category_id' => $this->childCategory()->id]);

        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $this->childCategory()->id,
            'name_en' => 'Duplicate',
            'slug' => 'duplicate',
        ])->assertSessionHasErrors('slug');
    }

    public function test_code_must_be_unique_inside_the_same_category(): void
    {
        $category = $this->childCategory();
        FarmVariety::factory()->create(['farm_category_id' => $category->id, 'code' => 'C500']);

        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $category->id,
            'name_en' => 'Cobb 500',
            'code' => 'C500',
        ])->assertSessionHasErrors('code');
    }

    public function test_same_code_can_exist_under_different_categories(): void
    {
        $first = $this->childCategory();
        $second = $this->childCategory();
        FarmVariety::factory()->create(['farm_category_id' => $first->id, 'code' => 'C500']);

        $this->actingAs($this->manager())->post(route('farm-varieties.store'), [
            'farm_category_id' => $second->id,
            'name_en' => 'Cobb 500',
            'code' => 'C500',
        ])->assertRedirect(route('farm-varieties.index'));

        $this->assertSame(2, FarmVariety::where('code', 'C500')->count());
    }

    public function test_locale_specific_display_names_work(): void
    {
        $variety = FarmVariety::factory()->create([
            'farm_category_id' => $this->childCategory()->id,
            'name_en' => 'Tilapia',
            'name_bn' => 'তেলাপিয়া',
        ]);

        App::setLocale('bn');
        $this->assertSame('তেলাপিয়া', $variety->display_name);

        App::setLocale('en');
        $this->assertSame('Tilapia', $variety->display_name);
    }

    public function test_search_works_with_bengali_and_english_names(): void
    {
        $user = $this->viewer('bn');
        FarmVariety::factory()->create([
            'farm_category_id' => $this->childCategory()->id,
            'name_en' => 'Tilapia',
            'name_bn' => 'তেলাপিয়া',
        ]);

        $this->actingAs($user)
            ->get(route('farm-varieties.index', ['search' => 'তেলাপিয়া']))
            ->assertOk()
            ->assertSee('তেলাপিয়া')
            ->assertSee('Tilapia');

        $this->actingAs($this->viewer('en'))
            ->get(route('farm-varieties.index', ['search' => 'Tilapia']))
            ->assertOk()
            ->assertSee('Tilapia')
            ->assertSee('তেলাপিয়া');
    }

    public function test_category_parent_and_status_filters_work(): void
    {
        $user = $this->viewer('en');
        $parent = FarmCategory::factory()->create(['name_en' => 'Poultry']);
        $broiler = FarmCategory::factory()->create(['name_en' => 'Broiler', 'parent_id' => $parent->id]);
        $duck = FarmCategory::factory()->create(['name_en' => 'Duck', 'parent_id' => $parent->id]);
        $cobb = FarmVariety::factory()->create(['farm_category_id' => $broiler->id, 'name_en' => 'Cobb 500', 'code' => 'COBB']);
        FarmVariety::factory()->create(['farm_category_id' => $duck->id, 'name_en' => 'Pekin', 'code' => 'PEKIN']);
        $inactive = FarmVariety::factory()->create(['farm_category_id' => $broiler->id, 'name_en' => 'Inactive Variety', 'code' => 'INACTIVE', 'is_active' => false]);

        $this->actingAs($user)
            ->get(route('farm-varieties.index', ['farm_category_id' => $broiler->id]))
            ->assertOk()
            ->assertSee($cobb->slug)
            ->assertSee($inactive->slug)
            ->assertDontSee('PEKIN');

        $this->actingAs($user)
            ->get(route('farm-varieties.index', ['parent_id' => $parent->id]))
            ->assertOk()
            ->assertSee('COBB')
            ->assertSee('PEKIN');

        $this->actingAs($user)
            ->get(route('farm-varieties.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('INACTIVE')
            ->assertDontSee('COBB');
    }

    public function test_created_by_stores_authenticated_user_id(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)->post(route('farm-varieties.store'), [
            'farm_category_id' => $this->childCategory()->id,
            'name_en' => 'Holstein Friesian',
        ])->assertRedirect(route('farm-varieties.index'));

        $this->assertDatabaseHas('farm_varieties', [
            'name_en' => 'Holstein Friesian',
            'created_by' => $manager->id,
        ]);
    }

    private function childCategory(): FarmCategory
    {
        $parent = FarmCategory::factory()->create(['parent_id' => null]);

        return FarmCategory::factory()->create(['parent_id' => $parent->id]);
    }

    private function manager(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->syncRoles('manager');

        return $user;
    }

    private function viewer(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->givePermissionTo('farm-varieties.view');

        return $user;
    }
}
