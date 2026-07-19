<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'metrics' => [
                'Active batches' => '0',
                'Total birds' => '0',
                'Feed stock' => '0 bags',
                'Current investment' => 'Tk0',
                'Estimated profit' => 'Tk0',
                "Today's mortality" => '0',
            ],
        ]);
    }
}
