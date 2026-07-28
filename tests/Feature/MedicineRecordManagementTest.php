<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Enums\MedicineRecordType;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\MedicineRecord;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineRecordManagementTest extends TestCase
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
        $this->get(route('medicine.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_medicine_records(): void
    {
        $this->actingAs($this->viewer())->get(route('medicine.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('medicine.index'))->assertForbidden();
    }

    public function test_manager_can_create_update_and_soft_delete_medicine_record(): void
    {
        $manager = $this->manager();
        $batch = $this->batch();

        $this->actingAs($manager)->post(route('medicine.store'), $this->payload([
            'batch_id' => $batch->id,
            'medicine_name' => '  Vitamin Mix  ',
            'quantity' => 5,
            'unit_price' => 250,
        ]))->assertRedirect();

        $record = MedicineRecord::firstOrFail();
        $this->assertSame('Vitamin Mix', $record->medicine_name);
        $this->assertEquals(1250, (float) $record->total_cost);
        $this->assertSame($manager->id, $record->created_by);

        $this->actingAs($manager)->put(route('medicine.update', $record), $this->payload([
            'batch_id' => $record->batch_id,
            'record_date' => '2026-07-22',
            'type' => MedicineRecordType::VACCINE,
            'medicine_name' => 'ND Vaccine',
            'quantity' => 2,
            'unit_price' => 800,
            'next_due_date' => '2026-08-22',
        ]))->assertRedirect(route('medicine.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->record_date->format('Y-m-d'));
        $this->assertSame(MedicineRecordType::VACCINE, $record->type->value);
        $this->assertEquals(1600, (float) $record->total_cost);

        $this->actingAs($manager)->delete(route('medicine.destroy', $record))->assertRedirect(route('medicine.index'));
        $this->assertSoftDeleted('medicine_records', ['id' => $record->id]);
    }

    public function test_worker_can_view_but_cannot_create_medicine_records(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $record = MedicineRecord::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($worker)->get(route('medicine.index'))->assertOk();
        $this->actingAs($worker)->get(route('medicine.show', $record))->assertOk();
        $this->actingAs($worker)->get(route('medicine.create'))->assertForbidden();
    }

    public function test_validation_rejects_invalid_medicine_data(): void
    {
        $batch = $this->batch(['purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('medicine.store'), $this->payload([
            'batch_id' => $batch->id,
            'record_date' => '2026-07-01',
            'type' => 'invalid',
            'quantity' => 0,
            'next_due_date' => '2026-06-30',
        ]))->assertSessionHasErrors(['record_date', 'type', 'quantity', 'next_due_date']);
    }

    public function test_search_type_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-080', 'batch_name' => 'Medicine Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-081', 'batch_name' => 'Hidden Medicine']);
        $visible = MedicineRecord::factory()->create([
            'batch_id' => $firstBatch->id,
            'record_date' => '2026-07-20',
            'type' => MedicineRecordType::VACCINE,
            'medicine_name' => 'ND Vaccine',
        ]);
        MedicineRecord::factory()->create([
            'batch_id' => $secondBatch->id,
            'record_date' => '2026-06-20',
            'type' => MedicineRecordType::MEDICINE,
            'medicine_name' => 'Hidden Antibiotic',
        ]);

        $this->actingAs($this->viewer())
            ->get(route('medicine.index', [
                'search' => 'ND',
                'batch_id' => $firstBatch->id,
                'type' => MedicineRecordType::VACCINE,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->medicine_name)
            ->assertDontSee('Hidden Antibiotic');
    }

    public function test_medicine_records_update_batch_live_details(): void
    {
        $batch = $this->batch([
            'initial_birds' => 500,
            'total_purchase_cost' => 25000,
            'medicine_budget' => 0,
            'other_budget' => 0,
        ]);

        MedicineRecord::factory()->create([
            'batch_id' => $batch->id,
            'quantity' => 4,
            'unit_price' => 500,
            'total_cost' => 2000,
        ]);

        $this->actingAs($this->viewer(['medicine.view', 'batches.view']))->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('Tk2,000.00')
            ->assertSee('Tk27,000.00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'product_id' => null,
            'record_date' => '2026-07-20',
            'type' => MedicineRecordType::MEDICINE,
            'medicine_name' => 'Vitamin Mix',
            'supplier_name' => 'Medicine Supplier',
            'dosage' => '1 ml per liter',
            'purpose' => 'General health',
            'quantity' => 2,
            'unit' => 'ml',
            'unit_price' => 300,
            'next_due_date' => null,
            'notes' => 'Medicine notes',
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

    private function viewer(array $permissions = ['medicine.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
