<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeReports($request);

        return view('reports.index', [
            'reports' => config('reports.items', []),
        ]);
    }

    public function show(Request $request, string $report): View
    {
        $this->authorizeReports($request);

        $reports = config('reports.items', []);
        abort_unless(isset($reports[$report]), 404);

        return view('reports.show', [
            'reportKey' => $report,
            'report' => $reports[$report],
            'reports' => $reports,
        ]);
    }

    private function authorizeReports(Request $request): void
    {
        abort_unless($request->user()->can('reports.view'), 403);
    }
}
