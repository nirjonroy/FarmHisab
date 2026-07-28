<?php

namespace App\Services;

use App\Enums\BatchStatus;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BatchCalculationService
{
    public function details(Batch $batch): array
    {
        $deadBirds = $this->sumExisting('mortality_records', $batch->id, ['dead_birds', 'quantity', 'birds'])
            + $this->sumExisting('daily_records', $batch->id, ['mortality_birds'])
            + $this->sumExisting('daily_records', $batch->id, ['culled_birds']);
        $soldBirds = $this->sumExisting('sales', $batch->id, ['birds_sold', 'quantity', 'sold_birds'])
            + $this->sumExisting('daily_records', $batch->id, ['sold_birds']);
        $feedConsumed = $this->sumExisting('feed_records', $batch->id, ['bags', 'quantity', 'feed_bags'])
            + $this->sumExisting('daily_records', $batch->id, ['feed_consumed_bags']);
        $feedCost = $this->sumExisting('feed_records', $batch->id, ['total_cost', 'amount', 'cost'])
            + $this->sumExisting('daily_records', $batch->id, ['feed_cost']);
        $medicineCost = $this->sumExisting('medicine_records', $batch->id, ['total_cost', 'amount', 'cost'])
            + $this->sumExisting('daily_records', $batch->id, ['medicine_cost']);
        $otherExpense = $this->sumExisting('expenses', $batch->id, ['amount', 'total_cost', 'cost']);
        $salesAmount = $this->sumExisting('sales', $batch->id, ['total_amount', 'amount', 'sale_amount']);
        $incomeAmount = $this->sumExisting('incomes', $batch->id, ['amount', 'total_amount']);
        $revenue = $salesAmount + $incomeAmount;
        $investment = (float) $batch->total_purchase_cost
            + $feedCost
            + $medicineCost
            + $otherExpense
            + (float) $batch->medicine_budget
            + (float) $batch->other_budget;

        return [
            'current_birds' => max(0, (int) $batch->initial_birds - (int) $deadBirds - (int) $soldBirds),
            'dead_birds' => (int) $deadBirds,
            'sold_birds' => (int) $soldBirds,
            'remaining_birds' => max(0, (int) $batch->initial_birds - (int) $deadBirds - (int) $soldBirds),
            'feed_consumed' => $feedConsumed,
            'feed_cost' => $feedCost,
            'medicine_cost' => $medicineCost,
            'other_expense' => $otherExpense,
            'investment' => $investment,
            'revenue' => $revenue,
            'profit' => $revenue - $investment,
        ];
    }

    public function dashboard(): array
    {
        $active = Batch::where('status', BatchStatus::ACTIVE)->count();
        $completed = Batch::where('status', BatchStatus::COMPLETED)->count();
        $activeBatches = Batch::where('status', BatchStatus::ACTIVE)->get();
        $allBatches = Batch::all();
        $totalBirds = $activeBatches->sum(fn (Batch $batch) => $this->details($batch)['current_birds']);
        $investment = $allBatches->sum(fn (Batch $batch) => $this->details($batch)['investment']);

        return [
            'active_batches' => $active,
            'completed_batches' => $completed,
            'total_birds' => $totalBirds,
            'total_investment' => $investment,
        ];
    }

    private function sumExisting(string $table, int $batchId, array $columns): float
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $column = collect($columns)->first(fn (string $column) => Schema::hasColumn($table, $column));

        if (! $column || ! Schema::hasColumn($table, 'batch_id')) {
            return 0;
        }

        $query = DB::table($table)->where('batch_id', $batchId);

        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return (float) $query->sum($column);
    }
}
