<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\User;
use App\Models\WeightRecord;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeightRecordManagementTest extends TestCase
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
        $this->get(route('weights.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_weight_records(): void
    {
        $this->actingAs($this->viewer())->get(route('weights.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('weights.index'))->assertForbidden();
    }

    public function test_worker_can_create_but_cannot_update_weight_records(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $batch = $this->batch(['purchase_date' => '2026-07-10']);

        $this->actingAs($worker)->post(route('weights.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-20',
            'age_days' => '',
            'sample_birds' => 50,
            'average_weight' => 1.25,
            'total_weight' => '',
            'weighed_by' => '  Farm Worker  ',
        ]))->assertRedirect();

        $record = WeightRecord::firstOrFail();

        $this->assertSame(10, $record->age_days);
        $this->assertEquals(62.5, (float) $record->total_weight);
        $this->assertSame('Farm Worker', $record->weighed_by);
        $this->assertSame($worker->id, $record->created_by);

        $this->actingAs($worker)->get(route('weights.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('weights.destroy', $record))->assertForbidden();
    }

    public function test_manager_can_update_and_soft_delete_weight_record(): void
    {
        $manager = $this->manager();
        $record = WeightRecord::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($manager)->put(route('weights.update', $record), $this->payload([
            'batch_id' => $record->batch_id,
            'record_date' => '2026-07-22',
            'sample_birds' => 75,
            'average_weight' => 1.75,
            'total_weight' => 131.25,
        ]))->assertRedirect(route('weights.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->record_date->format('Y-m-d'));
        $this->assertSame(75, $record->sample_birds);
        $this->assertEquals(1.75, (float) $record->average_weight);

        $this->actingAs($manager)->delete(route('weights.destroy', $record))->assertRedirect(route('weights.index'));
        $this->assertSoftDeleted('weight_records', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_weight_data(): void
    {
        $batch = $this->batch(['initial_birds' => 10, 'purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('weights.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-01',
            'sample_birds' => 11,
            'average_weight' => 0,
            'uniformity_percentage' => 101,
        ]))->assertSessionHasErrors(['record_date', 'sample_birds', 'average_weight', 'uniformity_percentage']);
    }

    public function test_record_date_must_be_unique_per_batch(): void
    {
        $batch = $this->batch();
        WeightRecord::factory()->create(['batch_id' => $batch->id, 'record_date' => '2026-07-20']);

        $this->actingAs($this->manager())->post(route('weights.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-20',
        ]))->assertSessionHasErrors(['record_date']);
    }

    public function test_search_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-100', 'batch_name' => 'Weight Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-101', 'batch_name' => 'Hidden Weight']);
        $visible = WeightRecord::factory()->create([
            'batch_id' => $firstBatch->id,
            'record_date' => '2026-07-20',
            'weighed_by' => 'Growth Team',
        ]);
        WeightRecord::factory()->create([
            'batch_id' => $secondBatch->id,
            'record_date' => '2026-06-20',
            'weighed_by' => 'Hidden Team',
        ]);

        $this->actingAs($this->viewer())
            ->get(route('weights.index', [
                'search' => 'Growth',
                'batch_id' => $firstBatch->id,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->weighed_by)
            ->assertDontSee('Hidden Team');
    }

    public function test_weight_details_page_displays_growth_summary(): void
    {
        $record = WeightRecord::factory()->create([
            'batch_id' => $this->batch()->id,
            'average_weight' => 1.654,
            'target_weight' => 1.800,
            'sample_birds' => 80,
            'uniformity_percentage' => 88.25,
        ]);

        $this->actingAs($this->viewer())->get(route('weights.show', $record))
            ->assertOk()
            ->assertSee('1.654 kg')
            ->assertSee('1.800 kg')
            ->assertSee('88.25%');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'record_date' => '2026-07-20',
            'age_days' => 10,
            'sample_birds' => 40,
            'average_weight' => 1.2,
            'total_weight' => 48,
            'target_weight' => 1.3,
            'uniformity_percentage' => 85,
            'weighed_by' => 'Farm worker',
            'notes' => 'Growth is on track',
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

    private function viewer(array $permissions = ['weights.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
