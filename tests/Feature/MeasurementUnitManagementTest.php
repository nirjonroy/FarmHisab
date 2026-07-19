<?php

namespace Tests\Feature;

use App\Models\MeasurementUnit;
use App\Models\User;
use Database\Seeders\MeasurementUnitSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class MeasurementUnitManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('measurement-units.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_units(): void
    {
        $this->actingAs($this->viewer())->get(route('measurement-units.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('measurement-units.index'))->assertForbidden();
    }

    public function test_manager_can_create_and_update_a_unit(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)->get(route('measurement-units.create'))->assertOk();

        $this->actingAs($manager)->post(route('measurement-units.store'), [
            'name_en' => 'Kilogram',
            'name_bn' => 'কিলোগ্রাম',
            'short_name_en' => 'kg',
            'short_name_bn' => 'কেজি',
            'code' => 'KG',
            'decimal_places' => 2,
            'is_active' => '1',
        ])->assertRedirect(route('measurement-units.index'));

        $unit = MeasurementUnit::where('code', 'kg')->firstOrFail();

        $this->actingAs($manager)->get(route('measurement-units.edit', $unit))->assertOk();

        $this->actingAs($manager)->put(route('measurement-units.update', $unit), [
            'name_en' => 'Piece',
            'name_bn' => 'পিস',
            'short_name_en' => 'pcs',
            'short_name_bn' => 'পিস',
            'code' => 'Piece',
            'decimal_places' => 0,
            'is_active' => '0',
        ])->assertRedirect(route('measurement-units.index'));

        $this->assertDatabaseHas('measurement_units', [
            'id' => $unit->id,
            'name_en' => 'Piece',
            'code' => 'piece',
            'decimal_places' => 0,
            'is_active' => false,
        ]);
    }

    public function test_worker_can_view_but_cannot_manage(): void
    {
        $worker = User::factory()->create();
        $worker->syncRoles('worker');
        $unit = MeasurementUnit::factory()->create();

        $this->actingAs($worker)->get(route('measurement-units.index'))->assertOk();
        $this->actingAs($worker)->get(route('measurement-units.create'))->assertForbidden();
        $this->actingAs($worker)->put(route('measurement-units.update', $unit), [
            'name_en' => 'Blocked',
            'short_name_en' => 'blk',
            'code' => $unit->code,
            'decimal_places' => 0,
        ])->assertForbidden();
    }

    public function test_unit_can_be_created_with_both_language_names(): void
    {
        $this->actingAs($this->manager())->post(route('measurement-units.store'), [
            'name_en' => 'Litre',
            'name_bn' => 'লিটার',
            'short_name_en' => 'L',
            'short_name_bn' => 'লিটার',
            'code' => 'litre',
            'decimal_places' => 2,
        ])->assertRedirect(route('measurement-units.index'));

        $this->assertDatabaseHas('measurement_units', ['name_en' => 'Litre', 'name_bn' => 'লিটার']);
    }

    public function test_unit_can_be_created_with_only_one_language_name(): void
    {
        $this->actingAs($this->manager())->post(route('measurement-units.store'), [
            'name_bn' => 'বস্তা',
            'short_name_bn' => 'বস্তা',
            'code' => 'bag',
            'decimal_places' => 0,
        ])->assertRedirect(route('measurement-units.index'));

        $this->assertDatabaseHas('measurement_units', ['name_en' => null, 'name_bn' => 'বস্তা', 'code' => 'bag']);
    }

    public function test_at_least_one_full_name_is_required(): void
    {
        $this->actingAs($this->manager())->post(route('measurement-units.store'), [
            'short_name_en' => 'kg',
            'code' => 'kg',
            'decimal_places' => 2,
        ])->assertSessionHasErrors(['name_en', 'name_bn']);
    }

    public function test_at_least_one_short_name_is_required(): void
    {
        $this->actingAs($this->manager())->post(route('measurement-units.store'), [
            'name_en' => 'Kilogram',
            'code' => 'kg',
            'decimal_places' => 2,
        ])->assertSessionHasErrors(['short_name_en', 'short_name_bn']);
    }

    public function test_code_must_be_unique(): void
    {
        MeasurementUnit::factory()->create(['code' => 'kg']);

        $this->actingAs($this->manager())->post(route('measurement-units.store'), [
            'name_en' => 'Kilogram',
            'short_name_en' => 'kg',
            'code' => 'KG',
            'decimal_places' => 2,
        ])->assertSessionHasErrors('code');
    }

    public function test_code_is_normalized_to_lowercase(): void
    {
        $this->actingAs($this->manager())->post(route('measurement-units.store'), [
            'name_en' => 'Bottle',
            'short_name_en' => 'btl',
            'code' => ' BOTTLE ',
            'decimal_places' => 0,
        ])->assertRedirect(route('measurement-units.index'));

        $this->assertDatabaseHas('measurement_units', ['code' => 'bottle']);
    }

    public function test_decimal_places_must_be_between_zero_and_four(): void
    {
        $payload = [
            'name_en' => 'Invalid',
            'short_name_en' => 'inv',
            'code' => 'invalid',
        ];

        $this->actingAs($this->manager())->post(route('measurement-units.store'), $payload + [
            'decimal_places' => -1,
        ])->assertSessionHasErrors('decimal_places');

        $this->actingAs($this->manager())->post(route('measurement-units.store'), $payload + [
            'code' => 'invalid-five',
            'decimal_places' => 5,
        ])->assertSessionHasErrors('decimal_places');
    }

    public function test_locale_display_and_fallbacks_work(): void
    {
        $unit = MeasurementUnit::factory()->create([
            'name_en' => 'Kilogram',
            'name_bn' => 'কিলোগ্রাম',
            'short_name_en' => 'kg',
            'short_name_bn' => 'কেজি',
        ]);

        App::setLocale('bn');
        $this->assertSame('কিলোগ্রাম', $unit->display_name);
        $this->assertSame('কেজি', $unit->display_short_name);

        App::setLocale('en');
        $this->assertSame('Kilogram', $unit->display_name);
        $this->assertSame('kg', $unit->display_short_name);

        $englishOnly = MeasurementUnit::factory()->create([
            'name_en' => 'Packet',
            'name_bn' => null,
            'short_name_en' => 'pkt',
            'short_name_bn' => null,
        ]);

        App::setLocale('bn');
        $this->assertSame('Packet', $englishOnly->display_name);
        $this->assertSame('pkt', $englishOnly->display_short_name);
    }

    public function test_search_works_with_bengali_and_english_names(): void
    {
        MeasurementUnit::factory()->create([
            'name_en' => 'Kilogram',
            'name_bn' => 'কিলোগ্রাম',
            'short_name_en' => 'kg',
            'short_name_bn' => 'কেজি',
            'code' => 'kg',
        ]);

        $this->actingAs($this->viewer('bn'))
            ->get(route('measurement-units.index', ['search' => 'কিলোগ্রাম']))
            ->assertOk()
            ->assertSee('কিলোগ্রাম')
            ->assertSee('Kilogram');

        $this->actingAs($this->viewer('en'))
            ->get(route('measurement-units.index', ['search' => 'Kilogram']))
            ->assertOk()
            ->assertSee('Kilogram')
            ->assertSee('কিলোগ্রাম');
    }

    public function test_status_filter_works(): void
    {
        MeasurementUnit::factory()->create(['name_en' => 'Enabled Unit', 'code' => 'enabled-unit', 'is_active' => true]);
        MeasurementUnit::factory()->create(['name_en' => 'Disabled Unit', 'code' => 'disabled-unit', 'is_active' => false]);

        $this->actingAs($this->viewer())
            ->get(route('measurement-units.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('disabled-unit')
            ->assertDontSee('enabled-unit');
    }

    public function test_created_by_stores_authenticated_user(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)->post(route('measurement-units.store'), [
            'name_en' => 'Dozen',
            'short_name_en' => 'dozen',
            'code' => 'dozen',
            'decimal_places' => 0,
        ])->assertRedirect(route('measurement-units.index'));

        $this->assertDatabaseHas('measurement_units', [
            'code' => 'dozen',
            'created_by' => $manager->id,
        ]);
    }

    public function test_seeder_creates_all_expected_units_without_duplicates(): void
    {
        $this->seed(MeasurementUnitSeeder::class);
        $this->seed(MeasurementUnitSeeder::class);

        $this->assertSame(12, MeasurementUnit::count());

        foreach (['kg', 'gram', 'maund', 'ton', 'litre', 'millilitre', 'bag', 'packet', 'piece', 'dozen', 'bundle', 'bottle'] as $code) {
            $this->assertDatabaseHas('measurement_units', [
                'code' => $code,
                'is_active' => true,
                'created_by' => null,
            ]);
        }
    }

    private function manager(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->syncRoles('manager');

        return $user;
    }

    private function viewer(string $locale = 'en'): User
    {
        $user = User::factory()->create(['locale' => $locale]);
        $user->givePermissionTo('measurement-units.view');

        return $user;
    }
}
