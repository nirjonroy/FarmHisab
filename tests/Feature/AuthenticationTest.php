<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_registration_succeeds(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Worker',
            'email' => 'worker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(User::where('email', 'worker@example.com')->first()->hasRole('worker'));
    }

    public function test_login_succeeds_and_logout_succeeds(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $user->syncRoles('worker');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_invalid_credentials_fail(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'is_active' => false,
        ]);
        $user->syncRoles('worker');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
