<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private SettingService $settings)
    {
    }

    public function edit(Request $request): View
    {
        abort_unless($request->user()->can('settings.manage'), 403);

        return view('settings.edit', [
            'settings' => $this->settings->all(),
            'locales' => config('localization.names', []),
            'months' => $this->months(),
            'timezones' => [
                'Asia/Dhaka',
                'UTC',
                'Asia/Kolkata',
                'Asia/Dubai',
                'Europe/London',
                'America/New_York',
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        return redirect()->route('settings.edit')->with('success', __('settings.update_success'));
    }

    private function months(): array
    {
        return [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
    }
}
