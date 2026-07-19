<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_sees_admin_navigation(): void
    {
        $user = User::factory()->create();
        $user->syncRoles('admin');

        $this->actingAs($user)->get(route('dashboard'))
            ->assertSee('Users')
            ->assertSee('Settings')
            ->assertSee('Reports');
    }

    public function test_manager_sees_manager_authorized_navigation(): void
    {
        $user = User::factory()->create();
        $user->syncRoles('manager');

        $this->actingAs($user)->get(route('dashboard'))
            ->assertDontSee('Users')
            ->assertDontSee('Settings')
            ->assertSee('Farms')
            ->assertSee('Reports');
    }

    public function test_worker_sees_worker_authorized_navigation(): void
    {
        $user = User::factory()->create();
        $user->syncRoles('worker');

        $this->actingAs($user)->get(route('dashboard'))
            ->assertDontSee('Users')
            ->assertDontSee('Sales')
            ->assertSee('Batches')
            ->assertSee('Daily records')
            ->assertSee('Feed');
    }
}
