<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Enums\MortalityRecordType;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\MortalityRecord;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MortalityRecordManagementTest extends TestCase
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
        $this->get(route('mortality.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_mortality_records(): void
    {
        $this->actingAs($this->viewer())->get(route('mortality.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('mortality.index'))->assertForbidden();
    }

    public function test_worker_can_create_but_cannot_update_mortality_records(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $batch = $this->batch(['initial_birds' => 1000]);

        $this->actingAs($worker)->post(route('mortality.store'), $this->payload([
            'batch_id' => $batch->id,
            'birds' => 12,
            'cause' => '  Heat stress  ',
        ]))->assertRedirect();

        $record = MortalityRecord::firstOrFail();

        $this->assertSame('Heat stress', $record->cause);
        $this->assertSame(12, $record->birds);
        $this->assertSame($worker->id, $record->created_by);

        $this->actingAs($worker)->get(route('mortality.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('mortality.destroy', $record))->assertForbidden();
    }

    public function test_manager_can_update_and_soft_delete_mortality_record(): void
    {
        $manager = $this->manager();
        $record = MortalityRecord::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($manager)->put(route('mortality.update', $record), $this->payload([
            'batch_id' => $record->batch_id,
            'record_date' => '2026-07-22',
            'type' => MortalityRecordType::CULL,
            'birds' => 6,
            'cause' => 'Weak birds',
        ]))->assertRedirect(route('mortality.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->record_date->format('Y-m-d'));
        $this->assertSame(MortalityRecordType::CULL, $record->type->value);
        $this->assertSame(6, $record->birds);

        $this->actingAs($manager)->delete(route('mortality.destroy', $record))->assertRedirect(route('mortality.index'));
        $this->assertSoftDeleted('mortality_records', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_mortality_data(): void
    {
        $batch = $this->batch(['initial_birds' => 10, 'purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('mortality.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-01',
            'type' => 'invalid',
            'birds' => 11,
        ]))->assertSessionHasErrors(['record_date', 'type', 'birds']);
    }

    public function test_search_type_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-090', 'batch_name' => 'Mortality Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-091', 'batch_name' => 'Hidden Mortality']);
        $visible = MortalityRecord::factory()->create([
            'batch_id' => $firstBatch->id,
            'record_date' => '2026-07-20',
            'type' => MortalityRecordType::MORTALITY,
            'cause' => 'Heat stress',
        ]);
        MortalityRecord::factory()->create([
            'batch_id' => $secondBatch->id,
            'record_date' => '2026-06-20',
            'type' => MortalityRecordType::CULL,
            'cause' => 'Hidden cause',
        ]);

        $this->actingAs($this->viewer())
            ->get(route('mortality.index', [
                'search' => 'Heat',
                'batch_id' => $firstBatch->id,
                'type' => MortalityRecordType::MORTALITY,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->cause)
            ->assertDontSee('Hidden cause');
    }

    public function test_mortality_records_update_batch_live_details(): void
    {
        $batch = $this->batch(['initial_birds' => 500]);

        MortalityRecord::factory()->create([
            'batch_id' => $batch->id,
            'birds' => 15,
        ]);

        $this->actingAs($this->viewer(['mortality.view', 'batches.view']))->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('485')
            ->assertSee('15');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'record_date' => '2026-07-20',
            'type' => MortalityRecordType::MORTALITY,
            'birds' => 2,
            'cause' => 'Heat stress',
            'action_taken' => 'Improved ventilation',
            'reported_by' => 'Farm worker',
            'notes' => 'Mortality notes',
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

    private function viewer(array $permissions = ['mortality.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
