<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('admin');

        $this->actingAs($user)->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('Manage FarmHisab Settings')
            ->assertSee('Business Name')
            ->assertSee('Low Stock Alerts');
    }

    public function test_worker_cannot_access_settings(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('worker');

        $this->actingAs($user)->get(route('settings.edit'))->assertForbidden();
    }

    public function test_admin_can_update_settings_and_app_name_is_dynamic(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('admin');

        $payload = [
            'app_name' => 'Poultry Ledger',
            'business_name' => 'Nirjon Poultry Farm',
            'owner_name' => 'Nirjon Roy',
            'phone' => '01700000000',
            'email' => 'owner@example.com',
            'address' => 'Sylhet',
            'default_locale' => 'en',
            'timezone' => 'Asia/Dhaka',
            'currency_code' => 'bdt',
            'currency_symbol' => 'Tk',
            'fiscal_year_start_month' => 'January',
            'low_stock_alert_enabled' => '1',
            'due_alert_enabled' => '0',
        ];

        $this->actingAs($user)->put(route('settings.update'), $payload)
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('settings', [
            'key' => 'app_name',
            'value' => 'Poultry Ledger',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'currency_code',
            'value' => 'BDT',
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Poultry Ledger');
    }

    public function test_invalid_settings_are_rejected(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('admin');

        $this->actingAs($user)->from(route('settings.edit'))->put(route('settings.update'), [
            'app_name' => '',
            'business_name' => '',
            'default_locale' => 'fr',
            'timezone' => '',
            'currency_code' => 'BDTK',
            'currency_symbol' => '',
            'fiscal_year_start_month' => 'NotAMonth',
        ])->assertRedirect(route('settings.edit'))
            ->assertSessionHasErrors([
                'app_name',
                'business_name',
                'default_locale',
                'timezone',
                'currency_code',
                'currency_symbol',
                'fiscal_year_start_month',
            ]);

        $this->assertSame(0, Setting::count());
    }
}
