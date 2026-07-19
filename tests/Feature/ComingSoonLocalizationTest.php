<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComingSoonLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_bengali_locale_shows_bengali_module_name_instead_of_english(): void
    {
        $user = $this->worker('bn');

        $this->actingAs($user)
            ->get(route('coming-soon', 'batches'))
            ->assertOk()
            ->assertSee('ব্যাচ')
            ->assertDontSee('Batches');
    }

    public function test_english_locale_shows_english_module_name(): void
    {
        $user = $this->worker('en');

        $this->actingAs($user)
            ->get(route('coming-soon', 'batches'))
            ->assertOk()
            ->assertSee('Batches');
    }

    public function test_coming_soon_heading_is_localized(): void
    {
        $this->actingAs($this->worker('bn'))
            ->get(route('coming-soon', 'feed'))
            ->assertOk()
            ->assertSee('শীঘ্রই আসছে');

        $this->actingAs($this->worker('en'))
            ->get(route('coming-soon', 'feed'))
            ->assertOk()
            ->assertSee('Coming Soon');
    }

    public function test_breadcrumb_uses_translated_module_name(): void
    {
        $user = $this->worker('bn');

        $this->actingAs($user)
            ->get(route('coming-soon', 'daily-records'))
            ->assertOk()
            ->assertSee('ড্যাশবোর্ড')
            ->assertSee('দৈনিক রেকর্ড');
    }

    public function test_supported_coming_soon_module_returns_ok(): void
    {
        $this->actingAs($this->worker('en'))
            ->get(route('coming-soon', 'medicine-vaccines'))
            ->assertOk()
            ->assertSee('Medicine &amp; Vaccines', false);
    }

    public function test_unsupported_module_returns_404(): void
    {
        $this->actingAs($this->worker('en'))
            ->get(route('coming-soon', 'unknown-module'))
            ->assertNotFound();
    }

    public function test_raw_module_input_is_not_rendered_as_html(): void
    {
        $this->actingAs($this->worker('en'))
            ->get('/coming-soon/%3Cstrong%3E')
            ->assertNotFound()
            ->assertDontSee('<strong>', false);
    }

    public function test_existing_permission_middleware_still_works(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)
            ->get(route('coming-soon', 'batches'))
            ->assertForbidden();
    }

    public function test_sidebar_displays_localized_module_labels(): void
    {
        $this->actingAs($this->worker('bn'))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('ব্যাচ')
            ->assertSee('দৈনিক রেকর্ড')
            ->assertSee('ফিড');

        $this->actingAs($this->worker('en'))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Batches')
            ->assertSee('Daily Records')
            ->assertSee('Feed');
    }

    private function worker(string $locale): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->syncRoles('worker');

        return $user;
    }
}
