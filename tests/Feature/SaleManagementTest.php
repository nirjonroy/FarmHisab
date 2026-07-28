<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Enums\PaymentMethod;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleManagementTest extends TestCase
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
        $this->get(route('sales.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_sales(): void
    {
        $this->actingAs($this->viewer())->get(route('sales.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('sales.index'))->assertForbidden();
    }

    public function test_worker_cannot_access_or_manage_sales(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $record = Sale::factory()->create(['batch_id' => $this->batch()->id]);

        $this->actingAs($worker)->get(route('sales.index'))->assertForbidden();
        $this->actingAs($worker)->get(route('sales.create'))->assertForbidden();
        $this->actingAs($worker)->post(route('sales.store'), $this->payload())->assertForbidden();
        $this->actingAs($worker)->get(route('sales.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('sales.destroy', $record))->assertForbidden();
    }

    public function test_manager_can_create_update_and_soft_delete_sale(): void
    {
        $manager = $this->manager();
        $batch = $this->batch(['initial_birds' => 1000]);

        $this->actingAs($manager)->post(route('sales.store'), $this->payload([
            'batch_id' => $batch->id,
            'buyer_name' => '  Dhaka Buyer  ',
            'birds_sold' => 100,
            'average_weight' => 2,
            'total_weight' => '',
            'rate_per_kg' => 190,
            'total_amount' => '',
            'paid_amount' => 30000,
        ]))->assertRedirect();

        $record = Sale::firstOrFail();

        $this->assertSame('Dhaka Buyer', $record->buyer_name);
        $this->assertEquals(200, (float) $record->total_weight);
        $this->assertEquals(38000, (float) $record->total_amount);
        $this->assertEquals(8000, (float) $record->due_amount);
        $this->assertSame($manager->id, $record->created_by);

        $this->actingAs($manager)->put(route('sales.update', $record), $this->payload([
            'batch_id' => $batch->id,
            'sale_date' => '2026-07-22',
            'birds_sold' => 120,
            'total_weight' => 252,
            'rate_per_kg' => 195,
            'total_amount' => 49140,
            'paid_amount' => 49140,
        ]))->assertRedirect(route('sales.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->sale_date->format('Y-m-d'));
        $this->assertSame(120, $record->birds_sold);
        $this->assertEquals(0, (float) $record->due_amount);

        $this->actingAs($manager)->delete(route('sales.destroy', $record))->assertRedirect(route('sales.index'));
        $this->assertSoftDeleted('sales', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_sale_data(): void
    {
        $batch = $this->batch(['initial_birds' => 10, 'purchase_date' => '2026-07-10']);

        $this->actingAs($this->manager())->post(route('sales.store'), $this->payload([
            'batch_id' => $batch->id,
            'sale_date' => '2026-07-01',
            'buyer_name' => '',
            'birds_sold' => 11,
            'total_weight' => 0,
            'rate_per_kg' => 0,
            'payment_method' => 'invalid',
            'paid_amount' => 999999,
        ]))->assertSessionHasErrors(['sale_date', 'buyer_name', 'birds_sold', 'total_weight', 'rate_per_kg', 'payment_method', 'paid_amount']);
    }

    public function test_search_payment_batch_and_date_filters_work(): void
    {
        $firstBatch = $this->batch(['batch_no' => 'B-2026-120', 'batch_name' => 'Sale Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-121', 'batch_name' => 'Hidden Sale']);
        $visible = Sale::factory()->create([
            'batch_id' => $firstBatch->id,
            'sale_date' => '2026-07-20',
            'payment_method' => PaymentMethod::CASH,
            'buyer_name' => 'Dhaka Buyer',
        ]);
        Sale::factory()->create([
            'batch_id' => $secondBatch->id,
            'sale_date' => '2026-06-20',
            'payment_method' => PaymentMethod::BANK,
            'buyer_name' => 'Hidden Buyer',
        ]);

        $this->actingAs($this->viewer())
            ->get(route('sales.index', [
                'search' => 'Dhaka',
                'batch_id' => $firstBatch->id,
                'payment_method' => PaymentMethod::CASH,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->buyer_name)
            ->assertDontSee('Hidden Buyer');
    }

    public function test_sales_update_batch_live_details(): void
    {
        $batch = $this->batch([
            'initial_birds' => 500,
            'total_purchase_cost' => 25000,
            'medicine_budget' => 0,
            'other_budget' => 0,
        ]);

        Sale::factory()->create([
            'batch_id' => $batch->id,
            'birds_sold' => 50,
            'total_amount' => 30000,
        ]);

        $this->actingAs($this->viewer(['sales.view', 'batches.view']))->get(route('batches.show', $batch))
            ->assertOk()
            ->assertSee('450')
            ->assertSee('50')
            ->assertSee('Tk5,000.00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $this->batch()->id,
            'sale_date' => '2026-07-20',
            'buyer_name' => 'Dhaka Buyer',
            'buyer_phone' => '01700000000',
            'birds_sold' => 40,
            'average_weight' => 2,
            'total_weight' => 80,
            'rate_per_kg' => 190,
            'total_amount' => 15200,
            'payment_method' => PaymentMethod::CASH,
            'paid_amount' => 15000,
            'due_amount' => 200,
            'reference_no' => 'SAL-001',
            'notes' => 'Sale notes',
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

    private function viewer(array $permissions = ['sales.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
