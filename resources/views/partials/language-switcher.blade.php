@php
    $currentLocale = app()->getLocale();
    $locales = config('localization.supported_locales', ['bn', 'en']);
    $localeNames = config('localization.names', []);
@endphp

<div class="dropdown">
    <button class="btn {{ $buttonClass ?? 'btn-outline-success' }} btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ __('common.language') }}: {{ $localeNames[$currentLocale] ?? $currentLocale }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @foreach ($locales as $locale)
            <li>
                <form method="POST" action="{{ route('language.switch', $locale) }}">
                    @csrf
                    <button type="submit" class="dropdown-item {{ $currentLocale === $locale ? 'active' : '' }}">
                        {{ $localeNames[$locale] ?? $locale }}
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
