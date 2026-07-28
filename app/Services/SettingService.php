<?php

namespace App\Services;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    public function __construct(private SettingRepository $settings)
    {
    }

    public function all(): array
    {
        return Cache::rememberForever('app.settings', function () {
            $defaults = config('settings.defaults', []);

            if (! Schema::hasTable('settings')) {
                return $defaults;
            }

            return array_merge($defaults, $this->settings->allKeyed()->toArray());
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function update(array $settings): void
    {
        $allowedKeys = array_keys(config('settings.defaults', []));
        $payload = array_intersect_key($settings, array_flip($allowedKeys));

        $this->settings->updateMany($payload);
        Cache::forget('app.settings');
    }
}
