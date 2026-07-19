<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ComingSoonController extends Controller
{
    public function __invoke(Request $request, string $module): View
    {
        $moduleConfig = config("modules.items.{$module}");

        abort_unless($moduleConfig, 404);
        abort_unless($request->user()->can($moduleConfig['permission']), 403);

        return view('coming-soon', [
            'moduleTitle' => __($moduleConfig['label']),
        ]);
    }
}
