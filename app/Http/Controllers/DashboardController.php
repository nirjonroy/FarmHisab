<?php

namespace App\Http\Controllers;

use App\Services\BatchCalculationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(BatchCalculationService $calculations): View
    {
        $summary = $calculations->dashboard();

        return view('dashboard.index', [
            'metrics' => [
                'dashboard.active_batches' => number_format($summary['active_batches']),
                'dashboard.completed_batches' => number_format($summary['completed_batches']),
                'dashboard.total_birds' => number_format($summary['total_birds']),
                'dashboard.total_investment' => 'Tk'.number_format($summary['total_investment'], 2),
            ],
        ]);
    }
}
