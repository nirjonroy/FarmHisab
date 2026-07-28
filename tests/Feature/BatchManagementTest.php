<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchManagementTest extends TestCase
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
        $this->get(route('batches.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_batches(): void
    {
        $this->actingAs($this->viewer())->get(route('batches.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('batches.index'))->assertForbidden();
    }

    public function test_manager_can_create_update_view_and_soft_delete_batch(): void
    {
        $manager = $this->manager();
        $payload = $this->payload([
            'batch_name' => '  July Broiler Batch  ',
            'initial_birds' => 1000,
            'purchase_price_per_bird' => 75.5,
        ]);

        $this->actingAs($manager)->get(route('batches.create'))->assertOk();

        $this->actingAs($manager)->post(route('batches.store'), $payload)
            ->assertRedirect(route('batches.index'));

        $batch = Batch::firstOrFail();

        $this->assertSame('B-2026-001', $batch->batch_no);
        $this->assertSame('July Broiler Batch', $batch->batch_name);
        $this->assertEquals(75500, (float) $batch->total_purchase_cost);
        $this->assertSame($manager->id, $batch->created_by);

        $this->actingAs($manager)->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('B-2026-001')
            ->assertSee('Current Birds')
            ->assertSee('1,000');

        $this->actingAs($manager)->put(route('batches.update', $batch), $this->payload([
            'batch_name' => 'Completed Broiler Batch',
            'status' => BatchStatus::COMPLETED,
        ], $batch))->assertRedirect(route('batches.show', $batch));

        $this->assertDatabaseHas('batches', [
            'id' => $batch->id,
            'batch_name' => 'Completed Broiler Batch',
            'status' => BatchStatus::COMPLETED,
        ]);

        $this->actingAs($manager)->delete(route('batches.destroy', $batch))
            ->assertRedirect(route('batches.index'));

        $this->assertSoftDeleted('batches', ['id' => $batch->id]);
    }

    public function test_worker_can_view_but_cannot_manage_batches(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $batch = Batch::factory()->create();

        $this->actingAs($worker)->get(route('batches.index'))->assertOk();
        $this->actingAs($worker)->get(route('batches.show', $batch))->assertOk();
        $this->actingAs($worker)->get(route('batches.create'))->assertForbidden();
        $this->actingAs($worker)->put(route('batches.update', $batch), $this->payload([], $batch))->assertForbidden();
        $this->actingAs($worker)->delete(route('batches.destroy', $batch))->assertForbidden();
    }

    public function test_search_status_and_date_filters_work(): void
    {
        $visible = Batch::factory()->create([
            'batch_no' => 'B-2026-010',
            'batch_name' => 'Visible Broiler',
            'supplier_name' => 'Searchable Supplier',
            'purchase_date' => '2026-07-20',
            'status' => BatchStatus::ACTIVE,
        ]);
        $hidden = Batch::factory()->create([
            'batch_no' => 'B-2026-011',
            'batch_name' => 'Hidden Layer',
            'purchase_date' => '2026-06-15',
            'status' => BatchStatus::COMPLETED,
        ]);

        $this->actingAs($this->viewer())
            ->get(route('batches.index', [
                'search' => 'Searchable',
                'status' => BatchStatus::ACTIVE,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->batch_no)
            ->assertDontSee($hidden->batch_no);
    }

    public function test_validation_rejects_invalid_batch_data(): void
    {
        $topLevel = FarmCategory::factory()->create(['parent_id' => null]);
        $otherBirdType = $this->birdType('Layer');
        $breed = FarmVariety::factory()->create(['farm_category_id' => $otherBirdType->id]);

        $this->actingAs($this->manager())->post(route('batches.store'), $this->payload([
            'bird_type_id' => $topLevel->id,
            'breed_id' => $breed->id,
            'arrival_date' => '2026-07-01',
            'purchase_date' => '2026-07-10',
            'initial_birds' => 0,
            'status' => 'invalid',
        ]))->assertSessionHasErrors([
            'bird_type_id',
            'breed_id',
            'arrival_date',
            'initial_birds',
            'status',
        ]);
    }

    public function test_dashboard_cards_show_batch_summary(): void
    {
        Batch::factory()->create([
            'initial_birds' => 500,
            'total_purchase_cost' => 25000,
            'medicine_budget' => 1000,
            'other_budget' => 500,
            'status' => BatchStatus::ACTIVE,
        ]);
        Batch::factory()->create([
            'initial_birds' => 400,
            'total_purchase_cost' => 20000,
            'medicine_budget' => 500,
            'other_budget' => 250,
            'status' => BatchStatus::COMPLETED,
        ]);

        $this->actingAs($this->manager())->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Active batches')
            ->assertSee('Completed batches')
            ->assertSee('500')
            ->assertSee('Tk47,250.00');
    }

    public function test_details_calculate_default_live_totals_without_future_record_tables(): void
    {
        $batch = Batch::factory()->create([
            'initial_birds' => 300,
            'total_purchase_cost' => 18000,
            'medicine_budget' => 1200,
            'other_budget' => 800,
        ]);

        $this->actingAs($this->viewer())->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('300')
            ->assertSee('Tk20,000.00')
            ->assertSee('Tk-20,000.00');
    }

    private function payload(array $overrides = [], ?Batch $batch = null): array
    {
        $birdType = $batch?->birdType ?? $this->birdType();
        $breed = $batch?->breed ?? FarmVariety::factory()->create(['farm_category_id' => $birdType->id]);

        return array_merge([
            'batch_name' => 'Test Batch',
            'farm_id' => $batch?->farm_id ?? Farm::factory()->create(['is_active' => true])->id,
            'bird_type_id' => $birdType->id,
            'breed_id' => $breed->id,
            'supplier_name' => 'Test Supplier',
            'purchase_date' => '2026-07-10',
            'arrival_date' => '2026-07-11',
            'initial_birds' => 100,
            'purchase_price_per_bird' => 100,
            'total_purchase_cost' => 0,
            'expected_market_weight' => 2.3,
            'expected_market_age' => 35,
            'feed_target_bags' => 30,
            'medicine_budget' => 1000,
            'other_budget' => 500,
            'notes' => 'Test notes',
            'status' => BatchStatus::ACTIVE,
        ], $overrides);
    }

    private function birdType(string $name = 'Broiler'): FarmCategory
    {
        $parent = FarmCategory::factory()->create(['parent_id' => null, 'name_en' => 'Poultry '.$name]);

        return FarmCategory::factory()->create(['parent_id' => $parent->id, 'name_en' => $name]);
    }

    private function manager(): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('manager');

        return $user;
    }

    private function viewer(): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo('batches.view');

        return $user;
    }
}
