<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Enums\InventoryMovementType;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
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
        $this->get(route('inventory.index'))->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_inventory(): void
    {
        $this->actingAs($this->viewer())->get(route('inventory.index'))->assertOk();
    }

    public function test_unauthorized_user_receives_403(): void
    {
        $this->actingAs(User::factory()->create())->get(route('inventory.index'))->assertForbidden();
    }

    public function test_worker_cannot_access_or_manage_inventory(): void
    {
        $worker = User::factory()->create(['locale' => 'en']);
        $worker->syncRoles('worker');
        $record = InventoryMovement::factory()->create([
            'product_id' => $this->product()->id,
            'batch_id' => $this->batch()->id,
        ]);

        $this->actingAs($worker)->get(route('inventory.index'))->assertForbidden();
        $this->actingAs($worker)->get(route('inventory.create'))->assertForbidden();
        $this->actingAs($worker)->post(route('inventory.store'), $this->payload())->assertForbidden();
        $this->actingAs($worker)->get(route('inventory.edit', $record))->assertForbidden();
        $this->actingAs($worker)->delete(route('inventory.destroy', $record))->assertForbidden();
    }

    public function test_manager_can_create_update_and_soft_delete_inventory_movement(): void
    {
        $manager = $this->manager();
        $product = $this->product();
        $batch = $this->batch();

        $this->actingAs($manager)->post(route('inventory.store'), $this->payload([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'quantity' => 25,
            'unit_cost' => 1200,
            'total_cost' => '',
            'supplier_name' => '  Feed Supplier  ',
        ]))->assertRedirect();

        $record = InventoryMovement::firstOrFail();

        $this->assertSame('Feed Supplier', $record->supplier_name);
        $this->assertEquals(30000, (float) $record->total_cost);
        $this->assertSame($manager->id, $record->created_by);

        $this->actingAs($manager)->put(route('inventory.update', $record), $this->payload([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'movement_date' => '2026-07-22',
            'type' => InventoryMovementType::ADJUSTMENT_IN,
            'quantity' => 30,
            'unit_cost' => 1100,
            'total_cost' => 33000,
        ]))->assertRedirect(route('inventory.show', $record));

        $record->refresh();
        $this->assertSame('2026-07-22', $record->movement_date->format('Y-m-d'));
        $this->assertSame(InventoryMovementType::ADJUSTMENT_IN, $record->type->value);
        $this->assertEquals(30, (float) $record->quantity);

        $this->actingAs($manager)->delete(route('inventory.destroy', $record))->assertRedirect(route('inventory.index'));
        $this->assertSoftDeleted('inventory_movements', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_inventory_data(): void
    {
        $batch = $this->batch(['purchase_date' => '2026-07-10']);
        $product = $this->product();

        $this->actingAs($this->manager())->post(route('inventory.store'), $this->payload([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'movement_date' => '2026-07-01',
            'type' => 'invalid',
            'quantity' => 0,
            'unit_cost' => -1,
        ]))->assertSessionHasErrors(['movement_date', 'type', 'quantity', 'unit_cost']);
    }

    public function test_outgoing_quantity_cannot_exceed_current_stock(): void
    {
        $product = $this->product();

        InventoryMovement::factory()->create([
            'product_id' => $product->id,
            'batch_id' => null,
            'type' => InventoryMovementType::PURCHASE,
            'quantity' => 10,
            'unit_cost' => 100,
            'total_cost' => 1000,
        ]);

        $this->actingAs($this->manager())->post(route('inventory.store'), $this->payload([
            'product_id' => $product->id,
            'batch_id' => null,
            'type' => InventoryMovementType::USAGE,
            'quantity' => 11,
        ]))->assertSessionHasErrors(['quantity']);
    }

    public function test_search_product_batch_type_and_date_filters_work(): void
    {
        $firstProduct = $this->product(['name_en' => 'Starter Feed', 'sku' => 'STARTER-001']);
        $secondProduct = $this->product(['name_en' => 'Hidden Feed', 'sku' => 'HIDDEN-001']);
        $firstBatch = $this->batch(['batch_no' => 'B-2026-130', 'batch_name' => 'Inventory Filter']);
        $secondBatch = $this->batch(['batch_no' => 'B-2026-131', 'batch_name' => 'Hidden Inventory']);
        $visible = InventoryMovement::factory()->create([
            'product_id' => $firstProduct->id,
            'batch_id' => $firstBatch->id,
            'movement_date' => '2026-07-20',
            'type' => InventoryMovementType::PURCHASE,
            'supplier_name' => 'Main Supplier',
        ]);
        InventoryMovement::factory()->create([
            'product_id' => $secondProduct->id,
            'batch_id' => $secondBatch->id,
            'movement_date' => '2026-06-20',
            'type' => InventoryMovementType::DAMAGE,
            'supplier_name' => 'Hidden Supplier',
        ]);

        $this->actingAs($this->viewer())
            ->get(route('inventory.index', [
                'search' => 'Starter',
                'product_id' => $firstProduct->id,
                'batch_id' => $firstBatch->id,
                'type' => InventoryMovementType::PURCHASE,
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee($visible->supplier_name)
            ->assertDontSee('Hidden Supplier');
    }

    public function test_inventory_index_displays_selected_product_stock_summary(): void
    {
        $product = $this->product();

        InventoryMovement::factory()->create([
            'product_id' => $product->id,
            'batch_id' => null,
            'type' => InventoryMovementType::PURCHASE,
            'quantity' => 100,
            'unit_cost' => 50,
            'total_cost' => 5000,
        ]);
        InventoryMovement::factory()->create([
            'product_id' => $product->id,
            'batch_id' => null,
            'type' => InventoryMovementType::USAGE,
            'quantity' => 30,
            'unit_cost' => 50,
            'total_cost' => 1500,
        ]);

        $this->actingAs($this->viewer())->get(route('inventory.index', ['product_id' => $product->id]))
            ->assertOk()
            ->assertSee('70.000')
            ->assertSee('Tk3,500.00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'product_id' => $this->product()->id,
            'batch_id' => $this->batch()->id,
            'movement_date' => '2026-07-20',
            'type' => InventoryMovementType::PURCHASE,
            'quantity' => 10,
            'unit_cost' => 100,
            'total_cost' => 1000,
            'supplier_name' => 'Feed Supplier',
            'reference_no' => 'INV-001',
            'notes' => 'Inventory notes',
        ], $overrides);
    }

    private function product(array $overrides = []): Product
    {
        return Product::factory()->create(array_merge([
            'is_active' => true,
            'is_stock_tracked' => true,
        ], $overrides));
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

    private function viewer(array $permissions = ['inventory.view']): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
