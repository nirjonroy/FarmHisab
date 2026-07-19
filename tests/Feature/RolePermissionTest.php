<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AccessControl;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_roles_receive_expected_permissions(): void
    {
        foreach (AccessControl::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::findByName($roleName);

            $this->assertEqualsCanonicalizing($permissions, $role->permissions->pluck('name')->all());
        }
    }

    public function test_admin_can_access_user_management_but_manager_and_worker_cannot(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles('admin');
        $manager = User::factory()->create();
        $manager->syncRoles('manager');
        $worker = User::factory()->create();
        $worker->syncRoles('worker');

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($manager)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($worker)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_permission_middleware_allows_users_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('users.view');

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk();
    }
}
