<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\DailyRecord;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyRecordManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-07-29 10:00:00');
        $this->seed(RolePermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('daily-records.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_daily_records(): void
    {
        $this->actingAs($this->viewer())->get(route('daily-records.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('daily-records.index'))->assertForbidden();
    }

    public function test_user_with_create_permission_can_create_daily_record(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo(['daily-records.view', 'daily-records.create']);
        $batch = $this->batch(['initial_birds' => 1000]);

        $this->actingAs($user)->post(route('daily-records.store'), $this->payload([
            'batch_id' => $batch->id,
            'opening_birds' => 1000,
            'mortality_birds' => 12,
            'culled_birds' => 3,
            'sold_birds' => 100,
            'feed_consumed_bags' => 20.5,
            'feed_cost' => 5000,
            'medicine_cost' => 350,
            'notes' => '  Morning check completed  ',
        ]))->assertRedirect();

        $record = DailyRecord::firstOrFail();

        $this->assertSame(885, $record->closing_birds);
        $this->assertSame('Morning check completed', $record->notes);
        $this->assertSame($user->id, $record->created_by);
    }

    public function test_manager_can_update_and_soft_delete_daily_record(): void
    {
        $manager = $this->manager();
        $record = DailyRecord::factory()->create([
            'batch_id' => $this->batch()->id,
            'record_date' => '2026-07-20',
            'opening_birds' => 500,
            'mortality_birds' => 5,
            'culled_birds' => 0,
            'sold_birds' => 0,
            'closing_birds' => 495,
        ]);

        $this->actingAs($manager)->put(route('daily-records.update', $record), $this->payload([
            'batch_id' => $record->batch_id,
            'record_date' => '2026-07-21',
            'opening_birds' => 495,
            'mortality_birds' => 4,
            'sold_birds' => 20,
        ]))->assertRedirect(route('daily-records.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-21', $record->record_date->format('Y-m-d'));
        $this->assertSame(471, $record->closing_birds);

        $this->actingAs($manager)->delete(route('daily-records.destroy', $record))
            ->assertRedirect(route('daily-records.index'));

        $this->assertSoftDeleted('daily_records', ['id' => $record->id]);
    }

    public function test_worker_can_create_but_cannot_update_daily_records(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $record = DailyRecord::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($worker)->get(route('daily-records.index'))->assertOk();
        $this->actingAs($worker)->get(route('daily-records.create'))->assertOk();
        $this->actingAs($worker)->get(route('daily-records.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('daily-records.destroy', $record))->assertForbidden();
    }

    public function test_validation_rejects_invalid_daily_record_data(): void
    {
        $batch = $this->batch(['purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('daily-records.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-01',
            'opening_birds' => 10,
            'mortality_birds' => 8,
            'culled_birds' => 3,
            'humidity' => 150,
        ]))->assertSessionHasErrors([
            'record_date',
            'opening_birds',
            'humidity',
        ]);
    }

    public function test_record_date_must_be_unique_per_batch(): void
    {
        $batch = $this->batch();
        DailyRecord::factory()->create(['batch_id' => $batch->id, 'record_date' => '2026-07-20']);

        $this->actingAs($this->manager())->post(route('daily-records.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-20',
        ]))->assertSessionHasErrors('record_date');
    }

    public function test_search_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-050', 'batch_name' => 'Filter Batch']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-051', 'batch_name' => 'Hidden Batch']);
        $visible = DailyRecord::factory()->create(['batch_id' => $firstBatch->id, 'record_date' => '2026-07-20']);
        $hidden = DailyRecord::factory()->create(['batch_id' => $secondBatch->id, 'record_date' => '2026-06-20']);

        $this->actingAs($this->viewer())
            ->get(route('daily-records.index', [
                'search' => 'Filter',
                'batch_id' => $firstBatch->id,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->record_date->format('Y-m-d'))
            ->assertDontSee('2026-06-20');
    }

    public function test_daily_records_update_batch_live_details(): void
    {
        $batch = $this->batch([
            'initial_birds' => 1000,
            'total_purchase_cost' => 50000,
            'medicine_budget' => 0,
            'other_budget' => 0,
        ]);
        DailyRecord::factory()->create([
            'batch_id' => $batch->id,
            'opening_birds' => 1000,
            'mortality_birds' => 10,
            'culled_birds' => 5,
            'sold_birds' => 100,
            'closing_birds' => 885,
            'feed_consumed_bags' => 30,
            'feed_cost' => 6000,
            'medicine_cost' => 500,
        ]);

        $this->actingAs($this->viewer(['daily-records.view', 'batches.view']))->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('885')
            ->assertSee('15')
            ->assertSee('30.00')
            ->assertSee('Tk56,500.00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'record_date' => '2026-07-20',
            'opening_birds' => 100,
            'mortality_birds' => 1,
            'culled_birds' => 0,
            'sold_birds' => 0,
            'feed_consumed_bags' => 5,
            'feed_cost' => 1000,
            'medicine_cost' => 100,
            'average_weight' => 1.25,
            'temperature' => 31,
            'humidity' => 70,
            'notes' => 'Daily notes',
        ], $overrides);
    }

    private function batch(array $overrides = []): Batch
    {
        $parent = FarmCategory::factory()->create(['parent_id' => null, 'name_en' => 'Poultry']);
        $birdType = FarmCategory::factory()->create(['parent_id' => $parent->id, 'name_en' => 'Broiler']);
        $breed = FarmVariety::factory()->create(['farm_category_id' => $birdType->id]);

        return Batch::factory()->create(array_merge([
            'farm_id' => Farm::factory()->create(['is_active' => true])->id,
            'bird_type_id' => $birdType->id,
            'breed_id' => $breed->id,
            'purchase_date' => '2026-07-10',
            'status' => BatchStatus::ACTIVE,
        ], $overrides));
    }

    private function manager(): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('manager');

        return $user;
    }

    private function viewer(array $permissions = ['daily-records.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
