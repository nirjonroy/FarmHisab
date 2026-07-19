<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_default_locale_is_bengali(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('লগইন');

        $this->assertSame('bn', App::getLocale());
    }

    public function test_user_can_switch_to_english(): void
    {
        $this->post(route('language.switch', 'en'))
            ->assertRedirect();

        $this->withSession(['locale' => 'en'])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Login');
    }

    public function test_user_can_switch_back_to_bengali(): void
    {
        $this->withSession(['locale' => 'en'])
            ->post(route('language.switch', 'bn'))
            ->assertRedirect();

        $this->withSession(['locale' => 'bn'])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('লগইন');
    }

    public function test_guest_locale_is_saved_in_session(): void
    {
        $this->post(route('language.switch', 'en'))
            ->assertSessionHas('locale', 'en');
    }

    public function test_authenticated_users_locale_is_saved_in_database(): void
    {
        $user = User::factory()->create(['locale' => 'bn']);

        $this->actingAs($user)
            ->post(route('language.switch', 'en'))
            ->assertSessionHas('locale', 'en');

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_authenticated_users_saved_locale_is_applied_on_next_request(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('worker');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Active batches');
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->post('/language/fr')->assertNotFound();
    }

    public function test_dashboard_displays_english_translation_after_switching_to_english(): void
    {
        $user = User::factory()->create(['locale' => 'bn']);
        $user->syncRoles('worker');

        $this->actingAs($user)->post(route('language.switch', 'en'));

        $this->actingAs($user->fresh())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Active batches');
    }

    public function test_dashboard_displays_bengali_translation_after_switching_to_bengali(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('worker');

        $this->actingAs($user)->post(route('language.switch', 'bn'));

        $this->actingAs($user->fresh())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('ড্যাশবোর্ড')
            ->assertSee('সক্রিয় ব্যাচ');
    }

    public function test_language_switch_route_requires_post(): void
    {
        $this->get('/language/en')->assertStatus(405);
    }

    public function test_existing_authentication_still_works(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'locale' => 'en',
        ]);
        $user->syncRoles('worker');

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }
}
