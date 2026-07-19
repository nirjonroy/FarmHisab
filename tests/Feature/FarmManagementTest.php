<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('farms.index'))->assertRedirect(route('login'));
    }

    public function test_user_with_farms_view_can_view_farm_list(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');

        $this->actingAs($user)->get(route('farms.index'))->assertOk();
    }

    public function test_user_without_farms_view_receives_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('farms.index'))->assertForbidden();
    }

    public function test_user_with_farms_manage_can_view_create_page(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');

        $this->actingAs($user)->get(route('farms.create'))->assertOk();
    }

    public function test_authorized_user_can_create_a_farm_and_created_by_is_stored(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');

        $this->actingAs($user)->post(route('farms.store'), [
            'name' => 'North Farm',
            'code' => 'NF-001',
            'phone' => '01700000000',
            'district' => 'Dhaka',
            'upazila' => 'Savar',
            'union_name' => 'Aminbazar',
            'address' => 'Farm road',
            'description' => 'Layer farm',
            'is_active' => '1',
        ])->assertRedirect(route('farms.index'));

        $this->assertDatabaseHas('farms', [
            'name' => 'North Farm',
            'code' => 'NF-001',
            'created_by' => $user->id,
            'is_active' => true,
        ]);
    }

    public function test_farm_code_must_be_unique(): void
    {
        Farm::factory()->create(['code' => 'DUP-001']);
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');

        $this->actingAs($user)->post(route('farms.store'), [
            'name' => 'Duplicate Farm',
            'code' => 'DUP-001',
        ])->assertSessionHasErrors('code');
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');

        $this->actingAs($user)->post(route('farms.store'), [])
            ->assertSessionHasErrors(['name', 'code']);
    }

    public function test_authorized_user_can_update_a_farm(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');
        $farm = Farm::factory()->create(['code' => 'OLD-001']);

        $this->actingAs($user)->put(route('farms.update', $farm), [
            'name' => 'Updated Farm',
            'code' => 'NEW-001',
            'phone' => '01800000000',
            'district' => 'Gazipur',
            'upazila' => 'Kapasia',
            'is_active' => '0',
        ])->assertRedirect(route('farms.index'));

        $this->assertDatabaseHas('farms', [
            'id' => $farm->id,
            'name' => 'Updated Farm',
            'code' => 'NEW-001',
            'is_active' => false,
        ]);
    }

    public function test_user_without_farms_manage_cannot_create_or_update_farms(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        $farm = Farm::factory()->create();

        $this->actingAs($user)->get(route('farms.create'))->assertForbidden();
        $this->actingAs($user)->post(route('farms.store'), [
            'name' => 'Blocked',
            'code' => 'BLK-001',
        ])->assertForbidden();
        $this->actingAs($user)->put(route('farms.update', $farm), [
            'name' => 'Blocked Update',
            'code' => $farm->code,
        ])->assertForbidden();
    }

    public function test_search_can_find_farm_by_name_or_code(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        Farm::factory()->create(['name' => 'Alpha Poultry', 'code' => 'ALPHA']);
        Farm::factory()->create(['name' => 'Beta Poultry', 'code' => 'BETA']);

        $this->actingAs($user)->get(route('farms.index', ['search' => 'Alpha']))
            ->assertSee('Alpha Poultry')
            ->assertDontSee('Beta Poultry');

        $this->actingAs($user)->get(route('farms.index', ['search' => 'BETA']))
            ->assertSee('Beta Poultry')
            ->assertDontSee('Alpha Poultry');
    }

    public function test_farm_list_pagination_works(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        Farm::factory()->count(16)->create();

        $this->actingAs($user)->get(route('farms.index'))->assertSee('pagination');
    }
}
