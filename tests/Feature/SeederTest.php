<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AccessControl;
use Database\Seeders\DefaultAdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permission_seeding_is_idempotent(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->assertSame(count(AccessControl::ROLES), Role::count());
        $this->assertSame(count(AccessControl::PERMISSIONS), Permission::count());
    }

    public function test_default_admin_is_created_and_password_is_not_overwritten(): void
    {
        $this->seed(RolePermissionSeeder::class);
        putenv('DEFAULT_ADMIN_NAME=Default Admin');
        putenv('DEFAULT_ADMIN_EMAIL=default-admin@example.com');
        putenv('DEFAULT_ADMIN_PASSWORD=password-one');
        $_ENV['DEFAULT_ADMIN_NAME'] = 'Default Admin';
        $_ENV['DEFAULT_ADMIN_EMAIL'] = 'default-admin@example.com';
        $_ENV['DEFAULT_ADMIN_PASSWORD'] = 'password-one';

        $this->seed(DefaultAdminSeeder::class);
        $admin = User::where('email', 'default-admin@example.com')->firstOrFail();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue(Hash::check('password-one', $admin->password));

        putenv('DEFAULT_ADMIN_PASSWORD=password-two');
        $_ENV['DEFAULT_ADMIN_PASSWORD'] = 'password-two';

        $this->seed(DefaultAdminSeeder::class);
        $admin->refresh();

        $this->assertTrue(Hash::check('password-one', $admin->password));
        $this->assertFalse(Hash::check('password-two', $admin->password));
    }
}
