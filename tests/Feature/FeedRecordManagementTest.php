<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\FeedRecord;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedRecordManagementTest extends TestCase
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
        $this->get(route('feed.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_feed_records(): void
    {
        $this->actingAs($this->viewer())->get(route('feed.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('feed.index'))->assertForbidden();
    }

    public function test_worker_can_create_feed_usage_but_cannot_update_or_delete(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $batch = $this->batch();

        $this->actingAs($worker)->get(route('feed.create'))->assertOk();
        $this->actingAs($worker)->post(route('feed.store'), $this->payload([
            'batch_id' => $batch->id,
            'bags' => 4,
            'weight_per_bag' => 50,
            'unit_price_per_bag' => 2500,
            'feed_name' => '  Starter Feed  ',
        ]))->assertRedirect();

        $record = FeedRecord::firstOrFail();

        $this->assertSame('Starter Feed', $record->feed_name);
        $this->assertEquals(200, (float) $record->quantity_kg);
        $this->assertEquals(10000, (float) $record->total_cost);
        $this->assertSame($worker->id, $record->created_by);

        $this->actingAs($worker)->get(route('feed.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('feed.destroy', $record))->assertForbidden();
    }

    public function test_manager_can_update_and_soft_delete_feed_record(): void
    {
        $manager = $this->manager();
        $record = FeedRecord::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($manager)->put(route('feed.update', $record), $this->payload([
            'batch_id' => $record->batch_id,
            'record_date' => '2026-07-22',
            'bags' => 6,
            'weight_per_bag' => 50,
            'unit_price_per_bag' => 2400,
        ]))->assertRedirect(route('feed.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->record_date->format('Y-m-d'));
        $this->assertEquals(300, (float) $record->quantity_kg);
        $this->assertEquals(14400, (float) $record->total_cost);

        $this->actingAs($manager)->delete(route('feed.destroy', $record))->assertRedirect(route('feed.index'));
        $this->assertSoftDeleted('feed_records', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_feed_data(): void
    {
        $batch = $this->batch(['purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('feed.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-01',
            'bags' => 0,
            'weight_per_bag' => 0,
        ]))->assertSessionHasErrors(['record_date', 'bags', 'weight_per_bag']);
    }

    public function test_search_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-070', 'batch_name' => 'Feed Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-071', 'batch_name' => 'Hidden Feed']);
        $visible = FeedRecord::factory()->create(['batch_id' => $firstBatch->id, 'record_date' => '2026-07-20', 'feed_name' => 'Starter Feed']);
        FeedRecord::factory()->create(['batch_id' => $secondBatch->id, 'record_date' => '2026-06-20', 'feed_name' => 'Grower Feed']);

        $this->actingAs($this->viewer())
            ->get(route('feed.index', [
                'search' => 'Starter',
                'batch_id' => $firstBatch->id,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->feed_name)
            ->assertDontSee('Grower Feed');
    }

    public function test_feed_records_update_batch_live_details(): void
    {
        $batch = $this->batch([
            'initial_birds' => 500,
            'total_purchase_cost' => 25000,
            'medicine_budget' => 0,
            'other_budget' => 0,
        ]);

        FeedRecord::factory()->create([
            'batch_id' => $batch->id,
            'bags' => 8,
            'weight_per_bag' => 50,
            'quantity_kg' => 400,
            'unit_price_per_bag' => 2200,
            'total_cost' => 17600,
        ]);

        $this->actingAs($this->viewer(['feed.view', 'batches.view']))->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('8.00')
            ->assertSee('Tk42,600.00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'product_id' => null,
            'record_date' => '2026-07-20',
            'feed_name' => 'Starter Feed',
            'supplier_name' => 'Feed Supplier',
            'bags' => 2,
            'weight_per_bag' => 50,
            'unit_price_per_bag' => 2300,
            'notes' => 'Feed notes',
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

    private function viewer(array $permissions = ['feed.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
