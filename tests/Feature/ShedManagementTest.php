<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\Shed;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShedManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('sheds.index'))->assertRedirect(route('login'));
    }

    public function test_user_with_farms_view_can_see_shed_list(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');

        $this->actingAs($user)->get(route('sheds.index'))->assertOk();
    }

    public function test_user_without_farms_view_receives_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('sheds.index'))->assertForbidden();
    }

    public function test_user_with_farms_manage_can_access_create_page(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');

        $this->actingAs($user)->get(route('sheds.create'))->assertOk();
    }

    public function test_authorized_user_can_create_a_shed(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');
        $farm = Farm::factory()->create();

        $this->actingAs($user)->post(route('sheds.store'), [
            'farm_id' => $farm->id,
            'name' => 'Brooder Shed',
            'code' => 'S-001',
            'capacity' => 1200,
            'description' => 'Starter shed',
            'is_active' => '1',
        ])->assertRedirect(route('sheds.index'));

        $this->assertDatabaseHas('sheds', [
            'farm_id' => $farm->id,
            'name' => 'Brooder Shed',
            'code' => 'S-001',
            'capacity' => 1200,
            'created_by' => $user->id,
            'is_active' => true,
        ]);
    }

    public function test_capacity_must_be_at_least_one(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');
        $farm = Farm::factory()->create();

        $this->actingAs($user)->post(route('sheds.store'), [
            'farm_id' => $farm->id,
            'name' => 'Invalid Shed',
            'code' => 'S-002',
            'capacity' => 0,
        ])->assertSessionHasErrors('capacity');
    }

    public function test_code_must_be_unique_inside_the_same_farm(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');
        $farm = Farm::factory()->create();
        Shed::factory()->create(['farm_id' => $farm->id, 'code' => 'A-001']);

        $this->actingAs($user)->post(route('sheds.store'), [
            'farm_id' => $farm->id,
            'name' => 'Duplicate Shed',
            'code' => 'A-001',
            'capacity' => 500,
        ])->assertSessionHasErrors('code');
    }

    public function test_same_code_can_be_used_in_different_farms(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');
        $farmOne = Farm::factory()->create();
        $farmTwo = Farm::factory()->create();
        Shed::factory()->create(['farm_id' => $farmOne->id, 'code' => 'A-001']);

        $this->actingAs($user)->post(route('sheds.store'), [
            'farm_id' => $farmTwo->id,
            'name' => 'Allowed Shed',
            'code' => 'A-001',
            'capacity' => 500,
        ])->assertRedirect(route('sheds.index'));

        $this->assertDatabaseHas('sheds', [
            'farm_id' => $farmTwo->id,
            'code' => 'A-001',
        ]);
    }

    public function test_authorized_user_can_update_a_shed(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.manage');
        $farm = Farm::factory()->create();
        $shed = Shed::factory()->create();

        $this->actingAs($user)->put(route('sheds.update', $shed), [
            'farm_id' => $farm->id,
            'name' => 'Updated Shed',
            'code' => 'UP-001',
            'capacity' => 2400,
            'description' => 'Updated description',
            'is_active' => '0',
        ])->assertRedirect(route('sheds.index'));

        $this->assertDatabaseHas('sheds', [
            'id' => $shed->id,
            'farm_id' => $farm->id,
            'name' => 'Updated Shed',
            'code' => 'UP-001',
            'capacity' => 2400,
            'is_active' => false,
        ]);
    }

    public function test_user_without_farms_manage_cannot_create_or_update_sheds(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        $farm = Farm::factory()->create();
        $shed = Shed::factory()->create();

        $this->actingAs($user)->get(route('sheds.create'))->assertForbidden();
        $this->actingAs($user)->post(route('sheds.store'), [
            'farm_id' => $farm->id,
            'name' => 'Blocked Shed',
            'code' => 'B-001',
            'capacity' => 100,
        ])->assertForbidden();
        $this->actingAs($user)->put(route('sheds.update', $shed), [
            'farm_id' => $shed->farm_id,
            'name' => 'Blocked Update',
            'code' => $shed->code,
            'capacity' => $shed->capacity,
        ])->assertForbidden();
    }

    public function test_search_works_by_shed_name_or_code(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        Shed::factory()->create(['name' => 'Alpha Shed', 'code' => 'ALPHA']);
        Shed::factory()->create(['name' => 'Beta Shed', 'code' => 'BETA']);

        $this->actingAs($user)->get(route('sheds.index', ['search' => 'Alpha']))
            ->assertSee('Alpha Shed')
            ->assertDontSee('Beta Shed');

        $this->actingAs($user)->get(route('sheds.index', ['search' => 'BETA']))
            ->assertSee('Beta Shed')
            ->assertDontSee('Alpha Shed');
    }

    public function test_farm_filter_works(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        $farmOne = Farm::factory()->create(['name' => 'North Farm']);
        $farmTwo = Farm::factory()->create(['name' => 'South Farm']);
        Shed::factory()->create(['farm_id' => $farmOne->id, 'name' => 'North Shed']);
        Shed::factory()->create(['farm_id' => $farmTwo->id, 'name' => 'South Shed']);

        $this->actingAs($user)->get(route('sheds.index', ['farm_id' => $farmOne->id]))
            ->assertSee('North Shed')
            ->assertDontSee('South Shed');
    }

    public function test_status_filter_works(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        Shed::factory()->create(['name' => 'Active Shed', 'is_active' => true]);
        Shed::factory()->create(['name' => 'Inactive Shed', 'is_active' => false]);

        $this->actingAs($user)->get(route('sheds.index', ['status' => 'inactive']))
            ->assertSee('Inactive Shed')
            ->assertDontSee('Active Shed');
    }

    public function test_farm_list_displays_its_shed_count(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('farms.view');
        $farm = Farm::factory()->create(['name' => 'Counted Farm']);
        Shed::factory()->count(2)->create(['farm_id' => $farm->id]);

        $this->actingAs($user)->get(route('farms.index'))
            ->assertSee('Counted Farm')
            ->assertSee('2');
    }
}
