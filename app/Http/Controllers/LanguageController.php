<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class LanguageController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, config('localization.supported_locales', []), true), 404);

        $request->session()->put('locale', $locale);

        if ($request->user() && $request->user()->locale !== $locale) {
            $request->user()->forceFill(['locale' => $locale])->save();
        }

        return redirect()->to($this->safePreviousUrl($request))
            ->with('success', __('messages.language_changed'));
    }

    private function safePreviousUrl(Request $request): string
    {
        $previous = URL::previous();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $previousHost = parse_url($previous, PHP_URL_HOST);

        if ($previousHost === null || $previousHost === $appHost || $previousHost === $request->getHost()) {
            return $previous;
        }

        return $request->user() ? route('dashboard') : route('login');
    }
}
