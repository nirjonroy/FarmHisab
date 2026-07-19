<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ComingSoonController extends Controller
{
    private const MODULE_PERMISSIONS = [
        'farms' => 'farms.view',
        'batches' => 'batches.view',
        'daily-records' => 'daily-records.view',
        'feed' => 'feed.view',
        'feed-usage' => 'feed-usage.create',
        'medicine' => 'medicine.view',
        'mortality' => 'mortality.view',
        'weights' => 'weights.view',
        'expenses' => 'expenses.view',
        'sales' => 'sales.view',
        'inventory' => 'inventory.view',
        'reports' => 'reports.view',
        'settings' => 'settings.manage',
    ];

    public function __invoke(Request $request, string $module): View
    {
        abort_unless(array_key_exists($module, self::MODULE_PERMISSIONS), 404);
        abort_unless($request->user()->can(self::MODULE_PERMISSIONS[$module]), 403);

        return view('coming-soon', [
            'module' => str($module)->replace('-', ' ')->title(),
        ]);
    }
}
