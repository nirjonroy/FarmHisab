<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'metrics' => [
                'dashboard.active_batches' => '0',
                'dashboard.total_birds' => '0',
                'dashboard.feed_stock' => '0 bags',
                'dashboard.current_investment' => 'Tk0',
                'dashboard.estimated_profit' => 'Tk0',
                'dashboard.todays_mortality' => '0',
            ],
        ]);
    }
}
