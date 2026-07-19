<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\Shed;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CrudLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_farm_index_displays_bengali_labels_when_locale_is_bn(): void
    {
        $user = $this->userWithRole('manager', 'bn');

        $this->actingAs($user)
            ->get(route('farms.index'))
            ->assertOk()
            ->assertSee('ফার্ম')
            ->assertSee('ফার্মের নাম')
            ->assertSee('ফার্ম অনুসন্ধান করুন')
            ->assertSee('ফার্ম যোগ করুন');
    }

    public function test_farm_index_displays_english_labels_when_locale_is_en(): void
    {
        $user = $this->userWithRole('manager', 'en');

        $this->actingAs($user)
            ->get(route('farms.index'))
            ->assertOk()
            ->assertSee('Farms')
            ->assertSee('Farm name')
            ->assertSee('Search farms')
            ->assertSee('Add Farm');
    }

    public function test_shed_create_form_displays_bengali_labels(): void
    {
        $user = $this->userWithRole('manager', 'bn');
        Farm::factory()->create();

        $this->actingAs($user)
            ->get(route('sheds.create'))
            ->assertOk()
            ->assertSee('শেড যোগ করুন')
            ->assertSee('ফার্ম নির্বাচন করুন')
            ->assertSee('ধারণক্ষমতা');
    }

    public function test_shed_create_form_displays_english_labels(): void
    {
        $user = $this->userWithRole('manager', 'en');
        Farm::factory()->create();

        $this->actingAs($user)
            ->get(route('sheds.create'))
            ->assertOk()
            ->assertSee('Add Shed')
            ->assertSee('Select farm')
            ->assertSee('Capacity');
    }

    public function test_farm_category_index_displays_translated_filter_labels(): void
    {
        $user = $this->userWithRole('manager', 'bn');

        $this->actingAs($user)
            ->get(route('farm-categories.index'))
            ->assertOk()
            ->assertSee('সব প্যারেন্ট ক্যাটাগরি')
            ->assertSee('সব লেভেল')
            ->assertSee('সব অবস্থা')
            ->assertSee('ফিল্টার');
    }

    public function test_user_management_index_displays_translated_column_labels(): void
    {
        $user = $this->userWithRole('admin', 'bn');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('নাম')
            ->assertSee('ইমেইল')
            ->assertSee('ভূমিকা')
            ->assertSee('কার্যক্রম');
    }

    public function test_role_labels_are_translated_without_changing_database_role_names(): void
    {
        $admin = $this->userWithRole('admin', 'bn');
        $manager = $this->userWithRole('manager', 'bn');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('ম্যানেজার');

        $this->assertSame('manager', $manager->roles()->first()->name);
    }

    public function test_translated_success_message_is_shown_after_creating_a_farm(): void
    {
        $user = $this->userWithRole('manager', 'bn');

        $this->actingAs($user)
            ->post(route('farms.store'), [
                'name' => 'North Farm',
                'code' => 'NF-001',
                'phone' => '01700000000',
                'is_active' => '1',
            ])
            ->assertRedirect(route('farms.index'))
            ->assertSessionHas('success', 'ফার্ম সফলভাবে তৈরি হয়েছে।');
    }

    public function test_translated_success_message_is_shown_after_updating_a_shed(): void
    {
        $user = $this->userWithRole('manager', 'en');
        $shed = Shed::factory()->create();

        $this->actingAs($user)
            ->put(route('sheds.update', $shed), [
                'farm_id' => $shed->farm_id,
                'name' => 'Updated Shed',
                'code' => $shed->code,
                'capacity' => 1500,
                'description' => $shed->description,
                'is_active' => '1',
            ])
            ->assertRedirect(route('sheds.index'))
            ->assertSessionHas('success', 'Shed updated successfully.');
    }

    public function test_validation_messages_respect_the_active_locale(): void
    {
        $user = $this->userWithRole('manager', 'bn');
        App::setLocale('bn');

        $this->actingAs($user)
            ->post(route('farms.store'), [
                'code' => 'NF-002',
            ])
            ->assertSessionHasErrors([
                'name' => __('validation.required', ['attribute' => __('farms.farm_name')]),
            ]);
    }

    public function test_switching_language_does_not_change_stored_business_data(): void
    {
        $user = $this->userWithRole('manager', 'bn');
        $farm = Farm::factory()->create(['name' => 'Stored Farm']);
        $shed = Shed::factory()->create(['farm_id' => $farm->id, 'name' => 'Stored Shed']);
        $category = FarmCategory::factory()->create(['name' => 'Stored Category']);

        $this->actingAs($user)->post(route('language.switch', 'en'));

        $this->assertSame('Stored Farm', $farm->fresh()->name);
        $this->assertSame('Stored Shed', $shed->fresh()->name);
        $this->assertSame('Stored Category', $category->fresh()->name);
        $this->assertSame('manager', $user->roles()->first()->name);
    }

    public function test_existing_permission_restrictions_still_work(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)
            ->get(route('farms.index'))
            ->assertForbidden();
    }

    private function userWithRole(string $role, string $locale): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->syncRoles($role);

        return $user;
    }
}
