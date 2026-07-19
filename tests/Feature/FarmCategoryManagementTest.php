<?php

namespace Tests\Feature;

use App\Models\FarmCategory;
use App\Models\User;
use Database\Seeders\FarmCategorySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('farm-categories.index'))->assertRedirect(route('login'));
    }

    public function test_user_with_view_permission_can_view_categories(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.view');

        $this->actingAs($user)->get(route('farm-categories.index'))->assertOk();
    }

    public function test_user_without_permission_receives_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('farm-categories.index'))->assertForbidden();
    }

    public function test_user_with_manage_permission_can_access_create_page(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');

        $this->actingAs($user)->get(route('farm-categories.create'))->assertOk();
    }

    public function test_authorized_user_can_create_top_level_category(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'name' => 'Horticulture',
            'slug' => 'horticulture',
            'sort_order' => 4,
            'is_active' => '1',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'name' => 'Horticulture',
            'slug' => 'horticulture',
            'parent_id' => null,
            'created_by' => $user->id,
        ]);
    }

    public function test_authorized_user_can_create_child_category(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');
        $parent = FarmCategory::factory()->create(['name' => 'Poultry', 'slug' => 'poultry']);

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'parent_id' => $parent->id,
            'name' => 'Broiler',
            'slug' => 'broiler',
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'name' => 'Broiler',
            'slug' => 'broiler',
            'parent_id' => $parent->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_slug_is_generated_when_omitted(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'name' => 'Fish Farming',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', ['slug' => 'fish-farming']);
    }

    public function test_slug_must_be_unique(): void
    {
        FarmCategory::factory()->create(['slug' => 'duplicate']);
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'name' => 'Duplicate',
            'slug' => 'duplicate',
        ])->assertSessionHasErrors('slug');
    }

    public function test_category_cannot_be_its_own_parent(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');
        $category = FarmCategory::factory()->create();

        $this->actingAs($user)->put(route('farm-categories.update', $category), [
            'parent_id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_child_category_cannot_be_used_as_another_category_parent(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');
        $parent = FarmCategory::factory()->create();
        $child = FarmCategory::factory()->create(['parent_id' => $parent->id]);

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'parent_id' => $child->id,
            'name' => 'Grandchild',
            'slug' => 'grandchild',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_more_than_two_category_levels_cannot_be_created(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');
        $parent = FarmCategory::factory()->create();
        $child = FarmCategory::factory()->create(['parent_id' => $parent->id]);

        $this->actingAs($user)->post(route('farm-categories.store'), [
            'parent_id' => $child->id,
            'name' => 'Third Level',
            'slug' => 'third-level',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_authorized_user_can_update_a_category(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.manage');
        $category = FarmCategory::factory()->create(['slug' => 'old']);

        $this->actingAs($user)->put(route('farm-categories.update', $category), [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'sort_order' => 9,
            'is_active' => '0',
        ])->assertRedirect(route('farm-categories.index'));

        $this->assertDatabaseHas('farm_categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'sort_order' => 9,
            'is_active' => false,
        ]);
    }

    public function test_user_without_manage_permission_cannot_create_or_update(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.view');
        $category = FarmCategory::factory()->create();

        $this->actingAs($user)->get(route('farm-categories.create'))->assertForbidden();
        $this->actingAs($user)->post(route('farm-categories.store'), ['name' => 'Blocked'])->assertForbidden();
        $this->actingAs($user)->put(route('farm-categories.update', $category), [
            'name' => 'Blocked',
            'slug' => $category->slug,
        ])->assertForbidden();
    }

    public function test_search_works_by_name_or_slug(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.view');
        FarmCategory::factory()->create(['name' => 'Poultry', 'slug' => 'poultry']);
        FarmCategory::factory()->create(['name' => 'Livestock', 'slug' => 'livestock']);

        $this->actingAs($user)->get(route('farm-categories.index', ['search' => 'Poultry']))
            ->assertSee('Poultry')
            ->assertSee('poultry');

        $this->actingAs($user)->get(route('farm-categories.index', ['search' => 'livestock']))
            ->assertSee('Livestock')
            ->assertSee('livestock');
    }

    public function test_parent_filter_and_status_filter_work(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.view');
        $parent = FarmCategory::factory()->create(['name' => 'Poultry']);
        FarmCategory::factory()->create(['parent_id' => $parent->id, 'name' => 'Broiler', 'is_active' => true]);
        FarmCategory::factory()->create(['name' => 'Inactive Category', 'is_active' => false]);

        $this->actingAs($user)->get(route('farm-categories.index', ['parent_id' => $parent->id]))
            ->assertSee('Broiler')
            ->assertSee('Poultry');

        $this->actingAs($user)->get(route('farm-categories.index', ['status' => 'inactive']))
            ->assertSee('Inactive Category')
            ->assertSee('Inactive');
    }

    public function test_children_count_is_available(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farm-categories.view');
        $parent = FarmCategory::factory()->create(['name' => 'Poultry']);
        FarmCategory::factory()->count(2)->create(['parent_id' => $parent->id]);

        $this->actingAs($user)->get(route('farm-categories.index'))
            ->assertSee('Poultry')
            ->assertSee('2');
    }

    public function test_worker_can_view_but_cannot_manage_categories(): void
    {
        $worker = User::factory()->create();
        $worker->syncRoles('worker');

        $this->actingAs($worker)->get(route('farm-categories.index'))->assertOk();
        $this->actingAs($worker)->get(route('farm-categories.create'))->assertForbidden();
    }

    public function test_farm_category_seeder_creates_defaults_idempotently(): void
    {
        $this->seed(FarmCategorySeeder::class);
        $this->seed(FarmCategorySeeder::class);

        $this->assertSame(16, FarmCategory::count());
        $this->assertDatabaseHas('farm_categories', ['name' => 'Poultry', 'slug' => 'poultry', 'parent_id' => null]);
        $this->assertDatabaseHas('farm_categories', ['name' => 'Livestock', 'slug' => 'livestock', 'parent_id' => null]);
        $this->assertDatabaseHas('farm_categories', ['name' => 'Aquaculture', 'slug' => 'aquaculture', 'parent_id' => null]);

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
}
