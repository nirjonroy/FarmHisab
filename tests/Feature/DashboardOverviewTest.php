<?php

namespace Tests\Feature;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\FeedRecord;
use App\Models\MedicineRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_dashboard_displays_finance_operations_charts_and_latest_batches(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $user->syncRoles('manager');
        $batch = Batch::factory()->create([
            'batch_no' => 'B-2026-501',
            'batch_name' => 'Summer Broiler',
            'initial_birds' => 1000,
            'total_purchase_cost' => 50000,
            'medicine_budget' => 0,
            'other_budget' => 0,
            'status' => BatchStatus::ACTIVE,
        ]);

        Sale::factory()->create([
            'batch_id' => $batch->id,
            'sale_date' => now()->toDateString(),
            'birds_sold' => 100,
            'total_amount' => 30000,
            'paid_amount' => 25000,
            'due_amount' => 5000,
            'created_by' => $user->id,
        ]);
        Expense::factory()->create([
            'batch_id' => $batch->id,
            'expense_date' => now()->toDateString(),
            'amount' => 2000,
            'created_by' => $user->id,
        ]);
        FeedRecord::factory()->create([
            'batch_id' => $batch->id,
            'total_cost' => 7000,
            'created_by' => $user->id,
        ]);
        MedicineRecord::factory()->create([
            'batch_id' => $batch->id,
            'total_cost' => 1200,
            'created_by' => $user->id,
        ]);
        MortalityRecord::factory()->create([
            'batch_id' => $batch->id,
            'birds' => 12,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Revenue vs Expenses')
            ->assertSee('Cost Breakdown')
            ->assertSee('Operations Snapshot')
            ->assertSee('Latest Active Batches')
            ->assertSee('Tk30,000.00')
            ->assertSee('Tk5,000.00')
            ->assertSee('Tk7,000.00')
            ->assertSee('12')
            ->assertSee('B-2026-501')
            ->assertSee('Summer Broiler');
    }
}
