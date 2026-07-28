<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_manager_can_open_report_center_and_report_page(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('manager');

        $this->actingAs($user)->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Report Center')
            ->assertSee('Profit & Loss')
            ->assertSee('Cashbook & Due');

        $this->actingAs($user)->get(route('reports.show', 'feed-usage'))
            ->assertOk()
            ->assertSee('Feed Usage')
            ->assertSee('Generate');
    }

    public function test_report_sidebar_contains_dropdown_items(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('manager');

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('sidebar-group', false)
            ->assertSee(route('reports.index'), false)
            ->assertSee(route('reports.show', 'profit-loss'), false)
            ->assertSee(route('reports.show', 'inventory'), false);
    }

    public function test_worker_cannot_access_reports(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('worker');

        $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
    }
}
