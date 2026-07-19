<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = config('localization.supported_locales', ['bn', 'en']);
        $defaultLocale = config('localization.default_locale', config('app.locale', 'bn'));

        $locale = $request->user()?->locale
            ?? $request->session()->get('locale')
            ?? $defaultLocale;

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $defaultLocale;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
