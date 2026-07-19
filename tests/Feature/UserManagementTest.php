<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create(['email' => 'admin@example.com']);
        $this->admin->syncRoles('admin');
    }

    public function test_authorized_user_can_list_create_and_update_users(): void
    {
        $this->actingAs($this->admin)->get(route('admin.users.index'))->assertOk();

        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'manager',
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'manager@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('manager'));

        $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'name' => 'Worker User',
            'email' => 'worker@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'role' => 'worker',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Worker User', $user->name);
        $this->assertSame('worker@example.com', $user->email);
        $this->assertTrue(Hash::check('newpassword', $user->password));
        $this->assertTrue($user->hasRole('worker'));
        $this->assertFalse($user->hasRole('manager'));
    }

    public function test_authorized_user_can_deactivate_another_user(): void
    {
        $user = User::factory()->create();
        $user->syncRoles('worker');

        $this->actingAs($this->admin)
            ->patch(route('admin.users.toggle-status', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_unauthorized_user_cannot_manage_users(): void
    {
        $worker = User::factory()->create();
        $worker->syncRoles('worker');

        $this->actingAs($worker)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_user_cannot_deactivate_or_delete_themselves(): void
    {
        $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->admin))->assertForbidden();
        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin))->assertForbidden();
    }

    public function test_last_active_admin_cannot_be_deleted_deactivated_or_lose_admin_role(): void
    {
        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin))->assertForbidden();
        $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->admin))->assertForbidden();

        $otherAdmin = User::factory()->create();
        $otherAdmin->syncRoles('admin');
        $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $otherAdmin));

        $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => 'manager',
        ])->assertSessionHasErrors('role');
    }

    public function test_duplicate_email_validation_works(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Duplicate',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'worker',
        ])->assertSessionHasErrors('email');
    }
}
