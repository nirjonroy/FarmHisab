<?php

namespace App\Http\Controllers;

use App\Enums\InventoryMovementType;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\FeedRecord;
use App\Models\InventoryMovement;
use App\Models\MedicineRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Services\BatchCalculationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(BatchCalculationService $calculations): View
    {
        $summary = $calculations->dashboard();
        $salesTotal = (float) Sale::query()->sum('total_amount');
        $salesDue = (float) Sale::query()->sum('due_amount');
        $expenseTotal = (float) Expense::query()->sum('amount');
        $feedCost = (float) FeedRecord::query()->sum('total_cost');
        $medicineCost = (float) MedicineRecord::query()->sum('total_cost');
        $mortalityBirds = (int) MortalityRecord::query()->sum('birds');
        $inventoryValue = $this->inventoryValue();
        $profit = $salesTotal - (float) $summary['total_investment'];
        $monthlyTrend = $this->monthlyTrend();
        $costBreakdown = $this->costBreakdown((float) $summary['total_investment'], $feedCost, $medicineCost, $expenseTotal);
        $latestBatches = Batch::query()
            ->with(['farm', 'breed'])
            ->active()
            ->ordered()
            ->limit(5)
            ->get()
            ->map(function (Batch $batch) use ($calculations) {
                $details = $calculations->details($batch);

                return [
                    'batch' => $batch,
                    'current_birds' => $details['current_birds'],
                    'investment' => $details['investment'],
                    'profit' => $details['profit'],
                ];
            });

        return view('dashboard.index', [
            'metrics' => [
                'dashboard.active_batches' => number_format($summary['active_batches']),
                'dashboard.completed_batches' => number_format($summary['completed_batches']),
                'dashboard.total_birds' => number_format($summary['total_birds']),
                'dashboard.total_investment' => 'Tk'.number_format($summary['total_investment'], 2),
            ],
            'financeMetrics' => [
                'dashboard.total_revenue' => 'Tk'.number_format($salesTotal, 2),
                'dashboard.total_expenses' => 'Tk'.number_format($expenseTotal + $feedCost + $medicineCost, 2),
                'dashboard.net_profit' => 'Tk'.number_format($profit, 2),
                'dashboard.total_due' => 'Tk'.number_format($salesDue, 2),
            ],
            'operationMetrics' => [
                'dashboard.feed_cost' => 'Tk'.number_format($feedCost, 2),
                'dashboard.medicine_cost' => 'Tk'.number_format($medicineCost, 2),
                'dashboard.mortality_birds' => number_format($mortalityBirds),
                'dashboard.inventory_value' => 'Tk'.number_format($inventoryValue, 2),
            ],
            'monthlyTrend' => $monthlyTrend,
            'costBreakdown' => $costBreakdown,
            'latestBatches' => $latestBatches,
        ]);
    }

    private function monthlyTrend(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $monthsAgo) => now()->startOfMonth()->subMonths($monthsAgo));
        $start = $months->first()->copy()->startOfMonth();
        $sales = Sale::query()->whereDate('sale_date', '>=', $start)->get(['sale_date', 'total_amount']);
        $expenses = Expense::query()->whereDate('expense_date', '>=', $start)->get(['expense_date', 'amount']);
        $max = 1;

        $data = $months->map(function (Carbon $month) use ($sales, $expenses, &$max) {
            $key = $month->format('Y-m');
            $revenue = (float) $sales
                ->filter(fn (Sale $sale) => optional($sale->sale_date)->format('Y-m') === $key)
                ->sum('total_amount');
            $expense = (float) $expenses
                ->filter(fn (Expense $expense) => optional($expense->expense_date)->format('Y-m') === $key)
                ->sum('amount');

            $max = max($max, $revenue, $expense);

            return [
                'label' => $month->format('M'),
                'revenue' => $revenue,
                'expenses' => $expense,
            ];
        });

        return [
            'max' => $max,
            'items' => $data,
        ];
    }

    private function costBreakdown(float $investment, float $feedCost, float $medicineCost, float $expenseTotal): Collection
    {
        $birdPurchase = max(0, $investment - $feedCost - $medicineCost - $expenseTotal);
        $items = collect([
            ['label' => 'dashboard.bird_purchase', 'amount' => $birdPurchase],
            ['label' => 'dashboard.feed_cost', 'amount' => $feedCost],
            ['label' => 'dashboard.medicine_cost', 'amount' => $medicineCost],
            ['label' => 'dashboard.other_expenses', 'amount' => $expenseTotal],
        ]);
        $max = max(1, (float) $items->max('amount'));

        return $items->map(fn (array $item) => $item + [
            'percentage' => round(((float) $item['amount'] / $max) * 100, 2),
        ]);
    }

    private function inventoryValue(): float
    {
        $inbound = (float) InventoryMovement::query()
            ->whereIn('type', InventoryMovementType::inboundValues())
            ->sum('total_cost');
        $outbound = (float) InventoryMovement::query()
            ->whereIn('type', InventoryMovementType::outboundValues())
            ->sum('total_cost');

        return max(0, $inbound - $outbound);
    }
}
